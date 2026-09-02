<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-hand-holding-usd text-primary me-2"></i>Pembagian SHU</h2>
        <p class="text-muted">Kalkulasi dan Distribusi Sisa Hasil Usaha</p>
    </div>
    <div class="col-md-6 text-end">
        
    <?= csrf_field() ?>" method="POST" class="d-inline">
            <input type="hidden" name="tahun" value="<?= $tahunSekarang ?? '' ?>">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Mulai proses kalkulasi draf SHU Tahun <?= $tahunSekarang ?? '' ?> berdasarkan snapshot data saat ini?');">
                <i class="fas fa-calculator me-2"></i>Kalkulasi Draf SHU <?= $tahunSekarang ?? '' ?>
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card glass-card mb-4">
            <div class="card-header bg-white pb-0 border-0">
                <h5 class="mb-0">Daftar Riwayat SHU</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Tahun</th>
                                <th class="text-end">Total Laba Bersih</th>
                                <th class="text-end">Jasa Modal</th>
                                <th class="text-end">Jasa Anggota</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayat)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data kalkulasi SHU</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($riwayat as $r): ?>
                                <tr>
                                    <td class="fw-bold"><?= $r['tahun'] ?? '' ?></td>
                                    <td class="text-end">Rp <?= number_format($r['total_shu'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($r['total_jasa_modal'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($r['total_jasa_usaha'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php if($r['status'] == 'Dihitung'): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Draf (Dihitung)</span>
                                        <?php elseif($r['status'] == 'APPROVED'): ?>
                                            <span class="badge bg-info text-dark"><i class="fas fa-check me-1"></i>Disetujui</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Dibagikan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/shu/detail/' . idhash_encode($r['id'])) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card glass-card">
            <div class="card-header bg-white pb-0 border-0">
                <h5 class="mb-0">Proporsi SHU</h5>
                <small class="text-muted">Parameter Pembagian Global</small>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php 
                    $total = 0;
                    foreach ($pengaturan as $p): 
                        $total += $p['persentase'];
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        <div>
                            <strong><?= $p['nama_alokasi'] ?? '' ?></strong><br>
                            <small class="text-muted"><?= $p['keterangan'] ?? '' ?></small>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= $p['persentase'] ?? '' ?>%</span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 mt-3 pt-3 border-top">
                        <strong>TOTAL PERSENTASE</strong>
                        <span class="badge <?= $total == 100 ? 'bg-success' : 'bg-danger' ?> rounded-pill fs-6"><?= $total ?? '' ?>%</span>
                    </li>
                </ul>
                <?php if($total != 100): ?>
                <div class="alert alert-danger mt-3 py-2 text-center" style="font-size: 0.85rem;">
                    <i class="fas fa-exclamation-triangle"></i> Total persentase tidak 100%. Harap perbarui di menu Pengaturan.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

