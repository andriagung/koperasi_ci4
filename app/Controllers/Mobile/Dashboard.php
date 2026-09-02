<?php
namespace App\Controllers\Mobile;
use App\Controllers\BaseController;
use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\NotificationModel;

class Dashboard extends BaseController
{
    protected $anggotaModel;
    protected $simpananModel;
    protected $notifModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
        $this->notifModel = new NotificationModel();
    }

    public function index()
    {
        $session = session();
        $userId = $session->get('anggota_id');
        $anggota = $this->anggotaModel->find($userId);
        
        // Data global yang dibutuhkan layout/main.php
        $totalSimpanan = $this->simpananModel->where('anggota_id', $userId)->selectSum('saldo')->first()['saldo'] ?? 0;

        $notifikasi = $this->notifModel->where('user_id', $userId)->where('user_type', 'Anggota')->orderBy('created_at', 'DESC')->findAll();

        $shuService = new \App\Services\ShuService();
        $simulasiShu = $shuService->kalkulasiSimulasi(date('Y'));
        
        $detailShu = [
            'poin_modal' => 0,
            'poin_usaha' => 0,
            'jasa_modal' => 0,
            'jasa_usaha' => 0,
            'total_shu' => 0,
            'has_laba' => $simulasiShu['has_laba'] ?? false
        ];
        
        if (isset($simulasiShu['distribusi'])) {
            foreach ($simulasiShu['distribusi'] as $dist) {
                if ($dist['anggota_id'] == $userId) {
                    $detailShu['poin_modal'] = $dist['poin_modal'] ?? 0;
                    $detailShu['poin_usaha'] = $dist['poin_usaha'] ?? 0;
                    $detailShu['jasa_modal'] = $dist['jasa_modal'] ?? 0;
                    $detailShu['jasa_usaha'] = $dist['jasa_usaha'] ?? 0;
                    $detailShu['total_shu'] = $dist['total_shu'] ?? 0;
                    break;
                }
            }
        }

        $data = [
            'isLoggedIn' => true,
            'totalSimpanan' => $totalSimpanan,
            'anggota' => $anggota,
            'notifikasi' => $notifikasi,
            'riwayat' => [],
            'detailShu' => $detailShu,
        ];
        return view('mobile/dashboard', $data);
    }

    public function readNotif()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error']);
        }
        
        $userId = $session->get('anggota_id');
        
        // Mark all as read for this user
        $this->notifModel->where('user_id', $userId)->where('user_type', 'Anggota')->set(['is_read' => 1])->update();
        
        return $this->response->setJSON(['status' => 'success']);
    }
}
