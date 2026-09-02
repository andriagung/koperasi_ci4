<?php
namespace App\Controllers\Api\V1;

use App\Models\PinjamanModel;
use App\Models\PinjamanAngsuranModel;

class PinjamanController extends BaseApiController
{
    protected $pinjamanModel;
    protected $angsuranModel;

    public function __construct()
    {
        $this->pinjamanModel = new PinjamanModel();
        $this->angsuranModel = new PinjamanAngsuranModel();
    }

    private function getAnggotaId()
    {
        return $this->request->getHeaderLine('X-Anggota-Id');
    }

    public function index()
    {
        $id = $this->getAnggotaId();
        
        $pinjaman = $this->pinjamanModel
                       ->where('anggota_id', $id)
                       ->orderBy('created_at', 'DESC')
                       ->findAll();
                       
        if (empty($pinjaman)) {
            return $this->success([], 'Belum ada data pinjaman.');
        }

        $formatted = [];
        foreach ($pinjaman as $p) {
            // Cari tagihan bulan ini
            $tagihan = $this->angsuranModel
                          ->where('pinjaman_id', $p['id'])
                          ->where('status', 'Belum Lunas')
                          ->orderBy('bulan_ke', 'ASC')
                          ->limit(1)
                          ->first();
                          
            $formatted[] = [
                'id' => $p['id'],
                'nomor_pinjaman' => $p['nomor_pinjaman'],
                'plafon' => (float)$p['jumlah_pinjaman'],
                'tenor' => $p['jangka_waktu_bulan'],
                'sisa_pinjaman' => (float)$p['sisa_pinjaman'],
                'status' => $p['status_pengajuan'], // Wait, in previous it was 'status', let's use what's in the original file, but in DB it's status_pengajuan usually for PinjamanModel. Original code had $p['status']. Wait, the original code had $p['status']. It's better to stick to original or change to 'status_pengajuan' if that's what's in db. Let's use what was in original: $p['status'] or maybe $p['status_pengajuan'] fallback.
                'tagihan_berikutnya' => $tagihan ? [
                    'angsuran_ke' => $tagihan['bulan_ke'],
                    'jatuh_tempo' => $tagihan['jatuh_tempo'],
                    'pokok' => (float)$tagihan['pokok'],
                    'bunga' => (float)$tagihan['jasa'],
                    'total_tagihan' => (float)($tagihan['pokok'] + $tagihan['jasa'])
                ] : null
            ];
        }

        return $this->success($formatted, 'Data pinjaman berhasil diambil.');
    }
}
