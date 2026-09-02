<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div>
        <h1>Pusat Laporan Terpadu (Report Center)</h1>
        <p class="text-muted">Cetak dan Ekspor laporan operasional dan keuangan Koperasi</p>
    </div>
</div>

<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger" style="background:#fee2e2; color:#991b1b; padding:15px; border-radius:8px; margin-bottom:20px;">
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3 style="margin:0;"><i class="fas fa-filter text-primary"></i> Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/laporan/generate') ?>" method="POST" target="_blank">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Pilih Jenis Laporan <span style="color:red;">*</span></label>
                <select name="jenis_laporan" class="form-control" style="width: 100%; padding: 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px;" required>
                    <option value="">-- Pilih Jenis Laporan --</option>
                    <optgroup label="Modul Simpan Pinjam">
                        <option value="anggota">Laporan Data Anggota Baru</option>
                        <option value="simpanan">Laporan Transaksi Simpanan</option>
                        <option value="pinjaman">Laporan Pinjaman & Tunggakan</option>
                    </optgroup>
                    <optgroup label="Modul Waserda">
                        <option value="waserda">Laporan Penjualan Waserda</option>
                        <option value="slow_moving">Laporan Barang Mati (Slow-Moving)</option>
                    </optgroup>
                    <optgroup label="Modul Akuntansi">
                        <option value="labarugi">Laporan Laba Rugi (Hasil Usaha)</option>
                        <option value="neraca">Laporan Neraca</option>
                        <option value="bukubesar">Laporan Buku Besar</option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group" id="coa-selection" style="margin-bottom: 20px; display: none;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Pilih Akun (COA) <span style="color:red;">*</span></label>
                <select name="coa_id" class="form-control" style="width: 100%; padding: 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px;">
                    <option value="">-- Pilih Akun COA --</option>
                    <?php foreach($daftarCoa as $c): ?>
                        <option value="<?= $c['id'] ?? '' ?>"><?= $c['kode_akun'] ?? '' ?> - <?= $c['nama_akun'] ?? '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row" style="margin-bottom: 30px;">
                <div class="col-md-6">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Dari Tanggal <span style="color:red;">*</span></label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= esc($awal ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;" required>
                </div>
                <div class="col-md-6">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Sampai Tanggal <span style="color:red;">*</span></label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= esc($akhir ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;" required>
                </div>
            </div>

            <div style="display: flex; gap: 15px; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" name="format" value="pdf" class="btn-primary" style="flex: 1; padding: 15px; font-size: 16px; display: flex; justify-content: center; align-items: center; gap: 10px; background: #dc2626; border: none;">
                    <i class="fas fa-file-pdf"></i> Cetak Laporan (PDF)
                </button>
                <button type="submit" name="format" value="csv" class="btn-primary" style="flex: 1; padding: 15px; font-size: 16px; display: flex; justify-content: center; align-items: center; gap: 10px; background: #16a34a; border: none;">
                    <i class="fas fa-file-excel"></i> Ekspor ke Excel (CSV)
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
    document.querySelector('select[name="jenis_laporan"]').addEventListener('change', function() {
        if(this.value === 'bukubesar') {
            document.getElementById('coa-selection').style.display = 'block';
            document.querySelector('select[name="coa_id"]').required = true;
        } else {
            document.getElementById('coa-selection').style.display = 'none';
            document.querySelector('select[name="coa_id"]').required = false;
        }
    });
</script>

<?= $this->endSection() ?>
