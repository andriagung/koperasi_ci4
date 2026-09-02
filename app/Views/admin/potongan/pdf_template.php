<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajuan Pemotongan Gaji - <?= $periode ?? '' ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3 { margin: 0; padding: 2px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #333; padding: 5px; text-align: left; }
        .table th { background-color: #f1f5f9; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .info-rekening { margin-top: 30px; border: 1px dashed #333; padding: 15px; }
        .ttd { margin-top: 40px; float: right; width: 250px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>KOPERASI KARYAWAN ASSYIFA RSUD 45</h2>
        <p>Jl. Sudirman No. 45, Kuningan, Jawa Barat</p>
        <h3>DAFTAR AJUAN PEMOTONGAN GAJI ANGGOTA</h3>
        <p><strong>Periode: <?= date('F Y', strtotime($periode . '-01')) ?></strong></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>NIK</th>
                <th>Nama Anggota</th>
                <th>Instansi / Bendahara</th>
                <th class="text-right">Simpanan Wajib</th>
                <th class="text-right">Angsuran Pinjaman</th>
                <th class="text-right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $totalSimpanan = 0;
            $totalAngsuran = 0;
            $totalKeseluruhan = 0;
            foreach ($tagihan as $row): 
                $totalSimpanan += $row['nominal_simpanan_wajib'];
                $totalAngsuran += $row['nominal_angsuran'];
                $totalKeseluruhan += $row['total_tagihan'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $row['nik'] ?? '' ?></td>
                <td><?= $row['nama'] ?? '' ?></td>
                <td><?= $row['nama_instansi'] ?? '-' ?></td>
                <td class="text-right">Rp <?= number_format($row['nominal_simpanan_wajib'] ?? 0, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($row['nominal_angsuran'] ?? 0, 0, ',', '.') ?></td>
                <td class="text-right"><strong>Rp <?= number_format($row['total_tagihan'] ?? 0, 0, ',', '.') ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-right">Rp <?= number_format($totalSimpanan ?? 0, 0, ',', '.') ?></th>
                <th class="text-right">Rp <?= number_format($totalAngsuran ?? 0, 0, ',', '.') ?></th>
                <th class="text-right">Rp <?= number_format($totalKeseluruhan ?? 0, 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="info-rekening">
        <h4>Informasi Pembayaran / Transfer:</h4>
        <p>Mohon agar total potongan gaji sebesar <strong>Rp <?= number_format($totalKeseluruhan ?? 0, 0, ',', '.') ?></strong> dapat ditransfer ke rekening koperasi berikut:</p>
        <ul>
            <li><strong>Bank:</strong> Bank BJB (Bank Jabar Banten)</li>
            <li><strong>Atas Nama:</strong> Koperasi Karyawan Assyifa RSUD 45</li>
            <li><strong>No. Rekening:</strong> 0123-4567-8910</li>
        </ul>
        <p>Setelah transfer, mohon informasikan atau serahkan bukti transfer (dan hasil file CSV import) kepada admin Koperasi.</p>
    </div>

    <div class="ttd">
        <p>Kuningan, <?= date('d F Y') ?></p>
        <p>Pengurus Koperasi,</p>
        <br><br><br>
        <p><strong>_____________________</strong></p>
        <p>Ketua Koperasi Assyifa</p>
    </div>

</body>
</html>
