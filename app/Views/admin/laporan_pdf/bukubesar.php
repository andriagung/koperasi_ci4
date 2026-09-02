<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $judul ?? '' ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        .info-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 15px; background: #f9fafb; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; text-align: left; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KOPKAR ASSYIFA RSUD 45</h2>
        <h2><?= strtoupper($judul) ?></h2>
        <p>Periode: <?= $periode ?? '' ?></p>
    </div>

    <div class="info-box">
        <table style="border:none; margin:0;">
            <tr>
                <td style="border:none; padding:2px; width: 100px; font-weight:bold;">Kode Akun</td>
                <td style="border:none; padding:2px;">: <?= esc($coa['kode_akun'] ?? '') ?></td>
                <td style="border:none; padding:2px; width: 100px; font-weight:bold;">Tipe Akun</td>
                <td style="border:none; padding:2px;">: <?= esc($coa['tipe_akun'] ?? '') ?></td>
            </tr>
            <tr>
                <td style="border:none; padding:2px; font-weight:bold;">Nama Akun</td>
                <td style="border:none; padding:2px;">: <?= esc($coa['nama_akun'] ?? '') ?></td>
                <td style="border:none; padding:2px; font-weight:bold;">Saldo Normal</td>
                <td style="border:none; padding:2px;">: <?= esc($coa['saldo_normal'] ?? '') ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 15%;">TANGGAL</th>
                <th style="width: 40%;">KETERANGAN MUTASI</th>
                <th class="text-right" style="width: 15%;">DEBIT</th>
                <th class="text-right" style="width: 15%;">KREDIT</th>
                <th class="text-right" style="width: 15%;">SALDO</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background:#f8fafc;">
                <td class="text-center font-bold"></td>
                <td class="font-bold">Saldo Awal (Sebelum Periode)</td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right font-bold">Rp <?= number_format($saldo_awal ?? 0, 0, ',', '.') ?></td>
            </tr>
            
            <?php 
            $saldo = $saldo_awal;
            if(!empty($mutasi)): foreach($mutasi as $m): 
                $debit = ($m['posisi'] == 'Debit') ? $m['nominal'] : 0;
                $kredit = ($m['posisi'] == 'Kredit') ? $m['nominal'] : 0;
                
                if ($m['posisi'] == $coa['saldo_normal']) {
                    $saldo += $m['nominal'];
                } else {
                    $saldo -= $m['nominal'];
                }
            ?>
            <tr>
                <td class="text-center"><?= date('d/m/Y', strtotime($m['tanggal'])) ?></td>
                <td><?= esc($m['keterangan'] ?? '') ?></td>
                <td class="text-right"><?= $debit ? number_format($debit ?? 0, 0, ',', '.') : '-' ?></td>
                <td class="text-right"><?= $kredit ? number_format($kredit ?? 0, 0, ',', '.') : '-' ?></td>
                <td class="text-right">Rp <?= number_format($saldo ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted">Tidak ada mutasi pada periode ini.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="font-bold" style="background:#f1f5f9;">
                <td colspan="4" class="text-right">SALDO AKHIR PERIODE:</td>
                <td class="text-right">Rp <?= number_format($saldo ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
