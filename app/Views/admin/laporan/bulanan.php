<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span><?= $judul ?? '' ?></span>
    </div>
    
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Tanggal Awal</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $awal ?? '' ?>" required>
            </div>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $akhir ?? '' ?>" required>
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter Laporan</button>
            </div>
        </form>
    </div>

    <div class="alert alert-info" style="background:#e0f2fe; color:#0369a1; padding:15px; border-radius:8px; margin-bottom:20px; border-left:4px solid #0284c7;">
        <i class="fas fa-info-circle"></i> Ini adalah rangkuman dari seluruh operasional Koperasi. Klik tombol Export PDF di bawah untuk mengunduh laporan eksekutif lengkap (1 Klik).
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Ringkasan Data (<?= date('d/m/Y', strtotime($awal)) ?> - <?= date('d/m/Y', strtotime($akhir)) ?>)</h3>
            
    <?= csrf_field() ?>
                <input type="hidden" name="jenis_laporan" value="bulanan">
                <input type="hidden" name="tgl_awal" value="<?= $awal ?? '' ?>">
                <input type="hidden" name="tgl_akhir" value="<?= $akhir ?? '' ?>">
                <input type="hidden" name="format" value="pdf">
                <button type="submit" class="btn-primary" style="background-color: #dc2626;"><i class="fas fa-file-pdf"></i> Export PDF (Laporan Eksekutif)</button>
            </form>
        </div>
        <div class="card-body">
            <div class="dashboard-cards" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="stat-card" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 10px 0; color: #475569;">Total Anggota Aktif</h4>
                    <div style="font-size: 24px; font-weight: bold; color: #0f172a;"><?= number_format($summary['total_anggota'] ?? 0, 0, ',', '.') ?> <span style="font-size:12px; color:#10b981;">(+<?= $summary['anggota_baru'] ?? '' ?> baru)</span></div>
                </div>
                <div class="stat-card" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 10px 0; color: #475569;">Saldo Kas Koperasi</h4>
                    <div style="font-size: 24px; font-weight: bold; color: #0f172a;">Rp <?= number_format($summary['saldo_kas'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 10px 0; color: #475569;">Penjualan Waserda</h4>
                    <div style="font-size: 24px; font-weight: bold; color: #0f172a;">Rp <?= number_format($summary['penjualan_waserda'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 10px 0; color: #475569;">Total Simpanan Anggota</h4>
                    <div style="font-size: 24px; font-weight: bold; color: #0f172a;">Rp <?= number_format($summary['total_simpanan'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 10px 0; color: #475569;">Piutang Pinjaman (Berjalan)</h4>
                    <div style="font-size: 24px; font-weight: bold; color: #0f172a;">Rp <?= number_format($summary['piutang_pinjaman'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card" style="background: <?= $summary['laba'] >= 0 ? '#ecfdf5' : '#fef2f2' ?>; padding: 15px; border-radius: 8px; border: 1px solid <?= $summary['laba'] >= 0 ? '#6ee7b7' : '#fca5a5' ?>;">
                    <h4 style="margin: 0 0 10px 0; color: <?= $summary['laba'] >= 0 ? '#047857' : '#b91c1c' ?>;">Laba Bersih (SHU Berjalan)</h4>
                    <div style="font-size: 24px; font-weight: bold; color: <?= $summary['laba'] >= 0 ? '#047857' : '#b91c1c' ?>;">Rp <?= number_format($summary['laba'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

