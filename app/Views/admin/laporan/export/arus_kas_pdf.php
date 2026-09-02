<!DOCTYPE html>
<html>
<head>
    <title><?= $judul ?? '' ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        h4 { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KOPERASI RSUD</h2>
        <h3><?= $judul ?? '' ?></h3>
        <p>Periode: <?= date('d/m/Y', strtotime($awal)) ?> s/d <?= date('d/m/Y', strtotime($akhir)) ?></p>
    </div>

    <h4>ARUS KAS MASUK (PENERIMAAN)</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($kasMasuk)): ?>
                <tr><td colspan="3" class="text-center">Tidak ada penerimaan kas.</td></tr>
            <?php else: ?>
                <?php foreach ($kasMasuk as $row): ?>
                <tr>
                    <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= $row['keterangan'] ?? '' ?></td>
                    <td class="text-right"><?= number_format($row['nominal'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="2" class="text-right">Total Kas Masuk</td>
                <td class="text-right"><?= number_format($totalMasuk ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <h4>ARUS KAS KELUAR (PENGELUARAN)</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($kasKeluar)): ?>
                <tr><td colspan="3" class="text-center">Tidak ada pengeluaran kas.</td></tr>
            <?php else: ?>
                <?php foreach ($kasKeluar as $row): ?>
                <tr>
                    <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= $row['keterangan'] ?? '' ?></td>
                    <td class="text-right"><?= number_format($row['nominal'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="2" class="text-right">Total Kas Keluar</td>
                <td class="text-right"><?= number_format($totalKeluar ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <h3 class="text-right" style="margin-top: 30px;">
        NET CASH FLOW (ARUS KAS BERSIH) : Rp <?= number_format($netCashFlow ?? 0, 0, ',', '.') ?>
    </h3>
</body>
</html>
