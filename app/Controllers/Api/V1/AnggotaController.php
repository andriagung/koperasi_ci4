<?php
namespace App\Controllers\Api\V1;

use App\Models\AnggotaModel;
use App\Models\SimpananModel;

class AnggotaController extends BaseApiController
{
    protected $anggotaModel;
    protected $simpananModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
    }

    private function getAnggotaId()
    {
        return $this->request->getHeaderLine('X-Anggota-Id');
    }

    public function profil()
    {
        $id = $this->getAnggotaId();
        $anggota = $this->anggotaModel->find($id);

        if (!$anggota) {
            return $this->error('Anggota tidak ditemukan.', null, 404);
        }

        return $this->success($anggota, 'Data profil berhasil diambil.');
    }

    public function saldo()
    {
        $id = $this->getAnggotaId();
        
        $simpanan = $this->simpananModel
                       ->where('anggota_id', $id)
                       ->findAll();
                       
        if (empty($simpanan)) {
            return $this->success([], 'Belum ada data simpanan.');
        }

        $formatted = [];
        $total = 0;
        foreach ($simpanan as $s) {
            $formatted[] = [
                'jenis_simpanan' => $s['jenis_simpanan'],
                'saldo' => (float)$s['saldo']
            ];
            $total += (float)$s['saldo'];
        }

        return $this->success([
            'rincian' => $formatted,
            'total_saldo' => $total
        ], 'Data saldo berhasil diambil.');
    }
}
