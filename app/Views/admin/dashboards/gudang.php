<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-boxes text-warning me-2"></i>Dashboard Gudang & Logistik</h2>
        <p class="text-muted">Pantau persediaan barang, stok masuk, dan stok opname</p>
    </div>
    <div class="col-md-6 text-end">
        <div id="clock" class="fw-bold fs-5 text-primary bg-white p-2 px-3 rounded shadow-sm d-inline-block"></div>
    </div>
</div>

<!-- 1. Summary Cards -->
<div class="row mb-4">
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-info">
            <div class="card-body px-2">
                <i class="fas fa-cubes fa-2x text-info mb-2 opacity-75"></i>
                <h6 class="text-muted small">Total Item Tersedia</h6>
                <h5 class="mb-0 fw-bold"><?= number_format($totalItemGudang ?? 0, 0, ',', '.') ?> Item</h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-danger">
            <div class="card-body px-2">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2 opacity-75"></i>
                <h6 class="text-muted small">Peringatan Stok Kritis</h6>
                <h5 class="mb-0 fw-bold text-danger"><?= $stokKritis ?? 0 ?> Produk</h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-secondary">
            <div class="card-body px-2">
                <i class="fas fa-clipboard-check fa-2x text-secondary mb-2 opacity-75"></i>
                <h6 class="text-muted small">Kategori Produk Aktif</h6>
                <h5 class="mb-0 fw-bold"><?= $totalKategori ?? 0 ?> Kategori</h5>
            </div>
        </div>
    </div>
</div>

<!-- 2. Quick Actions -->
<div class="row mb-4">
    <div class="col-md-4 mb-2">
        <a href="<?= base_url('admin/produk_waserda') ?>" class="btn btn-outline-primary w-100 py-3 shadow-sm rounded-3 fw-bold">
            <i class="fas fa-box fa-2x mb-2 d-block"></i>
            Katalog Produk
        </a>
    </div>
    <div class="col-md-4 mb-2">
        <a href="<?= base_url('admin/produk_waserda/barang_masuk') ?>" class="btn btn-outline-success w-100 py-3 shadow-sm rounded-3 fw-bold">
            <i class="fas fa-truck-loading fa-2x mb-2 d-block"></i>
            Input Barang Masuk
        </a>
    </div>
    <div class="col-md-4 mb-2">
        <a href="<?= base_url('admin/produk_waserda/kategori') ?>" class="btn btn-outline-secondary w-100 py-3 shadow-sm rounded-3 fw-bold">
            <i class="fas fa-tags fa-2x mb-2 d-block"></i>
            Kelola Kategori
        </a>
    </div>
</div>

<!-- 3. Tables -->
<div class="row">
    <!-- Tabel Stok Menipis -->
    <div class="col-md-12">
        <div class="card glass-card h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Produk Stok Menipis (Restock Diperlukan)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok Tersisa</th>
                                <th class="text-center">Batas Minimum</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topStokKritis)): ?>
                                <?php foreach ($topStokKritis as $stok): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 p-2 rounded me-3 text-danger"><i class="fas fa-box-open"></i></div>
                                                <span class="fw-semibold"><?= esc($stok['nama_produk'] ?? '') ?></span>
                                            </div>
                                        </td>
                                        <td><?= esc($stok['nama_kategori'] ?? '-') ?></td>
                                        <td class="text-center"><span class="badge bg-danger rounded-pill px-3 py-2"><?= $stok['stok'] ?? '' ?></span></td>
                                        <td class="text-center text-muted"><?= $stok['stok_minimum'] ?? '' ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('admin/produk_waserda') ?>" class="btn btn-sm btn-outline-primary">Restock</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Semua stok produk dalam kondisi aman.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    function updateClock() {
        const now = new Date();
        const str = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('clock').innerHTML = '<i class="far fa-clock me-2"></i>' + str;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
<?= $this->endSection() ?>
