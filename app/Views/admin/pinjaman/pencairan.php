<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    <a href="/admin/pinjaman/pengajuan" class="btn-primary" style="background:var(--text-muted); margin-right:15px; text-decoration:none;"><i class="fas fa-arrow-left"></i> Kembali</a>
    Proses Pencairan Pinjaman
</div>

<div class="card glass-card table-container" style="max-width:850px; margin:0 auto; padding:30px; border-radius: 16px;">
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:20px 25px; border-radius:12px; margin-bottom:25px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h4 style="color:#166534; margin:0 0 5px 0; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-check-circle"></i> Pinjaman Disetujui
            </h4>
            <p style="color:#15803d; margin:0; font-size: 0.9rem;">Siap untuk dicairkan ke <strong><?= esc($pinjaman['nama_lengkap'] ?? '') ?></strong> (<?= esc($pinjaman['nomor_anggota'] ?? '') ?>)</p>
        </div>
        <div style="text-align:right;">
            <p style="color:#166534; font-size:0.85rem; margin:0; font-weight: 500;">Nominal Disetujui:</p>
            <h2 style="color:#166534; margin:0; font-size: 1.5rem; font-weight: 700;">Rp <?= number_format($pinjaman['nominal_pengajuan'] ?? 0, 0, ',', '.') ?></h2>
        </div>
    </div>
    
    
    <?= csrf_field() ?>
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <input type="hidden" name="pinjaman_id" value="<?= $pinjaman['id'] ?? '' ?>">
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #334155;">Tanggal Pencairan <span style="color: #ef4444;">*</span></label>
                <input type="date" name="tanggal_pencairan" value="<?= date('Y-m-d') ?>" required style="width: 100%; font-size: 0.85rem; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #334155;">Metode Pencairan</label>
                <select name="metode_pembayaran" id="metode_pembayaran" onchange="toggleMetode()" required style="width: 100%; font-size: 0.85rem; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="Tunai">Tunai (Kas)</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                </select>
            </div>

            <div class="form-group" id="kas_div">
                <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #334155;">Sumber Dana Kas (Tunai) <span style="color: #ef4444;">*</span></label>
                <select name="kas_id" style="width: 100%; font-size: 0.85rem; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <?php foreach($kas as $k): ?>
                        <option value="<?= $k['id'] ?? '' ?>"><?= esc($k['nama'] ?? '') ?> (Saldo: Rp <?= number_format($k['saldo'] ?? 0, 0, ',', '.') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="bank_div" style="display:none;">
                <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #334155;">Sumber Dana Bank (Transfer) <span style="color: #ef4444;">*</span></label>
                <select name="bank_id" style="width: 100%; font-size: 0.85rem; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <?php if(!empty($bank)) { foreach($bank as $b): ?>
                        <option value="<?= $b['id'] ?? '' ?>"><?= esc($b['nama_bank'] ?? '') ?> - <?= esc($b['nomor_rekening'] ?? '') ?> (Saldo: Rp <?= number_format($b['saldo'] ?? 0, 0, ',', '.') ?>)</option>
                    <?php endforeach; } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #334155;">Potongan Biaya Admin (Rp)</label>
                <input type="number" name="biaya_admin" id="biaya_admin" value="50000" oninput="hitungTerima()" style="width: 100%; font-size: 0.85rem; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #334155;">Total Bersih Diterima Anggota (Rp)</label>
                <input type="text" id="nominal_diterima" value="<?= number_format($pinjaman['nominal_pengajuan'] - 50000, 0, ',', '.') ?>" readonly style="width: 100%; font-size: 0.95rem; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background:#f8fafc; font-weight:bold; color:var(--primary);">
            </div>
        </div>
        
        <div style="margin-top:25px; border-top:1px dashed #cbd5e1; padding-top:20px;">
            <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:18px; line-height: 1.5;"><i class="fas fa-info-circle text-primary"></i> Setelah pencairan, sistem secara otomatis menerbitkan jadwal angsuran (tenor <strong><?= $pinjaman['tenor_bulan'] ?? '' ?> bulan</strong>) dan mencatat transaksi kas/bank keluar di pembukuan.</p>
            
            <button type="submit" class="btn-primary" style="width:100%; padding:14px; font-size:1.05rem; font-weight: 600; border-radius: 10px; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);"><i class="fas fa-check-circle"></i> Cairkan Pinjaman Sekarang</button>
        </div>
    </form>
</div>

<script>
function toggleMetode() {
    let metode = document.getElementById('metode_pembayaran').value;
    if (metode === 'Tunai') {
        document.getElementById('kas_div').style.display = 'block';
        document.getElementById('bank_div').style.display = 'none';
    } else {
        document.getElementById('kas_div').style.display = 'none';
        document.getElementById('bank_div').style.display = 'block';
    }
}

function hitungTerima() {
    let plafon = <?= $pinjaman['nominal_pengajuan'] ?? 0 ?>;
    let admin = document.getElementById('biaya_admin').value || 0;
    let terima = plafon - parseInt(admin);
    document.getElementById('nominal_diterima').value = new Intl.NumberFormat('id-ID').format(terima);
}
</script>
<?= $this->endSection() ?>
