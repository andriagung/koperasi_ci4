<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi - Kopkar Assyifa</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 20px;
            color: #000;
            background: #fff;
            font-size: 14px;
        }
        .struk-container {
            width: 300px;
            margin: 0 auto;
            border: 1px dashed #000;
            padding: 15px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px dashed #000; margin-bottom: 10px; padding-bottom: 10px; }
        .border-top { border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; vertical-align: top; }
        @media print {
            body { padding: 0; }
            .struk-container { border: none; width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 1000);">
    <div class="struk-container">
        <div class="text-center border-bottom">
            <h2 style="margin: 0; font-size: 1.2rem;">KOPKAR ASSYIFA</h2>
            <p style="margin: 5px 0 0; font-size: 0.9rem;">RSUD Cicalengka</p>
            <p style="margin: 0; font-size: 0.8rem;">Struk Transaksi Waserda</p>
        </div>
        
        <table>
            <tr>
                <td>Tanggal</td>
                <td>: <?= date('d/m/Y H:i', strtotime($transaksi['created_at'] ?? date('Y-m-d H:i'))) ?></td>
            </tr>
            <tr>
                <td>Metode</td>
                <td>: <?= htmlspecialchars($transaksi['jenis_transaksi'] === 'Keluar' ? 'KASBON' : 'TUNAI') ?></td>
            </tr>
            <?php if (!empty($anggota)): ?>
            <tr>
                <td>Anggota</td>
                <td>: <?= htmlspecialchars($anggota['nama_lengkap']) ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <div class="border-top">
            <p class="font-bold">Keterangan:</p>
            <p style="margin-top: 0;"><?= htmlspecialchars($transaksi['keterangan'] ?? 'Pembelian POS') ?></p>
        </div>

        <div class="border-top border-bottom text-right font-bold" style="font-size: 1.1rem;">
            TOTAL: Rp <?= number_format($transaksi['nominal'] ?? 0, 0, ',', '.') ?>
        </div>

        <div class="text-center" style="font-size: 0.85rem;">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>-- Simpan struk ini sebagai bukti --</p>
        </div>
    </div>
</body>
</html>
