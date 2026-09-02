<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($judul ?? '') ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .kop h2 { margin: 0; font-size: 20px; color: #0f172a; }
        .kop p { margin: 5px 0 0 0; font-size: 12px; color: #64748b; }
        .report-title { text-align: center; margin-bottom: 20px; }
        .report-title h3 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .report-title p { margin: 5px 0 0 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        table th { background-color: #f8fafc; font-weight: bold; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-title { font-weight: bold; background-color: #e2e8f0; }
        .total-row { font-weight: bold; background-color: #f1f5f9; }
        .net-laba { font-weight: bold; font-size: 14px; background-color: #dcfce7; color: #166534; }
        .footer { text-align: right; margin-top: 40px; font-size: 12px; }
        .footer .ttd { margin-top: 60px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>KOPERASI KARYAWAN ASSYIFA</h2>
        <p>Jl. Kesehatan No. 123, Kota Medika | Telp: (021) 555-1234 | Email: info@kopkarassyifa.com</p>
    </div>
    <div class="report-title">
        <h3><?= esc($judul ?? '') ?></h3>
        <p>Periode: <?= esc($periode ?? '') ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th width="70%">Keterangan Akun</th>
                <th width="30%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" class="section-title">PENDAPATAN</td>
            </tr>
            <?php foreach($pendapatan as $k => $v): ?>
            <tr>
                <td style="padding-left: 20px;"><?= esc($k ?? '') ?></td>
                <td class="text-right"><?= number_format($v ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($pendapatan) == 0): ?>
            <tr><td colspan="2" class="text-center" style="font-style: italic;">(Tidak ada transaksi pendapatan)</td></tr>
            <?php endif; ?>
            <tr class="total-row">
                <td class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right"><?= number_format($total_pendapatan ?? 0, 0, ',', '.') ?></td>
            </tr>

            <tr>
                <td colspan="2" class="section-title">BEBAN & BIAYA</td>
            </tr>
            <?php foreach($beban as $k => $v): ?>
            <tr>
                <td style="padding-left: 20px;"><?= esc($k ?? '') ?></td>
                <td class="text-right"><?= number_format($v ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($beban) == 0): ?>
            <tr><td colspan="2" class="text-center" style="font-style: italic;">(Tidak ada transaksi beban)</td></tr>
            <?php endif; ?>
            <tr class="total-row">
                <td class="text-right">TOTAL BEBAN</td>
                <td class="text-right"><?= number_format($total_beban ?? 0, 0, ',', '.') ?></td>
            </tr>

            <tr class="net-laba">
                <td class="text-right">LABA / (RUGI) BERSIH</td>
                <td class="text-right"><?= number_format($laba_bersih ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
    <div class="footer">
        <p>Dicetak pada: <?= date('d-m-Y H:i:s') ?></p>
        <p>Pengurus Koperasi,</p>
        <div class="ttd">( Admin Koperasi )</div>
    </div>
</body>
</html>
