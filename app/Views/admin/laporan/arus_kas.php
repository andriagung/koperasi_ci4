<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span><?= $judul ?? '' ?></span>
    </div>
    
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <form action="" method="GET">
        <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Tanggal Awal</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $awal ?? '' ?>" required>
            </div>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $akhir ?? '' ?>" required>
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter Laporan</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-header" style="justify-content: space-between;">
            <h3>Detail Arus Kas</h3>
            <div>
                
    <form action="<?= base_url('admin/laporan/generate') ?>" method="POST">
        <?= csrf_field() ?>
                    <input type="hidden" name="jenis_laporan" value="aruskas">
                    <input type="hidden" name="tgl_awal" value="<?= $awal ?? '' ?>">
                    <input type="hidden" name="tgl_akhir" value="<?= $akhir ?? '' ?>">
                    <input type="hidden" name="format" value="pdf">
                    <button type="submit" class="btn-primary" style="background-color: #dc2626; margin-right: 5px;"><i class="fas fa-file-pdf"></i> Export PDF</button>
                </form>
                
    <form action="<?= base_url('admin/laporan/generate') ?>" method="POST">
        <?= csrf_field() ?>
                    <input type="hidden" name="jenis_laporan" value="aruskas">
                    <input type="hidden" name="tgl_awal" value="<?= $awal ?? '' ?>">
                    <input type="hidden" name="tgl_akhir" value="<?= $akhir ?? '' ?>">
                    <input type="hidden" name="format" value="csv">
                    <button type="submit" class="btn-primary" style="background-color: #0ea5e9;"><i class="fas fa-file-csv"></i> Export CSV</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <!-- Arus Kas Masuk -->
            <h4 style="color: #15803d; margin-top: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">ARUS KAS MASUK (PENERIMAAN)</h4>
            <table class="display" style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1;">Tanggal</th>
                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1;">Keterangan</th>
                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1; text-align: right;">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kasMasuk)): ?>
                        <tr><td colspan="3" style="text-align: center; padding: 10px;">Tidak ada penerimaan kas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($kasMasuk as $row): ?>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px dashed #e2e8f0;"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td style="padding: 10px; border-bottom: 1px dashed #e2e8f0;"><?= $row['keterangan'] ?? '' ?></td>
                            <td style="padding: 10px; border-bottom: 1px dashed #e2e8f0; text-align: right;"><?= number_format($row['nominal'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #ecfdf5;">
                        <td colspan="2" style="padding: 10px; text-align: right;">Total Kas Masuk</td>
                        <td style="padding: 10px; text-align: right; color: #15803d;"><?= number_format($totalMasuk ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Arus Kas Keluar -->
            <h4 style="color: #b91c1c; margin-top: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">ARUS KAS KELUAR (PENGELUARAN)</h4>
            <table class="display" style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1;">Tanggal</th>
                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1;">Keterangan</th>
                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1; text-align: right;">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kasKeluar)): ?>
                        <tr><td colspan="3" style="text-align: center; padding: 10px;">Tidak ada pengeluaran kas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($kasKeluar as $row): ?>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px dashed #e2e8f0;"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td style="padding: 10px; border-bottom: 1px dashed #e2e8f0;"><?= $row['keterangan'] ?? '' ?></td>
                            <td style="padding: 10px; border-bottom: 1px dashed #e2e8f0; text-align: right;"><?= number_format($row['nominal'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #fef2f2;">
                        <td colspan="2" style="padding: 10px; text-align: right;">Total Kas Keluar</td>
                        <td style="padding: 10px; text-align: right; color: #b91c1c;"><?= number_format($totalKeluar ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Net Cash Flow -->
            <div style="margin-top: 30px; padding: 15px; border-radius: 8px; background-color: <?= $netCashFlow >= 0 ? '#dcfce7' : '#fee2e2' ?>; border-left: 5px solid <?= $netCashFlow >= 0 ? '#22c55e' : '#ef4444' ?>;">
                <h3 style="margin: 0; display: flex; justify-content: space-between; color: <?= $netCashFlow >= 0 ? '#166534' : '#991b1b' ?>;">
                    <span>NET CASH FLOW (ARUS KAS BERSIH)</span>
                    <span>Rp <?= number_format($netCashFlow ?? 0, 0, ',', '.') ?></span>
                </h3>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

