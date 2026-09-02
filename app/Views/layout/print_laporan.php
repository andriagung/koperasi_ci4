<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul ?? 'Laporan Koperasi' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 5px 0 0 0;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        .laporan-info {
            margin-bottom: 15px;
        }
        .laporan-info table {
            width: auto;
            border: none;
        }
        .laporan-info td {
            padding: 2px 5px;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 6px;
        }
        table.data th {
            background-color: #f0f0f0;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .footer-ttd {
            width: 100%;
            margin-top: 40px;
        }
        .footer-ttd table {
            width: 100%;
            border: none;
            text-align: center;
        }
        .footer-ttd td {
            width: 33%;
            padding-bottom: 70px;
        }
        .footer-info {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            font-size: 10px;
            text-align: right;
            font-style: italic;
        }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page {
                size: A4 portrait; /* Bisa diganti landscape dari css injeksi per view */
                margin: 15mm;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; cursor: pointer;">Cetak Laporan / Simpan PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer;">Tutup Halaman</button>
    </div>

    <div class="header">
        <h2>KOPERASI PEGAWAI RSUD</h2>
        <p>Jl. Kesehatan No. 123, Kota Sehat, 12345 | Telp: (021) 1234567 | Email: info@koperasirsud.com</p>
        <h3 style="margin-top: 15px; border-top: 1px solid #ccc; padding-top: 10px;"><?= $judul ?? 'LAPORAN' ?></h3>
    </div>

    <div class="laporan-info">
        <table>
            <?php if(isset($periode)): ?>
            <tr>
                <td>Periode</td>
                <td>: <?= $periode ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Dicetak Oleh</td>
                <td>: <?= session()->get('nama') ?? 'Administrator' ?></td>
            </tr>
            <tr>
                <td>Waktu Cetak</td>
                <td>: <?= date('d M Y, H:i') ?></td>
            </tr>
        </table>
    </div>

    <?= $this->renderSection('content') ?>

    <div class="footer-ttd">
        <table>
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Ketua Koperasi</strong>
                    <br><br><br><br><br>
                    (........................................)
                </td>
                <td>
                    <br>
                    <strong>Manajer / Bendahara</strong>
                    <br><br><br><br><br>
                    (........................................)
                </td>
                <td>
                    Dibuat Oleh,<br>
                    <strong>Staff Administrasi</strong>
                    <br><br><br><br><br>
                    (........................................)
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-info">
        Dicetak dari Sistem Informasi Koperasi RSUD v2.0
    </div>

</body>
</html>
