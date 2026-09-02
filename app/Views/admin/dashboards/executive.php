
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-tachometer-alt text-primary me-2"></i>Dashboard Eksekutif</h2>
        <p class="text-muted">Ringkasan performa Koperasi Pegawai RSUD secara real-time</p>
    </div>
    <div class="col-md-6 text-end">
        <div id="clock" class="fw-bold fs-5 text-primary bg-white p-2 px-3 rounded shadow-sm d-inline-block"></div>
    </div>
</div>

<!-- 1. Summary Cards (4 Cards) -->
<div class="row mb-4">
    <div class="col-12 col-md-3 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-info">
            <div class="card-body px-2">
                <i class="fas fa-users fa-2x text-info mb-2 opacity-75"></i>
                <h6 class="text-muted small">Total Anggota Aktif</h6>
                <h5 class="mb-0 fw-bold"><?= $totalAnggotaAktif ?? '' ?> Orang</h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-success">
            <div class="card-body px-2">
                <i class="fas fa-cash-register fa-2x text-success mb-2 opacity-75"></i>
                <h6 class="text-muted small">Pendapatan Hari Ini</h6>
                <h5 class="mb-0 fw-bold">Rp <?= number_format($pendapatanHariIni ?? 0, 0, ',', '.') ?></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-warning">
            <div class="card-body px-2">
                <i class="fas fa-hand-holding-usd fa-2x text-warning mb-2 opacity-75"></i>
                <h6 class="text-muted small">Total Piutang Berjalan</h6>
                <h5 class="mb-0 fw-bold">Rp <?= number_format($piutangBerjalan ?? 0, 0, ',', '.') ?></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-danger">
            <div class="card-body px-2">
                <i class="fas fa-boxes fa-2x text-danger mb-2 opacity-75"></i>
                <h6 class="text-muted small">Peringatan Stok Menipis</h6>
                <h5 class="mb-0 fw-bold"><?= $stokKritis ?? '' ?> Item</h5>
            </div>
        </div>
    </div>
</div>

<!-- 2. Quick Actions -->
<div class="row mb-4">
    <div class="col-md-3 mb-2">
        <a href="<?= base_url('admin/anggota') ?>" class="btn btn-outline-primary w-100 py-3 shadow-sm rounded-3 fw-bold d-flex flex-column align-items-center justify-content-center h-100">
            <i class="fas fa-user-plus fa-2x mb-2"></i>
            Tambah Anggota
        </a>
    </div>
    <div class="col-md-3 mb-2">
        <a href="<?= base_url('admin/simpanan') ?>" class="btn btn-outline-success w-100 py-3 shadow-sm rounded-3 fw-bold d-flex flex-column align-items-center justify-content-center h-100">
            <i class="fas fa-piggy-bank fa-2x mb-2"></i>
            Input Setoran
        </a>
    </div>
    <div class="col-md-3 mb-2">
        <a href="<?= base_url('admin/waserda') ?>" class="btn btn-outline-info w-100 py-3 shadow-sm rounded-3 fw-bold d-flex flex-column align-items-center justify-content-center h-100">
            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
            Buka Kasir (POS)
        </a>
    </div>
    <div class="col-md-3 mb-2">
        <a href="<?= base_url('admin/laporan/penjualanHarian') ?>" class="btn btn-outline-secondary w-100 py-3 shadow-sm rounded-3 fw-bold d-flex flex-column align-items-center justify-content-center h-100">
            <i class="fas fa-print fa-2x mb-2"></i>
            Cetak Laporan Harian
        </a>
    </div>
</div>

<!-- 3. Charts -->
<div class="row mb-4">
    <!-- Chart 1: Penjualan Bulanan (Line Chart) -->
    <div class="col-md-8">
        <div class="card glass-card h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Grafik Penjualan Bulanan (30 Hari)</h5>
            </div>
            <div class="card-body">
                <canvas id="chartPenjualan" height="100"></canvas>
            </div>
        </div>
    </div>
    <!-- Chart 2: Komposisi Simpanan (Doughnut Chart) -->
    <div class="col-md-4">
        <div class="card glass-card h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Komposisi Simpanan</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="chartSimpanan"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 4. Tables -->
<div class="row">
    <!-- Peringatan Stok Kritis -->
    <div class="col-md-6">
        <div class="card glass-card shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Peringatan Stok Kritis</h5>
                <a href="<?= base_url('admin/inventory/kartu-stok') ?>" class="btn btn-sm btn-outline-danger">Cek Gudang</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th class="text-end">Sisa Stok</th>
                                <th class="text-end">Min. Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($topStokKritis)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Stok aman, tidak ada barang kritis.</td></tr>
                            <?php else: ?>
                                <?php foreach($topStokKritis as $s): ?>
                                <tr>
                                    <td class="fw-bold"><?= esc($s['nama_produk'] ?? '') ?></td>
                                    <td><?= esc($s['nama_kategori'] ?? '-') ?></td>
                                    <td class="text-end text-danger fw-bold"><?= $s['stok'] ?? '' ?></td>
                                    <td class="text-end text-muted"><?= $s['stok_minimum'] ?? '' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Widget Realtime Transaksi Waserda -->
    <div class="col-md-6">
        <div class="card glass-card h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-receipt text-primary me-2"></i>Transaksi Terakhir (Live)</h5>
                <i class="fas fa-circle text-success flash-animated" title="Real-time"></i>
            </div>
            <div class="card-body">
                <div id="live-transaksi-container" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Memuat transaksi...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Jam Realtime
    function updateClock() {
        const now = new Date();
        const str = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('clock').innerHTML = '<i class="far fa-clock me-2"></i>' + str;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Line Chart Penjualan Bulanan
    const ctxPenjualan = document.getElementById('chartPenjualan').getContext('2d');
    new Chart(ctxPenjualan, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartPenjualan['labels']) ?>,
            datasets: [{
                label: 'Omzet Penjualan (Rp)',
                data: <?= json_encode($chartPenjualan['data']) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: 'rgb(13, 110, 253)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Doughnut Chart Komposisi Simpanan
    const ctxSimpanan = document.getElementById('chartSimpanan').getContext('2d');
    new Chart(ctxSimpanan, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chartSimpanan['labels']) ?>,
            datasets: [{
                data: <?= json_encode($chartSimpanan['data']) ?>,
                backgroundColor: [
                    'rgba(25, 135, 84, 0.8)',   // Hijau
                    'rgba(13, 202, 240, 0.8)',  // Cyan
                    'rgba(255, 193, 7, 0.8)',   // Kuning
                    'rgba(111, 66, 193, 0.8)'   // Ungu
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    $(document).ready(function() {
        // Polling Transaksi Live setiap 10 detik
        function fetchLiveTransaksi() {
            $.get("<?= base_url('admin/dashboard/get_transaksi_live') ?>", function(data) {
                $('#live-transaksi-container').html(data);
            });
        }
        
        fetchLiveTransaksi(); // Initial load
        setInterval(fetchLiveTransaksi, 10000);
    });
</script>
<style>
.flash-animated {
    animation: flash 2s infinite;
}
@keyframes flash {
    0% { opacity: 1; }
    50% { opacity: 0.2; }
    100% { opacity: 1; }
}
</style>
<?= $this->endSection() ?>
