<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;
use App\Models\ProdukWaserdaModel;
use App\Models\PenarikanSimpananModel;
use App\Models\SetoranSimpananModel;
use App\Models\PengaturanModel;
use App\Models\AdminUsersModel;
use App\Models\RiwayatTransaksiModel;
use App\Models\AkunCoaModel;
use App\Models\JurnalTransaksiModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderModel;
use App\Models\AuditTrailModel;

class Waserda extends BaseController
{
    use \App\Traits\DataTablesTrait;

    public function index()
    {
        $waserdaModel = new \App\Models\ProdukWaserdaModel();
        $supplierModel = new \App\Models\SupplierModel();
        $poModel = new \App\Models\PurchaseOrderModel();
        
        $data = [
            'anggota' => (new \App\Models\AnggotaModel())->findAll(),
            'waserda' => $waserdaModel->findAll(),
            'suppliers' => $supplierModel->findAll(),
            'purchase_orders' => $poModel->findAll()
        ];
        return view('admin/waserda', $data);
    }

    public function gudang()
    {
        $waserdaModel = new \App\Models\ProdukWaserdaModel();
        $supplierModel = new \App\Models\SupplierModel();
        $data = [
            'waserda' => $waserdaModel->findAll(),
            'suppliers' => $supplierModel->findAll()
        ];
        return view('admin/gudang', $data);
    }

