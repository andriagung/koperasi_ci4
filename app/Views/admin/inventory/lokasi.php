<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Manajemen Lokasi (Gudang & Toko)
</div>

<div class="panel-view active">
    <button class="btn-primary" style="margin-bottom: 20px;" onclick="bukaModal('modal-tambah-lokasi')">
        <i class="fas fa-plus"></i> Tambah Lokasi Baru
    </button>
    
    <div class="table-container">
        <div class="table-responsive">
            <table class="display datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Lokasi</th>
                        <th>Tipe</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lokasi as $row): ?>
                    <tr>
                        <td><?= esc($row['kode'] ?? '') ?></td>
                        <td><?= esc($row['nama'] ?? '') ?></td>
                        <td><?= esc(ucfirst($row['tipe'])) ?></td>
                        <td><?= esc($row['alamat'] ?? '') ?></td>
                        <td>
                            <?php if($row['status'] == 'aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-action edit" onclick="editLokasiModal(<?= $row['id'] ?? '' ?>, '<?= esc($row['kode'] ?? '') ?>', '<?= esc($row['nama'] ?? '') ?>', '<?= esc($row['tipe'] ?? '') ?>', '<?= esc($row['alamat'] ?? '') ?>', '<?= esc($row['status'] ?? '') ?>')"><i class="fas fa-edit"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Lokasi -->
<div class="modal-overlay" id="modal-tambah-lokasi">
    <div class="modal-content">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-lokasi')"></i>
        <h3 style="margin-bottom: 20px; color: var(--primary);" id="modal-lokasi-title">Tambah Lokasi</h3>
        
    <form action="" method="POST">
        <?= csrf_field() ?>
            <input type="hidden" name="id" id="lokasi_id">
            <div class="form-group">
                <label>Kode Lokasi</label>
                <input type="text" name="kode" id="lokasi_kode" required>
            </div>
            <div class="form-group">
                <label>Nama Lokasi</label>
                <input type="text" name="nama" id="lokasi_nama" required>
            </div>
            <div class="form-group">
                <label>Tipe Lokasi</label>
                <select name="tipe" id="lokasi_tipe" required>
                    <option value="gudang">Gudang Utama</option>
                    <option value="toko">Toko / POS</option>
                    <option value="cabang">Cabang</option>
                </select>
            </div>
            <div class="form-group">
                <label>Alamat / Keterangan</label>
                <textarea name="alamat" id="lokasi_alamat" rows="2" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="lokasi_status" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan</button>
        </form>
    </div>
</div>

<script>
function editLokasiModal(id, kode, nama, tipe, alamat, status) {
    document.getElementById('modal-lokasi-title').innerText = 'Edit Lokasi';
    document.getElementById('lokasi_id').value = id;
    document.getElementById('lokasi_kode').value = kode;
    document.getElementById('lokasi_nama').value = nama;
    document.getElementById('lokasi_tipe').value = tipe;
    document.getElementById('lokasi_alamat').value = alamat;
    document.getElementById('lokasi_status').value = status;
    bukaModal('modal-tambah-lokasi');
}
</script>
<?= $this->endSection() ?>

