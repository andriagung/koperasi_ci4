<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title">Neraca (Balance Sheet)</h4>
            </div>
            <div class="card-body">
    <button type="button" class="btn-primary" style="margin-bottom: 20px;" onclick="window.print()"><i class="fas fa-print"></i> Cetak Neraca</button>

    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
        <h3 style="text-align:center; color: var(--primary); margin-bottom: 5px;">KOPERASI WARSERDA</h3>
        <h4 style="text-align:center; color: #555; margin-bottom: 20px;">NERACA <br><small>Per Tanggal: <?= date('d M Y') ?></small></h4>

        <div style="display: flex; gap: 40px; margin-top: 30px;">
            <!-- KIRI: AKTIVA -->
            <div style="flex: 1;">
                <h4 style="border-bottom: 2px solid #333; padding-bottom: 5px; margin-bottom: 15px;">AKTIVA (HARTA)</h4>
                <table style="width: 100%; font-size: 14px;">
                    <?php $totalAktiva = 0; foreach($aktiva as $a): $totalAktiva += $a['saldo']; ?>
                    <tr>
                        <td style="padding: 5px 0;"><?= esc($a['kode_akun'] ?? '') ?> - <?= esc($a['nama_akun'] ?? '') ?></td>
                        <td style="text-align:right;">Rp <?= number_format($a['saldo'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- KANAN: KEWAJIBAN & EKUITAS -->
            <div style="flex: 1;">
                <!-- KEWAJIBAN -->
                <h4 style="border-bottom: 2px solid #333; padding-bottom: 5px; margin-bottom: 15px;">KEWAJIBAN (UTANG)</h4>
                <table style="width: 100%; font-size: 14px; margin-bottom: 20px;">
                    <?php $totalKewajiban = 0; foreach($kewajiban as $k): $totalKewajiban += $k['saldo']; ?>
                    <tr>
                        <td style="padding: 5px 0;"><?= esc($k['kode_akun'] ?? '') ?> - <?= esc($k['nama_akun'] ?? '') ?></td>
                        <td style="text-align:right;">Rp <?= number_format($k['saldo'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <!-- EKUITAS -->
                <h4 style="border-bottom: 2px solid #333; padding-bottom: 5px; margin-bottom: 15px;">EKUITAS (MODAL)</h4>
                <table style="width: 100%; font-size: 14px;">
                    <?php $totalEkuitas = 0; foreach($ekuitas as $e): $totalEkuitas += $e['saldo']; ?>
                    <tr>
                        <td style="padding: 5px 0;"><?= esc($e['kode_akun'] ?? '') ?> - <?= esc($e['nama_akun'] ?? '') ?></td>
                        <td style="text-align:right;">Rp <?= number_format($e['saldo'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <!-- TOTAL FOOTER -->
        <div style="display: flex; gap: 40px; margin-top: 30px; border-top: 3px double #333; padding-top: 15px;">
            <div style="flex: 1; display:flex; justify-content:space-between; font-weight:bold; font-size: 16px;">
                <span>TOTAL AKTIVA</span>
                <span>Rp <?= number_format($totalAktiva ?? 0, 0, ',', '.') ?></span>
            </div>
            <div style="flex: 1; display:flex; justify-content:space-between; font-weight:bold; font-size: 16px;">
                <span>TOTAL PASIVA (KEWAJIBAN + EKUITAS)</span>
                <span>Rp <?= number_format($totalKewajiban + $totalEkuitas, 0, ',', '.') ?></span>
            </div>
        </div>
        
        <?php if ($totalAktiva != ($totalKewajiban + $totalEkuitas)): ?>
            <div style="margin-top: 20px; padding: 10px; background: #fee2e2; color: #dc2626; border-radius: 4px; text-align: center; font-weight: bold;">
                <i class="fas fa-exclamation-triangle"></i> Peringatan: Neraca Tidak Balance! (Selisih: Rp <?= number_format(abs($totalAktiva - ($totalKewajiban + $totalEkuitas)), 0, ',', '.') ?>) <br>
                <small>Pastikan seluruh Laba Rugi sudah dipindahkan ke SHU Tahun Berjalan (Tutup Buku) atau periksa jurnal yang belum seimbang.</small>
            </div>
        <?php else: ?>
            <div style="margin-top: 20px; text-align: center; color: #16a34a; font-weight: bold;">
                <i class="fas fa-check-circle"></i> Neraca Balance
            </div>
        <?php endif; ?>

    </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .card button { display: none !important; }
    .card > div { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; border: none; }
    .card > div * { visibility: visible; }
}
</style>
<?= $this->endSection() ?>
