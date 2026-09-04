<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span><?= $judul ?? '' ?></span>
    </div>
    
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <form action="" method="POST">
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
            <h3>Daftar Saldo Akun</h3>
            <div>
                
    <form action="" method="POST">
        <?= csrf_field() ?>
                    <input type="hidden" name="jenis_laporan" value="neracasaldo">
                    <input type="hidden" name="tgl_awal" value="<?= $awal ?? '' ?>">
                    <input type="hidden" name="tgl_akhir" value="<?= $akhir ?? '' ?>">
                    <input type="hidden" name="format" value="pdf">
                    <button type="submit" class="btn-primary" style="background-color: #dc2626; margin-right: 5px;"><i class="fas fa-file-pdf"></i> Export PDF</button>
                </form>
                
    <form action="" method="POST">
        <?= csrf_field() ?>
                    <input type="hidden" name="jenis_laporan" value="neracasaldo">
                    <input type="hidden" name="tgl_awal" value="<?= $awal ?? '' ?>">
                    <input type="hidden" name="tgl_akhir" value="<?= $akhir ?? '' ?>">
                    <input type="hidden" name="format" value="csv">
                    <button type="submit" class="btn-primary" style="background-color: #0ea5e9;"><i class="fas fa-file-csv"></i> Export CSV</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="display" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">KODE AKUN</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">NAMA AKUN</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">SALDO NORMAL</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: right;">DEBIT (Rp)</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: right;">KREDIT (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totDebit = 0;
                    $totKredit = 0;
                    foreach ($data as $row): 
                        $saldo = $row['saldo_akhir'];
                        $isDebit = ($row['saldo_normal'] == 'Debit' && $saldo >= 0) || ($row['saldo_normal'] == 'Kredit' && $saldo < 0);
                        $debitVal = $isDebit ? abs($saldo) : 0;
                        $kreditVal = !$isDebit ? abs($saldo) : 0;
                        $totDebit += $debitVal;
                        $totKredit += $kreditVal;
                    ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $row['kode_akun'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $row['nama_akun'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $row['saldo_normal'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;"><?= number_format($debitVal ?? 0, 0, ',', '.') ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;"><?= number_format($kreditVal ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #e2e8f0;">
                        <td colspan="3" style="padding: 12px; text-align: right;">TOTAL</td>
                        <td style="padding: 12px; text-align: right; color: #15803d;"><?= number_format($totDebit ?? 0, 0, ',', '.') ?></td>
                        <td style="padding: 12px; text-align: right; color: #15803d;"><?= number_format($totKredit ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

