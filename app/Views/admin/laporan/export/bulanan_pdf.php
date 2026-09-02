<!DOCTYPE html>
<html>
<head>
    <title><?= $judul ?? '' ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; padding: 0; font-size: 18px; }
        .header p { margin: 5px 0 0 0; }
        .section-title { font-weight: bold; background-color: #f2f2f2; padding: 5px; margin-top: 15px; border-left: 4px solid #0369a1; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .box-container { width: 100%; margin-top: 10px; }
        .box { width: 48%; display: inline-block; border: 1px solid #ddd; padding: 10px; margin-right: 1%; vertical-align: top; box-sizing: border-box; }
        .box h3 { margin-top: 0; font-size: 14px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .value { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI RSUD</h1>
        <p><b>LAPORAN EKSEKUTIF BULANAN</b></p>
        <p>Periode: <?= date('d/m/Y', strtotime($awal)) ?> s/d <?= date('d/m/Y', strtotime($akhir)) ?></p>
    </div>

    <div class="section-title">A. RINGKASAN KINERJA</div>
    <div class="box-container">
        <div class="box">
            <h3>Laba Bersih (SHU Berjalan)</h3>
            <div class="value text-right" style="color: <?= $summary['laba_bersih'] >= 0 ? 'green' : 'red' ?>;">
                Rp <?= number_format($summary['laba_bersih'] ?? 0, 0, ',', '.') ?>
            </div>
        </div>
        <div class="box">
            <h3>Saldo Kas Koperasi</h3>
            <div class="value text-right">
                Rp <?= number_format($summary['saldo_kas'] ?? 0, 0, ',', '.') ?>
            </div>
        </div>
    </div>
    <div class="box-container">
        <div class="box">
            <h3>Total Anggota Aktif</h3>
            <div class="value text-right">
                <?= number_format($summary['total_anggota'] ?? 0, 0, ',', '.') ?> Orang
                <div style="font-size: 10px; font-weight: normal; color: green;">(+<?= $summary['anggota_baru'] ?? '' ?> Anggota Baru)</div>
            </div>
        </div>
        <div class="box">
            <h3>Penjualan Waserda</h3>
            <div class="value text-right">
                Rp <?= number_format($summary['penjualan_waserda'] ?? 0, 0, ',', '.') ?>
            </div>
        </div>
    </div>
    <div class="box-container">
        <div class="box">
            <h3>Total Simpanan Anggota</h3>
            <div class="value text-right">
                Rp <?= number_format($summary['total_simpanan'] ?? 0, 0, ',', '.') ?>
            </div>
        </div>
        <div class="box">
            <h3>Piutang Pinjaman (Berjalan)</h3>
            <div class="value text-right">
                Rp <?= number_format($summary['piutang_pinjaman'] ?? 0, 0, ',', '.') ?>
            </div>
        </div>
    </div>

    <div class="section-title" style="margin-top: 30px;">B. RINGKASAN LABA RUGI (HASIL USAHA)</div>
    <table class="table">
        <tbody>
            <tr>
                <td>Total Pendapatan (Bunga, Provisi, Administrasi, dll)</td>
                <td class="text-right">Rp <?= number_format($labaRugi['totalPendapatan'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Laba Kotor Waserda</td>
                <td class="text-right">Rp <?= number_format($labaRugi['labaKotorWaserda'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td>TOTAL PENDAPATAN & LABA KOTOR</td>
                <td class="text-right">Rp <?= number_format($labaRugi['totalPendapatan'] + $labaRugi['labaKotorWaserda'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Total Beban Operasional (Beban Gaji, Listrik, dll)</td>
                <td class="text-right">Rp <?= number_format($labaRugi['totalBeban'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr style="font-weight: bold; border-top: 2px solid #000;">
                <td>SISA HASIL USAHA (SHU) BERSIH</td>
                <td class="text-right" style="color: <?= $labaRugi['shuBersih'] >= 0 ? 'green' : 'red' ?>;">
                    Rp <?= number_format($labaRugi['shuBersih'] ?? 0, 0, ',', '.') ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 50px; width: 100%;">
        <div style="float: right; width: 250px; text-align: center;">
            <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
            <p>Mengetahui,</p>
            <br><br><br>
            <p><b>_________________________</b></p>
            <p>Ketua Koperasi</p>
        </div>
    </div>

</body>
</html>
