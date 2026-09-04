<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">Master Jenis Simpanan</div>

<?php if(session()->getFlashdata('message')): ?>
    <div class="alert alert-success" style="padding:15px; margin-bottom:20px; background-color:#d1fae5; color:#065f46; border-radius:6px;">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>

<div class="panel-view active">
    <div class="table-container">
        <div style="display:flex; justify-content:space-between; margin-bottom:15px; align-items:center;">
            <h4 style="margin:0;">Daftar Jenis Simpanan</h4>
            <button class="btn-primary" onclick="bukaModal('modal-tambah')"><i class="fas fa-plus"></i> Tambah</button>
        </div>
        
        <div class="table-responsive">
            <table id="tabelJenisSimpanan" class="display" style="width:100%; text-align:left; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid #e2e8f0;">
                        <th style="padding:10px;">Kode</th>
                        <th style="padding:10px;">Nama</th>
                        <th style="padding:10px;">Sifat</th>
                        <th style="padding:10px; text-align:right;">Nominal Default</th>
                        <th style="padding:10px; text-align:center;">Bisa Ditarik?</th>
                        <th style="padding:10px;">Status</th>
                        <th style="padding:10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($jenis as $j): ?>
                    <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:10px;"><?= esc($j['kode'] ?? '') ?></td>
                        <td style="padding:10px; font-weight:600;"><?= esc($j['nama'] ?? '') ?></td>
                        <td style="padding:10px; text-transform:capitalize;"><?= esc($j['jenis'] ?? '') ?></td>
                        <td style="padding:10px; text-align:right;">Rp <?= number_format($j['nominal_default'] ?? 0, 0, ',', '.') ?></td>
                        <td style="padding:10px; text-align:center;">
                            <?= $j['dapat_ditarik'] ? '<span style="color:#16a34a;"><i class="fas fa-check"></i> Ya</span>' : '<span style="color:#dc2626;"><i class="fas fa-times"></i> Tidak</span>' ?>
                        </td>
                        <td style="padding:10px;">
                            <span class="status-badge <?= $j['status'] == 'aktif' ? 'status-approved' : 'status-rejected' ?>"><?= esc($j['status'] ?? '') ?></span>
                        </td>
                        <td style="padding:10px;">
                            <button class="btn-action edit" onclick='editJenis(<?= json_encode($j) ?>)'><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal-overlay" id="modal-tambah">
    <div class="modal-content" style="width: 800px; max-width: 95%;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah')"></i>
        <h3 id="modal-title" style="margin-bottom:20px; color:var(--primary);">Tambah Jenis Simpanan</h3>
        
    <form action="" method="POST">
        <?= csrf_field() ?>
            <input type="hidden" name="id" id="jenis_id">
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>Kode Unik</label>
                    <input type="text" name="kode" id="jenis_kode" required>
                </div>
                
                <div class="form-group">
                    <label>Nama Simpanan</label>
                    <input type="text" name="nama" id="jenis_nama" required>
                </div>
                
                <div class="form-group">
                    <label>Sifat (Jenis)</label>
                    <select name="jenis" id="jenis_sifat" required>
                        <option value="pokok">Simpanan Pokok (Sekali diawal)</option>
                        <option value="wajib">Simpanan Wajib (Rutin)</option>
                        <option value="sukarela">Simpanan Sukarela (Bebas)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="jenis_status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nominal Default (Rp)</label>
                    <input type="number" name="nominal_default" id="jenis_nominal" value="0">
                </div>
                
                <div class="form-group">
                    <label>Minimal Saldo (Rp)</label>
                    <input type="number" name="minimal_saldo" id="jenis_minimal" value="0">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 10px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="dapat_ditarik" id="jenis_tarik" value="1" style="width:auto;">
                    <span>Dapat ditarik sewaktu-waktu oleh anggota</span>
                </label>
            </div>
            
            <button type="submit" class="btn-primary" style="width:100%; margin-top:20px;">Simpan</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#tabelJenisSimpanan').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json" }
    });
});
function editJenis(data) {
    document.getElementById('modal-title').innerText = 'Edit Jenis Simpanan';
    document.getElementById('jenis_id').value = data.id;
    document.getElementById('jenis_kode').value = data.kode;
    document.getElementById('jenis_nama').value = data.nama;
    document.getElementById('jenis_sifat').value = data.jenis;
    document.getElementById('jenis_nominal').value = data.nominal_default;
    document.getElementById('jenis_minimal').value = data.minimal_saldo;
    document.getElementById('jenis_tarik').checked = (data.dapat_ditarik == 1);
    document.getElementById('jenis_status').value = data.status;
    bukaModal('modal-tambah');
}
</script>
<?= $this->endSection() ?>
