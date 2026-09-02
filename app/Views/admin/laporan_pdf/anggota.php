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
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 10px; }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-warning { background-color: #fef08a; color: #854d0e; }
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
                <th width="5%">No</th>
                <th width="15%">NIP</th>
                <th width="35%">Nama Lengkap</th>
                <th width="25%">Tanggal Bergabung</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($anggota as $a): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= esc($a['nip'] ?? '') ?></td>
                <td><?= esc($a['nama_lengkap'] ?? '') ?></td>
                <td class="text-center"><?= date('d-m-Y', strtotime($a['created_at'])) ?></td>
                <td class="text-center">
                    <?php if($a['status'] == 'Aktif'): ?>
                        <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-warning"><?= esc($a['status'] ?? '') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($anggota) == 0): ?>
            <tr>
                <td colspan="5" class="text-center">Tidak ada data anggota baru pada periode ini.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d-m-Y H:i:s') ?></p>
        <p>Pengurus Koperasi,</p>
        <div class="ttd">( Admin Koperasi )</div>
    </div>

</body>
</html>
