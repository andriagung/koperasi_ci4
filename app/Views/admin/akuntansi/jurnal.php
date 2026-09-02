<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title">Jurnal Umum</h4>
            </div>
            <div class="card-body">
    <p style="color: #666; margin-bottom: 20px;">Pencatatan double-entry otomatis untuk setiap transaksi.</p>
    
    <table class="table" style="font-size: 14px;">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="width: 120px;">Tanggal</th>
                <th style="width: 150px;">Nomor Bukti</th>
                <th style="width: 300px;">Keterangan</th>
                <th>Akun</th>
                <th style="text-align: right; width: 120px;">Debit</th>
                <th style="text-align: right; width: 120px;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($jurnal)): ?>
                <tr><td colspan="6" style="text-align:center;">Belum ada entri jurnal.</td></tr>
            <?php else:
                // Group by nomor_bukti for visual aid
                $currentBukti = '';
                foreach($jurnal as $j): 
                    $isNewGroup = ($j['nomor_bukti'] !== $currentBukti);
                    if ($isNewGroup) $currentBukti = $j['nomor_bukti'];
            ?>
                <tr <?= $isNewGroup ? 'style="border-top: 2px solid #e2e8f0;"' : '' ?>>
                    <td><?= $isNewGroup ? date('d-m-Y', strtotime($j['tanggal'])) : '' ?></td>
                    <td><strong><?= $isNewGroup ? esc($j['nomor_bukti'] ?? '') : '' ?></strong></td>
                    <td><?= $isNewGroup ? esc($j['keterangan'] ?? '') : '' ?></td>
                    <td <?= strtolower($j['posisi']) == 'kredit' ? 'style="padding-left:30px;"' : '' ?>>
                        <?= esc($j['kode_akun'] ?? '') ?> - <?= esc($j['nama_akun'] ?? '') ?>
                    </td>
                    <td style="text-align: right; color: <?= strtolower($j['posisi']) == 'debit' ? '#000' : 'transparent' ?>;">
                        <?= strtolower($j['posisi']) == 'debit' ? number_format($j['nominal'] ?? 0, 0, ',', '.') : '-' ?>
                    </td>
                    <td style="text-align: right; color: <?= strtolower($j['posisi']) == 'kredit' ? '#000' : 'transparent' ?>;">
                        <?= strtolower($j['posisi']) == 'kredit' ? number_format($j['nominal'] ?? 0, 0, ',', '.') : '-' ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
