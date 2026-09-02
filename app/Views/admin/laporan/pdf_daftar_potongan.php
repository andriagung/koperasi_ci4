<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Potongan Anggota</title>
    <style>
        body {
            font-family: "Courier New", Courier, monospace;
            font-size: 8px; /* Extremely small to fit 17 columns */
            margin: 0;
            padding: 0;
            color: #000;
        }
        .header {
            margin-bottom: 20px;
        }
        .header p {
            margin: 0;
            line-height: 1.2;
        }
        .filters {
            margin-top: 15px;
            width: 50%;
            float: left;
        }
        .title-box {
            width: 50%;
            float: right;
            text-align: right;
            margin-top: 25px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        
        .dashed-border-top {
            border-top: 1px dashed #000;
        }
        .dashed-border-bottom {
            border-bottom: 1px dashed #000;
        }
        th, td {
            padding: 4px 2px;
            vertical-align: top;
            text-align: right;
        }
        th.text-left, td.text-left {
            text-align: left;
        }
        th.text-center, td.text-center {
            text-align: center;
        }
        
        /* Fixed widths to prevent table expansion breaking layout */
        .w-no { width: 2%; }
        .w-nik { width: 4%; }
        .w-nama { width: 14%; }
        .w-unit { width: 12%; }
        /* The money columns evenly distributed */
        .w-money { width: 5%; }
        
    </style>
</head>
<body>

    <div class="clearfix header">
        <div style="float: left; width: 45%; line-height: 1.1; margin-top: 5px;">
            Koperasi As-syfa RSUD 45 Kuningan<br>
            Jl.Jend.Sudirman No.68<br>
            Kuningan Telp. 0232-871885-159
        </div>
        <div class="title-box">
            <div class="title">DAFTAR POTONGAN ANGGOTA</div>
        </div>
        <div style="clear: both;"></div>

        <div class="filters" style="width: 100%; margin-top: 15px;">
            <table style="width: 70%; border: none;">
                <tr>
                    <td class="text-left" style="width: 120px; padding: 0;">Unit Kerja</td>
                    <td class="text-left" style="padding: 0;">: <?= esc($unitKerjaLabel) ?></td>
                    <td class="text-left" style="width: 60px; padding: 0;">Golongan</td>
                    <td class="text-left" style="padding: 0;">: <?= esc($golonganLabel) ?></td>
                </tr>
                <tr>
                    <td class="text-left" style="padding: 0;">Status Anggota</td>
                    <td class="text-left" style="padding: 0;">: <?= esc($statusLabel) ?></td>
                    <td class="text-left" style="padding: 0;"></td>
                    <td class="text-left" style="padding: 0;"></td>
                </tr>
                <tr>
                    <td class="text-left" style="padding: 0;">Tanggal</td>
                    <td class="text-left" style="padding: 0;" colspan="3">: <?= date('d/m/Y', strtotime($tglAwal)) ?> s/d <?= date('d/m/Y', strtotime($tglAkhir)) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <td colspan="10" class="dashed-border-bottom" style="padding: 0; padding-top: 5px;"></td>
            </tr>
            <tr>
                <th rowspan="2" class="text-left" style="width: 3%;">No.</th>
                <th rowspan="2" class="text-left" style="width: 20%;">No.Anggota N a m a</th>
                <th rowspan="2" class="text-left" style="width: 25%;">UnitKerja</th>
                <th class="text-right">Pot.SW</th>
                <th class="text-right">Pot.SS</th>
                <th class="text-right">Pot.PPU</th>
                <th class="text-right">Pot.PPB</th>
                <th class="text-right">Pot.PPS</th>
                <th class="text-right">DanSos</th>
                <th class="text-right">Hal : <script type="text/php">if(isset($pdf)){$pdf->page_text($pdf->get_width()-30,$pdf->get_height()-770,"{PAGE_NUM}",null,8,array(0,0,0));}</script></th>
            </tr>
            <tr>
                <th class="text-right">Pot.SP</th>
                <th class="text-right">Pot.SL</th>
                <th class="text-right">Pot.BPU</th>
                <th class="text-right">Pot.BPB</th>
                <th class="text-right">Pot.BPS</th>
                <th class="text-right">Pangan</th>
                <th class="text-right">Jumlah</th>
            </tr>
            <tr>
                <td colspan="10" class="dashed-border-bottom" style="padding: 0; padding-bottom: 5px;"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
                $no = 1;
                // Totals
                $t_sw=0; $t_sp=0; $t_ss=0; $t_sl=0;
                $t_ppu=0; $t_bpu=0; $t_bpb=0; $t_ppb=0;
                $t_pps=0; $t_bps=0; $t_dansos=0; $t_pangan=0;
                $t_jumlah=0;

                foreach($potongan as $p):
                    $t_sw += $p['pot_sw']; $t_sp += $p['pot_sp'];
                    $t_ss += $p['pot_ss']; $t_sl += $p['pot_sl'];
                    $t_ppu += $p['pot_ppu']; $t_bpu += $p['pot_bpu'];
                    $t_bpb += $p['pot_bpb']; $t_ppb += $p['pot_ppb'];
                    $t_pps += $p['pot_pps']; $t_bps += $p['pot_bps'];
                    $t_dansos += $p['dansos']; $t_pangan += $p['pangan'];
                    $t_jumlah += $p['jumlah'];
            ?>
            <tr>
                <td class="text-left"><?= $no++ ?></td>
                <td class="text-left"><?= esc($p['nik']) ?> <?= esc($p['nama_lengkap']) ?> [ <?= esc($p['golongan']) ?: '-' ?> ]</td>
                <td class="text-left"><?= esc($p['unit_kerja'] ?? 'Anggota Luar Biasa') ?></td>
                <td class="text-right"><?= number_format($p['pot_sw'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_ss'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_ppu'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_ppb'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_pps'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['dansos'], 0, ',', '.') ?></td>
                <td class="text-right">0</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= number_format($p['pot_sp'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_sl'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_bpu'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_bpb'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pot_bps'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['pangan'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($p['jumlah'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="10" style="padding-top: 5px;"></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="dashed-border-top" style="padding: 0;"></td>
            </tr>
            <tr>
                <td colspan="3" class="text-left" style="font-weight: bold; padding-top: 5px;">TOTAL : Pot.SW :</td>
                <td class="text-right" style="font-weight: bold; padding-top: 5px;"><?= number_format($t_sw, 0, ',', '.') ?></td>
                <td class="text-left" style="font-weight: bold; padding-top: 5px;">Pot.SS :</td>
                <td class="text-right" style="font-weight: bold; padding-top: 5px;"><?= number_format($t_ss, 0, ',', '.') ?></td>
                <td class="text-left" style="font-weight: bold; padding-top: 5px;">Pot.PPU :</td>
                <td class="text-right" style="font-weight: bold; padding-top: 5px;"><?= number_format($t_ppu, 0, ',', '.') ?></td>
                <td class="text-left" style="font-weight: bold; padding-top: 5px;">Pot.PPB :</td>
                <td class="text-right" style="font-weight: bold; padding-top: 5px;"><?= number_format($t_ppb, 0, ',', '.') ?></td>
            </tr>
            <!-- We will adjust the layout further because the total spans dynamically -->
        </tfoot>
    </table>
    <!-- Footer Custom Layout mimicking screenshot exactly -->
    <table style="width: 100%; border: none; margin-top: -10px;">
        <tr>
            <td style="width: 28%; font-weight: bold;">TOTAL : Pot.SW :</td>
            <td style="width: 12%; text-align: right; font-weight: bold;"><?= number_format($t_sw, 0, ',', '.') ?></td>
            <td style="width: 10%; font-weight: bold; text-align: right;">Pot.SS :</td>
            <td style="width: 10%; text-align: right; font-weight: bold;"><?= number_format($t_ss, 0, ',', '.') ?></td>
            <td style="width: 10%; font-weight: bold; text-align: right;">Pot.PPU :</td>
            <td style="width: 10%; text-align: right; font-weight: bold;"><?= number_format($t_ppu, 0, ',', '.') ?></td>
            <td style="width: 10%; font-weight: bold; text-align: right;">Pot.PPB :</td>
            <td style="width: 10%; text-align: right; font-weight: bold;"><?= number_format($t_ppb, 0, ',', '.') ?></td>
            <td style="width: 10%; font-weight: bold; text-align: right;">Pot.PPS :</td>
            <td style="width: 10%; text-align: right; font-weight: bold;"><?= number_format($t_pps, 0, ',', '.') ?></td>
            <td style="width: 10%; font-weight: bold; text-align: right;">DanSos :</td>
            <td style="width: 10%; text-align: right; font-weight: bold;"><?= number_format($t_dansos, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TOTAL : Pot.SP :</td>
            <td style="text-align: right; font-weight: bold;"><?= number_format($t_sp, 0, ',', '.') ?></td>
            <td style="font-weight: bold; text-align: right;">Pot.SL :</td>
            <td style="text-align: right; font-weight: bold;"><?= number_format($t_sl, 0, ',', '.') ?></td>
            <td style="font-weight: bold; text-align: right;">Pot.BPU :</td>
            <td style="text-align: right; font-weight: bold;"><?= number_format($t_bpu, 0, ',', '.') ?></td>
            <td style="font-weight: bold; text-align: right;">Pot.BPB :</td>
            <td style="text-align: right; font-weight: bold;"><?= number_format($t_bpb, 0, ',', '.') ?></td>
            <td style="font-weight: bold; text-align: right;">Pot.BPS :</td>
            <td style="text-align: right; font-weight: bold;"><?= number_format($t_bps, 0, ',', '.') ?></td>
            <td style="font-weight: bold; text-align: right;">Pangan :</td>
            <td style="text-align: right; font-weight: bold;"><?= number_format($t_pangan, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TOTAL Jumlah   :</td>
            <td style="text-align: right; font-weight: bold;"><?= number_format($t_jumlah, 0, ',', '.') ?></td>
            <td colspan="10"></td>
        </tr>
    </table>

</body>
</html>
