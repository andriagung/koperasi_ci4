<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-cash-register text-info me-2"></i>Dashboard Kasir Waserda</h2>
        <p class="text-muted">Ringkasan operasional minimarket hari ini</p>
    </div>
    <div class="col-md-6 text-end">
        <div id="clock" class="fw-bold fs-5 text-primary bg-white p-2 px-3 rounded shadow-sm d-inline-block"></div>
    </div>
</div>

<!-- 1. Summary Cards -->
<div class="row mb-4">
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-success">
            <div class="card-body px-2">
                <i class="fas fa-hand-holding-usd fa-2x text-success mb-2 opacity-75"></i>
                <h6 class="text-muted small">Pendapatan Hari Ini</h6>
                <h5 class="mb-0 fw-bold">Rp <?= number_format($pendapatanHariIni ?? 0, 0, ',', '.') ?></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-primary">
            <div class="card-body px-2">
                <i class="fas fa-receipt fa-2x text-primary mb-2 opacity-75"></i>
                <h6 class="text-muted small">Jumlah Transaksi (Struk)</h6>
                <h5 class="mb-0 fw-bold"><?= $jumlahTransaksiHariIni ?? 0 ?> Transaksi</h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-xl-0">
        <div class="card glass-card text-center py-3 h-100 border-start border-4 border-warning">
            <div class="card-body px-2">
                <i class="fas fa-shopping-basket fa-2x text-warning mb-2 opacity-75"></i>
                <h6 class="text-muted small">Total Item Terjual Hari Ini</h6>
                <h5 class="mb-0 fw-bold"><?= $itemTerjualHariIni ?? 0 ?> Item</h5>
            </div>
        </div>
    </div>
</div>

<!-- 2. Quick Actions -->
<div class="row mb-4">
    <div class="col-md-6 mb-2">
        <a href="<?= base_url('admin/waserda') ?>" class="btn btn-outline-info w-100 py-4 shadow-sm rounded-3 fw-bold d-flex flex-column align-items-center justify-content-center h-100 fs-5">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            Buka Layar Kasir (POS)
        </a>
    </div>
    <div class="col-md-6 mb-2">
        <a href="<?= base_url('admin/waserda/laporan_harian') ?>" class="btn btn-outline-secondary w-100 py-4 shadow-sm rounded-3 fw-bold d-flex flex-column align-items-center justify-content-center h-100 fs-5">
            <i class="fas fa-print fa-3x mb-3"></i>
            Riwayat Penjualan Kasir
        </a>
    </div>
</div>

<!-- 3. Tables -->
<div class="row">
    <!-- Widget Realtime Transaksi Waserda -->
    <div class="col-md-8 mx-auto">
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
<script>
    function updateClock() {
        const now = new Date();
        const str = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('clock').innerHTML = '<i class="far fa-clock me-2"></i>' + str;
    }
    setInterval(updateClock, 1000);
    updateClock();

    $(document).ready(function() {
        // Polling Transaksi Live setiap 10 detik
        function fetchLiveTransaksi() {
            $.get("<?= base_url('admin/dashboard/get_transaksi_live') ?>", function(data) {
                $('#live-transaksi-container').html(data);
            });
        }
        
        fetchLiveTransaksi();
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
