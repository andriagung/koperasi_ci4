<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Stock Opname per Lokasi
</div>

<div class="panel-view active">
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <form action="" method="POST">
        <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Pilih Lokasi untuk Opname</label>
                <select name="lokasi_id" class="form-control" style="width: 250px;">
                    <?php foreach($lokasi as $l): ?>
                        <option value="<?= $l['id'] ?? '' ?>" <?= $l['id'] == $lokasi_id ? 'selected' : '' ?>><?= esc($l['nama'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn-primary">Muat Data Stok</button>
            </div>
        </form>
    </div>

    
    <form action="" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="lokasi_id" value="<?= $lokasi_id ?? '' ?>">
        <div class="table-container">
            <div class="table-responsive">
                <table class="display datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th style="text-align: center;">Stok Sistem (S)</th>
                            <th style="text-align: center; width: 150px;">Stok Fisik (F)</th>
                            <th style="text-align: center;">Selisih (F - S)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stok as $i => $row): ?>
                        <tr>
                            <td style="font-weight: bold;">
                                <?= esc($row['nama_produk'] ?? '') ?>
                                <input type="hidden" name="produk_id[]" value="<?= $row['produk_id'] ?? '' ?>">
                                <input type="hidden" name="stok_sistem[]" value="<?= $row['qty'] ?? '' ?>" id="stok_sistem_<?= $i ?? '' ?>">
                            </td>
                            <td style="text-align: center; font-size: 1.1rem;"><?= number_format($row['qty'] ?? 0, 0, ',', '.') ?></td>
                            <td style="text-align: center;">
                                <input type="number" name="stok_fisik[]" class="form-control" value="<?= $row['qty'] ?? '' ?>" min="0" step="1" id="stok_fisik_<?= $i ?? '' ?>" oninput="hitungSelisih(<?= $i ?? '' ?>)" style="text-align: center; font-weight: bold;">
                            </td>
                            <td style="text-align: center;">
                                <span id="selisih_<?= $i ?? '' ?>" class="badge bg-secondary" style="font-size: 1rem;">0</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn-primary" style="padding: 15px 30px; font-size: 1.1rem;" onclick="return confirm('Apakah Anda yakin data fisik sudah benar? Penyesuaian stok akan dilakukan secara permanen.')">
                    <i class="fas fa-check-circle"></i> Simpan Hasil Opname & Sesuaikan Stok
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function hitungSelisih(index) {
    let sistem = parseInt(document.getElementById('stok_sistem_' + index).value) || 0;
    let fisik = parseInt(document.getElementById('stok_fisik_' + index).value) || 0;
    let selisih = fisik - sistem;
    
    let span = document.getElementById('selisih_' + index);
    span.innerText = selisih > 0 ? '+' + selisih : selisih;
    
    if (selisih > 0) {
        span.className = 'badge bg-success';
    } else if (selisih < 0) {
        span.className = 'badge bg-danger';
    } else {
        span.className = 'badge bg-secondary';
    }
}
</script>
<?= $this->endSection() ?>

