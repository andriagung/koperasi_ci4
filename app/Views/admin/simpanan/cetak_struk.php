<!DOCTYPE html>
<html>
<head>
    <title>Cetak Transaksi - <?= esc($trx['nomor_transaksi'] ?? '') ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; margin: 0; padding: 20px; color: #000; }
        .struk-container { width: 350px; margin: 0 auto; border: 1px dashed #000; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 18px; }
        .header p { margin: 0; font-size: 12px; }
        .content { margin-bottom: 15px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .footer { text-align: center; border-top: 1px dashed #000; padding-top: 10px; margin-top: 15px; font-size: 12px; }
        @media print {
            body { padding: 0; }
            .struk-container { border: none; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="struk-container">
        <div class="header">
            <h2>KOPERASI WARSERDA</h2>
            <p>Bukti <?= $trx['jenis_transaksi'] == 'setoran' ? 'Setoran' : 'Penarikan' ?> Simpanan</p>
            <p><?= esc($trx['nomor_transaksi'] ?? '') ?></p>
        </div>
        
        <div class="content">
            <div class="row"><span>Tanggal</span> <span><?= date('d/m/Y', strtotime($trx['tanggal'])) ?></span></div>
            <div class="row"><span>Nomor Anggota</span> <span><?= esc($trx['nomor_anggota'] ?? '') ?></span></div>
            <div class="row"><span>Nama</span> <span><?= esc($trx['nama_lengkap'] ?? '') ?></span></div>
            <div class="row"><span>Jenis Simpanan</span> <span><?= esc($trx['nama_simpanan'] ?? '') ?></span></div>
            <div class="row" style="margin-top:10px; font-weight:bold; font-size:16px;">
                <span>NOMINAL</span> 
                <span>Rp <?= number_format($trx['nominal'] ?? 0, 0, ',', '.') ?></span>
            </div>
            <?php if(!empty($trx['keterangan'])): ?>
                <div class="row" style="margin-top:10px; font-size:12px;">
                    <span>Ket: <?= esc($trx['keterangan'] ?? '') ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <div style="display:flex; justify-content:space-between; margin-bottom:40px; margin-top:20px;">
                <div style="width:45%; text-align:center;">Teller</div>
                <div style="width:45%; text-align:center;">Penyetor/Penarik</div>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <div style="width:45%; text-align:center; border-bottom:1px solid #000;">( .................... )</div>
                <div style="width:45%; text-align:center; border-bottom:1px solid #000;">( <?= esc($trx['nama_lengkap'] ?? '') ?> )</div>
            </div>
            <p style="margin-top:20px;">Terima kasih atas kepercayaan Anda.</p>
        </div>
    </div>
</body>
</html>