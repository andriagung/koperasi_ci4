<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title"><?= esc($judul ?? 'Laporan Daftar Potongan Gaji') ?></div>

<div class="card glass-card" style="padding: 20px;">
    <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-filter"></i> Filter Laporan Daftar Potongan</h3>
    
    <form action="" method="POST">
        <?= csrf_field() ?>" method="POST" target="_blank">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label>Unit Kerja</label>
                <select name="unit_kerja" class="form-control" style="width: 100%; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                    <option value="all">Semua Unit Kerja</option>
                    <?php if (isset($unit_kerja)): foreach ($unit_kerja as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= esc($u['nama_instansi']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Status Anggota</label>
                <select name="status" class="form-control" style="width: 100%; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                    <option value="all">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Non-Aktif">Non-Aktif</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Golongan</label>
                <select name="golongan" class="form-control" style="width: 100%; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
                    <option value="all">Semua Golongan</option>
                    <option value="I">I</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="Non-PNS">Non-PNS</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Dari Tanggal (Periode)</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $awal ?>" required style="width: 100%; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
            </div>
            
            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $akhir ?>" required style="width: 100%; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn-primary" style="padding: 12px 25px;"><i class="fas fa-file-pdf"></i> Cetak PDF Laporan</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

