<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-mobile-alt text-primary me-2"></i>Manajemen PPOB</h2>
        <p class="text-muted">Kelola produk PPOB dan pantau transaksi digital</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('admin/ppob/kasir') ?>" class="btn btn-success me-2">
            <i class="fas fa-cash-register me-1"></i> Buka Kasir PPOB
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tambah-ppob">
            <i class="fas fa-plus me-1"></i> Tambah Produk PPOB
        </button>
    </div>
</div>

<?php if(session()->getFlashdata('message')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session()->getFlashdata('message') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Tabs -->
<div class="nav-tabs mb-4">
    <button class="nav-tab-btn active" onclick="switchTab('produk')"><i class="fas fa-box me-2"></i>Produk PPOB</button>
    <button class="nav-tab-btn" onclick="switchTab('transaksi')"><i class="fas fa-history me-2"></i>Riwayat Transaksi</button>
</div>

<!-- Tab: Produk -->
<div id="tab-produk" class="tab-pane active">
    <div class="card glass-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="table-ppob">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Kode</th>
                            <th width="20%">Produk</th>
                            <th width="15%">Kategori/Provider</th>
                            <th width="15%">Harga Beli</th>
                            <th width="15%">Harga Jual</th>
                            <th width="10%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Riwayat Transaksi -->
<div id="tab-transaksi" class="tab-pane">
    <div class="card glass-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="table-transaksi">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="15%">Invoice</th>
                            <th width="15%">Anggota</th>
                            <th width="20%">Produk / No Pelanggan</th>
                            <th width="10%">Total</th>
                            <th width="10%">Bayar</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modal-tambah-ppob" tabindex="-1">
    <div class="modal-dialog modal-lg">
        
    <form action="" method="GET">
        <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk PPOB</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Kode Produk</label>
                    <input type="text" name="kode_produk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Nama Produk (Contoh: Telkomsel 50rb)</label>
                    <input type="text" name="nama_produk" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="Pulsa">Pulsa</option>
                            <option value="Paket Data">Paket Data</option>
                            <option value="Token PLN">Token PLN</option>
                            <option value="Tagihan PLN">Tagihan PLN</option>
                            <option value="BPJS">BPJS</option>
                            <option value="PDAM">PDAM</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Provider</label>
                        <input type="text" name="provider" class="form-control" placeholder="Telkomsel, PLN, dll" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Harga Beli (Modal)</label>
                        <input type="number" name="harga_beli" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label>Harga Jual</label>
                        <input type="number" name="harga_jual" class="form-control" required>
                    </div>
                </div>
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked value="1">
                    <label class="form-check-label" for="isActive">Produk Aktif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Produk -->
<div class="modal fade" id="modal-edit-ppob" tabindex="-1">
    <div class="modal-dialog modal-lg">
        
    <form action="" method="GET">
        <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk PPOB</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Kode Produk</label>
                    <input type="text" name="kode_produk" id="edit_kode" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" id="edit_nama" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Kategori</label>
                        <select name="kategori" id="edit_kategori" class="form-select" required>
                            <option value="Pulsa">Pulsa</option>
                            <option value="Paket Data">Paket Data</option>
                            <option value="Token PLN">Token PLN</option>
                            <option value="Tagihan PLN">Tagihan PLN</option>
                            <option value="BPJS">BPJS</option>
                            <option value="PDAM">PDAM</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Provider</label>
                        <input type="text" name="provider" id="edit_provider" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Harga Beli</label>
                        <input type="number" name="harga_beli" id="edit_beli" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label>Harga Jual</label>
                        <input type="number" name="harga_jual" id="edit_jual" class="form-control" required>
                    </div>
                </div>
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_active" value="1">
                    <label class="form-check-label" for="edit_active">Produk Aktif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modal-hapus-ppob" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form action="" method="POST" id="form-hapus-ppob">
        <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Hapus Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <p>Yakin ingin menghapus produk ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
let tableProduk, tableTransaksi;

$(document).ready(function() {
    tableProduk = $('#table-ppob').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/ajax-ppob-produk',
            type: 'POST'
        },
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});

function switchTab(tab) {
    $('.nav-tab-btn').removeClass('active');
    $('.tab-pane').removeClass('active');
    
    $(`button[onclick="switchTab('${tab}')"]`).addClass('active');
    $(`#tab-${tab}`).addClass('active');

    if (tab === 'transaksi' && !tableTransaksi) {
        tableTransaksi = $('#table-transaksi').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/ajax-ppob-transaksi',
                type: 'POST'
            },
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[0, 'desc']]
        });
    }
}

function editProduk(id, kode, nama, kategori, provider, beli, jual, is_active) {
    $('#form-edit-ppob').attr('action', '/admin/ppob/editProduk/' + id);
    $('#edit_kode').val(kode);
    $('#edit_nama').val(nama);
    $('#edit_kategori').val(kategori);
    $('#edit_provider').val(provider);
    $('#edit_beli').val(beli);
    $('#edit_jual').val(jual);
    $('#edit_active').prop('checked', is_active == 1);
    
    var modal = new bootstrap.Modal(document.getElementById('modal-edit-ppob'));
    modal.show();
}

function hapusProduk(id) {
    $('#form-hapus-ppob').attr('action', '/admin/ppob/hapusProduk/' + id);
    var modal = new bootstrap.Modal(document.getElementById('modal-hapus-ppob'));
    modal.show();
}
</script>
<?= $this->endSection() ?>

