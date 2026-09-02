<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PinjamanAngsuranModel;

class Penagihan extends BaseController
{
    use \App\Traits\DataTablesTrait;

    public function index()
    {
        $angsuranModel = new PinjamanAngsuranModel();
        
        // Hitung statistik untuk cards
        $tagihanMacet = $angsuranModel->getTunggakanSummary();
        
        $totalTagihan = 0;
        $totalMacetParah = 0; // > 90 hari
        $anggotaPerluDihubungi = count($tagihanMacet);
        
        foreach($tagihanMacet as $tag) {
            $totalTagihan += $tag['sisa_pinjaman'];
            
            if ($tag['hari_keterlambatan'] > 90) {
                $totalMacetParah += $tag['sisa_pinjaman'];
            }
        }
        
        $data = [
            'totalTagihan' => $totalTagihan,
            'totalMacetParah' => $totalMacetParah,
            'anggotaPerluDihubungi' => $anggotaPerluDihubungi
        ];
        
        return view('admin/penagihan', $data);
    }

    public function ajaxPenagihan()
    {
        $request = service('request');
        $limit = (int)($request->getPost('length') ?? 10);
        $offset = (int)($request->getPost('start') ?? 0);
        $search = $request->getPost('search')['value'] ?? '';
        
        $angsuranModel = new PinjamanAngsuranModel();
        $result = $angsuranModel->getTunggakanDatatable($search, $limit, $offset);
        
        $response = [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $result['totalData'],
            'recordsFiltered' => $result['totalFiltered'],
            'data' => []
        ];
        
        foreach ($result['data'] as $i => $row) {
            if ($row['hari_keterlambatan'] > 90) {
                $kolektibilitas = 'Macet';
                $kolek_class = 'bg-danger';
            } elseif ($row['hari_keterlambatan'] > 30) {
                $kolektibilitas = 'Kurang Lancar';
                $kolek_class = 'bg-warning';
            } else {
                $kolektibilitas = 'Dalam Perhatian';
                $kolek_class = 'bg-success';
            }
            
            $waPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $row['no_hp'] ?? ''));
            $waText = urlencode("Halo Bapak/Ibu " . $row['nama_lengkap'] . ", kami dari Koperasi Assyifa menginfokan tagihan angsuran pinjaman Anda telah jatuh tempo sebesar Rp " . number_format($row['sisa_pinjaman'], 0, ',', '.') . " (" . $row['hari_keterlambatan'] . " hari). Mohon segera melakukan pembayaran. Terima kasih.");
            $waLink = "https://wa.me/" . $waPhone . "?text=" . $waText;
            
            $badge = '<span class="badge ' . $kolek_class . '">' . $kolektibilitas . '</span>';
            $action = '
                <div class="action-btns" style="display:flex; gap:6px; justify-content:center;">
                    <a href="'.$waLink.'" target="_blank" class="btn btn-sm" style="background: #25D366; color: white; padding: 5px 10px; font-size: 0.8rem; border-radius: 6px; text-decoration: none;" title="Hubungi via WA"><i class="fab fa-whatsapp"></i> Chat WA</a>
                </div>';
                
            $response['data'][] = [
                $offset + $i + 1, // 0
                '<strong>' . esc($row['nip']) . '</strong>', // 1
                esc($row['nama_lengkap']) . '<br><small style="color:var(--text-muted);"><i class="fas fa-phone"></i> ' . esc($row['no_hp'] ?? '-') . '</small>', // 2
                '<strong style="color: #ef4444;">Rp ' . number_format($row['sisa_pinjaman'], 0, ',', '.') . '</strong><br><small style="color:var(--text-muted);">' . esc($row['jumlah_angsuran_nunggak']) . ' Angsuran</small>', // 3
                '<span style="color: #ef4444; font-weight: bold;">' . esc($row['hari_keterlambatan']) . ' Hari</span>', // 4
                $badge, // 5
                $action // 6
            ];
        }
        return $this->response->setJSON($response);
    }

    public function datatables()
    {
        return $this->ajaxPenagihan();
    }
}
