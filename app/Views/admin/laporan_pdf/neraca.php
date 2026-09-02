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
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        th { background-color: #f4f4f4; text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .section-title { background: #f4f4f4; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KOPKAR ASSYIFA RSUD 45</h2>
        <h2><?= strtoupper($judul) ?></h2>
        <p><?= $periode ?? '' ?></p>
    </div>

    <table>
        <tr>
            <th style="width: 50%; font-size: 14px; text-align: center; border-bottom: 2px solid #333;">AKTIVA (HARTA)</th>
            <th style="width: 50%; font-size: 14px; text-align: center; border-bottom: 2px solid #333;">PASIVA (HUTANG & MODAL)</th>
        </tr>
        <tr>
            <td>
                <!-- Aktiva -->
                <table style="border: none; margin:0;">
                    <?php foreach($harta as $akun => $nominal): ?>
                    <tr>
                        <td style="border:none; padding: 4px 0;"><?= esc($akun ?? '') ?></td>
                        <td style="border:none; padding: 4px 0;" class="text-right">Rp <?= number_format($nominal ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
            <td>
                <!-- Pasiva -->
                <table style="border: none; margin:0;">
                    <tr><td colspan="2" style="border:none; padding: 4px 0; font-weight: bold; color: #666;">Hutang / Kewajiban</td></tr>
                    <?php foreach($hutang as $akun => $nominal): ?>
                    <tr>
                        <td style="border:none; padding: 4px 0; padding-left: 10px;"><?= esc($akun ?? '') ?></td>
                        <td style="border:none; padding: 4px 0;" class="text-right">Rp <?= number_format($nominal ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <tr><td colspan="2" style="border:none; padding: 8px 0 4px 0; font-weight: bold; color: #666;">Ekuitas / Modal</td></tr>
                    <?php foreach($modal as $akun => $nominal): ?>
                    <tr>
                        <td style="border:none; padding: 4px 0; padding-left: 10px;"><?= esc($akun ?? '') ?></td>
                        <td style="border:none; padding: 4px 0;" class="text-right">Rp <?= number_format($nominal ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
        </tr>
        <tr>
            <td class="font-bold">
                <div style="display:flex; justify-content:space-between; border-top: 2px solid #333; padding-top: 5px;">
                    <span>TOTAL AKTIVA</span>
                    <span>Rp <?= number_format($total_harta ?? 0, 0, ',', '.') ?></span>
                </div>
            </td>
            <td class="font-bold">
                <div style="display:flex; justify-content:space-between; border-top: 2px solid #333; padding-top: 5px;">
                    <span>TOTAL PASIVA</span>
                    <span>Rp <?= number_format($total_pasiva ?? 0, 0, ',', '.') ?></span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
