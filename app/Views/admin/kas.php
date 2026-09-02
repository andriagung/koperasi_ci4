<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div id="view-kas" class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Transaksi Kas Manual</span>
        <button class="btn-primary" onclick="openKasModal()"><i class="fas fa-plus"></i> Tambah Transaksi</button>
    </div>
    
    <div class="alert" style="background-color: #e0f2fe; color: #0284c7; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0284c7;">
        <i class="fas fa-info-circle"></i> Gunakan menu ini untuk mencatat penerimaan/pengeluaran kas di luar operasional utama (contoh: biaya listrik, bayar honor, dll). Setiap transaksi akan otomatis masuk ke Jurnal Umum.
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table id="tabel-kas" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Akun Lawan</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Kas -->
<div id="modal-kas" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 10px;">
        <span class="close" onclick="closeKasModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2 style="margin-top: 0;">Input Transaksi Kas</h2>
        
    <?= csrf_field() ?>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Jenis Transaksi</label>
                <select name="jenis" class="form-control" style="width: 100%; padding: 8px;" required>
                    <option value="Masuk">Kas Masuk (Debit Kas, Kredit Lawan)</option>
                    <option value="Keluar">Kas Keluar (Kredit Kas, Debit Lawan)</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 8px;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Nominal (Rp)</label>
                <input type="text" name="nominal" class="form-control" id="kas-nominal" onkeyup="formatRupiahInput(this)" style="width: 100%; padding: 8px;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Akun Tujuan (Lawan)</label>
                <select name="akun_lawan_id" class="form-control" style="width: 100%; padding: 8px;" required>
                    <option value="">-- Pilih Akun --</option>
                    <?php foreach($akunLawan as $akun): ?>
                        <option value="<?= $akun['id'] ?? '' ?>"><?= $akun['kode_akun'] ?? '' ?> - <?= $akun['nama_akun'] ?? '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" style="width: 100%; padding: 8px;" required></textarea>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn-danger" onclick="closeKasModal()">Batal</button>
                <button type="submit" class="btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openKasModal() {
        document.getElementById('modal-kas').style.display = 'block';
    }
    function closeKasModal() {
        document.getElementById('modal-kas').style.display = 'none';
        document.getElementById('form-kas').reset();
    }
    
    // Close modal if clicked outside
    window.onclick = function(event) {
        let modal = document.getElementById('modal-kas');
        if (event.target == modal) {
            closeKasModal();
        }
    }
</script>
<?= $this->endSection() ?>

