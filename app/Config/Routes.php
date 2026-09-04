<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->addPlaceholder('hash', '[a-zA-Z0-9]+');

// --- PUBLIC ROUTES ---
$routes->get('/', 'Mobile\Auth::index'); // Login form

$routes->get('/auth/login', function() { return redirect()->to('/'); });
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');
$routes->get('/test-email', 'Admin\Potongan::testEmailSmtp');

// --- MOBILE PROTECTED ROUTES ---
$routes->group('mobile', ['filter' => 'roleFilter:Anggota'], function($routes) {
    $routes->get('dashboard', 'Mobile\Dashboard::index');
    $routes->get('simpanan', 'Mobile\Simpanan::index');
    $routes->get('pinjaman', 'Mobile\Pinjaman::index');
    $routes->post('pinjaman/simulasi', 'Mobile\Pinjaman::simulasi');
    $routes->get('waserda', 'Mobile\Waserda::index');
    $routes->get('profil', 'Mobile\Profil::index');
    $routes->get('qr-code', 'Mobile\Profil::qrCode');
    
    $routes->post('tarik-simpanan', 'Mobile\Simpanan::tarikSimpanan');
    $routes->post('setor-simpanan', 'Mobile\Simpanan::setorSimpanan');
    $routes->post('ajukan-pinjaman', 'Mobile\Pinjaman::ajukanPinjaman');
    $routes->post('verify-pin', 'Mobile\Profil::verifyPin');
    $routes->get('download-pdf', 'Mobile\Profil::downloadPdf');
    $routes->post('read-notif', 'Mobile\Dashboard::readNotif');
    $routes->post('checkout-waserda', 'Mobile\Waserda::checkoutWaserda');
});

// --- PAYMENT GATEWAY ROUTES ---
$routes->post('/api/midtrans-token', 'MidtransController::generateToken');
$routes->post('/api/midtrans-callback', 'Api\Midtrans::callback');

// --- ADMIN ROUTES (Secured by RoleFilter) ---

$routes->get('/admin/login', 'Admin\Auth::index');
$routes->post('/admin/login', 'Admin\Auth::login');
$routes->get('/admin/logout', 'Admin\Auth::logout');

// 1. Umum (Bisa diakses oleh semua role yang valid)
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('dashboard/get_notif', 'Admin\Dashboard::get_notif');
    
    // Redirect admin/jurnal -> admin/akuntansi/jurnal
    $routes->get('jurnal', function() {
        return redirect()->to(base_url('admin/akuntansi/jurnal'));
    });
});

// 2. Modul Kasir Waserda (SuperAdmin, Admin, Kasir)
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin,Admin,Kasir'], function($routes) {
    $routes->get('waserda', 'Admin\Waserda::index');
    $routes->get('waserda/cetak-struk/(:hash)', 'Admin\Waserda::cetakStruk/$1');
    $routes->post('checkout-kasir', 'Admin\Waserda::checkoutKasir');
    
    $routes->post('ajax-waserda-produk', 'Admin\Waserda::ajaxProduk');
    $routes->post('ajax-waserda-transaksi', 'Admin\Waserda::ajaxTransaksi');
    $routes->get('waserda/cetak-faktur/(:hash)', 'Admin\Waserda::cetakFaktur/$1');
});

