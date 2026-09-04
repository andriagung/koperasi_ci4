<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Laporan Penjualan Harian & Margin</span>
    </div>
    
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <form action="" method="GET">
        <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Pilih Bulan</label>
                <input type="month" name="bulan" class="form-control" value="<?= esc($bulan ?? '') ?>" required>
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter Laporan</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="display datatable" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Tanggal</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: center;">Jml Transaksi</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: right;">Omset (Rp)</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: right;">Profit Margin (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalTx = 0; $totalOmset = 0; $totalProfit = 0;
                    if(empty($data)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 20px;">Tidak ada data penjualan pada periode ini.</td></tr>
                    <?php else:
                        foreach ($data as $row): 
                            $totalTx += $row['total_transaksi'];
                            $totalOmset += $row['omset'];
                            $totalProfit += $row['profit'];
                    ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold;"><?= date('d M Y', strtotime($row['tgl'])) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;"><?= number_format($row['total_transaksi'] ?? 0, 0, ',', '.') ?> Nota</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #166534; font-weight: bold;">
                            <?= number_format($row['omset'] ?? 0, 0, ',', '.') ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #1d4ed8; font-weight: bold;">
                            <?= number_format($row['profit'] ?? 0, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #e2e8f0;">
                        <td style="padding: 12px; text-align: right;">TOTAL AKUMULASI</td>
                        <td style="padding: 12px; text-align: center;"><?= number_format($totalTx ?? 0, 0, ',', '.') ?> Nota</td>
                        <td style="padding: 12px; text-align: right; color: #166534;">Rp <?= number_format($totalOmset ?? 0, 0, ',', '.') ?></td>
                        <td style="padding: 12px; text-align: right; color: #1d4ed8;">Rp <?= number_format($totalProfit ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