    public function po()
    {
        $poModel = new \App\Models\PurchaseOrderModel();
        $supplierModel = new \App\Models\SupplierModel();
        $waserdaModel = new \App\Models\ProdukWaserdaModel();
        $data = [
            'purchase_orders' => $poModel->findAll(),
            'suppliers' => $supplierModel->findAll(),
            'waserda' => $waserdaModel->findAll()
        ];
        return view('admin/po', $data);
    }
    protected $anggotaModel;
    protected $simpananModel;
    protected $pinjamanModel;
    protected $waserdaModel;
    protected $transaksiModel;
    protected $pengaturanModel;
    protected $adminUsersModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $supplierModel;
    protected $poModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
        $this->pinjamanModel = new \App\Models\PinjamanModel();
        $this->waserdaModel = new ProdukWaserdaModel();
        $this->transaksiModel = new RiwayatTransaksiModel();
        $this->pengaturanModel = new PengaturanModel();
        $this->adminUsersModel = new AdminUsersModel();
        $this->coaModel = new AkunCoaModel();
        $this->jurnalModel = new JurnalTransaksiModel();
        $this->supplierModel = new SupplierModel();
        $this->poModel = new PurchaseOrderModel();
    }

    public function checkoutKasir() {
        $post = $this->request->getPost();
        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        
        $db = \Config\Database::connect();
        $db->transStart();

        $riwayatModel->insert([
            'anggota_id' => !empty($post['anggota_id']) ? $post['anggota_id'] : null,
            'kategori' => 'Waserda',
            'jenis_transaksi' => $post['metode'] === 'kasbon' ? 'Keluar' : 'Masuk',
            'nominal' => $post['total'],
            'keterangan' => 'Pembelian Kasir Waserda (' . ucfirst($post['metode']) . ')'
        ]);

        // [AUTO-JURNAL] Transaksi Waserda
        // Debit: Kas Koperasi (1100) atau Piutang Anggota (1200)
        // Kredit: Pendapatan Penjualan Waserda (4200)
        $coaModel = new \App\Models\AkunCoaModel();
        $jurnalModel = new \App\Models\JurnalTransaksiModel();
        $produkModel = new \App\Models\ProdukWaserdaModel();
        $mutasiModel = new \App\Models\StokMutasiModel();
        
        $kodeDebit = $post['metode'] === 'kasbon' ? '1200' : '1100';
        $akunDebit = cache()->remember('coa_' . $kodeDebit, 3600, function() use ($coaModel, $kodeDebit) { return $coaModel->where('kode_akun', $kodeDebit)->first(); });
        $akunPendapatan = cache()->remember('coa_4200', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '4200')->first(); });
        $noBukti = 'WSR-' . date('YmdHis');

        if ($akunDebit && $akunPendapatan) {
            $jurnalModel->insertBatch([
                ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunDebit['id'], 'posisi' => 'Debit', 'nominal' => $post['total'], 'keterangan' => 'Penjualan Waserda (' . ucfirst($post['metode']) . ')'],
                ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunPendapatan['id'], 'posisi' => 'Kredit', 'nominal' => $post['total'], 'keterangan' => 'Pendapatan Penjualan Waserda (' . ucfirst($post['metode']) . ')']
            ]);
        }

        // Proses Item Keranjang: Potong Stok & Hitung HPP
        $totalHpp = 0;
        if (!empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $item) {
                $qty = (int)$item['qty'];
                $hpp = (float)$item['hargabeli'];
                $totalHpp += ($qty * $hpp);

                // Potong Stok
                $produk = $produkModel->find($item['id']);
                if ($produk) {
                    if ($produk['stok'] < $qty) {
                        $db->transRollback();
                        return $this->response->setJSON(['status' => 'error', 'message' => 'Stok ' . $produk['nama_produk'] . ' tidak mencukupi (Sisa: ' . $produk['stok'] . ').']);
                    }

                    $produkModel->update($item['id'], [
                        'stok' => $produk['stok'] - $qty
                    ]);

                    // Catat Mutasi
                    $mutasiModel->insert([
                        'produk_id' => $item['id'],
                        'jenis' => 'Keluar',
                        'jumlah' => $qty,
                        'keterangan' => 'Penjualan POS: ' . $noBukti
                    ]);
                }
            }
            cache()->delete('waserda_list');
        }

        // [AUTO-JURNAL] HPP vs Persediaan
        // Debit: HPP Waserda (5100), Kredit: Persediaan (1300)
        if ($totalHpp > 0) {
            $akunHpp = cache()->remember('coa_5100', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '5100')->first(); });
            $akunPersediaan = cache()->remember('coa_1300', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '1300')->first(); });
            if ($akunHpp && $akunPersediaan) {
                $jurnalModel->insertBatch([
                    ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunHpp['id'], 'posisi' => 'Debit', 'nominal' => $totalHpp, 'keterangan' => 'HPP Penjualan POS: ' . $noBukti],
                    ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunPersediaan['id'], 'posisi' => 'Kredit', 'nominal' => $totalHpp, 'keterangan' => 'Pengurangan Persediaan POS: ' . $noBukti]
                ]);
            }
        }

        // [AUDIT TRAIL]
        $auditModel = new \App\Models\AuditTrailModel();
        $auditModel->logAction('CHECKOUT_WASERDA', 'Admin memproses transaksi POS Waserda sejumlah Rp ' . number_format($post['total'],0,',','.') . ' (Metode: ' . $post['metode'] . ')');

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaksi database gagal.']);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    // --- Promo & Produk CRUD ---
    public function tambahProduk() {
        $model = new ProdukWaserdaModel();
        $model->insert([
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga_normal' => $this->request->getPost('harga_normal'),
            'harga_promo' => $this->request->getPost('harga_promo'),
            'harga_beli' => $this->request->getPost('harga_beli'),
            'stok' => $this->request->getPost('stok'),
            'stok_minimum' => $this->request->getPost('stok_minimum'),
            'ikon' => $this->request->getPost('ikon') ?: 'fas fa-box',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ]);
        cache()->delete('waserda_list');
        return redirect()->to(previous_url(true))->with('message', 'Produk/Promo berhasil ditambahkan.');
    }
    public function editProduk($id) {
        $model = new ProdukWaserdaModel();
        $model->update($id, [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga_normal' => $this->request->getPost('harga_normal'),
            'harga_promo' => $this->request->getPost('harga_promo'),
            'harga_beli' => $this->request->getPost('harga_beli'),
            'stok' => $this->request->getPost('stok'),
            'stok_minimum' => $this->request->getPost('stok_minimum'),
            'ikon' => $this->request->getPost('ikon'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ]);
        cache()->delete('waserda_list');
        return redirect()->to(previous_url(true))->with('message', 'Produk/Promo berhasil diupdate.');
    }
    public function hapusProduk($id) {
        $model = new ProdukWaserdaModel();
        $model->delete($id);
        cache()->delete('waserda_list');
        return redirect()->to(previous_url(true))->with('success', 'Produk berhasil dihapus.');
    }

    // --- Supplier CRUD ---
    public function tambahSupplier() {
        $model = new SupplierModel();
        $model->insert([
            'kode_supplier' => $this->request->getPost('kode_supplier'),
            'nama_supplier' => $this->request->getPost('nama_supplier'),
            'kontak' => $this->request->getPost('kontak'),
            'alamat' => $this->request->getPost('alamat'),
        ]);
        return redirect()->to('/admin')->with('success', 'Supplier berhasil ditambahkan.');
    }

    // --- Purchase Order (Restock) ---
    public function tambahPurchaseOrder() {
        $poModel = new PurchaseOrderModel();
        $stokMutasiModel = new \App\Models\StokMutasiModel();
        $produkModel = new ProdukWaserdaModel();
        $jurnalModel = new JurnalTransaksiModel();
        $coaModel = new AkunCoaModel();

        $post = $this->request->getPost();
        
        $nomorPo = 'PO-' . date('YmdHis');
        $totalHarga = floatval($post['total_harga']);
        $produkId = $post['produk_id'];
        $jumlahBeli = intval($post['jumlah']);

        if ($jumlahBeli <= 0 || $totalHarga < 0) {
            return redirect()->to('/admin/dashboard')->with('error', 'Validasi gagal: Jumlah beli harus lebih dari 0 dan total harga tidak boleh negatif.');
        }

        // Simpan PO
        $poId = $poModel->insert([
            'nomor_po' => $nomorPo,
            'supplier_id' => $post['supplier_id'],
            'tanggal' => date('Y-m-d'),
            'total_harga' => $totalHarga,
            'status' => 'Selesai'
        ]);

        // Tambah Stok Mutasi
        $stokMutasiModel->insert([
            'produk_id' => $produkId,
            'jenis' => 'Masuk',
            'jumlah' => $jumlahBeli,
            'keterangan' => 'Restock dari PO: ' . $nomorPo
        ]);

        // Update Stok Produk
        $produk = $produkModel->find($produkId);
        if ($produk) {
            $produkModel->update($produkId, [
                'stok' => $produk['stok'] + $jumlahBeli
            ]);
        }
        cache()->delete('waserda_list');

        // Auto-Jurnal (Pembelian Barang)
        // Debit: Persediaan (1300), Kredit: Kas Koperasi (1100)
        $akunPersediaan = cache()->remember('coa_1300', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '1300')->first(); });
        $akunKas = cache()->remember('coa_1100', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '1100')->first(); });

        if ($akunPersediaan && $akunKas) {
            $jurnalModel->insertBatch([
                ['nomor_bukti' => $nomorPo, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunPersediaan['id'], 'posisi' => 'Debit', 'nominal' => $totalHarga, 'keterangan' => 'Pembelian Barang (Restock) PO: ' . $nomorPo],
                ['nomor_bukti' => $nomorPo, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunKas['id'], 'posisi' => 'Kredit', 'nominal' => $totalHarga, 'keterangan' => 'Pembayaran Pembelian Barang PO: ' . $nomorPo]
            ]);
        }

        return redirect()->to('/admin')->with('success', 'Purchase Order berhasil diselesaikan dan stok telah bertambah.');
    }

    // --- Pengaturan Umum ---

    public function ajaxProduk()
    {
        $model = new \App\Models\ProdukWaserdaModel();
        // Pencarian berdasarkan nama produk saja, kode_produk dihapus karena tidak ada di DB
        $result = $this->processDataTables($model, ['nama_produk']);
        
        $response = [
            'draw' => $result['draw'],
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => []
        ];
        
        foreach ($result['data'] as $i => $row) {
            $statusBadge = ($row['stok'] > 10) 
                ? '<span class="status-badge status-approved">Tersedia</span>'
                : (($row['stok'] > 0) ? '<span class="status-badge status-pending">Menipis</span>' : '<span class="status-badge status-rejected">Habis</span>');
                
            $kodeFormated = 'PRD-' . str_pad($row['id'], 4, '0', STR_PAD_LEFT);
            // onclick memanggil editProdukModal(id, nama, beli, normal, promo, stok, stok_min, is_active)
            $actionBtns = '
                <div class="action-btns">
                    <button class="btn-action edit" onclick="editProdukModal('.$row['id'].', \''.htmlspecialchars($row['nama_produk'], ENT_QUOTES).'\', '.$row['harga_beli'].', '.$row['harga_normal'].', '.$row['harga_promo'].', '.$row['stok'].', '.$row['stok_minimum'].', '.$row['is_active'].')" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn-action delete" onclick="hapusProduk('.$row['id'].')" title="Hapus"><i class="fas fa-trash"></i></button>
                </div>';
                
            $response['data'][] = [
                $result['offset'] + $i + 1,
                $kodeFormated, // Menampilkan Kode (dari ID)
                $row['nama_produk'],
                'Rp ' . number_format($row['harga_beli'], 0, ',', '.'),
                'Rp ' . number_format($row['harga_normal'], 0, ',', '.'),
                $row['stok'],
                $statusBadge,
                $actionBtns
            ];
        }
        return $this->response->setJSON($response);
    }

    public function ajaxTransaksi()
    {
        $db = \Config\Database::connect();
        $request = service('request');
        $limit = $request->getPost('length') ?? 10;
        $offset = $request->getPost('start') ?? 0;
        $search = $request->getPost('search')['value'] ?? '';
        
        $builder = $db->table('riwayat_transaksi rt')
                      ->select('rt.*, a.nama_lengkap')
                      ->join('anggota a', 'a.id = rt.anggota_id', 'left')
                      ->where('rt.kategori', 'Waserda');
                      
        $totalData = $builder->countAllResults(false);
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('rt.keterangan', $search)
                    ->orLike('a.nama_lengkap', $search)
                    ->groupEnd();
        }
        $totalFiltered = $builder->countAllResults(false);
        $data = $builder->orderBy('rt.id', 'DESC')->limit($limit, $offset)->get()->getResultArray();
        
        $response = [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => []
        ];
        
        foreach ($data as $i => $row) {
            $statusBadge = ($row['jenis_transaksi'] == 'Keluar') 
                ? '<span class="status-badge" style="background:#e0e7ff; color:#4338ca;">Kasbon</span>'
                : '<span class="status-badge status-approved">Tunai</span>';
                
            $printUrl = "/admin/waserda/cetak-struk/" . $row['id'];
            $actionBtn = '<div class="action-btns"><button class="btn-action edit" onclick="window.open(\''.$printUrl.'\', \'_blank\', \'width=350,height=600\')" title="Cetak Struk"><i class="fas fa-print"></i></button></div>';
            
            $response['data'][] = [
                $offset + $i + 1,
                date('d/m/Y H:i', strtotime($row['created_at'])),
                $row['keterangan'],
                $row['nama_lengkap'] ?: 'Umum (Non-Anggota)',
                'Rp ' . number_format($row['nominal'], 0, ',', '.'),
                $statusBadge,
                $actionBtn
            ];
        }
        return $this->response->setJSON($response);
    }

    public function cetakStruk($id)
    {
        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        $anggotaModel = new \App\Models\AnggotaModel();
        
        $transaksi = $riwayatModel->find($id);
        if (!$transaksi || $transaksi['kategori'] !== 'Waserda') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Transaksi tidak ditemukan.');
        }

        $anggota = null;
        if ($transaksi['anggota_id']) {
            $anggota = $anggotaModel->find($transaksi['anggota_id']);
        }

        return view('admin/cetak_struk', [
            'transaksi' => $transaksi,
            'anggota' => $anggota
        ]);
    }

    public function ajaxPO()
    {
        // PO requires WaserdaPurchaseOrderModel and joining suppliers and produk_waserda
        $db = \Config\Database::connect();
        $request = service('request');
        $limit = $request->getPost('length') ?? 10;
        $offset = $request->getPost('start') ?? 0;
        $search = $request->getPost('search')['value'] ?? '';
        
        $builder = $db->table('purchase_order po')
                      ->select('po.*, s.nama_supplier, p.nama_produk, sm.jumlah')
                      ->join('supplier s', 's.id = po.supplier_id', 'left')
                      ->join('stok_mutasi sm', "sm.keterangan = CONCAT('Restock dari PO: ', po.nomor_po)", 'left')
                      ->join('produk_waserda p', 'p.id = sm.produk_id', 'left');
                      
        $totalData = $builder->countAllResults(false);
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('po.nomor_po', $search)
                    ->orLike('s.nama_supplier', $search)
                    ->orLike('p.nama_produk', $search)
                    ->groupEnd();
        }
        $totalFiltered = $builder->countAllResults(false);
        $data = $builder->orderBy('po.id', 'DESC')->limit($limit, $offset)->get()->getResultArray();
        
        $response = [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => []
        ];
        
        foreach ($data as $i => $row) {
            $response['data'][] = [
                $offset + $i + 1,
                date('d/m/Y', strtotime($row['created_at'])),
                $row['nomor_po'],
                $row['nama_supplier'],
                $row['nama_produk'],
                $row['jumlah'],
                'Rp ' . number_format($row['total_harga'], 0, ',', '.')
            ];
        }
        return $this->response->setJSON($response);
    }

    public function simpanStockOpname()
    {
        $post = $this->request->getPost();
        
        if (empty($post['produk_id']) || !isset($post['stok_fisik'])) {
            return redirect()->to('/admin/gudang#tab-opname')->with('error', 'Data tidak lengkap');
        }
        
        $produkModel = new \App\Models\ProdukWaserdaModel();
        $produk = $produkModel->find($post['produk_id']);
        if (!$produk) {
            return redirect()->to('/admin/gudang#tab-opname')->with('error', 'Produk tidak ditemukan');
        }
        
        $stokFisik = (int)$post['stok_fisik'];
        $stokSistem = (int)$produk['stok'];
        $selisih = $stokFisik - $stokSistem;
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $opnameModel = new \App\Models\StockOpnameModel();
        $opnameModel->insert([
            'produk_id' => $produk['id'],
            'stok_sistem' => $stokSistem,
            'stok_fisik' => $stokFisik,
            'selisih' => $selisih,
            'keterangan' => $post['keterangan'] ?? '',
            'petugas' => 'Admin' // Should be from session
        ]);
        
        // Sesuaikan stok produk dengan stok fisik
        $produkModel->update($produk['id'], ['stok' => $stokFisik]);
        
        // Catat mutasi penyesuaian stok opname jika ada selisih
        if ($selisih !== 0) {
            $mutasiModel = new \App\Models\StokMutasiModel();
            $mutasiModel->insert([
                'produk_id' => $produk['id'],
                'jenis' => $selisih > 0 ? 'Masuk' : 'Keluar',
                'jumlah' => abs($selisih),
                'keterangan' => 'Penyesuaian Stock Opname'
            ]);
            
            // [AUTO-JURNAL] Penyesuaian Persediaan (opsional)
            $coaModel = new \App\Models\AkunCoaModel();
            $jurnalModel = new \App\Models\JurnalTransaksiModel();
            $akunPersediaan = $coaModel->where('kode_akun', '1130')->first(); // Persediaan Barang
            $akunBeban = $coaModel->where('kode_akun', '5100')->first(); // Beban Lain / Selisih
            
            if ($akunPersediaan && $akunBeban) {
                $nilaiSelisih = abs($selisih) * $produk['harga_beli'];
                $noBukti = 'OPN-' . date('YmdHis');
                if ($selisih < 0) {
                    // Rugi/Beban karena stok kurang
                    $jurnalModel->insertBatch([
                        ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunBeban['id'], 'posisi' => 'Debit', 'nominal' => $nilaiSelisih, 'keterangan' => 'Selisih Kurang Stock Opname: ' . $produk['nama_produk']],
                        ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunPersediaan['id'], 'posisi' => 'Kredit', 'nominal' => $nilaiSelisih, 'keterangan' => 'Selisih Kurang Stock Opname: ' . $produk['nama_produk']]
                    ]);
                } else {
                    // Laba/Stok berlebih
                    $jurnalModel->insertBatch([
                        ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunPersediaan['id'], 'posisi' => 'Debit', 'nominal' => $nilaiSelisih, 'keterangan' => 'Selisih Lebih Stock Opname: ' . $produk['nama_produk']],
                        ['nomor_bukti' => $noBukti, 'tanggal' => date('Y-m-d'), 'akun_id' => $akunBeban['id'], 'posisi' => 'Kredit', 'nominal' => $nilaiSelisih, 'keterangan' => 'Selisih Lebih Stock Opname: ' . $produk['nama_produk']]
                    ]);
                }
            }
        }
        
        // [AUDIT TRAIL]
        $auditModel = new \App\Models\AuditTrailModel();
        $auditModel->logAction('STOCK_OPNAME', 'Admin melakukan Stock Opname untuk ' . $produk['nama_produk'] . ' (Selisih: '.$selisih.')');
        
        $db->transComplete();
        if ($db->transStatus() === false) {
            return redirect()->to('/admin/gudang#tab-opname')->with('error', 'Gagal memproses Stock Opname');
        }
        
        return redirect()->to('/admin/gudang#tab-opname')->with('message', 'Stock Opname berhasil disimpan');
    }
    
    public function ajaxStockOpname()
    {
        $db = \Config\Database::connect();
        $request = service('request');
        $limit = $request->getPost('length') ?? 10;
        $offset = $request->getPost('start') ?? 0;
        $search = $request->getPost('search')['value'] ?? '';
        
        $builder = $db->table('stock_opname so')
                      ->select('so.*, p.nama_produk')
                      ->join('produk_waserda p', 'p.id = so.produk_id', 'left');
                      
        $totalData = $builder->countAllResults(false);
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('p.nama_produk', $search)
                    ->orLike('so.petugas', $search)
                    ->groupEnd();
        }
        $totalFiltered = $builder->countAllResults(false);
        $data = $builder->orderBy('so.id', 'DESC')->limit($limit, $offset)->get()->getResultArray();
        
        $response = [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => []
        ];
        
        foreach ($data as $i => $row) {
            $selisihBadge = '';
            if ($row['selisih'] < 0) {
                $selisihBadge = '<span class="badge bg-danger">'.$row['selisih'].'</span>';
            } else if ($row['selisih'] > 0) {
                $selisihBadge = '<span class="badge bg-success">+'.$row['selisih'].'</span>';
            } else {
                $selisihBadge = '<span class="badge bg-secondary">0</span>';
            }
            
            $response['data'][] = [
                $offset + $i + 1,
                date('d/m/Y', strtotime($row['created_at'])),
                $row['nama_produk'],
                $row['stok_sistem'],
                $row['stok_fisik'],
                $selisihBadge,
                $row['keterangan'] ?? '-',
                $row['petugas']
            ];
        }
        return $this->response->setJSON($response);
    }
}