// 3. Modul Gudang & Stok (SuperAdmin, Admin, Gudang)
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin,Admin,Gudang'], function($routes) {
    // Waserda
    $routes->get('waserda', 'Admin\Waserda::index');
    $routes->post('waserda/tambah-produk', 'Admin\Waserda::tambahProduk');
    $routes->post('waserda/edit-produk/(:hash)', 'Admin\Waserda::editProduk/$1');
    $routes->post('waserda/hapus-produk/(:hash)', 'Admin\Waserda::hapusProduk/$1');
    $routes->post('waserda/checkout', 'Admin\Waserda::checkoutKasir');
    $routes->get('waserda/cetak-struk/(:hash)', 'Admin\Waserda::cetakStruk/$1');
    $routes->get('waserda/cari-barcode/(:any)', 'Admin\Waserda::cariBarcode/$1');
    
    // Kategori
    $routes->post('waserda/tambah-kategori', 'Admin\Waserda::tambahKategori');
    $routes->post('waserda/edit-kategori/(:hash)', 'Admin\Waserda::editKategori/$1');
    $routes->post('waserda/hapus-kategori/(:hash)', 'Admin\Waserda::hapusKategori/$1');

    // Gudang & Stok
    $routes->get('gudang', 'Admin\Waserda::gudang');
    $routes->post('gudang/stock-opname', 'Admin\Waserda::simpanStockOpname');
    $routes->post('gudang/tambah-supplier', 'Admin\Waserda::tambahSupplier');
    $routes->post('gudang/edit-supplier/(:hash)', 'Admin\Waserda::editSupplier/$1');
    $routes->post('ajax-stock-opname', 'Admin\Waserda::ajaxStockOpname');
    
    // Retur Penjualan
    $routes->post('waserda/retur-penjualan', 'Admin\Waserda::prosesReturPenjualan');

    // Inventory Phase 8 (Lokasi, Transfer, Opname, Kartu Stok)
    $routes->get('inventory/barang', function() { return redirect()->to(base_url('admin/gudang')); });
    $routes->get('inventory/lokasi', 'Admin\Inventory::lokasi');
    $routes->post('inventory/simpan-lokasi', 'Admin\Inventory::simpanLokasi');
    $routes->get('inventory/kartu-stok', 'Admin\Inventory::kartuStok');
    $routes->get('inventory/transfer', 'Admin\Inventory::transfer');
    $routes->post('inventory/simpan-transfer', 'Admin\Inventory::simpanTransfer');
    $routes->get('inventory/opname', 'Admin\Inventory::opname');
    $routes->post('inventory/simpan-opname', 'Admin\Inventory::simpanOpname');

    // Keuangan Phase 9 (Kas, Bank, Rekonsiliasi)
    $routes->get('keuangan/kas', 'Admin\KasBank::kas');
    $routes->post('keuangan/simpan-kas', 'Admin\KasBank::simpanKas');
    $routes->get('keuangan/mutasi-kas/(:hash)', 'Admin\KasBank::mutasiKas/$1');
    $routes->get('keuangan/bank', 'Admin\KasBank::bank');
    $routes->post('keuangan/simpan-bank', 'Admin\KasBank::simpanBank');
    $routes->get('keuangan/mutasi-bank/(:hash)', 'Admin\KasBank::mutasiBank/$1');
    $routes->get('keuangan/rekonsiliasi', 'Admin\KasBank::rekonsiliasi');

    $routes->get('po', 'Admin\Waserda::po');
    $routes->post('po/tambah', 'Admin\Waserda::tambahPurchaseOrder');
    $routes->post('po/update-status/(:hash)', 'Admin\Waserda::updateStatusPurchaseOrder/$1');
    $routes->post('tambah-supplier', 'Admin\Waserda::tambahSupplier');
    $routes->post('tambah-po', 'Admin\Waserda::tambahPurchaseOrder');
});

// 4. Modul Purchase Order (SuperAdmin, Admin) -> Asumsi Open Question: Admin yang ACC PO
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin,Admin'], function($routes) {
    $routes->get('po', 'Admin\Waserda::po');
    $routes->post('ajax-waserda-po', 'Admin\Waserda::ajaxPO');
    $routes->post('tambah-supplier', 'Admin\Waserda::tambahSupplier');
    $routes->post('tambah-po', 'Admin\Waserda::tambahPurchaseOrder');
});

// 5.a Modul Anggota (SuperAdmin, Admin, Pengurus, Manajer, Bendahara, Petugas Kredit)
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin,Admin,Pengurus,Manajer,Bendahara,Petugas Kredit'], function($routes) {
    // Anggota
    $routes->get('anggota', 'Admin\Anggota::index');
    $routes->post('anggota/datatables', 'Admin\Anggota::getAnggotaDataTable');
    $routes->post('tambah-anggota', 'Admin\Anggota::tambahAnggota');
    $routes->post('edit-anggota/(:hash)', 'Admin\Anggota::editAnggota/$1');
    $routes->get('hapus-anggota/(:hash)', 'Admin\Anggota::hapusAnggota/$1');
    $routes->get('template-import-anggota', 'Admin\Anggota::templateImport');
    $routes->post('import-anggota', 'Admin\Anggota::import');
    $routes->post('reset-pin-anggota/(:hash)', 'Admin\Anggota::resetPinAnggota/$1');
    $routes->post('upload-dokumen', 'Admin\Anggota::uploadDokumen');
    $routes->post('hapus-dokumen/(:hash)', 'Admin\Anggota::hapusDokumen/$1');
    $routes->get('dokumen-anggota/(:hash)', 'Admin\Anggota::getDokumen/$1');
    $routes->get('anggota/kartu', 'Admin\Anggota::kartu');
    $routes->get('anggota/kartu/(:hash)', 'Admin\Anggota::kartu/$1');
    
    // Bendahara Gaji
    $routes->get('bendahara', 'Admin\Bendahara::index');
    $routes->post('bendahara/simpan', 'Admin\Bendahara::simpan');
    $routes->post('bendahara/hapus/(:hash)', 'Admin\Bendahara::hapus/$1');

    $routes->post('ajax-anggota', 'Admin\Anggota::ajaxAnggota');
});

