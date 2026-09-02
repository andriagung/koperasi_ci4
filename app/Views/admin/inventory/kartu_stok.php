<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Kartu Stok per Lokasi
</div>

<div class="panel-view active">
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Pilih Lokasi</label>
                <select name="lokasi_id" class="form-control" style="width: 250px;">
                    <?php foreach($lokasi as $l): ?>
                        <option value="<?= $l['id'] ?? '' ?>" <?= $l['id'] == $lokasi_id ? 'selected' : '' ?>><?= esc($l['nama'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn-primary">Tampilkan Kartu Stok</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="display datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="text-align: right;">Stok Fisik</th>
                        <th style="text-align: right;">Batas Minimum</th>
                        <th style="text-align: right;">Nilai Aset (Rp)</th>
                        <th>Status Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stok as $row): 
                        $nilaiAset = $row['qty'] * $row['harga_beli'];
                    ?>
                    <tr>
                        <td style="font-weight: bold;"><?= esc($row['nama_produk'] ?? '') ?></td>
                        <td style="text-align: right;"><?= number_format($row['qty'] ?? 0, 0, ',', '.') ?></td>
                        <td style="text-align: right;"><?= number_format($row['stok_minimum'] ?? 0, 0, ',', '.') ?></td>
                        <td style="text-align: right;">Rp <?= number_format($nilaiAset ?? 0, 0, ',', '.') ?></td>
                        <td>
                            <?php if($row['qty'] <= 0): ?>
                                <span class="badge bg-danger">Habis</span>
                            <?php elseif($row['qty'] <= $row['stok_minimum']): ?>
                                <span class="badge bg-warning text-dark">Kritis</span>
                            <?php else: ?>
                                <span class="badge bg-success">Aman</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

