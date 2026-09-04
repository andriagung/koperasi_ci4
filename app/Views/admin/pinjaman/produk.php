<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">Master Produk Pinjaman</div>

<?php if(session()->getFlashdata('message')): ?>
    <div class="alert alert-success" style="padding:15px; margin-bottom:20px; background-color:#d1fae5; color:#065f46; border-radius:6px;">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>

<div class="card glass-card" style="padding: 20px;">
    <div style="display:flex; justify-content:space-between; margin-bottom:15px; align-items: center;">
        <h4 style="margin:0;">Daftar Produk Pinjaman</h4>
        <button class="btn btn-primary" onclick="bukaModal('modal-tambah')"><i class="fas fa-plus me-1"></i> Tambah Produk</button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm align-middle" id="table-produk" style="width:100%; border-collapse:collapse; text-align:left; font-size: 13px;">
            <thead>
                <tr style="background:#f1f5f9;">
                    <th style="padding:10px;">Kode</th>
                    <th style="padding:10px;">Nama Produk</th>
                    <th style="padding:10px;">Bunga</th>
                    <th style="padding:10px;">Tenor Max</th>
                    <th style="padding:10px;">Plafon Max</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($produk as $p): ?>
                <tr>
                    <td style="padding:10px; font-weight:600;"><?= esc($p['kode'] ?? '') ?></td>
                    <td style="padding:10px;"><?= esc($p['nama'] ?? '') ?></td>
                    <td style="padding:10px;"><?= esc($p['persentase_bunga'] ?? '') ?>% (<?= esc($p['jenis_bunga'] ?? '') ?>)</td>
                    <td style="padding:10px;"><?= esc($p['tenor_max'] ?? '') ?> bln</td>
                    <td style="padding:10px;">Rp <?= number_format($p['plafon_max'] ?? 0, 0, ',', '.') ?></td>
                    <td style="padding:10px;">
                        <span class="status-badge <?= $p['status'] == 'aktif' ? 'status-approved' : 'status-rejected' ?>"><?= esc($p['status'] ?? '') ?></span>
                    </td>
                    <td style="padding:10px; text-align: center;">
                        <button class="btn btn-sm btn-info" style="color:white;" onclick='editProduk(<?= json_encode($p) ?>)'><i class="fas fa-edit"></i> Edit</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Produk -->
<div class="modal-overlay" id="modal-tambah">
    <div class="modal-content" style="max-width: 800px;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah')"></i>
        <h3 id="modal-title" style="margin-bottom:20px; color:var(--primary);">Tambah Produk Pinjaman</h3>
        
    <form action="" method="POST">
        <?= csrf_field() ?>
            <input type="hidden" name="id" id="produk_id">
            
            <div class="grid-2-col">
                <div class="form-group">
                    <label>Kode Produk</label>
                    <input type="text" name="kode" id="produk_kode" required>
                </div>
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama" id="produk_nama" required>
                </div>
                
                <div class="form-group">
                    <label>Jenis Bunga</label>
                    <select name="jenis_bunga" id="produk_jenis_bunga">
                        <option value="flat">Flat</option>
                        <option value="efektif">Efektif / Menurun</option>
                        <option value="anuitas">Anuitas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Persentase Bunga (% per bulan)</label>
                    <input type="number" step="0.01" name="persentase_bunga" id="produk_persentase_bunga" required>
                </div>
                
                <div class="form-group">
                    <label>Plafon Minimal (Rp)</label>
                    <input type="number" name="plafon_min" id="produk_plafon_min" value="0">
                </div>
                <div class="form-group">
                    <label>Plafon Maksimal (Rp)</label>
                    <input type="number" name="plafon_max" id="produk_plafon_max" value="0">
                </div>
                
                <div class="form-group">
                    <label>Tenor Min (Bulan)</label>
                    <input type="number" name="tenor_min" id="produk_tenor_min" value="1">
                </div>
                <div class="form-group">
                    <label>Tenor Max (Bulan)</label>
                    <input type="number" name="tenor_max" id="produk_tenor_max" value="12">
                </div>
                
                <div class="form-group">
                    <label>Biaya Admin (Rp)</label>
                    <input type="number" name="biaya_admin" id="produk_biaya_admin" value="0">
                </div>
                <div class="form-group">
                    <label>Denda Keterlambatan (Rp)</label>
                    <input type="number" name="denda_keterlambatan" id="produk_denda" value="0">
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label>Status</label>
                    <select name="status" id="produk_status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn-primary" style="width:100%; margin-top:20px;">Simpan Produk</button>
        </form>
    </div>
</div>

<script>
function editProduk(data) {
    document.getElementById('modal-title').innerText = 'Edit Produk Pinjaman';
    document.getElementById('produk_id').value = data.id;
    document.getElementById('produk_kode').value = data.kode;
    document.getElementById('produk_nama').value = data.nama;
    document.getElementById('produk_jenis_bunga').value = data.jenis_bunga;
    document.getElementById('produk_persentase_bunga').value = data.persentase_bunga;
    document.getElementById('produk_plafon_min').value = data.plafon_min;
    document.getElementById('produk_plafon_max').value = data.plafon_max;
    document.getElementById('produk_tenor_min').value = data.tenor_min;
    document.getElementById('produk_tenor_max').value = data.tenor_max;
    document.getElementById('produk_biaya_admin').value = data.biaya_admin;
    document.getElementById('produk_denda').value = data.denda_keterlambatan;
    document.getElementById('produk_status').value = data.status;
    bukaModal('modal-tambah');
}
</script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-produk').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>