// 5. Modul Simpan Pinjam, & Laporan (SuperAdmin, Admin)
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin,Admin'], function($routes) {
    
    // Simpanan
    $routes->get('simpanan', function() { return redirect()->to(base_url('admin/simpanan/transaksi')); });
    $routes->get('simpanan/jenis', 'Admin\Simpanan::jenis');
    $routes->post('simpanan/simpanJenis', 'Admin\Simpanan::simpanJenis');
    $routes->post('simpanan/hapusJenis/(:hash)', 'Admin\Simpanan::hapusJenis/$1');
    
    $routes->get('simpanan/transaksi', 'Admin\Simpanan::transaksi'); 
    $routes->post('simpanan/koreksi', 'Admin\Simpanan::koreksiSimpanan'); // maybe keep for backward compat
    $routes->post('simpanan/transfer', 'Admin\Simpanan::transferSimpanan'); // maybe keep for backward compat
    $routes->post('simpanan/proses', 'Admin\Simpanan::prosesTransaksi');
    $routes->get('simpanan/cetak/(:hash)', 'Admin\Simpanan::cetak/$1');
    $routes->get('simpanan/buku', 'Admin\Simpanan::buku');
    $routes->get('simpanan/mutasi', 'Admin\Simpanan::mutasi');
    $routes->get('simpanan/cetakBuku', 'Admin\Simpanan::cetakBuku');
    $routes->post('simpanan/datatablesTransaksi', 'Admin\Simpanan::datatablesTransaksi');
    $routes->post('simpanan/datatablesMutasi', 'Admin\Simpanan::datatablesMutasi');
    
    // Pinjaman
    $routes->get('pinjaman/produk', 'Admin\Pinjaman::produk');
    $routes->post('pinjaman/simpanProduk', 'Admin\Pinjaman::simpanProduk');
    $routes->post('pinjaman/hapusProduk/(:hash)', 'Admin\Pinjaman::hapusProduk/$1');
    
    $routes->get('pinjaman/pengajuan', 'Admin\Pinjaman::pengajuan');
    $routes->post('pinjaman/ajax-pengajuan', 'Admin\Pinjaman::ajaxPengajuan');
    $routes->post('pinjaman/simpanPengajuan', 'Admin\Pinjaman::simpanPengajuan');
    $routes->get('pinjaman/detail/(:hash)', 'Admin\Pinjaman::detail/$1');
    $routes->post('pinjaman/setujui/(:hash)', 'Admin\Pinjaman::setujui/$1');
    $routes->post('pinjaman/tolak/(:hash)', 'Admin\Pinjaman::tolak/$1');
    $routes->get('pinjaman/pencairan/(:hash)', 'Admin\Pinjaman::pencairan/$1');
    $routes->post('pinjaman/proses-pencairan', 'Admin\Pinjaman::prosesPencairan');
    $routes->get('pinjaman/jadwal/(:hash)', 'Admin\Pinjaman::jadwal/$1');
    $routes->post('pinjaman/bayar-angsuran', 'Admin\Pinjaman::bayarAngsuran');
    $routes->get('pinjaman/restrukturisasi', 'Admin\Pinjaman::restrukturisasi');
    $routes->post('pinjaman/prosesRestrukturisasi', 'Admin\Pinjaman::prosesRestrukturisasi');
    
    // Akuntansi
    $routes->get('akuntansi/coa', 'Admin\Akuntansi::coa');
    $routes->get('akuntansi/jurnal', 'Admin\Akuntansi::jurnal');
    $routes->get('akuntansi/buku-besar', 'Admin\Akuntansi::bukuBesar');
    $routes->get('akuntansi/laba-rugi', 'Admin\Akuntansi::labaRugi');
    $routes->get('akuntansi/neraca', 'Admin\Akuntansi::neraca');
    
    // SHU
    $routes->get('shu', 'Admin\Shu::index');
    $routes->post('shu/tutup-buku', 'Admin\Shu::tutupBuku');
    $routes->get('shu/detail', 'Admin\Shu::detail');
    
    // Penagihan
    $routes->get('penagihan', 'Admin\Penagihan::index');
    $routes->post('penagihan/datatables', 'Admin\Penagihan::datatables');
    
    // (Potongan Gaji moved below)
    
    // Laporan
    $routes->get('laporan/anggota', 'Admin\Laporan::anggota');
    $routes->get('laporan/simpanan', 'Admin\Laporan::simpanan');
    $routes->get('laporan/pinjaman', 'Admin\Laporan::pinjaman');
    $routes->get('laporan/waserda', 'Admin\Laporan::waserda');
    $routes->get('laporan/inventory', 'Admin\Laporan::inventory');
    $routes->get('laporan/rat', 'Admin\Laporan::rat');
    $routes->get('laporan/slow_moving', 'Admin\Laporan::slowMoving');
    
    // Redirect Akuntansi reports to proper modules
    $routes->get('laporan/labarugi', function() { return redirect()->to(base_url('admin/akuntansi/laba-rugi')); });
    $routes->get('laporan/neraca', function() { return redirect()->to(base_url('admin/akuntansi/neraca')); });
    $routes->get('laporan/bukubesar', function() { return redirect()->to(base_url('admin/akuntansi/buku-besar')); });
    $routes->get('laporan/neracasaldo', function() { return redirect()->to(base_url('admin/akuntansi/neraca-saldo')); });
    
    $routes->get('laporan/aruskas', 'Admin\Laporan::arusKas');
    $routes->get('laporan/bulanan', 'Admin\Laporan::bulanan');
    $routes->get('laporan/tunggakanPinjaman', 'Admin\Laporan::tunggakanPinjaman');
    $routes->get('laporan/penjualanHarian', 'Admin\Laporan::penjualanHarian');
    $routes->get('laporan/produkTerlaris', 'Admin\Laporan::produkTerlaris');
    $routes->get('laporan/daftar-potongan', 'Admin\Laporan::daftarPotongan');
    $routes->post('laporan/cetak-daftar-potongan', 'Admin\Laporan::cetakDaftarPotongan');
    $routes->get('laporan/cetak-daftar-potongan', 'Admin\Laporan::cetakDaftarPotongan');
    $routes->post('laporan/generate', 'Admin\Laporan::generate');
    
    // Audit Trail
    $routes->get('audit', 'Admin\Audit::index');
    
    // AJAX
    $routes->post('ajax-laporan-anggota', 'Admin\Laporan::ajaxAnggota');
    $routes->post('ajax-laporan-simpanan', 'Admin\Laporan::ajaxSimpanan');
    $routes->post('ajax-laporan-pinjaman', 'Admin\Laporan::ajaxPinjaman');
    $routes->post('ajax-laporan-waserda', 'Admin\Laporan::ajaxWaserda');
    $routes->post('ajax-laporan-inventory', 'Admin\Laporan::ajaxInventory');
    $routes->post('ajax-dashboard-anggota', 'Admin\Dashboard::ajaxDashboardAnggota');
    $routes->get('dashboard/get_transaksi_live', 'Admin\Dashboard::get_transaksi_live');
    $routes->post('ajax-penagihan', 'Admin\Penagihan::ajaxPenagihan');
    $routes->post('ajax-bendahara', 'Admin\Bendahara::ajaxBendahara');
});

