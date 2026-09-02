<?php
namespace App\Controllers\Mobile;
use App\Controllers\BaseController;
use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;

class Pinjaman extends BaseController
{
    protected $anggotaModel;
    protected $simpananModel;
    protected $pinjamanModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
        $this->pinjamanModel = new PinjamanModel();
    }

    public function index()
    {
        $session = session();
        $userId = $session->get('anggota_id');
        $anggota = $this->anggotaModel->find($userId);
        
        // Data global yang dibutuhkan layout/main.php
        $totalSimpanan = $this->simpananModel->where('anggota_id', $userId)->selectSum('saldo')->first()['saldo'] ?? 0;

        $pinjamanAktifRow = $this->pinjamanModel->where('anggota_id', $userId)->where('status_pengajuan', 'ACTIVE')->first();
        $sisaPokokHutang = 0;
        $jadwalAngsuran = [];
        
        if ($pinjamanAktifRow) {
            $sisaPokokHutang = $pinjamanAktifRow['nominal_pengajuan'];
            // Mock schedule
            $jadwalAngsuran = [
                ['bulan_ke' => 1, 'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+1 month')), 'nominal_angsuran' => $sisaPokokHutang / $pinjamanAktifRow['tenor_bulan'], 'status' => 'Belum Lunas']
            ];
        }
        
        $data = [
            'isLoggedIn' => true,
            'totalSimpanan' => $totalSimpanan,
            'anggota' => $anggota,
            'sisaPokokHutang' => $sisaPokokHutang,
            'jadwalAngsuran' => $jadwalAngsuran
        ];
        return view('mobile/pinjaman', $data);
    }

    public function ajukanPinjaman()
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
        $tenor = $this->request->getPost('tenor');
        $tujuan = $this->request->getPost('tujuan');

        if ($nominal > 15000000) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pengajuan ditolak. Nominal melebihi sisa plafon maksimal (Rp 15.000.000).']);
        }
        
        $penghasilan = $this->request->getPost('penghasilan_bulanan') ?? 0;
        $cicilan_lainnya = $this->request->getPost('cicilan_lainnya') ?? 0;

        // Simpan sebagai Pending
        $this->pinjamanModel->save([
            'anggota_id' => $anggotaId,
            'nominal_pengajuan' => $nominal,
            'tenor_bulan' => $tenor,
            'tujuan' => $tujuan,
            'penghasilan_bulanan' => $penghasilan,
            'cicilan_lainnya' => $cicilan_lainnya,
            'status_pengajuan' => 'SUBMITTED',
            'tanggal_pengajuan' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Pengajuan pinjaman berhasil dikirim. Menunggu persetujuan admin.']);
    }

    public function simulasi()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $nominal = $this->request->getPost('nominal');
        $tenor = $this->request->getPost('tenor');
        $bungaPersen = $this->request->getPost('bunga') ?? 2;
        
        if (!$nominal || !$tenor) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }
        
        $bunga = $nominal * ($bungaPersen / 100);
        $pokok = $nominal / $tenor;
        $totalPerBulan = $pokok + $bunga;
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'pokok' => $pokok,
                'bunga' => $bunga,
                'total_per_bulan' => $totalPerBulan
            ]
        ]);
    }
}
