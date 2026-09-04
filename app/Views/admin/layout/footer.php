    <!-- MODAL TAMBAH ANGGOTA -->
    <div class="modal-overlay" id="modal-tambah-anggota">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-anggota')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Registrasi Anggota Baru</h3>
            
    <form action="<?= base_url(\'admin/anggota/simpan\') ?>" method="POST">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" placeholder="Masukkan NIP (15 Digit)" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap dengan Gelar" required>
                </div>
                <div class="form-group">
                    <label>Divisi / Poliklinik</label>
                    <select name="divisi" required>
                        <option value="">Pilih Divisi</option>
                        <option value="IGD">IGD</option>
                        <option value="Poli Dalam">Poli Dalam</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Farmasi">Farmasi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>PIN Default Akun (6 Digit)</label>
                    <input type="password" value="123456" readonly style="background: #f1f5f9; cursor: not-allowed;">
                    <small style="color: var(--text-muted); display: block; margin-top: 5px;">PIN default ini akan digunakan anggota untuk login pertama kali.</small>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan Data Anggota</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT ANGGOTA -->
    <div class="modal-overlay" id="modal-edit-anggota">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-anggota')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Master Anggota</h3>
            
    <form action="" method="POST" id="form-edit-anggota">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" id="edit_nip" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_nama" required>
                </div>
                <div class="form-group">
                    <label>Divisi / Poliklinik</label>
                    <select name="divisi" id="edit_divisi" required>
                        <option value="">Pilih Divisi</option>
                        <option value="IGD">IGD</option>
                        <option value="Poli Dalam">Poli Dalam</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Farmasi">Farmasi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Keanggotaan</label>
                    <select name="status" id="edit_status" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Resign">Resign</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Perbarui Data Anggota</button>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH PROMO WASERDA -->
    <div class="modal-overlay" id="modal-tambah-promo">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-promo')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Tambah Produk/Promo Baru</h3>
            
    <form action="<?= base_url(\'admin/waserda/simpan-produk\') ?>" method="POST">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Produk / Paket</label>
                    <input type="text" name="nama_produk" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Beli (HPP)</label>
                        <input type="number" name="harga_beli" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Normal Jual</label>
                        <input type="number" name="harga_normal" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Harga Promo (Opsional)</label>
                    <input type="number" name="harga_promo" value="0">
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Stok Awal</label>
                        <input type="number" name="stok" value="0" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Batas Stok (Minimum)</label>
                        <input type="number" name="stok_minimum" value="5" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ikon (Class FontAwesome)</label>
                    <input type="text" name="ikon" placeholder="fas fa-box" value="fas fa-box">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" value="1" checked> Tampilkan di POS & Mobile Anggota</label>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan Produk</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PROMO WASERDA -->
    <div class="modal-overlay" id="modal-edit-promo">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-promo')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Produk/Promo</h3>
            
    <form action="" method="POST" id="form-edit-produk">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Produk / Paket</label>
                    <input type="text" name="nama_produk" id="edit_promo_nama" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Beli (HPP)</label>
                        <input type="number" name="harga_beli" id="edit_promo_beli" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Normal Jual</label>
                        <input type="number" name="harga_normal" id="edit_promo_normal" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Harga Promo (Opsional)</label>
                    <input type="number" name="harga_promo" id="edit_promo_harga">
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Stok Saat Ini (Hanya Admin)</label>
                        <input type="number" name="stok" id="edit_promo_stok" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Batas Stok Minimum</label>
                        <input type="number" name="stok_minimum" id="edit_promo_stokmin" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ikon (Class FontAwesome)</label>
                    <input type="text" name="ikon" id="edit_promo_ikon">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" id="edit_promo_active" value="1"> Tampilkan di POS & Mobile Anggota</label>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Update Produk</button>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH SUPPLIER -->
    <div class="modal-overlay" id="modal-tambah-supplier">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-supplier')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Tambah Supplier Baru</h3>
            
    <form action="<?= base_url(\'admin/tambah-supplier\') ?>" method="POST">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Kode Supplier</label>
                    <input type="text" name="kode_supplier" placeholder="Contoh: SUP-01" required>
                </div>
                <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" name="nama_supplier" required>
                </div>
                <div class="form-group">
                    <label>Kontak (No HP / Email)</label>
                    <input type="text" name="kontak">
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" style="width:100%; padding: 10px; border: 1px solid var(--border); border-radius:5px;"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan Supplier</button>
            </form>
        </div>
    </div>

    <!-- MODAL PURCHASE ORDER -->
    <div class="modal-overlay" id="modal-tambah-po">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-po')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Buat Purchase Order Baru</h3>
            
    <form action="<?= base_url(\'admin/tambah-po\') ?>" method="POST">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Pilih Supplier</label>
                    <select name="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?? '' ?>"><?= esc($s['nama_supplier'] ?? '') ?> (<?= esc($s['kode_supplier'] ?? '') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Produk yang Dibeli</label>
                    <select name="produk_id" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach($waserda as $w): ?>
                        <option value="<?= $w['id'] ?? '' ?>"><?= esc($w['nama_produk'] ?? '') ?> (Sisa: <?= $w['stok'] ?? '' ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Jumlah Masuk (Qty)</label>
                        <input type="number" name="jumlah" min="1" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Total Harga Tagihan (Rp)</label>
                        <input type="number" name="total_harga" min="1" required>
                    </div>
                </div>
                <p style="font-size: 0.8rem; color: #ef4444; margin-bottom:10px;"><i class="fas fa-info-circle"></i> PO ini akan memotong saldo Kas (1100) dan menambah Persediaan (1300).</p>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Selesaikan PO & Restock</button>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH ADMIN -->
    <div class="modal-overlay" id="modal-tambah-admin">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-admin')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Tambah Akses Admin</h3>
            
    <form action="<?= base_url(\'admin/tambah-admin\') ?>" method="POST">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required>
                </div>
                <div class="form-group">
                    <label>Username (NIP / Singkatan)</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role Akses</label>
                    <select name="role" required>
                        <option value="Admin Master">Admin Master</option>
                        <option value="Kasir Waserda">Kasir Waserda</option>
                        <option value="Petugas Simpan Pinjam">Petugas Simpan Pinjam</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan Admin</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT ADMIN -->
    <div class="modal-overlay" id="modal-edit-admin">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-admin')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Akses Admin</h3>
            
    <form action="" method="POST" id="form-edit-admin">
        <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_admin_nama" required>
                </div>
                <div class="form-group">
                    <label>Username (NIP / Singkatan)</label>
                    <input type="text" name="username" id="edit_admin_username" required>
                </div>
                <div class="form-group">
                    <label>Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password">
                </div>
                <div class="form-group">
                    <label>Role Akses</label>
                    <select name="role" id="edit_admin_role" required>
                        <option value="Admin Master">Admin Master</option>
                        <option value="Kasir Waserda">Kasir Waserda</option>
                        <option value="Petugas Simpan Pinjam">Petugas Simpan Pinjam</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Update Admin</button>
            </form>
        </div>
    </div>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Scripts: jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    
    <!-- Script: Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <?php
        $topLabels = []; $topData = [];
        if(isset($topWaserda) && is_array($topWaserda)) {
            foreach($topWaserda as $top) {
                $topLabels[] = $top['nama_produk'];
                $topData[] = $top['total_terjual'];
            }
        }
        $totalPersediaan = 0;
        if(isset($waserda) && is_array($waserda)) {
            foreach($waserda as $w) { $totalPersediaan += ($w['stok'] * $w['harga_beli']); }
        }
    ?>
    <script>
        window.AppConfig = {
            flashMessage: <?= json_encode(session()->getFlashdata('message') ?? '') ?>,
            flashError: <?= json_encode(session()->getFlashdata('error') ?? '') ?>,
            chartArusKas: {
                labels: <?= json_encode($chartArusKas['labels'] ?? []) ?>,
                pendapatan: <?= json_encode($chartArusKas['pendapatan'] ?? []) ?>,
                pengeluaran: <?= json_encode($chartArusKas['pengeluaran'] ?? []) ?>
            },
            neraca: {
                kas: <?= $neraca['kas'] ?? 0 ?>,
                piutang: <?= $neraca['piutang'] ?? 0 ?>,
                persediaanBarang: <?= $totalPersediaan ?? '' ?>
            },
            topWaserda: {
                labels: <?= json_encode($topLabels) ?>,
                data: <?= json_encode($topData) ?>
            }
        };
    </script>
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>
