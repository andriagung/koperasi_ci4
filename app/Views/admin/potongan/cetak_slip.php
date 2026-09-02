<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Potongan Koperasi</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2, .header h3, .header p { margin: 0; }
        .content { margin-top: 20px; }
        .table-rincian { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table-rincian th, .table-rincian td { border: 1px solid #000; padding: 8px; text-align: left; }
        .table-rincian th { background-color: #f2f2f2; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 30%; }
        .signature p { margin-bottom: 60px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="padding:10px 20px; margin-bottom: 20px; cursor: pointer;">Cetak Slip</button>
    
    <div class="header">
        <h2>KOPERASI PEGAWAI REPUBLIK INDONESIA (KPRI)</h2>
        <h3>RSUD '45 KUNINGAN</h3>
        <p>Jl. Jend. Sudirman No. 68 Kuningan - Jawa Barat</p>
    </div>

    <div class="content">
        <h4 style="text-align: center; text-decoration: underline;">SLIP POTONGAN KOPERASI</h4>
        <p style="text-align: center; margin-top: -15px;">Bulan: <?= date('F Y') ?></p>

        <table style="width: 100%; margin-top: 20px;">
            <tr><td style="width: 150px;">Nama Pegawai</td><td>: <?= esc($anggota['nama_lengkap'] ?? 'NAMA PEGAWAI') ?></td></tr>
            <tr><td>NIP / No. Pegawai</td><td>: <?= esc($anggota['nomor_anggota'] ?? 'NIP') ?></td></tr>
            <tr><td>Unit Kerja</td><td>: RSUD '45 Kuningan</td></tr>
        </table>

        <table class="table-rincian">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Rincian Potongan</th>
                    <th style="text-align: right;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>Simpanan Wajib</td>
                    <td style="text-align: right;"><?= number_format($potongan['wajib'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>Angsuran Pinjaman Pokok</td>
                    <td style="text-align: right;"><?= number_format($potongan['angsuran_pokok'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>Jasa / Bunga Pinjaman</td>
                    <td style="text-align: right;"><?= number_format($potongan['angsuran_bunga'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td>Toko / Waserda</td>
                    <td style="text-align: right;"><?= number_format($potongan['waserda'] ?? 0, 0, ',', '.') ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align: right;">TOTAL POTONGAN:</th>
                    <th style="text-align: right;">
                        <?= number_format(
                            ($potongan['wajib'] ?? 0) + 
                            ($potongan['angsuran_pokok'] ?? 0) + 
                            ($potongan['angsuran_bunga'] ?? 0) + 
                            ($potongan['waserda'] ?? 0), 
                            0, ',', '.'
                        ) ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        <div class="signature">
            <p>Bendahara Gaji / Pemotong</p>
            <br>
            <strong>( ..................................... )</strong>
        </div>
        <div class="signature">
            <p>Kuningan, <?= date('d F Y') ?><br>Pengurus Koperasi</p>
            <br>
            <strong>( ..................................... )</strong>
        </div>
    </div>
</body>
</html>
