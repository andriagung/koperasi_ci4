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
    </style>
</head>
<body>
    <div class="header">
        <h2>KOPERASI RSUD</h2>
        <h3><?= $judul ?? '' ?></h3>
        <p>Periode: <?= date('d/m/Y', strtotime($awal)) ?> s/d <?= date('d/m/Y', strtotime($akhir)) ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>KODE AKUN</th>
                <th>NAMA AKUN</th>
                <th>SALDO NORMAL</th>
                <th>DEBIT (Rp)</th>
                <th>KREDIT (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): 
                $saldo = $row['saldo_akhir'];
                $isDebit = ($row['saldo_normal'] == 'Debit' && $saldo >= 0) || ($row['saldo_normal'] == 'Kredit' && $saldo < 0);
                $debitVal = $isDebit ? abs($saldo) : 0;
                $kreditVal = !$isDebit ? abs($saldo) : 0;
            ?>
            <tr>
                <td><?= $row['kode_akun'] ?? '' ?></td>
                <td><?= $row['nama_akun'] ?? '' ?></td>
                <td class="text-center"><?= $row['saldo_normal'] ?? '' ?></td>
                <td class="text-right"><?= number_format($debitVal ?? 0, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($kreditVal ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-right"><?= number_format($totalDebit ?? 0, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($totalKredit ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
