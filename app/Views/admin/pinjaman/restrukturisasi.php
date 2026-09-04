<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">Restrukturisasi Pinjaman</div>

<?php if(session()->getFlashdata('message')): ?>
    <div class="alert alert-success" style="padding:15px; margin-bottom:20px; background-color:#d1fae5; color:#065f46; border-radius:6px;">
        <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" style="padding:15px; margin-bottom:20px; background-color:#fee2e2; color:#991b1b; border-radius:6px;">
        <i class="fas fa-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="card glass-card table-container" style="padding: 25px;">
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px; line-height: 1.5;">
        <i class="fas fa-info-circle text-primary"></i> Modul ini digunakan untuk melakukan penyesuaian/penjadwalan ulang kredit pinjaman anggota (restrukturisasi) dengan memperpanjang tenor atau menyesuaikan suku bunga agar pembayaran cicilan menjadi lebih ringan.
    </p>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm align-middle" id="table-restruk" style="font-size: 13px; width: 100%;">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th>No</th>
                <th>Anggota</th>
                <th>Tanggal Cair</th>
                <th style="text-align: right;">Total Pinjaman</th>
                <th style="text-align: right;">Sisa Pokok</th>
                <th style="text-align: center;">Tenor</th>
                <th>Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(!empty($list_pinjaman)):
                $no = 1;
                foreach($list_pinjaman as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= esc($p['nama_lengkap'] ?? '') ?></strong><br><small style="color:var(--text-muted);"><?= esc($p['nip'] ?? '') ?></small></td>
                    <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                    <td style="text-align: right;">Rp <?= number_format($p['nominal_pengajuan'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: bold; color: var(--primary);">Rp <?= number_format($p['sisa_pokok'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: center;"><?= esc($p['tenor_bulan'] ?? '') ?> Bulan</td>
                    <td><span class="status-badge" style="background:#3b82f6; color:#fff;">Aktif</span></td>
                    <td style="text-align: center;">
                        <button class="btn btn-sm btn-primary" style="background:#3b82f6; padding:6px 12px; font-size:12px; border-radius: 6px;" onclick="bukaModal('modal-restruk-<?= idhash_encode($p['id']) ?>')">
                            <i class="fas fa-sync-alt"></i> Restruk
                        </button>
                    </td>
                </tr>
                
                <!-- Modal Restruk -->
                <div class="modal-overlay" id="modal-restruk-<?= idhash_encode($p['id']) ?>">
                    <div class="modal-content" style="max-width: 800px; text-align: left; padding: 25px; border-radius: 16px;">
                        <i class="fas fa-times modal-close" onclick="tutupModal('modal-restruk-<?= idhash_encode($p['id']) ?>')"></i>
                        <h3 style="margin-bottom: 15px; color: var(--primary); font-size: 1.2rem; font-weight: 600;"><i class="fas fa-sync-alt"></i> Form Restrukturisasi Pinjaman</h3>
                        <p style="color:#64748b; font-size:0.85rem; margin-bottom:20px; line-height: 1.5;">Proses ini akan mengonversi sisa pokok pinjaman saat ini menjadi pinjaman baru dengan tenor dan bunga yang disesuaikan. Pinjaman sebelumnya akan ditutup dengan status <strong>"RESTRUCTURED"</strong>.</p>
                        
                        
    <form action="" method="POST">
        <?= csrf_field() ?>
                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                            <input type="hidden" name="pinjaman_id" value="<?= idhash_encode($p['id']) ?>">
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 5px;">Sisa Pokok Outstanding</label>
                                <input type="text" value="Rp <?= number_format($p['sisa_pokok'] ?? 0, 0, ',', '.') ?>" class="form-control" readonly style="background:#f8fafc; font-weight: bold; color: var(--primary); font-size: 0.9rem; padding: 8px 12px;">
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                <div class="form-group">
                                    <label style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 5px;">Tenor Baru (Bulan) <span style="color:#ef4444;">*</span></label>
                                    <input type="number" name="tenor_baru" class="form-control" required min="1" max="120" value="<?= esc($p['tenor_bulan'] ?? '') + 12 ?>" style="font-size: 0.85rem; padding: 8px 12px; border-radius: 8px;">
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 5px;">Bunga Baru (% / Bulan) <span style="color:#ef4444;">*</span></label>
                                    <input type="number" step="0.01" name="bunga_baru" class="form-control" required min="0" value="1.00" style="font-size: 0.85rem; padding: 8px 12px; border-radius: 8px;">
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 5px;">Alasan Restrukturisasi <span style="color:#ef4444;">*</span></label>
                                <textarea name="alasan" rows="3" class="form-control" required placeholder="Contoh: Penurunan omzet usaha anggota, penyesuaian kemampuan bayar..." style="font-size: 0.85rem; padding: 8px 12px; border-radius: 8px;"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem; font-weight: 600; border-radius: 8px;">
                                <i class="fas fa-check-circle me-1"></i> Proses Restrukturisasi Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-restruk').DataTable();
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>

