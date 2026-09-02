<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Transfer Stok Antar Lokasi
</div>

<div class="panel-view active">
    <button class="btn-primary" style="margin-bottom: 20px;" onclick="bukaModal('modal-transfer')">
        <i class="fas fa-exchange-alt"></i> Buat Transfer Baru
    </button>
    
    <div class="table-container">
        <div class="table-responsive">
            <table class="display datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>No Transfer</th>
                        <th>Tanggal</th>
                        <th>Dari Lokasi</th>
                        <th>Ke Lokasi</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($riwayat as $row): ?>
                    <tr>
                        <td style="font-weight: bold;"><?= esc($row['nomor_transfer'] ?? '') ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= esc($row['asal'] ?? '') ?></td>
                        <td><?= esc($row['tujuan'] ?? '') ?></td>
                        <td><?= esc($row['keterangan'] ?? '') ?></td>
                        <td>
                            <span class="badge bg-success"><?= esc(ucfirst($row['status'])) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Transfer -->
<div class="modal-overlay" id="modal-transfer">
    <div class="modal-content">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-transfer')"></i>
        <h3 style="margin-bottom: 20px; color: var(--primary);">Transfer Stok</h3>
        
    <?= csrf_field() ?>
            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label>Dari Lokasi Asal</label>
                    <select name="lokasi_asal_id" required>
                        <option value="">-- Pilih Lokasi Asal --</option>
                        <?php foreach($lokasi as $l): ?>
                            <option value="<?= $l['id'] ?? '' ?>"><?= esc($l['nama'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Ke Lokasi Tujuan</label>
                    <select name="lokasi_tujuan_id" required>
                        <option value="">-- Pilih Lokasi Tujuan --</option>
                        <?php foreach($lokasi as $l): ?>
                            <option value="<?= $l['id'] ?? '' ?>"><?= esc($l['nama'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Pilih Produk</label>
                <select name="produk_id" required class="form-control select2">
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach($produk as $p): ?>
                        <option value="<?= $p['id'] ?? '' ?>"><?= esc($p['nama_produk'] ?? '') ?> (Barcode: <?= esc($p['barcode'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Jumlah Transfer (Qty)</label>
                <input type="number" name="qty" min="1" step="1" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Keterangan / Referensi (Opsional)</label>
                <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Restock Etalase Depan">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Proses Transfer</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

