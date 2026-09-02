<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    <a href="/admin/pinjaman/pengajuan" class="btn-primary" style="background:var(--text-muted); margin-right:15px; text-decoration:none;"><i class="fas fa-arrow-left"></i> Kembali</a>
    Proses Verifikasi & Approval Pinjaman
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:25px;">
    <!-- Data Pengajuan -->
    <div class="card glass-card table-container" style="padding:25px; margin-bottom: 0; border-radius: 16px;">
        <h4 style="margin-bottom:15px; border-bottom:2px solid #f1f5f9; padding-bottom:10px; font-size: 1.05rem; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-user-circle text-primary"></i> Informasi Pemohon
        </h4>
        <table style="width:100%; line-height:2.0; font-size: 0.9rem;">
            <tr><td style="width:38%; color:var(--text-muted);">Nama Anggota</td> <td style="font-weight:600; color:#0f172a;"><?= esc($pinjaman['nama_lengkap'] ?? '') ?></td></tr>
            <tr><td style="color:var(--text-muted);">NIP</td> <td><?= esc($pinjaman['nip'] ?? '-') ?></td></tr>
            <tr><td style="color:var(--text-muted);">Divisi</td> <td><?= esc($pinjaman['divisi'] ?? '-') ?></td></tr>
            <tr><td style="color:var(--text-muted);">No HP</td> <td><?= esc($pinjaman['no_hp'] ?? '-') ?></td></tr>
        </table>
        
        <h4 style="margin-top:25px; margin-bottom:15px; border-bottom:2px solid #f1f5f9; padding-bottom:10px; font-size: 1.05rem; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-invoice-dollar text-primary"></i> Detail Pengajuan Pinjaman
        </h4>
        <table style="width:100%; line-height:2.0; font-size: 0.9rem;">
            <tr><td style="width:38%; color:var(--text-muted);">Tgl Pengajuan</td> <td><?= date('d M Y', strtotime($pinjaman['created_at'])) ?></td></tr>
            <tr><td style="color:var(--text-muted);">Plafon Pinjaman</td> <td style="font-weight:bold; font-size:1.15rem; color:var(--primary);">Rp <?= number_format($pinjaman['nominal_pengajuan'] ?? 0, 0, ',', '.') ?></td></tr>
            <tr><td style="color:var(--text-muted);">Tenor Diajukan</td> <td style="font-weight: 600;"><?= esc($pinjaman['tenor_bulan'] ?? '') ?> Bulan</td></tr>
            <tr><td style="color:var(--text-muted);">Tujuan Pinjaman</td> <td style="font-style: italic;"><?= esc($pinjaman['tujuan_pinjaman'] ?? '-') ?></td></tr>
            <tr><td style="color:var(--text-muted);">Status Saat Ini</td> 
                <td>
                    <span class="status-badge <?= $pinjaman['status_pengajuan'] == 'APPROVED' ? 'status-approved' : ($pinjaman['status_pengajuan'] == 'SUBMITTED' ? 'status-pending' : 'status-rejected') ?>">
                        <?= esc($pinjaman['status_pengajuan'] ?? '') ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Analisis Kredit & Aksi -->
    <div class="card glass-card table-container" style="padding:25px; margin-bottom: 0; border-radius: 16px;">
        <h4 style="margin-bottom:15px; border-bottom:2px solid #f1f5f9; padding-bottom:10px; font-size: 1.05rem; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-chart-line text-primary"></i> Analisis Finansial (Credit Scoring)
        </h4>
        
        <?php if(!empty($analisis)): ?>
            <div style="background: #f8fafc; padding:18px; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:20px;">
                <table style="width:100%; line-height:2.0; font-size: 0.9rem;">
                    <tr><td style="width:50%; color:var(--text-muted);">Pendapatan Bulanan</td> <td style="text-align:right; font-weight: 500;">Rp <?= number_format($analisis['pendapatan_bulanan'] ?? 0, 0, ',', '.') ?></td></tr>
                    <tr><td style="color:var(--text-muted);">Pengeluaran Bulanan</td> <td style="text-align:right; font-weight: 500;">Rp <?= number_format($analisis['pengeluaran_bulanan'] ?? 0, 0, ',', '.') ?></td></tr>
                    <tr><td style="color:var(--text-muted);">Angsuran Lainnya</td> <td style="text-align:right; font-weight: 500;">Rp <?= number_format($analisis['angsuran_lain'] ?? 0, 0, ',', '.') ?></td></tr>
                </table>
                <hr style="margin:12px 0; border:0; border-top:1px dashed #cbd5e1;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight:600; color:#0f172a; font-size: 0.95rem;">Debt Service Ratio (DSR)</span>
                    <span style="font-weight:bold; font-size:1.25rem; <?= $analisis['dsr_score'] > 35 ? 'color:#dc2626;' : 'color:#16a34a;' ?>">
                        <?= number_format($analisis['dsr_score'] ?? 0, 1) ?>%
                    </span>
                </div>
                <div style="margin-top:12px; font-size:0.85rem; padding: 8px 12px; border-radius: 8px; background: <?= $analisis['rekomendasi'] == 'Aman' ? '#f0fdf4' : '#fef2f2' ?>; color: <?= $analisis['rekomendasi'] == 'Aman' ? '#16a34a' : '#dc2626' ?>;">
                    <i class="fas <?= $analisis['rekomendasi'] == 'Aman' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i> Rekomendasi Sistem: <strong><?= esc($analisis['rekomendasi'] ?? '') ?></strong><br>
                    <span style="color: #64748b; font-size: 0.8rem;"><?= esc($analisis['catatan_analis'] ?? '') ?></span>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; margin-bottom: 20px; text-align: center;">
                <p style="color:var(--text-muted); font-style:italic; margin: 0;"><i class="fas fa-info-circle"></i> Data analisis finansial DSR belum tercatat pada pengajuan ini.</p>
            </div>
        <?php endif; ?>
        
        <?php if($pinjaman['status_pengajuan'] == 'SUBMITTED'): ?>
            <h4 style="margin-bottom:15px; border-bottom:2px solid #f1f5f9; padding-bottom:10px; margin-top:20px; font-size: 1.05rem; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-gavel text-primary"></i> Keputusan Komite Kredit
            </h4>
            
    <?= csrf_field() ?>" method="POST" id="form-approve">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <div class="form-group">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Catatan Persetujuan / Verifikasi</label>
                    <textarea name="catatan" rows="3" placeholder="Misal: Dokumen lengkap, limit masih mencukupi, disetujui..." style="width: 100%; font-size: 0.85rem; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
                </div>
                
                <div style="display:flex; gap:12px; margin-top:15px;">
                    <button type="submit" class="btn-primary" style="flex:1; background:#16a34a; border-radius: 8px; padding: 12px; font-weight: 600; justify-content: center;"><i class="fas fa-check-circle"></i> Setujui Pengajuan</button>
                    <button type="button" class="btn-action" style="flex:1; background:#dc2626; color:white; border:none; border-radius:8px; cursor:pointer; height: auto; padding: 12px; font-weight: 600; font-size: 0.9rem;" onclick="showTolakModal()"><i class="fas fa-times-circle"></i> Tolak Pengajuan</button>
                </div>
            </form>
            
            
    <?= csrf_field() ?>" method="POST" id="form-reject" style="display:none; margin-top:15px; background:#fee2e2; padding:18px; border-radius:12px; border: 1px solid #fca5a5;">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <div class="form-group">
                    <label style="color:#991b1b; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px;">Alasan Penolakan <span style="color:#dc2626;">*</span></label>
                    <textarea name="catatan" rows="3" required placeholder="Wajib diisi mengapa pengajuan pinjaman ini ditolak..." style="width: 100%; font-size: 0.85rem; padding: 10px 12px; border-radius: 8px; border: 1px solid #f87171;"></textarea>
                </div>
                <div style="display:flex; gap:10px; margin-top:12px;">
                    <button type="submit" class="btn-action" style="flex:1; background:#991b1b; color:white; border:none; border-radius:8px; cursor:pointer; height: auto; padding: 10px; font-weight: 600; font-size: 0.85rem;">Konfirmasi Tolak</button>
                    <button type="button" class="btn-action" style="flex:1; background:#cbd5e1; color:#333; border:none; border-radius:8px; cursor:pointer; height: auto; padding: 10px; font-weight: 600; font-size: 0.85rem;" onclick="hideTolakModal()">Batal</button>
                </div>
            </form>
        <?php elseif($pinjaman['status_pengajuan'] == 'APPROVED'): ?>
            <div style="background:#f0f9ff; padding:25px; text-align:center; border-radius:12px; border:1px solid #bae6fd; margin-top: 15px;">
                <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
                    <i class="fas fa-check-double"></i>
                </div>
                <h4 style="color:#0369a1; margin-bottom:8px; font-size: 1.1rem; font-weight: 600;">Pengajuan Telah Disetujui</h4>
                <p style="color:#0c4a6e; font-size:0.88rem; margin-bottom:18px; line-height: 1.5;">Langkah berikutnya adalah mencairkan dana ke anggota (pilih sumber Kas atau Bank) dan membuat jadwal angsuran.</p>
                <a href="/admin/pinjaman/pencairan/<?= idhash_encode($pinjaman['id']) ?>" class="btn-primary" style="display:inline-flex; align-items: center; gap: 8px; text-decoration:none; padding: 12px 24px; border-radius: 8px; font-weight: 600;"><i class="fas fa-money-bill-wave"></i> Proses Pencairan Dana</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showTolakModal() {
    document.getElementById('form-approve').style.display = 'none';
    document.getElementById('form-reject').style.display = 'block';
}
function hideTolakModal() {
    document.getElementById('form-approve').style.display = 'block';
    document.getElementById('form-reject').style.display = 'none';
}
</script>
<?= $this->endSection() ?>
