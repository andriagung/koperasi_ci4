<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Laporan RAT Tahun <?= $tahun ?? '' ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; padding: 40px; }
        .cover { text-align: center; margin-top: 100px; margin-bottom: 200px; }
        .cover h1 { font-size: 24pt; margin-bottom: 10px; }
        .cover h2 { font-size: 18pt; margin-bottom: 50px; }
        .cover h3 { font-size: 16pt; margin-top: 100px; }
        .page-break { page-break-after: always; }
        h1, h2, h3, h4 { text-align: center; }
        .text-justify { text-align: justify; }
        .table-data { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 30px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 8px; }
        .table-data th { background-color: #f2f2f2; text-align: center; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="padding:10px 20px; position: fixed; top: 20px; right: 20px; cursor: pointer;">Cetak Laporan</button>

    <!-- COVER PAGE -->
    <div class="cover">
        <h1>BUKU LAPORAN PERTANGGUNGJAWABAN (RAT)</h1>
        <h2>PENGURUS DAN PENGAWAS KPRI RSUD '45 KUNINGAN</h2>
        <h3>TAHUN BUKU <?= $tahun ?? '' ?></h3>
        <p style="margin-top: 20px;">Jl. Jend. Sudirman No. 68 Kuningan - Jawa Barat</p>
    </div>
    
    <div class="page-break"></div>

    <!-- DAFTAR ISI -->
    <h2>DAFTAR ISI</h2>
    <ul style="list-style-type: none; padding: 0;">
        <li>BAB I. PENDAHULUAN ........................................................................ 3</li>
        <li>BAB II. BIDANG ORGANISASI DAN MANAJEMEN ........................... 4</li>
        <li>BAB III. BIDANG USAHA DAN PERMODALAN ................................. 5</li>
        <li>BAB IV. LAPORAN KEUANGAN (NERACA & SHU) ........................... 6</li>
        <li>BAB V. PENUTUP .................................................................................... 7</li>
    </ul>

    <div class="page-break"></div>

    <!-- BAB I -->
    <h2>BAB I<br>PENDAHULUAN</h2>
    <p class="text-justify">
        Puji syukur kita panjatkan ke hadirat Allah SWT, karena atas rahmat dan hidayah-Nya, Pengurus Koperasi Pegawai Republik Indonesia (KPRI) RSUD '45 Kuningan dapat menyelesaikan penyusunan Laporan Pertanggungjawaban Tahun Buku <?= $tahun ?? '' ?>.
    </p>
    <p class="text-justify">
        Laporan ini disusun sebagai bentuk pertanggungjawaban tugas Pengurus kepada seluruh Anggota dalam Rapat Anggota Tahunan (RAT), sesuai dengan amanat Undang-Undang No. 25 Tahun 1992 tentang Perkoperasian dan Anggaran Dasar/Anggaran Rumah Tangga (AD/ART) KPRI RSUD '45 Kuningan.
    </p>

    <!-- BAB II -->
    <h2 style="margin-top: 50px;">BAB II<br>BIDANG ORGANISASI DAN MANAJEMEN</h2>
    <h4>A. Keanggotaan</h4>
    <p>Jumlah anggota KPRI RSUD '45 Kuningan pada tahun buku <?= $tahun ?? '' ?> mengalami perkembangan sebagai berikut:</p>
    <table class="table-data">
        <thead>
            <tr>
                <th>Uraian</th>
                <th>Jumlah (Orang)</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Anggota per 31 Desember <?= $tahun - 1 ?></td><td style="text-align: center;">...</td></tr>
            <tr><td>Anggota Masuk Tahun <?= $tahun ?? '' ?></td><td style="text-align: center;">...</td></tr>
            <tr><td>Anggota Keluar Tahun <?= $tahun ?? '' ?></td><td style="text-align: center;">...</td></tr>
            <tr><td><strong>Jumlah Anggota per 31 Desember <?= $tahun ?? '' ?></strong></td><td style="text-align: center;"><strong>...</strong></td></tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- BAB IV (Contoh Laporan Keuangan) -->
    <h2>BAB IV<br>LAPORAN KEUANGAN</h2>
    <h4>A. Ringkasan Neraca</h4>
    <table class="table-data">
        <thead>
            <tr>
                <th>Uraian</th>
                <th>Tahun <?= $tahun - 1 ?> (Rp)</th>
                <th>Tahun <?= $tahun ?? '' ?> (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>AKTIVA</strong></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Aktiva Lancar (Kas, Bank, Piutang)</td>
                <td style="text-align: right;">...</td>
                <td style="text-align: right;">...</td>
            </tr>
            <tr>
                <td>Aktiva Tetap (Inventaris)</td>
                <td style="text-align: right;">...</td>
                <td style="text-align: right;">...</td>
            </tr>
            <tr>
                <td><strong>PASIVA</strong></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Kewajiban (Hutang)</td>
                <td style="text-align: right;">...</td>
                <td style="text-align: right;">...</td>
            </tr>
            <tr>
                <td>Ekuitas (Simpanan Pokok, Wajib, SHU)</td>
                <td style="text-align: right;">...</td>
                <td style="text-align: right;">...</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>LEMBAR PENGESAHAN</h2>
    <p style="text-align: center; margin-top: 50px;">
        Kuningan, ................................ <?= $tahun + 1 ?><br>
        <strong>PENGURUS KPRI RSUD '45 KUNINGAN</strong>
    </p>
    <br><br><br><br>
    <table style="width: 100%; text-align: center; margin-top: 50px;">
        <tr>
            <td style="width: 50%;">
                <u><strong>( ......................................... )</strong></u><br>
                Ketua
            </td>
            <td style="width: 50%;">
                <u><strong>( ......................................... )</strong></u><br>
                Sekretaris
            </td>
        </tr>
    </table>
</body>
</html>
