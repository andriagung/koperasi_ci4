<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-coins text-success me-2"></i>Dashboard Keuangan</h2>
        <p class="text-muted">Pantau arus kas, simpanan, dan pinjaman anggota</p>
    </div>
    <div class="col-md-6 text-end">
        <div id="clock" class="fw-bold fs-5 text-primary bg-white p-2 px-3 rounded shadow-sm d-inline-block"></div>
    </div>
</div>

<!-- 1. Summary Cards -->
<div class="row mb-4">
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-warning">
            <div class="card-body px-2">
                <i class="fas fa-hand-holding-usd fa-2x text-warning mb-2 opacity-75"></i>
                <h6 class="text-muted small">Total Piutang (Berjalan)</h6>
                <h5 class="mb-0 fw-bold">Rp <?= number_format($piutangBerjalan ?? 0, 0, ',', '.') ?></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-success">
            <div class="card-body px-2">
                <i class="fas fa-wallet fa-2x text-success mb-2 opacity-75"></i>
                <h6 class="text-muted small">Total Simpanan (Semua Jenis)</h6>
                <h5 class="mb-0 fw-bold">Rp <?= number_format(array_sum($chartSimpanan['data'] ?? []), 0, ',', '.') ?></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-primary">
            <div class="card-body px-2">
                <i class="fas fa-users fa-2x text-primary mb-2 opacity-75"></i>
                <h6 class="text-muted small">Total Anggota Aktif</h6>
                <h5 class="mb-0 fw-bold"><?= $totalAnggotaAktif ?? 0 ?> Anggota</h5>
            </div>
        </div>
    </div>
</div>

<!-- 2. Quick Actions -->
<div class="row mb-4">
    <div class="col-md-4 mb-2">
        <a href="<?= base_url('admin/simpanan') ?>" class="btn btn-outline-success w-100 py-3 shadow-sm rounded-3 fw-bold">
            <i class="fas fa-piggy-bank fa-2x mb-2 d-block"></i>
            Kelola Simpanan
        </a>
    </div>
    <div class="col-md-4 mb-2">
        <a href="<?= base_url('admin/pinjaman') ?>" class="btn btn-outline-warning w-100 py-3 shadow-sm rounded-3 fw-bold">
            <i class="fas fa-handshake fa-2x mb-2 d-block"></i>
            Kelola Pinjaman
        </a>
    </div>
    <div class="col-md-4 mb-2">
        <a href="<?= base_url('admin/laporan/neraca') ?>" class="btn btn-outline-info w-100 py-3 shadow-sm rounded-3 fw-bold">
            <i class="fas fa-file-invoice-dollar fa-2x mb-2 d-block"></i>
            Laporan Keuangan
        </a>
    </div>
</div>

<!-- 3. Charts -->
<div class="row">
    <!-- Chart Komposisi Simpanan -->
    <div class="col-md-6 mx-auto">
        <div class="card glass-card h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0"><i class="fas fa-chart-pie text-warning me-2"></i>Komposisi Simpanan</h5>
            </div>
            <div class="card-body">
                <div style="position: relative; height:300px; width:100%">
                    <canvas id="chartSimpanan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function updateClock() {
        const now = new Date();
        const str = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('clock').innerHTML = '<i class="far fa-clock me-2"></i>' + str;
    }
    setInterval(updateClock, 1000);
    updateClock();

    $(document).ready(function() {
        // Init Chart Komposisi Simpanan
        const ctxSimpanan = document.getElementById('chartSimpanan');
        if (ctxSimpanan) {
            new Chart(ctxSimpanan, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($chartSimpanan['labels'] ?? []) ?>,
                    datasets: [{
                        data: <?= json_encode($chartSimpanan['data'] ?? []) ?>,
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.8)',   // Success
                            'rgba(13, 110, 253, 0.8)',  // Primary
                            'rgba(255, 193, 7, 0.8)',   // Warning
                            'rgba(220, 53, 69, 0.8)',   // Danger
                            'rgba(13, 202, 240, 0.8)'   // Info
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '60%'
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
