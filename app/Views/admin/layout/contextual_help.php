<?php
$uri = service('uri');
$seg1 = $uri->getTotalSegments() >= 1 ? $uri->getSegment(1) : '';
$seg2 = $uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : '';
$seg3 = $uri->getTotalSegments() >= 3 ? $uri->getSegment(3) : '';

$helpTitle = '';
$helpContent = '';

if ($seg1 === 'admin') {
    switch ($seg2) {
        case 'dashboard':
        case '':
            $helpTitle = 'Dashboard Pusat';
            $helpContent = 'Halaman ini memberikan Anda ringkasan statistik harian, jumlah anggota, transaksi terakhir, serta notifikasi peringatan seperti barang stok menipis dan barang yang akan kedaluwarsa (FEFO).';
            break;
            
        case 'anggota':
            $helpTitle = 'Manajemen Keanggotaan';
            $helpContent = 'Gunakan halaman ini untuk mendaftarkan anggota baru, melihat detail anggota, mengatur status (Aktif/Nonaktif), serta mencetak kartu identitas koperasi. Setiap anggota terhubung otomatis dengan Simpanan dan Potongan Gaji.';
            break;
            
        case 'bendahara':
            $helpTitle = 'Manajemen Bendahara Gaji';
            $helpContent = 'Gunakan halaman ini untuk mendaftarkan Bendahara Gaji per instansi. Setelah didaftarkan, Anda dapat mengatur "Bendahara Gaji" pada data setiap Anggota agar tagihan potongannya dikelompokkan dan dikirim dengan benar.';
            break;
            
        case 'simpanan':
            if ($seg3 === 'jenis') {
                $helpTitle = 'Pengaturan Jenis Simpanan';
                $helpContent = 'Atur master data jenis simpanan (misal: Simpanan Pokok, Wajib, Sukarela) beserta besaran tarif atau batas minimalnya. Ini menjadi acuan tagihan anggota.';
            } else if ($seg3 === 'mutasi') {
                $helpTitle = 'Mutasi Simpanan Anggota';
                $helpContent = 'Lihat histori mutasi (keluar/masuk) simpanan dari anggota secara spesifik. Anda juga bisa mencetak Buku Simpanan Anggota.';
            } else {
                $helpTitle = 'Penerimaan / Penarikan Simpanan';
                $helpContent = 'Lakukan transaksi simpanan di sini. Setiap transaksi Sukses akan otomatis meng-update saldo Anggota, saldo Kas Koperasi, dan menghasilkan jurnal akuntansi otomatis.';
            }
            break;
            
        case 'pinjaman':
            if ($seg3 === 'produk') {
                $helpTitle = 'Produk Pinjaman';
                $helpContent = 'Tentukan produk pinjaman yang tersedia (misal: Pinjaman Barang, Uang Tunai) lengkap dengan persentase bunga/jasa (Flat/Menurun), plafon maksimal, dan tenor.';
            } else if ($seg3 === 'pengajuan' || $seg3 === 'detail') {
                $helpTitle = 'Persetujuan Pengajuan Kredit';
                $helpContent = 'Anggota yang mengajukan pinjaman akan masuk ke sini. Lakukan evaluasi kredit, periksa kelayakan (kolektibilitas), dan Setujui atau Tolak pengajuan. Setelah disetujui, pinjaman harus Dicairkan.';
            } else if ($seg3 === 'restrukturisasi') {
                $helpTitle = 'Restrukturisasi Pinjaman';
                $helpContent = 'Gunakan fitur ini untuk merestrukturisasi pinjaman yang macet, misalnya memberikan perpanjangan tenor cicilan atau memutihkan bunga.';
            } else {
                $helpTitle = 'Daftar Pinjaman Berjalan';
                $helpContent = 'Lihat seluruh daftar pinjaman anggota beserta status pembayarannya (Lancar/Macet). Dari sini Anda juga dapat menerima pembayaran cicilan manual di luar Potongan Gaji bulanan.';
            }
            break;
            
        case 'gudang':
            if ($seg3 === 'supplier') {
                $helpTitle = 'Data Supplier';
                $helpContent = 'Kelola daftar Pemasok / Distributor barang kebutuhan koperasi Anda.';
            } else {
                $helpTitle = 'Gudang & Stok';
                $helpContent = 'Pusat pemantauan keluar masuk persediaan barang. Anda dapat melihat kartu stok barang untuk mendeteksi kehilangan/penyesuaian (Stock Opname).';
            }
            break;
            
        case 'po':
            $helpTitle = 'Purchase Order (Pembelian)';
            $helpContent = 'Buat pesanan barang baru ke supplier. Barang yang dibeli dari halaman ini akan langsung menambah Persediaan Stok di Waserda dan memotong saldo Kas (HPP). Jangan lupa memasukkan tanggal kedaluwarsa.';
            break;
            
        case 'waserda':
            if ($seg3 === '') {
                $helpTitle = 'POS / Kasir Waserda';
                $helpContent = 'Lakukan transaksi jual beli fisik. Pelanggan dapat berupa masyarakat umum (Tunai) atau Anggota Koperasi (Kasbon). Transaksi Kasbon akan otomatis diproses dalam tagihan Potongan Gaji bulanan.';
            } else {
                $helpTitle = 'Katalog Waserda';
                $helpContent = 'Daftar keseluruhan barang dagangan. Anda dapat mengubah harga jual dan mengontrol kategori barang.';
            }
            break;
            
        case 'ppob':
            if ($seg3 === 'kasir') {
                $helpTitle = 'Kasir PPOB (Pulsa & Tagihan)';
                $helpContent = 'Layanan transaksi digital koperasi. Sama halnya seperti barang fisik, anggota dapat membeli Token Listrik atau Pulsa dan membayarnya via Kasbon. HPP dan Pendapatan Jasa otomatis dicatat.';
            } else {
                $helpTitle = 'Manajemen Produk PPOB';
                $helpContent = 'Atur harga beli ke Biller/Provider dan tetapkan harga jual ke pelanggan/anggota. Aktif atau Nonaktifkan produk sesuai ketersediaan Biller.';
            }
            break;
            
        case 'potongan':
            $helpTitle = 'Potongan Gaji (Payroll)';
            $helpContent = 'Ini adalah fitur unggulan koperasi. Di akhir bulan, Anda dapat men-_generate_ satu file berisikan tagihan Simpanan Wajib + Cicilan Kasbon Waserda + Kasbon PPOB + Cicilan Pinjaman per anggota, untuk langsung diserahkan ke bagian SDM (Payroll Perusahaan).';
            break;
            
        case 'akuntansi':
            if ($seg3 === 'coa') {
                $helpTitle = 'Chart of Accounts (COA)';
                $helpContent = 'Bagan Induk Akun Akuntansi. Ubah jika Anda memahami prinsip akuntansi. COA mengatur kerangka dasar Laporan Keuangan.';
            } else if ($seg3 === 'jurnal' || $seg3 === 'detail-jurnal') {
                $helpTitle = 'Jurnal Umum';
                $helpContent = 'Catatan akuntansi double-entry harian. 90% transaksi masuk ke sini secara otomatis (Auto-Posting). Anda tetap bisa membuat jurnal penyesuaian manual dari sini jika diperlukan.';
            } else if ($seg3 === 'laba-rugi') {
                $helpTitle = 'Laporan Laba Rugi';
                $helpContent = 'Mempertemukan total Pendapatan dan Total Beban/HPP untuk menghitung profitabilitas. Laba yang dihasilkan akan dikapitalisasi menjadi modal pembagian SHU.';
            } else if ($seg3 === 'buku-besar') {
                $helpTitle = 'Buku Besar (Ledger)';
                $helpContent = 'Pantau rincian perputaran (Debit/Kredit) dari satu akun spesifik (Misal: Kas Utama, atau Piutang) selama rentang waktu tertentu.';
            } else {
                $helpTitle = 'Modul Akuntansi';
                $helpContent = 'Pusat laporan keuangan berstandar SAK. Anda dapat melihat Buku Besar, Neraca Saldo, Laba Rugi, dan Neraca Keuangan secara real-time.';
            }
            break;
            
        case 'laporan':
            $helpTitle = 'Pusat Laporan & Analitik';
            $helpContent = 'Halaman terpadu untuk mengekspor segala aktivitas Koperasi. Tersedia laporan anggota, perputaran stok (termasuk Barang Mati / Slow Moving), penjualan, dan arus kas.';
            break;
            
        case 'shu':
            $helpTitle = 'Sisa Hasil Usaha (SHU)';
            $helpContent = 'Hitung proporsi bagi hasil koperasi di akhir tahun buku. SHU dibagikan berdasarkan besaran partisipasi modal (Simpanan) dan transaksi belanja (Jasa Anggota) dari masing-masing orang.';
            break;

        case 'penagihan':
            $helpTitle = 'Penagihan & Tunggakan';
            $helpContent = 'Monitor anggota yang menunggak cicilan pinjaman melewati tanggal jatuh tempo agar kolektibilitas dan cash flow koperasi tetap sehat.';
            break;
    }
}

// Jika ada teks bantuan, tampilkan kotak komponennya
if (!empty($helpTitle)): 
?>
<div class="alert alert-info alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="background: #eff6ff; color: #1e3a8a;">
    <div class="d-flex align-items-start">
        <div class="me-3 mt-1">
            <i class="fas fa-lightbulb fa-2x text-primary" style="opacity: 0.8;"></i>
        </div>
        <div>
            <h6 class="alert-heading fw-bold mb-1"><i class="fas fa-question-circle me-1"></i> Panduan: <?= $helpTitle ?? '' ?></h6>
            <p class="mb-0" style="font-size: 0.95rem; line-height: 1.5;"><?= $helpContent ?? '' ?></p>
        </div>
    </div>
    <button type="button" class="btn-close mt-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
