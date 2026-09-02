<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Mutasi Kas: <?= esc($kas['nama'] ?? '') ?> (<?= esc($kas['kode'] ?? '') ?>)
</div>

<div class="panel-view active">
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-end;">
        
    <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Pilih Bulan</label>
                <input type="month" name="bulan" class="form-control" value="<?= esc($bulan ?? '') ?>" required>
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter</button>
            </div>
        </form>
        <div style="text-align: right;">
            <span style="font-size: 14px; color: #64748b;">Saldo Akhir Saat Ini:</span><br>
            <span style="font-size: 24px; font-weight: bold; color: #166534;">Rp <?= number_format($kas['saldo'] ?? 0, 2, ',', '.') ?></span>
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="display datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th style="text-align: right;">Masuk (Rp)</th>
                        <th style="text-align: right;">Keluar (Rp)</th>
                        <th style="text-align: right;">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalMasuk = 0; $totalKeluar = 0;
                    foreach($mutasi as $row): 
                        if ($row['jenis'] == 'Masuk') {
                            $masuk = $row['nominal'];
                            $keluar = 0;
                            $totalMasuk += $masuk;
                        } else {
                            $masuk = 0;
                            $keluar = $row['nominal'];
                            $totalKeluar += $keluar;
                        }
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td>
                            <?php if($row['jenis'] == 'Masuk'): ?>
                                <span class="badge bg-success">Masuk</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($row['keterangan'] ?? '') ?></td>
                        <td style="text-align: right; color: #166534;"><?= $masuk > 0 ? number_format($masuk ?? 0, 2, ',', '.') : '-' ?></td>
                        <td style="text-align: right; color: #dc2626;"><?= $keluar > 0 ? number_format($keluar ?? 0, 2, ',', '.') : '-' ?></td>
                        <td style="text-align: right; font-weight: bold;"><?= number_format($row['saldo_sesudah'] ?? 0, 2, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #f1f5f9;">
                        <td colspan="3" style="text-align: right;">TOTAL MUTASI BULAN INI:</td>
                        <td style="text-align: right; color: #166534;">Rp <?= number_format($totalMasuk ?? 0, 2, ',', '.') ?></td>
                        <td style="text-align: right; color: #dc2626;">Rp <?= number_format($totalKeluar ?? 0, 2, ',', '.') ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

