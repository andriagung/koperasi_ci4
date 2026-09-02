<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dasbor Business Intelligence (BI)</h1>
    <span class="badge bg-primary px-3 py-2" style="font-size: 0.9rem;">Intelligence & Analitik</span>
</div>

<div class="row">
    <!-- Tren Keuangan (Line Chart) -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tren Finansial Koperasi (12 Bulan)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 350px;">
                    <canvas id="trendKeuanganChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pertumbuhan Anggota (Bar Chart) -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pertumbuhan Anggota Baru</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 350px;">
                    <canvas id="pertumbuhanAnggotaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Penabung -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Top 5 Anggota Penabung</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Anggota</th>
                                <th>Divisi</th>
                                <th>Total Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($topPenabung)): ?>
                                <?php foreach($topPenabung as $tp): ?>
                                <tr>
                                    <td><?= esc($tp['nama_lengkap'] ?? '') ?></td>
                                    <td><?= esc($tp['divisi'] ?? '') ?></td>
                                    <td class="text-success fw-bold">Rp <?= number_format($tp['total_saldo'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center">Belum ada data simpanan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Deteksi Anomali -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4 h-100 border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Deteksi Anomali (Alerts)</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Sistem mendeteksi transaksi janggal atau penarikan massal dalam 30 hari terakhir (> Rp 5.000.000).</p>
                <?php if(!empty($anomaliPenarikan)): ?>
                    <ul class="list-group">
                        <?php foreach($anomaliPenarikan as $ano): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= esc($ano['nama_lengkap'] ?? '') ?></strong><br>
                                <span class="small text-muted"><?= date('d M Y, H:i', strtotime($ano['created_at'])) ?></span>
                            </div>
                            <span class="badge bg-danger rounded-pill">Penarikan Rp <?= number_format($ano['nominal'] ?? 0, 0, ',', '.') ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-success" role="alert">
                        Tidak ada aktivitas anomali finansial yang terdeteksi. Sistem berjalan normal.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Credit Scoring (Demo) -->
<div class="modal fade" id="creditScoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Simulasi Credit Scoring</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Pilih ID Anggota (Contoh: 1, 2, 3)</label>
                    <input type="number" id="inputAnggotaId" class="form-control" value="1">
                </div>
                <button type="button" class="btn btn-primary w-100 mb-3" onclick="hitungSkor()">Hitung Skor Kelayakan</button>
                
                <div id="scoreResult" style="display:none;" class="alert alert-info">
                    <h4 class="alert-heading text-center mb-0">Total Skor: <span id="valScore" class="fw-bold fs-2">0</span></h4>
                    <p class="text-center fw-bold mt-2 mb-3" id="valRekomendasi"></p>
                    <hr>
                    <ul id="listDetails" class="mb-0"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Memuat Chart.js secara lokal dari CDN yang sudah didefinisikan di footer atau mendefinisikan logic chart disini -->
<script>
    // Data dari Controller
    const labels = <?= json_encode($labelsBulan) ?>;
    const dataSimpanan = <?= json_encode($dataTotalSimpanan) ?>;
    const dataPinjaman = <?= json_encode($dataTotalPinjaman) ?>;
    const dataWaserda = <?= json_encode($dataPemasukanWaserda) ?>;
    const dataAnggota = <?= json_encode($dataAnggotaBaru) ?>;

    // Inisialisasi Chart Tren Keuangan
    const ctxTrend = document.getElementById('trendKeuanganChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Simpanan Masuk (Rp)',
                    data: dataSimpanan,
                    borderColor: '#10b981', // green
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Pinjaman Dicairkan (Rp)',
                    data: dataPinjaman,
                    borderColor: '#ef4444', // red
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Penjualan Waserda (Rp)',
                    data: dataWaserda,
                    borderColor: '#3b82f6', // blue
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            if(value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });

    // Inisialisasi Chart Pertumbuhan Anggota
    const ctxAnggota = document.getElementById('pertumbuhanAnggotaChart').getContext('2d');
    new Chart(ctxAnggota, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Anggota Baru',
                data: dataAnggota,
                backgroundColor: '#f59e0b',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // Fungsi fetch AJAX Credit Scoring
    function hitungSkor() {
        const id = document.getElementById('inputAnggotaId').value;
        if(!id) return alert('Masukkan ID Anggota');
        
        fetch(`<?= base_url('admin/analitik/scoring') ?>/${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('scoreResult').style.display = 'block';
                document.getElementById('valScore').innerText = data.score;
                
                const rec = document.getElementById('valRekomendasi');
                rec.innerText = data.rekomendasi;
                if(data.score >= 80) rec.className = 'text-center fw-bold mt-2 mb-3 text-success';
                else if(data.score >= 60) rec.className = 'text-center fw-bold mt-2 mb-3 text-warning';
                else rec.className = 'text-center fw-bold mt-2 mb-3 text-danger';

                const ul = document.getElementById('listDetails');
                ul.innerHTML = '';
                for(const [key, value] of Object.entries(data.details)) {
                    ul.innerHTML += `<li>${key}: <strong>${value}</strong></li>`;
                }
            } else {
                alert(data.message);
                document.getElementById('scoreResult').style.display = 'none';
            }
        });
    }
</script>
<?= $this->endSection() ?>
