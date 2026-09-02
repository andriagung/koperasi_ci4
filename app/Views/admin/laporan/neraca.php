<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="page-title">
        <div>
            <i class="fas fa-balance-scale text-primary"></i> <?= esc($judul ?? '') ?>
            <p style="font-size: 0.9rem; color: #64748b; font-weight: normal; margin-top: 5px;">Posisi Keuangan Koperasi per tanggal <?= date('d M Y', strtotime($akhir)) ?>.</p>
        </div>
    </div>
</div>

<div class="table-container" style="margin-bottom: 20px; padding: 15px;">
    
    <?= csrf_field() ?>
        <!-- Neraca is usually a snapshot at a given date. We keep tgl_awal just for UI consistency if needed, but only tgl_akhir is mathematically used for Neraca. -->
        <input type="hidden" name="tgl_awal" value="<?= esc($awal ?? '') ?>">
        <div>
            <label style="font-weight: bold; font-size: 0.85rem; margin-bottom: 5px; display: block;">Per Tanggal</label>
            <input type="date" name="tgl_akhir" class="form-control" value="<?= esc($akhir ?? '') ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; outline: none;" required>
        </div>
        <div>
            <button type="submit" class="btn-primary" style="padding: 9px 20px;"><i class="fas fa-filter"></i> Tampilkan</button>
        </div>
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <a href="<?= base_url('admin/laporan/generate?jenis_laporan=neraca&format=pdf&tgl_awal=' . $awal . '&tgl_akhir=' . $akhir) ?>" target="_blank" class="btn-primary" style="background: #dc2626;"><i class="fas fa-file-pdf"></i> Print PDF</a>
        </div>
    </form>
</div>

<div class="row" style="display: flex; gap: 20px;">
    <!-- AKTIVA -->
    <div style="flex: 1;">
        <div class="table-container">
            <h4 style="margin-bottom: 15px; color:#0f172a; border-bottom: 2px solid #059669; padding-bottom:10px;">AKTIVA (ASET)</h4>
            <table class="dataTable" style="width: 100%;">
                <tbody>
                    <?php foreach($data['aktiva'] as $a): ?>
                    <tr>
                        <td><?= esc($a['nama_akun'] ?? '') ?></td>
                        <td class="text-right"><?= number_format($a['saldo'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background:#e0f2fe;">
                        <td style="font-weight:bold;">TOTAL AKTIVA</td>
                        <td class="text-right" style="font-weight:bold; color:#0284c7;"><?= number_format($data['totalAktiva'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- PASIVA -->
    <div style="flex: 1;">
        <div class="table-container">
            <h4 style="margin-bottom: 15px; color:#0f172a; border-bottom: 2px solid #dc2626; padding-bottom:10px;">PASIVA (KEWAJIBAN & MODAL)</h4>
            <table class="dataTable" style="width: 100%;">
                <tbody>
                    <tr><td colspan="2" style="font-weight:bold; background:#f1f5f9;">KEWAJIBAN (HUTANG)</td></tr>
                    <?php foreach($data['kewajiban'] as $k): ?>
                    <tr>
                        <td style="padding-left: 20px;"><?= esc($k['nama_akun'] ?? '') ?></td>
                        <td class="text-right"><?= number_format($k['saldo'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <tr><td colspan="2" style="font-weight:bold; background:#f1f5f9;">MODAL (EKUITAS & SIMPANAN)</td></tr>
                    <?php foreach($data['modal'] as $m): ?>
                    <tr>
                        <td style="padding-left: 20px;"><?= esc($m['nama_akun'] ?? '') ?></td>
                        <td class="text-right"><?= number_format($m['saldo'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <!-- Laba Ditahan / SHU Berjalan dimasukkan ke modal -->
                    <tr>
                        <td style="padding-left: 20px;">Laba Tahun Berjalan (SHU)</td>
                        <td class="text-right"><?= number_format($data['labaTahunBerjalan'] ?? 0, 0, ',', '.') ?></td>
                    </tr>

                    <tr style="background:#fee2e2;">
                        <td style="font-weight:bold;">TOTAL PASIVA</td>
                        <td class="text-right" style="font-weight:bold; color:#b91c1c;"><?= number_format($data['totalPasiva'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?= $this->endSection() ?>

