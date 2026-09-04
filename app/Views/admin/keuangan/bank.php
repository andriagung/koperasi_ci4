<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Manajemen Rekening Bank
</div>

<div class="panel-view active">
    <button class="btn-primary" style="margin-bottom: 20px;" onclick="bukaModal('modal-tambah-bank')">
        <i class="fas fa-plus"></i> Tambah Rekening Bank
    </button>
    
    <div class="table-container">
        <div class="table-responsive">
            <table class="display datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama Bank</th>
                        <th>Nomor Rekening</th>
                        <th>Atas Nama</th>
                        <th style="text-align: right;">Saldo Sistem</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bank as $row): ?>
                    <tr>
                        <td><?= esc($row['nama_bank'] ?? '') ?></td>
                        <td><?= esc($row['nomor_rekening'] ?? '') ?></td>
                        <td><?= esc($row['atas_nama'] ?? '') ?></td>
                        <td style="text-align: right; font-weight: bold; color: #1d4ed8;">Rp <?= number_format($row['saldo'] ?? 0, 2, ',', '.') ?></td>
                        <td>
                            <?php if($row['status'] == 'aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="/admin/keuangan/mutasi-bank/<?= idhash_encode($row['id']) ?>" class="btn-action view" title="Lihat Mutasi"><i class="fas fa-list"></i></a>
                                <button class="btn-action edit" onclick="editBankModal(<?= $row['id'] ?? '' ?>, '<?= esc($row['nama_bank'] ?? '') ?>', '<?= esc($row['nomor_rekening'] ?? '') ?>', '<?= esc($row['atas_nama'] ?? '') ?>', '<?= esc($row['status'] ?? '') ?>')" title="Edit"><i class="fas fa-edit"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Bank -->
<div class="modal-overlay" id="modal-tambah-bank">
    <div class="modal-content">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-bank')"></i>
        <h3 style="margin-bottom: 20px; color: var(--primary);" id="modal-bank-title">Tambah Rekening Bank</h3>
        
    <form action="<?= base_url('admin/keuangan/simpan-bank') ?>" method="POST" id="form-bank">
        <?= csrf_field() ?>
            <input type="hidden" name="id" id="bank_id">
            <div class="form-group">
                <label>Nama Bank (Contoh: BCA, Mandiri)</label>
                <input type="text" name="nama_bank" id="bank_nama" required>
            </div>
            <div class="form-group">
                <label>Nomor Rekening</label>
                <input type="text" name="nomor_rekening" id="bank_rekening" required>
            </div>
            <div class="form-group">
                <label>Atas Nama</label>
                <input type="text" name="atas_nama" id="bank_an" required>
            </div>
            <div class="form-group" id="group-saldo-awal-bank">
                <label>Saldo Awal (Hanya untuk rekening baru)</label>
                <input type="number" name="saldo_awal" id="bank_saldo_awal" class="form-control" value="0" min="0">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="bank_status" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan</button>
        </form>
    </div>
</div>

<script>
function editBankModal(id, nama, rek, an, status) {
    document.getElementById('modal-bank-title').innerText = 'Edit Rekening Bank';
    document.getElementById('bank_id').value = id;
    document.getElementById('bank_nama').value = nama;
    document.getElementById('bank_rekening').value = rek;
    document.getElementById('bank_an').value = an;
    document.getElementById('bank_status').value = status;
    document.getElementById('group-saldo-awal-bank').style.display = 'none'; // Sembunyikan saldo awal jika edit
    bukaModal('modal-tambah-bank');
}
</script>
<?= $this->endSection() ?>

