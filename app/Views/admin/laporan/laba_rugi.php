<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="page-title">
        <div>
            <i class="fas fa-chart-line text-primary"></i> <?= esc($judul ?? '') ?>
            <p style="font-size: 0.9rem; color: #64748b; font-weight: normal; margin-top: 5px;">Ringkasan Pendapatan dan Beban pada periode <?= date('d M Y', strtotime($awal)) ?> s/d <?= date('d M Y', strtotime($akhir)) ?>.</p>
        </div>
    </div>
</div>

<div class="table-container" style="margin-bottom: 20px; padding: 15px;">
    
    <form action="" method="POST">
        <?= csrf_field() ?>
        <div>
            <label style="font-weight: bold; font-size: 0.85rem; margin-bottom: 5px; display: block;">Dari Tanggal</label>
            <input type="date" name="tgl_awal" class="form-control" value="<?= esc($awal ?? '') ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; outline: none;" required>
        </div>
        <div>
            <label style="font-weight: bold; font-size: 0.85rem; margin-bottom: 5px; display: block;">Sampai Tanggal</label>
            <input type="date" name="tgl_akhir" class="form-control" value="<?= esc($akhir ?? '') ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; outline: none;" required>
        </div>
        <div>
            <button type="submit" class="btn-primary" style="padding: 9px 20px;"><i class="fas fa-filter"></i> Tampilkan</button>
        </div>
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <a href="<?= base_url('admin/laporan/generate?jenis_laporan=labarugi&format=pdf&tgl_awal=' . $awal . '&tgl_akhir=' . $akhir) ?>" target="_blank" class="btn-primary" style="background: #dc2626;"><i class="fas fa-file-pdf"></i> Print PDF</a>
        </div>
    </form>
</div>

<div class="table-container">
    <table class="dataTable" style="width: 100%;">
        <thead>
            <tr>
                <th width="70%">Keterangan Akun</th>
                <th width="30%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="2" style="font-weight:bold; background:#f1f5f9;">PENDAPATAN (BUNGA & WASERDA)</td></tr>
            <?php foreach($data['pendapatan'] as $p): ?>
            <tr>
                <td style="padding-left: 30px;"><?= esc($p['nama_akun'] ?? '') ?></td>
                <td class="text-right"><?= number_format($p['saldo'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td style="font-weight:bold; text-align:right;">Total Pendapatan</td>
                <td class="text-right" style="font-weight:bold; color:#059669;"><?= number_format($data['totalPendapatan'] ?? 0, 0, ',', '.') ?></td>
            </tr>

            <tr><td colspan="2" style="font-weight:bold; background:#f1f5f9;">BEBAN & PENGELUARAN</td></tr>
            <?php foreach($data['beban'] as $b): ?>
            <tr>
                <td style="padding-left: 30px;"><?= esc($b['nama_akun'] ?? '') ?></td>
                <td class="text-right"><?= number_format($b['saldo'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td style="font-weight:bold; text-align:right;">Total Beban</td>
                <td class="text-right" style="font-weight:bold; color:#dc2626;"><?= number_format($data['totalBeban'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            
            <tr style="background:#e0f2fe;">
                <td style="font-weight:bold; font-size:1.1em; text-align:right;">LABA BERSIH (SHU BERJALAN)</td>
                <td class="text-right" style="font-weight:bold; font-size:1.1em; color:#0284c7;"><?= number_format($data['shuBersih'] ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?= $this->endSection() ?>

