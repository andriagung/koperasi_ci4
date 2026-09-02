<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan</title>
    <style>
        body {
            font-family: "Courier New", Courier, monospace;
            font-size: 12px;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .dashed-border-top {
            border-top: 1px dashed #000;
        }
        .dashed-border-bottom {
            border-bottom: 1px dashed #000;
        }
        .padding-y {
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .header-left {
            line-height: 1.2;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .items-table th, .items-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .signature-area {
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <div class="header-left">
        Koperasi As-syfa RSUD 45 Kuningan<br>
        Jl.Jend.Sudirman No.68<br>
        Kuningan Telp. 0232-871885-159
    </div>

    <div class="title">
        FAKTUR PENJUALAN
    </div>

    <table>
        <tr>
            <td class="text-left" style="width: 50%;">
                PENJUALAN RSUD45 [004]<br>
                Nomor FAKTUR: <?= esc($penjualan['no_invoice']) ?>
            </td>
            <td class="text-right" style="width: 50%; vertical-align: bottom;">
                Tanggal: <?= date('d/m/Y', strtotime($penjualan['tanggal'])) ?>
            </td>
        </tr>
    </table>

    <table class="items-table" style="margin-top: 10px;">
        <thead>
            <tr>
                <th class="text-left dashed-border-top dashed-border-bottom padding-y" style="width: 5%;">No.</th>
                <th class="text-left dashed-border-top dashed-border-bottom padding-y" style="width: 15%;">Kode Barang</th>
                <th class="text-left dashed-border-top dashed-border-bottom padding-y" style="width: 35%;">Nama Barang</th>
                <th class="text-left dashed-border-top dashed-border-bottom padding-y" style="width: 10%;">Satuan</th>
                <th class="text-right dashed-border-top dashed-border-bottom padding-y" style="width: 15%;">Harga</th>
                <th class="text-center dashed-border-top dashed-border-bottom padding-y" style="width: 5%;">Qty</th>
                <th class="text-right dashed-border-top dashed-border-bottom padding-y" style="width: 15%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $subTotal = 0;
                $no = 1;
                foreach($details as $d): 
                    $subTotal += $d['subtotal'];
            ?>
            <tr>
                <td class="text-left"><?= $no++ ?></td>
                <td class="text-left"><?= esc($d['kode_produk']) ?></td>
                <td class="text-left"><?= esc($d['nama_produk']) ?></td>
                <td class="text-left"><?= esc($d['satuan'] ?? 'BUAH') ?></td>
                <td class="text-right"><?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>
                <td class="text-center"><?= $d['qty'] ?></td>
                <td class="text-right"><?= number_format($d['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table style="margin-top: 10px;">
        <tr>
            <td class="dashed-border-top padding-y" style="width: 70%;">NI</td>
            <td class="text-right dashed-border-top padding-y" style="width: 15%;">Sub Total<br>PPN<br>T O T A L</td>
            <td class="text-right dashed-border-top padding-y" style="width: 15%;">
                <?= number_format($subTotal, 0, ',', '.') ?><br>
                0<br>
                <?= number_format($subTotal, 0, ',', '.') ?>
            </td>
        </tr>
    </table>

    <table class="signature-area">
        <tr>
            <td class="text-center" style="width: 50%;">
                Penerima,<br><br><br><br><br>
                (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
            </td>
            <td class="text-center" style="width: 50%;">
                Dibuat Oleh,<br><br><br><br><br>
                ( Yudi Hartono, SE, MSi)
            </td>
        </tr>
    </table>

</body>
</html>
