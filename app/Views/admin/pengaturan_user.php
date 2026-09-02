<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title"><i class="fas fa-shield-alt"></i> Pengaturan Admin (Role)</h4>
                <button class="btn btn-primary" onclick="bukaModal('modal-tambah-admin')"><i class="fas fa-plus me-1"></i> Tambah Akses Admin</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-admin-users" class="table table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH ADMIN -->
<div class="modal-overlay" id="modal-tambah-admin">
    <div class="modal-content">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-admin')"></i>
        <h4 class="mb-4 text-primary">Tambah Akses Admin</h4>
        <form action="<?= base_url('admin/tambah-admin') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username (NIP / Singkatan)</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role Akses</label>
                <select name="role" class="form-select" required>
                    <option value="Super Admin">Super Admin</option>
                    <option value="Admin">Admin</option>
                    <option value="Kasir">Kasir (Waserda)</option>
                    <option value="Gudang">Gudang</option>
                    <option value="Teller">Teller</option>
                    <option value="Petugas Kredit">Petugas Kredit / Simpan Pinjam</option>
                    <option value="Akuntansi">Akuntansi</option>
                    <option value="Pengurus">Pengurus</option>
                    <option value="Manajer">Manajer</option>
                    <option value="Bendahara">Bendahara</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Simpan Admin</button>
        </form>
    </div>
</div>

<!-- MODAL EDIT ADMIN -->
<div class="modal-overlay" id="modal-edit-admin">
    <div class="modal-content">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-admin')"></i>
        <h4 class="mb-4 text-primary">Edit Akses Admin</h4>
        <form id="form-edit-admin" action="" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="edit_admin_nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username (NIP / Singkatan)</label>
                <input type="text" name="username" id="edit_admin_username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Role Akses</label>
                <select name="role" id="edit_admin_role" class="form-select" required>
                    <option value="Super Admin">Super Admin</option>
                    <option value="Admin">Admin</option>
                    <option value="Kasir">Kasir (Waserda)</option>
                    <option value="Gudang">Gudang</option>
                    <option value="Teller">Teller</option>
                    <option value="Petugas Kredit">Petugas Kredit / Simpan Pinjam</option>
                    <option value="Akuntansi">Akuntansi</option>
                    <option value="Pengurus">Pengurus</option>
                    <option value="Manajer">Manajer</option>
                    <option value="Bendahara">Bendahara</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Update Admin</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
