<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="page-title">
    Kartu Anggota Digital
</div>

<div class="panel-view active" style="padding: 20px;">
    
    <form action="" method="GET">
    <?= csrf_field() ?>
        <select name="id" class="form-control" style="max-width: 300px;" required>
            <option value="">-- Pilih Anggota --</option>
            <?php foreach($list_anggota as $a): ?>
                <option value="<?= $a['id'] ?? '' ?>" <?= isset($anggota) && $anggota['id'] == $a['id'] ? 'selected' : '' ?>>
                    <?= esc($a['nama_lengkap'] ?? '') ?> (<?= esc($a['nip'] ?? '') ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary">Tampilkan Kartu</button>
    </form>

    <?php if(isset($anggota)): ?>
        <div style="border: 1px solid #ccc; border-radius: 10px; width: 400px; padding: 20px; background: url('https://www.transparenttextures.com/patterns/cubes.png') #f8f9fa; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; position: relative;">
            <div style="text-align: center; border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 15px;">
                <h3 style="margin: 0; color: var(--primary); text-transform: uppercase;">Koperasi WARSERDA</h3>
                <small style="color: #666;">Kartu Tanda Anggota</small>
            </div>
            
            <div style="display: flex; gap: 15px; align-items: flex-start;">
                <!-- Photo -->
                <div style="width: 80px; height: 100px; background: #ddd; border: 2px solid #ccc; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if(!empty($anggota['foto'])): ?>
                        <img src="/uploads/foto/<?= esc($anggota['foto'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-user fa-3x" style="color: #aaa;"></i>
                    <?php endif; ?>
                </div>
                
                <div style="flex: 1;">
                    <table style="width: 100%; font-size: 14px; line-height: 1.5;">
                        <tr>
                            <td style="width: 80px; color: #555;">No. Anggota</td>
                            <td>: <strong><?= esc($anggota['nip'] ?? '') ?></strong></td>
                        </tr>
                        <tr>
                            <td style="color: #555;">Nama</td>
                            <td>: <strong><?= esc($anggota['nama_lengkap'] ?? '') ?></strong></td>
                        </tr>
                        <tr>
                            <td style="color: #555;">Telepon</td>
                            <td>: <?= esc($anggota['no_hp'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td style="color: #555;">Bergabung</td>
                            <td>: <?= date('d M Y', strtotime($anggota['tanggal_masuk'] ?? $anggota['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Placeholder QR Code -->
            <div style="position: absolute; bottom: 15px; right: 15px; width: 60px; height: 60px; background: #fff; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-qrcode fa-2x"></i>
            </div>
        </div>

        <button class="btn-primary" style="margin-top: 20px;" onclick="window.print()"><i class="fas fa-print"></i> Cetak Kartu</button>
    <?php else: ?>
        <p style="color: #666;">Pilih anggota untuk menampilkan kartu digital.</p>
    <?php endif; ?>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .panel-view > div:nth-child(2), .panel-view > div:nth-child(2) * { visibility: visible; }
    .panel-view > div:nth-child(2) { position: absolute; left: 0; top: 0; }
}
</style>
<?= $this->endSection() ?>

