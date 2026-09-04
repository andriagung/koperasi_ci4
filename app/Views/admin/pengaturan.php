<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-cog"></i> Pengaturan Sistem Koperasi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        
    <form action="" method="POST">
        <?= csrf_field() ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Limit Plafon Pinjaman Maksimal (Rp)</label>
                                <input type="number" name="limit_pinjaman_max" class="form-control" value="<?= esc($pengaturan['limit_pinjaman_max'] ?? '15000000') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jasa / Bunga Pinjaman (% per Bulan)</label>
                                <input type="number" step="0.1" name="jasa_bunga_pinjaman" class="form-control" value="<?= esc($pengaturan['jasa_bunga_pinjaman'] ?? '1.0') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Limit Maksimal Kasbon Waserda (Rp)</label>
                                <input type="number" name="limit_kasbon_waserda" class="form-control" value="<?= esc($pengaturan['limit_kasbon_waserda'] ?? '1500000') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Simpanan Wajib Bulanan (Rp)</label>
                                <input type="number" name="simpanan_wajib_bulan" class="form-control" value="<?= esc($pengaturan['simpanan_wajib_bulan'] ?? '50000') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Taksiran Beban Operasional Tahunan (Rp)</label>
                                <input type="number" name="beban_operasional_tahunan" class="form-control" value="<?= esc($pengaturan['beban_operasional_tahunan'] ?? '0') ?>">
                                <small class="text-muted">Digunakan untuk memotong pendapatan kotor dan menghitung SHU Bersih.</small>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Konfigurasi</button>
                        </form>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="fas fa-database text-warning"></i> Utilitas Pencadangan Basis Data</h5>
                        <p class="text-muted mb-3">
                            Buat cadangan (backup) seluruh data dan struktur Koperasi saat ini. Simpan file <code>.sql</code> ini di tempat yang aman.
                        </p>
                        <a href="<?= base_url('admin/backup-db') ?>" target="_blank" class="btn btn-outline-primary"><i class="fas fa-download me-1"></i> Unduh Backup Database (.sql)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

