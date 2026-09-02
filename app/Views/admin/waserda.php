<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div id="view-waserda" class="panel-view active">
                <div class="page-title">Mesin Kasir Waserda (POS)</div>
                <div style="display: flex; gap: 20px;">
                    <div class="table-container" style="flex: 2;">
                        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <input type="text" id="posBarcode" placeholder="Scan Barcode / Ketik (Enter)" style="flex: 1; padding: 12px; border: 1px solid var(--border); border-radius: 8px;" onkeypress="handleBarcodeScan(event)">
                            <select id="posProdukSelect" style="flex: 1; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                                <option value="">-- Pilih Manual Produk --</option>
                                <?php foreach($waserda as $w): if($w['is_active']): ?>
                                    <option value="<?= idhash_encode($w['id'] ?? '') ?>" data-nama="<?= esc($w['nama_produk'] ?? '') ?>" data-harga="<?= $w['harga_promo'] > 0 ? $w['harga_promo'] : $w['harga_normal'] ?>" data-hargabeli="<?= $w['harga_beli'] ?? '' ?>" data-stok="<?= $w['stok'] ?? '' ?>">
                                        <?= esc($w['nama_produk'] ?? '') ?> - Rp <?= number_format($w['harga_promo'] > 0 ? $w['harga_promo'] : $w['harga_normal'], 0, ',', '.') ?> (Stok: <?= $w['stok'] ?? '' ?>)
                                    </option>
                                <?php endif; endforeach; ?>
                            </select>
                            <button class="btn-primary" onclick="tambahKeKeranjang()"><i class="fas fa-plus"></i> Tambah</button>
                        </div>
                        <div class="table-responsive">
                        <table class="display" style="width:100%" id="tabelPOS">
                            <thead>
                                <tr><th>Barang</th><th>Harga</th><th style="width:80px;">Qty</th><th>Subtotal</th><th>Aksi</th></tr>
                            </thead>
                            <tbody id="posKeranjang">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="table-container" style="flex: 1; background: #f8fafc; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Pembayaran</h3>
                            <div class="form-group">
                                <label>Pembeli (Anggota)</label>
                                <select id="posAnggota" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                                    <option value="">-- Kasir Tunai Umum (Tanpa Anggota) --</option>
                                    <?php foreach($anggota as $ag): ?>
                                    <option value="<?= idhash_encode($ag['id'] ?? '') ?>"><?= esc($ag['nama_lengkap'] ?? '') ?> - <?= esc($ag['nip'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small style="color: #16a34a; font-size: 0.75rem; display: block; margin-top: 5px;">* Pilih anggota untuk melihat limit kasbon</small>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; margin-bottom: 20px; border-top: 2px dashed var(--border); padding-top: 15px;">
                                <span>Total:</span>
                                <span style="color: var(--primary);" id="posTotalSpan">Rp 0</span>
                            </div>
                            <button class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem;" onclick="checkoutPOS('kasbon')"><i class="fas fa-money-check"></i> Bayar via Kasbon</button>
                            <button class="btn-primary" style="width: 100%; padding: 10px; font-size: 0.9rem; background: white; color: var(--text-main); border: 1px solid var(--border); margin-top: 10px;" onclick="checkoutPOS('tunai')"><i class="fas fa-wallet"></i> Bayar Tunai</button>
                        </div>
                    </div>
                </div>

            </div>
<?= $this->include('admin/waserda_modals') ?>
<?= $this->endSection() ?>
