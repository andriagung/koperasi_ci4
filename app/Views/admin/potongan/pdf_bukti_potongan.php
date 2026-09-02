<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Potongan Gaji - <?= esc($tagihan['nama'] ?? '') ?></title>
    <style>
        @page {
            size: A5 landscape;
            margin: 12mm 15mm 10mm 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header-title {
            font-size: 13px;
            font-weight: bold;
            color: #065f46;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-sub {
            font-size: 8.5px;
            color: #475569;
            margin: 2px 0 0 0;
        }
        .slip-badge {
            text-align: right;
            vertical-align: middle;
        }
        .slip-title {
            display: inline-block;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9.5px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            width: 18%;
        }
        .meta-val {
            font-weight: bold;
            color: #0f172a;
            width: 32%;
        }

        /* 2-Column Comparison Layout */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .col-box {
            width: 48.5%;
            vertical-align: top;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        .col-header {
            background: #f1f5f9;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 9.5px;
            color: #334155;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .item-table td {
            padding: 3.5px 8px;
            border-bottom: 1px dashed #f1f5f9;
        }
        .item-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .num {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9.5px;
        }
        
        .total-box {
            background: #f0fdf4;
            border-top: 1.5px solid #059669;
            font-weight: bold;
            color: #065f46;
            padding: 6px 8px;
            font-size: 10.5px;
        }
        
        .footer-table {
            width: 100%;
            margin-top: 8px;
            font-size: 9px;
        }
        .footer-table td {
            vertical-align: top;
            text-align: center;
        }
        .ttd-space {
            height: 38px;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .sys-note {
            font-size: 8px;
            color: #94a3b8;
            font-style: italic;
            text-align: left;
            margin-top: 6px;
            border-top: 1px dotted #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 65%;">
                <h1 class="header-title">Koperasi Karyawan As-Syifa RSUD 45 Kuningan</h1>
                <p class="header-sub">Jl. Jend. Sudirman No. 68 Kuningan - Jawa Barat | Telp: (0232) 871885-159</p>
            </td>
            <td class="slip-badge">
                <div class="slip-title">BUKTI POTONGAN GAJI</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 3px;">BULAN: <strong><?= strtoupper(date('F Y', strtotime(($tagihan['periode'] ?? date('Y-m')) . '-01'))) ?></strong></div>
            </td>
        </tr>
    </table>

    <!-- Meta Information -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">No. Anggota</td>
            <td class="meta-val">: <?= esc($anggota['nip'] ?? $tagihan['nik'] ?? '-') ?></td>
            <td class="meta-label">Unit Kerja</td>
            <td class="meta-val">: <?= strtoupper(esc($anggota['divisi'] ?? $tagihan['nama_instansi'] ?? 'INST. FARMASI')) ?></td>
        </tr>
        <tr>
            <td class="meta-label">Nama [Gol]</td>
            <td class="meta-val">: <?= strtoupper(esc($tagihan['nama'] ?? $anggota['nama_lengkap'] ?? '-')) ?> <?= !empty($anggota['golongan']) ? '[' . esc($anggota['golongan']) . ']' : '' ?></td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-val">: <?= date('d/m/Y') ?> Jam :<?= date('H:i:s') ?></td>
        </tr>
    </table>

    <!-- 2 Column Comparison Table -->
    <table class="content-table">
        <tr>
            <!-- Kolom Kiri: Keadaan Bulan Lalu / Saldo -->
            <td class="col-box">
                <div class="col-header">Keadaan Saldo / Pinjaman :</div>
                <table class="item-table">
                    <tr>
                        <td style="width: 55%;">1. Simpanan Pokok</td>
                        <td style="width: 10%;">Rp</td>
                        <td class="text-right num" style="width: 35%;"><?= number_format($saldo_lalu['simpanan_pokok'] ?? 25000, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>2. Simpanan Wajib (s/d Bln Lalu)</td>
                        <td>Rp</td>
                        <td class="text-right num"><?= number_format($saldo_lalu['simpanan_wajib'] ?? 1200000, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>3. Simpanan Sukarela (M-Suka)</td>
                        <td>Rp</td>
                        <td class="text-right num"><?= number_format($saldo_lalu['simpanan_sukarela'] ?? 50000, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>4. Sisa Pokok Pinjaman Uang</td>
                        <td>Rp</td>
                        <td class="text-right num" style="color: #b91c1c;"><?= number_format($saldo_lalu['sisa_pokok_pinjaman'] ?? ($pinjaman_aktif['sisa_pokok'] ?? 0), 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>5. Masa Pinjaman Berjalan</td>
                        <td colspan="2" class="text-right" style="font-weight: bold; color: #1e40af;">
                            <?= !empty($pinjaman_aktif) ? 'Bulan Ke-' . ($pinjaman_aktif['angsuran_ke'] ?? 1) . ' dr ' . ($pinjaman_aktif['tenor_bulan'] ?? 12) . ' Bln' : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>6. Sisa Angsuran Belanja WASERDA</td>
                        <td>Rp</td>
                        <td class="text-right num"><?= number_format($saldo_lalu['sisa_waserda'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </td>

            <td style="width: 3%;"></td>

            <!-- Kolom Kanan: Rincian Potongan Bulan Ini -->
            <td class="col-box">
                <div class="col-header" style="background: #ecfdf5; color: #065f46;">Potongan Bulan Ini :</div>
                <table class="item-table">
                    <tr>
                        <td style="width: 55%;">1. Setor Simpanan Wajib</td>
                        <td style="width: 10%;">Rp</td>
                        <td class="text-right num" style="width: 35%;"><?= number_format($tagihan['nominal_simpanan_wajib'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>2. Setor Simpanan Sukarela (M-Suka)</td>
                        <td>Rp</td>
                        <td class="text-right num"><?= number_format($tagihan['nominal_simpanan_sukarela'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>3. Angsuran Pokok Pinjaman</td>
                        <td>Rp</td>
                        <td class="text-right num"><?= number_format($angsuran_detail['pokok'] ?? ($tagihan['nominal_angsuran'] > 0 ? ($tagihan['nominal_angsuran'] * 0.7) : 0), 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>4. Jasa Pinjaman Uang</td>
                        <td>Rp</td>
                        <td class="text-right num"><?= number_format($angsuran_detail['jasa'] ?? ($tagihan['nominal_angsuran'] > 0 ? ($tagihan['nominal_angsuran'] * 0.3) : 0), 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>5. Iuran Dana Sosial Koperasi</td>
                        <td>Rp</td>
                        <td class="text-right num"><?= number_format($tagihan['dana_sosial'] ?? 7500, 0, ',', '.') ?></td>
                    </tr>
                </table>
                <div class="total-box">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; font-size: 10px;">TOTAL POTONGAN</td>
                            <td class="text-right num" style="font-size: 11px; font-weight: bold; color: #065f46;">
                                Rp <?= number_format(($tagihan['total_tagihan'] ?? 0) + ($tagihan['dana_sosial'] ?? 7500), 0, ',', '.') ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <table class="footer-table">
        <tr>
            <td style="width: 35%;">
                Mengetahui,<br>
                <strong>Sekretaris</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">Dinda Tria N, S.Kom</div>
            </td>
            <td style="width: 30%;">
                <div style="font-size: 8.5px; color: #64748b; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; margin-top: 10px;">
                    Status Pelunasan:<br>
                    <strong style="color: <?= ($tagihan['status'] ?? '') === 'Lunas' ? '#15803d' : '#b45309' ?>; font-size: 10px;">
                        <?= strtoupper($tagihan['status'] ?? 'PENDING') ?>
                    </strong>
                </div>
            </td>
            <td style="width: 35%;">
                Kuningan, <?= date('d F Y') ?><br>
                <strong>Bendahara</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">Adijanto, SST, Ft</div>
            </td>
        </tr>
    </table>

    <div class="sys-note">
        * Lembar bukti potongan ini dicetak secara sah dan otomatis oleh SIM Koperasi As-Syifa RSUD 45 Kuningan, berlaku sebagai tanda bukti resmi tanpa stempel basah.
    </div>

</body>
</html>
