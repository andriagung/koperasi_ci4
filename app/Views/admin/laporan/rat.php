<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title"><?= $judul ?? '' ?></h4>
                <a href="<?= base_url('admin/laporan/rat?action=print&tahun=' . $tahun) ?>" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fas fa-print"></i> Cetak Dokumen RAT
                </a>
            </div>
            <div class="card-body">
                
    <form action="" method="GET">
        <?= csrf_field() ?>
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="tahun">Pilih Tahun Buku</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    <?php 
                                    $currentYear = date('Y');
                                    for($i = $currentYear; $i >= $currentYear - 5; $i--): 
                                    ?>
                                        <option value="<?= $i ?? '' ?>" <?= ($tahun == $i) ? 'selected' : '' ?>><?= $i ?? '' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="alert alert-info">
                    Ini adalah *preview* draf laporan. Laporan RAT mengambil data ringkasan neraca, laba/rugi, dan pertumbuhan anggota tahun berjalan.
                </div>

                <div class="border p-4 bg-light text-center">
                    <h5>DRAF BUKU LAPORAN PERTANGGUNGJAWABAN (RAT) PENGURUS</h5>
                    <h6>KPRI RSUD '45 KUNINGAN TAHUN BUKU <?= $tahun ?? '' ?></h6>
                    <br>
                    <p class="text-muted">Gunakan tombol <strong>Cetak Dokumen RAT</strong> di sudut kanan atas untuk melihat dan mencetak versi dokumen resmi.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

