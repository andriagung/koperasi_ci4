<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Laporan 10 Produk Terlaris</span>
    </div>
    
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
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
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Peringkat</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Nama Produk</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: center;">Total Terjual</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: right;">Omset (Rp)</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: right;">Profit Margin (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(empty($data)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data penjualan pada periode ini.</td></tr>
                    <?php else:
                        $rank = 1;
                        foreach ($data as $row): 
                    ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">#<?= $rank++ ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= esc($row['nama_produk'] ?? '') ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center; font-weight: bold;"><?= number_format($row['total_terjual'] ?? 0, 0, ',', '.') ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #166534;">
                            <?= number_format($row['omset'] ?? 0, 0, ',', '.') ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #1d4ed8;">
                            <?= number_format($row['profit'] ?? 0, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

