<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="page-title">
    Buku Simpanan (Passbook)
</div>

<div class="card glass-card" style="padding: 20px;">
    <form action="" method="GET" class="d-flex gap-2 align-items-center mb-4">
    <?= csrf_field() ?>
        <select name="anggota_id" class="form-control" style="max-width: 300px;" required>
            <option value="">-- Pilih Anggota --</option>
            <?php foreach($list_anggota as $a): ?>
                <option value="<?= idhash_encode($a['id']) ?>" <?= isset($anggota) && $anggota['id'] == $a['id'] ? 'selected' : '' ?>>
                    <?= esc($a['nama_lengkap'] ?? '') ?> (<?= esc($a['nip'] ?? '') ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Lihat Buku Simpanan</button>
    </form>

    <?php if(isset($anggota)): ?>
        <div style="display:flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px; border-bottom: 2px solid var(--primary); padding-bottom: 10px;">
            <div>
                <h4 style="margin: 0; color: var(--primary);">Buku Simpanan Anggota</h4>
                <div style="color: #666; margin-top: 5px;">
                    <strong>Nama:</strong> <?= esc($anggota['nama_lengkap'] ?? '') ?> | <strong>No. Anggota:</strong> <?= esc($anggota['nip'] ?? '') ?>
                </div>
            </div>
            <a href="<?= base_url('admin/simpanan/cetakBuku?anggota_id=' . idhash_encode($anggota['id'])) ?>" target="_blank" class="btn btn-primary"><i class="fas fa-print me-1"></i> Cetak Buku</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tabelBukuSimpanan" style="font-size: 14px;">
            <thead>
                <tr style="background-color: #f1f5f9;">
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 100px;">Sandi</th>
                    <th>Keterangan</th>
                    <th style="text-align: right; width: 120px;">Debit</th>
                    <th style="text-align: right; width: 120px;">Kredit</th>
                    <th style="text-align: right; width: 120px;">Saldo Pokok</th>
                    <th style="text-align: right; width: 120px;">Saldo Wajib</th>
                    <th style="text-align: right; width: 120px;">Saldo Sukarela</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $saldoPokok = 0;
                $saldoWajib = 0;
                $saldoSukarela = 0;
                
                foreach($transaksi as $t): 
                    // Update running balance based on jenis simpanan ID (Assuming 1=Pokok, 2=Wajib, 3=Sukarela for demo purposes. 
                    // In a real app we should join with jenis_simpanan or do logic based on name)
                    // Wait, in simpanan_transaksi, we only have jenis_simpanan_id and nominal and jenis_transaksi
                    
                    $isMasuk = ($t['jenis_transaksi'] == 'Setoran' || $t['jenis_transaksi'] == 'Masuk');
                    
                    if ($t['jenis_simpanan_id'] == 1) {
                        $saldoPokok += $isMasuk ? $t['nominal'] : -$t['nominal'];
                    } elseif ($t['jenis_simpanan_id'] == 2) {
                        $saldoWajib += $isMasuk ? $t['nominal'] : -$t['nominal'];
                    } else {
                        $saldoSukarela += $isMasuk ? $t['nominal'] : -$t['nominal'];
                    }
                ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
                        <td><?= $isMasuk ? 'C' : 'D' ?> - <?= esc($t['jenis_simpanan_id'] ?? '') ?></td>
                        <td><?= esc($t['keterangan'] ?? '') ?></td>
                        <td style="text-align: right; color: red;"><?= !$isMasuk ? number_format($t['nominal'] ?? 0, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right; color: green;"><?= $isMasuk ? number_format($t['nominal'] ?? 0, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right;"><?= number_format($saldoPokok ?? 0, 0, ',', '.') ?></td>
                        <td style="text-align: right;"><?= number_format($saldoWajib ?? 0, 0, ',', '.') ?></td>
                        <td style="text-align: right;"><?= number_format($saldoSukarela ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .card > form, .card > div button { display: none !important; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#tabelBukuSimpanan').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json" },
        "ordering": false // Keep the original order of transactions
    });
});
</script>
<?= $this->endSection() ?>

