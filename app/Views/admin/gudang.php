<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
            <div class="page-title">
                Gudang & Manajemen Stok Waserda
            </div>

            <!-- Navigation Tabs -->
            <div class="nav-tabs">
                <button class="nav-tab-btn active" onclick="switchGudangTab('tab-produk', this)"><i class="fas fa-box"></i> Master Produk</button>
                <button class="nav-tab-btn" onclick="switchGudangTab('tab-kategori', this)"><i class="fas fa-tags"></i> Kategori</button>
                <button class="nav-tab-btn" onclick="switchGudangTab('tab-supplier', this)"><i class="fas fa-truck"></i> Supplier</button>
                <button class="nav-tab-btn" onclick="switchGudangTab('tab-transaksi', this)"><i class="fas fa-history"></i> Riwayat Transaksi</button>
                <button class="nav-tab-btn" onclick="switchGudangTab('tab-opname', this)"><i class="fas fa-clipboard-check"></i> Stock Opname</button>
            </div>

            <!-- Tab: Master Produk -->
            <div id="tab-produk" class="tab-pane active">
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabel-waserda-produk" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Tab: Kategori -->
            <div id="tab-kategori" class="tab-pane">
                <div class="table-container">
                    <button class="btn-primary" style="margin-bottom: 15px;" onclick="bukaModal('modal-tambah-kategori')"><i class="fas fa-plus"></i> Tambah Kategori</button>
                    <div class="table-responsive">
                    <table class="display datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($kategori)): foreach($kategori as $i => $k): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($k['nama_kategori'] ?? '') ?></td>
                                <td><?= esc($k['deskripsi'] ?? '-') ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action edit" onclick="editKategoriModal('<?= idhash_encode($k['id']) ?>', '<?= esc($k['nama_kategori'] ?? '') ?>', '<?= esc($k['deskripsi'] ?? '') ?>')"><i class="fas fa-edit"></i></button>
                                        <button class="btn-action delete" onclick="if(confirm('Hapus kategori?')) window.location='/admin/waserda/hapus-kategori/<?= idhash_encode($k['id']) ?>'"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Tab: Supplier -->
            <div id="tab-supplier" class="tab-pane">
                <div class="table-container">
                    <button class="btn-primary" style="margin-bottom: 15px;" onclick="bukaModal('modal-tambah-supplier')"><i class="fas fa-plus"></i> Tambah Supplier</button>
                    <div class="table-responsive">
                    <table class="display datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Supplier</th>
                                <th>Kontak</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($suppliers)): foreach($suppliers as $i => $s): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($s['kode_supplier'] ?? '') ?></td>
                                <td><?= esc($s['nama_supplier'] ?? '') ?></td>
                                <td><?= esc($s['kontak'] ?? '') ?></td>
                                <td><?= esc($s['alamat'] ?? '') ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action edit" onclick="editSupplierModal('<?= idhash_encode($s['id']) ?>', '<?= esc($s['kode_supplier'] ?? '') ?>', '<?= esc($s['nama_supplier'] ?? '') ?>', '<?= esc($s['kontak'] ?? '') ?>', '<?= esc($s['npwp'] ?? '') ?>', '<?= esc($s['nama_bank'] ?? '') ?>', '<?= esc($s['rekening_bank'] ?? '') ?>', '<?= esc($s['alamat'] ?? '') ?>')"><i class="fas fa-edit"></i></button>
                                        <button class="btn-action delete" onclick="if(confirm('Hapus supplier?')) window.location='/admin/waserda/hapus-supplier/<?= idhash_encode($s['id']) ?>'"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Tab: Riwayat Transaksi -->
            <div id="tab-transaksi" class="tab-pane">
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabel-waserda-transaksi" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No. Ref</th>
                                <th>Anggota</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Tab: Stock Opname -->
            <div id="tab-opname" class="tab-pane">
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabel-stock-opname" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Produk</th>
                                <th>Stok Sistem</th>
                                <th>Stok Fisik</th>
                                <th>Selisih</th>
                                <th>Keterangan</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>

<script>
function switchGudangTab(tabId, btn) {
    // Sembunyikan semua tab
    document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
    // Hapus active state dari semua button
    document.querySelectorAll('.nav-tab-btn').forEach(el => el.classList.remove('active'));
    
    // Tampilkan tab yang dipilih
    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

// Menambahkan custom button Tambah Produk di tab produk dan Penyesuaian Stok di tab opname
document.addEventListener("DOMContentLoaded", function() {
    // Cek hash URL untuk membuka tab yang tepat
    if (window.location.hash) {
        var hash = window.location.hash.substring(1);
        var btn = document.querySelector('button[onclick*="' + hash + '"]');
        if (btn) {
            switchGudangTab(hash, btn);
        }
    }

    $('#tabel-waserda-produk').on('init.dt', function() {
        var btnProduk = `
            <div style="display:inline-flex; gap:10px;">
                <button class="btn-sm btn-primary" style="padding: 8px 15px; font-size: 0.85rem;" onclick="bukaModal('modal-tambah-promo')"><i class="fas fa-plus"></i> Tambah Produk Baru</button>
            </div>
        `;
        $('#tabel-waserda-produk_wrapper .dt-custom-buttons').append(btnProduk);
    });

    // Custom Button untuk Stock Opname
    $('#tabel-stock-opname').on('init.dt', function() {
        var btnOpname = `
            <div style="display:inline-flex; gap:10px;">
                <button class="btn-sm btn-primary" style="background: #ea580c; padding: 8px 15px; font-size: 0.85rem;" onclick="bukaModal('modal-stock-opname')"><i class="fas fa-clipboard-check"></i> Penyesuaian Stok Fisik</button>
            </div>
        `;
        $('#tabel-stock-opname_wrapper .dt-custom-buttons').append(btnOpname);
    });
});
</script>
<?= $this->include('admin/waserda_modals') ?>
<?= $this->endSection() ?>
