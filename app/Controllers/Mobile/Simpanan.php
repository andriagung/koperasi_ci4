<?php
namespace App\Controllers\Mobile;
use App\Controllers\BaseController;
use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\SimpananTransaksiModel;
use App\Models\RiwayatTransaksiModel;

class Simpanan extends BaseController
{
    protected $anggotaModel;
    protected $simpananModel;
    protected $transaksiModel;
    protected $riwayatModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
        $this->transaksiModel = new SimpananTransaksiModel();
        $this->riwayatModel = new RiwayatTransaksiModel();
    }

    public function index()
    {
        $session = session();
        $userId = $session->get('anggota_id');
        $anggota = $this->anggotaModel->find($userId);
        
        // Data global yang dibutuhkan layout/main.php
        $totalSimpanan = $this->simpananModel->where('anggota_id', $userId)->selectSum('saldo')->first()['saldo'] ?? 0;

        $simpanan = $this->simpananModel->where('anggota_id', $userId)->findAll();
        $simpananWajib = 0; $simpananSukarela = 0;
        foreach ($simpanan as $s) {
            if ($s['jenis_simpanan'] === 'Wajib') $simpananWajib = $s['saldo'];
            if ($s['jenis_simpanan'] === 'Sukarela') $simpananSukarela = $s['saldo'];
        }
        
        $penarikanPending = $this->transaksiModel->where('anggota_id', $userId)->where('jenis_transaksi', 'penarikan')->where('status', 'DRAFT')->findAll();
        $setoranPending = $this->transaksiModel->where('anggota_id', $userId)->where('jenis_transaksi', 'setoran')->where('status', 'DRAFT')->findAll();
        
        $riwayat = $this->riwayatModel->where('anggota_id', $userId)
                                ->where('kategori', 'Simpanan')
                                ->orderBy('created_at', 'DESC')
                                ->findAll();
        
        $data = [
            'isLoggedIn' => true,
            'totalSimpanan' => $totalSimpanan,
            'anggota' => $anggota,
            'simpanan' => $simpanan,
            'simpananWajib' => $simpananWajib,
            'simpananSukarela' => $simpananSukarela,
            'sukarelaSaldo' => $simpananSukarela,
            'penarikan_pending' => $penarikanPending,
            'setoran_pending' => $setoranPending,
            'pinjamanPending' => false,
            'riwayat' => $riwayat
        ];
        return view('mobile/simpanan', $data);
    }

    public function tarikSimpanan()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $anggotaId = $session->get('id');
        $nominal = $this->request->getPost('nominal');
        $bank = $this->request->getPost('bank_pencairan');
        $pin = $this->request->getPost('pin_konfirmasi');

        // Validasi PIN
        $anggota = $this->anggotaModel->find($anggotaId);
        if (!password_verify($pin, $anggota['pin'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'PIN Konfirmasi Salah!']);
        }

        // Cek saldo Simpanan Sukarela
        $sukarela = $this->simpananModel->where('anggota_id', $anggotaId)
                                  ->where('jenis_simpanan', 'Sukarela')
                                  ->first();
        
        $saldoTersedia = $sukarela ? $sukarela['saldo'] : 0;
        
        // Aturan: Sisakan 50000
        if ($nominal > ($saldoTersedia - 50000)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Saldo Sukarela tidak mencukupi. (Harus tersisa min Rp 50.000)']);
        }

        // Cek antrean
        $pending = $this->transaksiModel->where('anggota_id', $anggotaId)->where('jenis_transaksi', 'penarikan')->where('status', 'DRAFT')->first();
        if ($pending) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Masih ada pengajuan penarikan yang menunggu (Pending).']);
        }

        // Insert
        $this->transaksiModel->insert([
            'nomor_transaksi' => 'TRX-' . time() . '-' . rand(100, 999),
            'anggota_id' => $anggotaId,
            'jenis_transaksi' => 'penarikan',
            'nominal' => $nominal,
            'metode_pembayaran' => $bank,
            'tanggal' => date('Y-m-d H:i:s'),
            'status' => 'DRAFT',
            'keterangan' => 'Pengajuan penarikan via Mobile'
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Pengajuan penarikan berhasil.']);
    }

    public function setorSimpanan()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $anggotaId = $session->get('id');
        $nominal = $this->request->getPost('nominal');
        $bank = $this->request->getPost('bank_pengirim');

        if ($nominal < 10000) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Minimum setoran adalah Rp 10.000']);
        }
        
        $pending = $this->transaksiModel->where('anggota_id', $anggotaId)->where('jenis_transaksi', 'setoran')->where('status', 'DRAFT')->first();
        if ($pending) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Masih ada pengajuan setoran yang menunggu verifikasi admin.']);
        }

        $this->transaksiModel->insert([
            'nomor_transaksi' => 'TRX-' . time() . '-' . rand(100, 999),
            'anggota_id' => $anggotaId,
            'jenis_transaksi' => 'setoran',
            'nominal' => $nominal,
            'metode_pembayaran' => $bank,
            'tanggal' => date('Y-m-d H:i:s'),
            'status' => 'DRAFT',
            'keterangan' => 'Setoran simpanan via Mobile'
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Notifikasi setoran berhasil dikirim. Menunggu verifikasi admin.']);
    }
}
