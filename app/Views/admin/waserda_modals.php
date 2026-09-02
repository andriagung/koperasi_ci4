    <!-- MODAL TAMBAH PROMO WASERDA -->
    <div class="modal-overlay" id="modal-tambah-promo">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-promo')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Tambah Produk/Promo Baru</h3>
            <form action="/admin/tambah-produk" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Produk / Paket</label>
                    <input type="text" name="nama_produk" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Barcode (Kosongi untuk generate otomatis)</label>
                        <input type="text" name="barcode">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Kategori</label>
                        <select name="kategori_id">
                            <option value="">-- Tanpa Kategori --</option>
                            <?php if(isset($kategori)): foreach($kategori as $k): ?>
                            <option value="<?= $k['id'] ?? '' ?>"><?= esc($k['nama_kategori'] ?? '') ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
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
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Promo (Opsional)</label>
                        <input type="number" name="harga_promo" value="0">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Member Khusus</label>
                        <input type="number" name="harga_member" value="0">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Satuan</label>
                        <input type="text" name="satuan" value="pcs">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Supplier Dasar</label>
                        <select name="supplier_id">
                            <option value="">-- Tanpa Supplier --</option>
                            <?php if(isset($suppliers)): foreach($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?? '' ?>"><?= esc($s['nama_supplier'] ?? '') ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Tgl Kedaluwarsa (FEFO)</label>
                        <input type="date" name="tanggal_kadaluarsa">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Stok Awal</label>
                        <input type="number" name="stok" value="0">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Stok Minimum</label>
                        <input type="number" name="stok_minimum" value="5">
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Simpan Produk</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PROMO -->
    <div class="modal-overlay" id="modal-edit-promo">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-promo')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Produk/Promo</h3>
            <form id="form-edit-promo" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Produk / Paket</label>
                    <input type="text" id="edit_promo_nama" name="nama_produk" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Barcode</label>
                        <input type="text" id="edit_promo_barcode" name="barcode">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Kategori</label>
                        <select id="edit_promo_kategori" name="kategori_id">
                            <option value="">-- Tanpa Kategori --</option>
                            <?php if(isset($kategori)): foreach($kategori as $k): ?>
                            <option value="<?= $k['id'] ?? '' ?>"><?= esc($k['nama_kategori'] ?? '') ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Beli (HPP)</label>
                        <input type="number" id="edit_promo_beli" name="harga_beli" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Normal Jual</label>
                        <input type="number" id="edit_promo_normal" name="harga_normal" required>
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Promo</label>
                        <input type="number" id="edit_promo_harga" name="harga_promo">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Harga Member</label>
                        <input type="number" id="edit_promo_member" name="harga_member">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Satuan</label>
                        <input type="text" id="edit_promo_satuan" name="satuan">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Supplier Dasar</label>
                        <select id="edit_promo_supplier" name="supplier_id">
                            <option value="">-- Tanpa Supplier --</option>
                            <?php if(isset($suppliers)): foreach($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?? '' ?>"><?= esc($s['nama_supplier'] ?? '') ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Tgl Kedaluwarsa</label>
                        <input type="date" id="edit_promo_kadaluarsa" name="tanggal_kadaluarsa">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Stok Tersedia</label>
                        <input type="number" id="edit_promo_stok" name="stok">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Stok Minimum</label>
                        <input type="number" id="edit_promo_stokmin" name="stok_minimum">
                    </div>
                </div>
                <div class="form-group" style="flex-direction: row; align-items: center; gap: 10px; display: flex; margin-top:10px;">
                    <input type="checkbox" id="edit_promo_active" name="is_active" value="1">
                    <label style="margin:0;">Aktif Tampil di POS / Mobile</label>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Update Produk</button>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH SUPPLIER -->
    <div class="modal-overlay" id="modal-tambah-supplier">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-supplier')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Tambah Supplier Baru</h3>
            <form action="/admin/tambah-supplier" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Kode Supplier</label>
                    <input type="text" name="kode_supplier" required>
                </div>
                <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" name="nama_supplier" required>
                </div>
                <div class="form-group">
                    <label>Kontak (HP/Email)</label>
                    <input type="text" name="kontak" required>
                </div>
                <div class="form-group">
                    <label>NPWP (Opsional)</label>
                    <input type="text" name="npwp">
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Nama Bank</label>
                        <input type="text" name="nama_bank">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>No. Rekening</label>
                        <input type="text" name="rekening_bank">
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Simpan Supplier</button>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH PO -->
    <div class="modal-overlay" id="modal-tambah-po">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-po')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Buat Purchase Order Baru</h3>
            <form action="/admin/tambah-po" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                        <?php if(isset($suppliers)): foreach($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?? '' ?>"><?= esc($s['nama_supplier'] ?? '') ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Produk yang Dipesan</label>
                    <select name="produk_id" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                        <?php if(isset($waserda)): foreach($waserda as $w): ?>
                        <option value="<?= $w['id'] ?? '' ?>"><?= esc($w['nama_produk'] ?? '') ?> (Sisa: <?= $w['stok'] ?? '' ?>)</option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Jumlah Pesanan (Qty)</label>
                        <input type="number" name="jumlah" required min="1">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Total Harga Tagihan</label>
                        <input type="number" name="total_harga" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Simpan & Proses PO</button>
            </form>
        </div>
    </div>

    <!-- MODAL STOCK OPNAME -->
    <div class="modal-overlay" id="modal-stock-opname">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-stock-opname')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Form Stock Opname</h3>
            <form action="/admin/simpan-stock-opname" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Pilih Produk</label>
                    <select name="produk_id" id="opname_produk" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;" onchange="updateStokSistem()">
                        <option value="">-- Pilih Produk --</option>
                        <?php if(isset($waserda)): foreach($waserda as $w): ?>
                        <option value="<?= $w['id'] ?? '' ?>" data-stok="<?= $w['stok'] ?? '' ?>"><?= esc($w['nama_produk'] ?? '') ?> (Sistem: <?= $w['stok'] ?? '' ?>)</option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Stok di Sistem</label>
                        <input type="number" id="opname_stok_sistem" readonly style="background: #e2e8f0; color: #475569;">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Stok Fisik Aktual</label>
                        <input type="number" name="stok_fisik" id="opname_stok_fisik" required onkeyup="hitungSelisih()">
                    </div>
                </div>
                <div class="form-group">
                    <label>Selisih</label>
                    <input type="text" id="opname_selisih" readonly style="font-weight: bold; font-size: 1.1em;">
                </div>
                <div class="form-group">
                    <label>Keterangan Penyesuaian</label>
                    <textarea name="keterangan" placeholder="Misal: Barang rusak, hilang, dsb..." style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px; background: #ea580c;">Simpan Penyesuaian Stok</button>
            </form>
        </div>
    </div>
    <!-- MODAL UPDATE PO -->
    <div class="modal-overlay" id="modal-update-po">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-update-po')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Update Status PO</h3>
            <form id="form-update-po" method="POST">
                <?= csrf_field() ?>
                <!-- Input hidden for stock update -->
                <input type="hidden" name="produk_id" id="update_po_produk_id">
                <input type="hidden" name="jumlah" id="update_po_jumlah">

                <div class="form-group">
                    <label>Pilih Status Baru</label>
                    <select name="status" id="update_po_status" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                        <option value="Draft">Draft</option>
                        <option value="Dikirim">Dikirim</option>
                        <option value="Diterima Lengkap">Diterima Lengkap</option>
                        <option value="Dibayar">Dibayar</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Update Status PO</button>
            </form>
        </div>
    </div>
    <div class="modal-overlay" id="modal-edit-supplier">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-supplier')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Supplier</h3>
            <form id="form-edit-supplier" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Kode Supplier</label>
                    <input type="text" id="edit_supplier_kode" name="kode_supplier" required>
                </div>
                <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" id="edit_supplier_nama" name="nama_supplier" required>
                </div>
                <div class="form-group">
                    <label>Kontak (HP/Email)</label>
                    <input type="text" id="edit_supplier_kontak" name="kontak" required>
                </div>
                <div class="form-group">
                    <label>NPWP (Opsional)</label>
                    <input type="text" id="edit_supplier_npwp" name="npwp">
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Nama Bank</label>
                        <input type="text" id="edit_supplier_bank" name="nama_bank">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>No. Rekening</label>
                        <input type="text" id="edit_supplier_rek" name="rekening_bank">
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea id="edit_supplier_alamat" name="alamat" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Update Supplier</button>
            </form>
        </div>
    </div>

    <!-- MODAL RETUR PENJUALAN -->
    <div class="modal-overlay" id="modal-retur-penjualan">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-retur-penjualan')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Form Retur Penjualan</h3>
            <form action="/admin/waserda/retur-penjualan" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Produk yang Diretur</label>
                    <select name="produk_id" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                        <option value="">-- Pilih Produk --</option>
                        <?php if(isset($waserda)): foreach($waserda as $w): ?>
                        <option value="<?= $w['id'] ?? '' ?>"><?= esc($w['nama_produk'] ?? '') ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah Retur (Qty)</label>
                    <input type="number" name="jumlah" required min="1">
                </div>
                <div class="form-group">
                    <label>Alasan Retur</label>
                    <textarea name="alasan" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;" placeholder="Misal: Barang cacat, kadaluarsa..."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px; background: #ea580c;">Proses Retur</button>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div class="modal-overlay" id="modal-tambah-kategori">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-kategori')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Tambah Kategori</h3>
            <form action="/admin/waserda/tambah-kategori" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Simpan Kategori</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI -->
    <div class="modal-overlay" id="modal-edit-kategori">
        <div class="modal-content">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-kategori')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Kategori</h3>
            <form id="form-edit-kategori" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" id="edit_kategori_nama" name="nama_kategori" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi (Opsional)</label>
                    <textarea id="edit_kategori_deskripsi" name="deskripsi" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Update Kategori</button>
            </form>
        </div>
    </div>
    
    <script>
    function updateStokSistem() {
        var select = document.getElementById('opname_produk');
        var stok = select.options[select.selectedIndex].getAttribute('data-stok');
        document.getElementById('opname_stok_sistem').value = stok || 0;
        hitungSelisih();
    }
    
    function hitungSelisih() {
        var stokSistem = parseInt(document.getElementById('opname_stok_sistem').value) || 0;
        var stokFisik = parseInt(document.getElementById('opname_stok_fisik').value) || 0;
        var selisih = stokFisik - stokSistem;
        var elSelisih = document.getElementById('opname_selisih');
        
        if (selisih > 0) {
            elSelisih.value = "+" + selisih + " (Lebih)";
            elSelisih.style.color = "green";
        } else if (selisih < 0) {
            elSelisih.value = selisih + " (Kurang)";
            elSelisih.style.color = "red";
        } else {
            elSelisih.value = "0 (Sesuai)";
            elSelisih.style.color = "black";
        }
    }
    </script>