// 5.b Modul Potongan Gaji (SuperAdmin, Admin, Bendahara)
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin,Admin,Bendahara'], function($routes) {
    $routes->get('potongan', 'Admin\Potongan::index');
    $routes->post('potongan/generate', 'Admin\Potongan::generate');
    $routes->get('potongan/exportCsv', 'Admin\Potongan::exportCsv');
    $routes->post('potongan/importCsv', 'Admin\Potongan::importCsv');
    $routes->post('potongan/sendEmail', 'Admin\Potongan::sendEmail');
    $routes->get('potongan/exportPdf', 'Admin\Potongan::exportPdf');
    $routes->get('potongan/exportExcel', 'Admin\Potongan::exportExcel');
    $routes->get('potongan/cetakBukti/(:hash)', 'Admin\Potongan::cetakBukti/$1');
    $routes->match(['get', 'post'], 'potongan/sendEmailSingle/(:hash)', 'Admin\Potongan::sendEmailSingle/$1');
    $routes->match(['get', 'post'], 'potongan/sendEmailMassal', 'Admin\Potongan::sendEmailMassal');
});

// 6. Modul Keuangan, Akuntansi & Jurnal (SuperAdmin, Admin)
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin,Admin'], function($routes) {
    $routes->get('akuntansi', 'Admin\Akuntansi::index');
    
    $routes->get('akuntansi/coa', 'Admin\Akuntansi::coa');
    $routes->post('akuntansi/ajax-coa', 'Admin\Akuntansi::ajaxDaftarCoa');
    
    $routes->get('akuntansi/jurnal', 'Admin\Akuntansi::jurnal');
    $routes->post('akuntansi/ajax-jurnal', 'Admin\Akuntansi::ajaxJurnalUmum');
    $routes->post('akuntansi/ajax-kas', 'Admin\Akuntansi::ajaxDaftarKas');
    $routes->get('akuntansi/detail-jurnal/(:hash)', 'Admin\Akuntansi::detailJurnal/$1');
    $routes->post('akuntansi/simpan-jurnal', 'Admin\Akuntansi::simpanJurnal');
    
    $routes->get('akuntansi/buku-besar', 'Admin\Akuntansi::bukuBesar');
    $routes->get('akuntansi/neraca-saldo', 'Admin\Akuntansi::neracaSaldo');
    $routes->get('akuntansi/laba-rugi', 'Admin\Akuntansi::labaRugi');
    
    // PPOB
    $routes->get('ppob', 'Admin\Ppob::index');
    $routes->get('ppob/kasir', 'Admin\Ppob::kasir');
    $routes->post('ppob/tambahProduk', 'Admin\Ppob::tambahProduk');
    $routes->post('ppob/editProduk/(:hash)', 'Admin\Ppob::editProduk/$1');
    $routes->post('ppob/hapusProduk/(:hash)', 'Admin\Ppob::hapusProduk/$1');
    $routes->post('ajax-ppob-produk', 'Admin\Ppob::ajaxProduk');
    $routes->post('ajax-ppob-transaksi', 'Admin\Ppob::ajaxTransaksi');
    $routes->get('api/ppob/produk', 'Admin\Ppob::getProdukByKategori');
    $routes->post('api/ppob/checkout', 'Admin\Ppob::checkout');
    $routes->get('akuntansi/neraca', 'Admin\Akuntansi::neraca');
});

