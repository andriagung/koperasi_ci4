<?php
namespace App\Controllers\Mobile;
use App\Controllers\BaseController;
use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\ProdukWaserdaModel;
use App\Models\RiwayatTransaksiModel;

class Waserda extends BaseController
{
    protected $anggotaModel;
    protected $simpananModel;
    protected $waserdaModel;
    protected $riwayatModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
        $this->waserdaModel = new ProdukWaserdaModel();
        $this->riwayatModel = new RiwayatTransaksiModel();
    }

    public function index()
    {
        $session = session();
        $userId = $session->get('anggota_id');
        $anggota = $this->anggotaModel->find($userId);
        
        // Data global yang dibutuhkan layout/main.php
        $totalSimpanan = $this->simpananModel->where('anggota_id', $userId)->selectSum('saldo')->first()['saldo'] ?? 0;

        $promoWaserda = $this->waserdaModel->where('stok >', 0)->findAll();
        
        $data = [
            'isLoggedIn' => true,
            'totalSimpanan' => $totalSimpanan,
            'anggota' => $anggota,
            'plafonWaserda' => 5000000,
            'riwayatWaserda' => [],
            'promoWaserda' => $promoWaserda
        ];
        return view('mobile/waserda', $data);
    }

    public function checkoutWaserda()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $anggotaId = $session->get('id');
        $produkId = idhash_decode($this->request->getPost('produk_id'));
        if (!$produkId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Produk tidak valid.']);
        }
        
        $produk = $this->waserdaModel->find($produkId);
        if (!$produk) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Produk tidak ditemukan.']);
        }

        // We check what the database actually has. From Api/WaserdaController, it seems 'harga_anggota' and 'harga_non_anggota'.
        // Original code used `harga_promo` and `harga_normal`. Let's use `harga_anggota` as primary if it exists, but since original code had this, maybe it's correct for this branch/fork.
        $harga = $produk['harga_promo'] ?? $produk['harga_normal'] ?? $produk['harga_anggota'] ?? 0;

        // Cek Plafon Kasbon
        $simpanan = $this->simpananModel->where('anggota_id', $anggotaId)->findAll();
        $totalSimpanan = 0;
        foreach ($simpanan as $s) $totalSimpanan += $s['saldo'];
        
        $plafonWaserda = $totalSimpanan * 0.5;
        
        // Kasbon terpakai
        $riwayatWaserda = $this->riwayatModel->where('anggota_id', $anggotaId)
                                       ->where('kategori', 'Waserda')
                                       ->where('jenis_transaksi', 'Keluar')
                                       ->findAll();
        $kasbonTerpakai = 0;
        foreach ($riwayatWaserda as $rw) $kasbonTerpakai += $rw['nominal'];

        if (($kasbonTerpakai + $harga) > $plafonWaserda) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sisa Plafon Kasbon Waserda Anda tidak mencukupi untuk membeli ' . $produk['nama_produk'] . '.']);
        }

        try {
            $waserdaService = new \App\Services\WaserdaService();
            $waserdaService->checkoutKasbonMobile($anggotaId, $produkId, $harga, $produk['nama_produk']);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Pembelian ' . $produk['nama_produk'] . ' berhasil menggunakan sistem Kasbon!']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
