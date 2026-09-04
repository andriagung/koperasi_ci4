<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title">Buku Besar</h4>
            </div>
            <div class="card-body">
    
    <form action="" method="POST">
        <?= csrf_field() ?>
        <select name="akun_id" class="form-control" style="max-width: 400px;" required>
            <option value="">-- Pilih Akun --</option>
            <?php foreach($list_akun as $a): ?>
                <option value="<?= $a['id'] ?? '' ?>" <?= isset($selectedAkun) && $selectedAkun['id'] == $a['id'] ? 'selected' : '' ?>>
                    <?= esc($a['kode_akun'] ?? '') ?> - <?= esc($a['nama_akun'] ?? '') ?> (<?= esc($a['saldo_normal'] ?? '') ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary">Lihat Buku Besar</button>
    </form>

    <?php if(isset($selectedAkun)): ?>
        <h4 style="margin-bottom: 15px; border-bottom: 2px solid var(--primary); padding-bottom:10px; color: var(--primary);">
            Buku Besar: <?= esc($selectedAkun['kode_akun'] ?? '') ?> - <?= esc($selectedAkun['nama_akun'] ?? '') ?>
        </h4>
        
        <table class="table" style="font-size: 14px;">
            <thead>
                <tr style="background-color: #f1f5f9;">
                    <th style="width: 120px;">Tanggal</th>
                    <th style="width: 150px;">Nomor Bukti</th>
                    <th>Keterangan</th>
                    <th style="text-align: right; width: 120px;">Debit</th>
                    <th style="text-align: right; width: 120px;">Kredit</th>
                    <th style="text-align: right; width: 150px;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $saldo = 0;
                $isDebitNormal = ($selectedAkun['saldo_normal'] == 'Debit');

                if(empty($buku_besar)): ?>
                    <tr><td colspan="6" style="text-align:center;">Belum ada riwayat transaksi pada akun ini.</td></tr>
                <?php else:
                    foreach($buku_besar as $b): 
                        if ($isDebitNormal) {
                            $saldo += (strtolower($b['posisi']) == 'debit') ? $b['nominal'] : -$b['nominal'];
                        } else {
                            $saldo += (strtolower($b['posisi']) == 'kredit') ? $b['nominal'] : -$b['nominal'];
                        }
                ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($b['tanggal'])) ?></td>
                        <td><?= esc($b['nomor_bukti'] ?? '') ?></td>
                        <td><?= esc($b['keterangan'] ?? '') ?></td>
                        <td style="text-align: right;"><?= strtolower($b['posisi']) == 'debit' ? number_format($b['nominal'] ?? 0, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right;"><?= strtolower($b['posisi']) == 'kredit' ? number_format($b['nominal'] ?? 0, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right; font-weight: bold;"><?= number_format($saldo ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

