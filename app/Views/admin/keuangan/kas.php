<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Manajemen Kas (Tunai)
</div>

<div class="panel-view active">
    <button class="btn-primary" style="margin-bottom: 20px;" onclick="bukaModal('modal-tambah-kas')">
        <i class="fas fa-plus"></i> Tambah Kas Baru
    </button>
    
    <div class="table-container">
        <div class="table-responsive">
            <table class="display datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Kode Kas</th>
                        <th>Nama Kas</th>
                        <th style="text-align: right;">Saldo Saat Ini</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($kas as $row): ?>
                    <tr>
                        <td><?= esc($row['kode'] ?? '') ?></td>
                        <td><?= esc($row['nama'] ?? '') ?></td>
                        <td style="text-align: right; font-weight: bold; color: #166534;">Rp <?= number_format($row['saldo'] ?? 0, 2, ',', '.') ?></td>
                        <td>
                            <?php if($row['status'] == 'aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="/admin/keuangan/mutasi-kas/<?= idhash_encode($row['id']) ?>" class="btn-action view" title="Lihat Mutasi"><i class="fas fa-list"></i></a>
                                <button class="btn-action edit" onclick="editKasModal('<?= idhash_encode($row['id']) ?>', '<?= esc($row['kode'] ?? '') ?>', '<?= esc($row['nama'] ?? '') ?>', '<?= esc($row['status'] ?? '') ?>')" title="Edit"><i class="fas fa-edit"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Kas -->
<div class="modal-overlay" id="modal-tambah-kas">
    <div class="modal-content">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-kas')"></i>
        <h3 style="margin-bottom: 20px; color: var(--primary);" id="modal-kas-title">Tambah Kas</h3>
        
    <form action="<?= base_url('admin/keuangan/simpan-kas') ?>" method="POST" id="form-kas">
        <?= csrf_field() ?>
            <input type="hidden" name="id" id="kas_id">
            <div class="form-group">
                <label>Kode Kas</label>
                <input type="text" name="kode" id="kas_kode" required>
            </div>
            <div class="form-group">
                <label>Nama Kas (Misal: Kas Utama, Kas Kecil)</label>
                <input type="text" name="nama" id="kas_nama" required>
            </div>
            <div class="form-group" id="group-saldo-awal">
                <label>Saldo Awal (Hanya untuk Kas baru)</label>
                <input type="number" name="saldo_awal" id="kas_saldo_awal" class="form-control" value="0" min="0">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="kas_status" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan</button>
        </form>
    </div>
</div>

<script>
function editKasModal(id, kode, nama, status) {
    document.getElementById('modal-kas-title').innerText = 'Edit Kas';
    document.getElementById('kas_id').value = id;
    document.getElementById('kas_kode').value = kode;
    document.getElementById('kas_nama').value = nama;
    document.getElementById('kas_status').value = status;
    document.getElementById('group-saldo-awal').style.display = 'none'; // Sembunyikan saldo awal jika edit
    bukaModal('modal-tambah-kas');
}
</script>
<?= $this->endSection() ?>