// 7. Modul Sangat Sensitif: Pembagian SHU & Pengaturan (Hanya SuperAdmin)
$routes->group('admin', ['filter' => 'roleFilter:SuperAdmin'], function($routes) {
    $routes->get('shu', 'Admin\Shu::index');
    $routes->post('shu/kalkulasi', 'Admin\Shu::kalkulasi');
    $routes->get('shu/detail/(:hash)', 'Admin\Shu::detail/$1');
    $routes->post('shu/setujui/(:hash)', 'Admin\Shu::setujui/$1');
    $routes->post('shu/bagikan/(:hash)', 'Admin\Shu::bagikan/$1');
    
    $routes->get('pengaturan', 'Admin\Pengaturan::index');
    $routes->get('pengaturan/user', 'Admin\Pengaturan::user');
    
    // Analitik & BI
    $routes->get('analitik', 'Admin\Analitik::index');
    $routes->get('analitik/scoring/(:hash)', 'Admin\Analitik::creditScoring/$1');
    
    $routes->get('audit', 'Admin\Pengaturan::audit');
    $routes->get('riwayat', 'Admin\Pengaturan::riwayat');
    $routes->post('simpan-pengaturan', 'Admin\Pengaturan::simpanPengaturan');
    $routes->get('backup-db', 'Admin\Pengaturan::backupDb');
    
    $routes->post('tambah-admin', 'Admin\Pengaturan::tambahAdmin');
    $routes->post('edit-admin/(:hash)', 'Admin\Pengaturan::editAdmin/$1');
    $routes->post('hapus-admin/(:hash)', 'Admin\Pengaturan::hapusAdmin/$1');
    $routes->post('ajax-admin-users', 'Admin\Pengaturan::ajaxDaftarAdmin');
    $routes->post('ajax-audit-trail', 'Admin\Pengaturan::ajaxAuditTrail');
});

// 8. REST API untuk Mobile (Phase 14)
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function($routes) {
    // Auth
    $routes->post('auth/login', 'AuthController::login');
    
    // Protected Routes
    $routes->group('', ['filter' => 'apiAuth'], function($routes) {
        $routes->get('anggota/profil', 'AnggotaController::profil');
        $routes->get('anggota/saldo', 'AnggotaController::saldo');
        
        $routes->get('pinjaman', 'PinjamanController::index');
        
        $routes->get('waserda/katalog', 'WaserdaController::katalog');
    });
});

// QA Test Route
$routes->get('testrunner/run', 'TestRunner::run');
$routes->cli('testrunner/run', 'TestRunner::run');


