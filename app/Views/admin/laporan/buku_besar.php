<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="page-title">
        <div>
            <i class="fas fa-book-open text-primary"></i> <?= esc($judul ?? '') ?>
            <p style="font-size: 0.9rem; color: #64748b; font-weight: normal; margin-top: 5px;">Rincian transaksi per akun (COA) pada periode <?= date('d M Y', strtotime($awal)) ?> s/d <?= date('d M Y', strtotime($akhir)) ?>.</p>
        </div>
    </div>
</div>

<div class="table-container" style="margin-bottom: 20px; padding: 15px;">
    
    <form action="" method="POST">
        <?= csrf_field() ?>
        <div>
            <label style="font-weight: bold; font-size: 0.85rem; margin-bottom: 5px; display: block;">Pilih Akun (COA)</label>
            <select name="coa_id" class="form-control" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; outline: none; min-width: 200px;" required>
                <option value="">-- Pilih Akun --</option>
                <?php foreach($daftarCoa as $c): ?>
                    <option value="<?= $c['id'] ?? '' ?>" <?= ($coa_id == $c['id']) ? 'selected' : '' ?>><?= esc($c['kode_akun'] ?? '') ?> - <?= esc($c['nama_akun'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
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
        <?php if($akunTerpilih): ?>
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <a href="<?= base_url('admin/laporan/generate?jenis_laporan=bukubesar&format=pdf&coa_id=' . $coa_id . '&tgl_awal=' . $awal . '&tgl_akhir=' . $akhir) ?>" target="_blank" class="btn-primary" style="background: #dc2626;"><i class="fas fa-file-pdf"></i> Print PDF</a>
        </div>
        <?php endif; ?>
    </form>
</div>

<?php if($akunTerpilih): ?>
<div class="table-container">
    <div style="margin-bottom: 15px;">
        <h4 style="margin: 0;">Buku Besar: <?= esc($akunTerpilih['kode_akun'] ?? '') ?> - <?= esc($akunTerpilih['nama_akun'] ?? '') ?></h4>
    </div>
    <table class="dataTable" style="width: 100%;">
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="15%">No. Bukti</th>
                <th width="40%">Keterangan</th>
                <th width="15%">Debit (Rp)</th>
                <th width="15%">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totDebit = 0;
            $totKredit = 0;
            foreach($data as $d): 
                if($d['posisi'] == 'Debit') $totDebit += $d['nominal'];
                if($d['posisi'] == 'Kredit') $totKredit += $d['nominal'];
            ?>
            <tr>
                <td class="text-center"><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                <td class="text-center"><?= esc($d['nomor_bukti'] ?? '') ?></td>
                <td><?= esc($d['keterangan'] ?? '') ?></td>
                <td class="text-right"><?= ($d['posisi'] == 'Debit') ? number_format($d['nominal'] ?? 0, 0, ',', '.') : '-' ?></td>
                <td class="text-right"><?= ($d['posisi'] == 'Kredit') ? number_format($d['nominal'] ?? 0, 0, ',', '.') : '-' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($data) == 0): ?>
            <tr>
                <td colspan="5" class="text-center">Tidak ada transaksi pada periode ini.</td>
            </tr>
            <?php else: ?>
            <tr style="background:#f1f5f9; font-weight:bold;">
                <td colspan="3" class="text-right">TOTAL MUTASI</td>
                <td class="text-right text-success"><?= number_format($totDebit ?? 0, 0, ',', '.') ?></td>
                <td class="text-right text-danger"><?= number_format($totKredit ?? 0, 0, ',', '.') ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('.dataTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
            paging: false,
            searching: false,
            info: false
        });
    });
</script>
<?php else: ?>
<div class="table-container" style="text-align: center; padding: 50px;">
    <i class="fas fa-hand-pointer text-muted" style="font-size: 3rem; margin-bottom: 15px;"></i>
    <p class="text-muted">Silakan pilih Akun COA dan klik Tampilkan untuk melihat Buku Besar.</p>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

