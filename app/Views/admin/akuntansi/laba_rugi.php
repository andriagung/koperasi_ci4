<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title">Laporan Laba Rugi</h4>
            </div>
            <div class="card-body">
    
    <form action="" method="POST">
        <?= csrf_field() ?>
        <select name="bulan" class="form-control" style="max-width: 150px;">
            <?php for($i=1; $i<=12; $i++): $m = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                <option value="<?= $m ?? '' ?>" <?= $bulan == $m ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,10)) ?></option>
            <?php endfor; ?>
        </select>
        <select name="tahun" class="form-control" style="max-width: 150px;">
            <?php for($i=date('Y'); $i>=date('Y')-5; $i--): ?>
                <option value="<?= $i ?? '' ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?? '' ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn-primary">Tampilkan</button>
        <button type="button" class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
    </form>

    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
        <h3 style="text-align:center; color: var(--primary); margin-bottom: 5px;">KOPERASI WARSERDA</h3>
        <h4 style="text-align:center; color: #555; margin-bottom: 20px;">LAPORAN LABA RUGI <br><small>Periode: <?= $bulan ?? '' ?> / <?= $tahun ?? '' ?></small></h4>

        <!-- PENDAPATAN -->
        <h5 style="border-bottom: 1px solid #ccc; padding-bottom: 5px; color: #333;">PENDAPATAN</h5>
        <table style="width: 100%; margin-bottom: 20px; font-size: 14px;">
            <?php $totalPendapatan = 0; foreach($pendapatan as $p): $totalPendapatan += $p['total']; ?>
            <tr>
                <td style="padding: 5px 0 5px 20px;"><?= esc($p['kode_akun'] ?? '') ?> - <?= esc($p['nama_akun'] ?? '') ?></td>
                <td style="text-align:right;">Rp <?= number_format($p['total'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold; background: #f8f9fa;">
                <td style="padding: 10px 0;">Total Pendapatan</td>
                <td style="text-align:right;">Rp <?= number_format($totalPendapatan ?? 0, 0, ',', '.') ?></td>
            </tr>
        </table>

        <!-- BEBAN -->
        <h5 style="border-bottom: 1px solid #ccc; padding-bottom: 5px; color: #333;">BEBAN & PENGELUARAN</h5>
        <table style="width: 100%; margin-bottom: 20px; font-size: 14px;">
            <?php $totalBeban = 0; foreach($beban as $b): $totalBeban += $b['total']; ?>
            <tr>
                <td style="padding: 5px 0 5px 20px;"><?= esc($b['kode_akun'] ?? '') ?> - <?= esc($b['nama_akun'] ?? '') ?></td>
                <td style="text-align:right;">Rp <?= number_format($b['total'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold; background: #f8f9fa;">
                <td style="padding: 10px 0;">Total Beban</td>
                <td style="text-align:right;">Rp <?= number_format($totalBeban ?? 0, 0, ',', '.') ?></td>
            </tr>
        </table>

        <!-- LABA BERSIH -->
        <?php $laba = $totalPendapatan - $totalBeban; ?>
        <table style="width: 100%; font-size: 16px; margin-top: 20px; border-top: 2px solid var(--primary);">
            <tr style="font-weight: bold; color: <?= $laba >= 0 ? 'green' : 'red' ?>;">
                <td style="padding: 15px 0;">LABA / (RUGI) BERSIH</td>
                <td style="text-align:right;">Rp <?= number_format($laba ?? 0, 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .card form, .card button { display: none !important; }
    .card > div, .card > div * { visibility: visible; }
    .card > div { position: absolute; left: 0; top: 0; width: 100%; border: none; }
}
</style>
<?= $this->endSection() ?>

