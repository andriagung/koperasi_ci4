<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<?php
    $totalPotongan = 0;
    $totalSimpananWajib = 0;
    $totalAngsuran = 0;
    $totalLunas = 0;
    $totalPending = 0;
    foreach ($tagihan as $t) {
        $totalPotongan += (float)($t['total_tagihan'] ?? 0);
        $totalSimpananWajib += (float)($t['nominal_simpanan_wajib'] ?? 0);
        $totalAngsuran += (float)($t['nominal_angsuran'] ?? 0);
        if (($t['status'] ?? '') === 'Lunas') {
            $totalLunas++;
        } else {
            $totalPending++;
        }
    }
?>

<div class="page-title" style="margin-bottom: 20px;">
    Manajemen Potongan Gaji (Payroll Deduction)
</div>

<!-- Info Banner -->
<div class="card glass-card" style="padding: 16px 20px; margin-bottom: 20px; border-left: 4px solid var(--primary-light); background: linear-gradient(135deg, rgba(238, 242, 255, 0.7) 0%, rgba(255, 255, 255, 0.9) 100%);">
    <div style="display: flex; align-items: flex-start; gap: 15px;">
        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.1rem; flex-shrink: 0;">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div style="flex: 1;">
            <strong style="color: #1e293b; font-size: 0.95rem;">Panduan Potongan Gaji Kolektif (Payroll)</strong>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem; line-height: 1.5;">
                Fitur ini merekapitulasi seluruh kewajiban anggota bulanan (Simpanan Wajib + Angsuran Pinjaman) menjadi satu dokumen tagihan kolektif untuk diserahkan ke bagian Keuangan/SDM (Payroll Perusahaan).
            </p>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('message')) : ?>
    <div class="alert alert-success" style="padding: 12px 20px; margin-bottom: 20px; background: #ecfdf5; color: #065f46; border-radius: 10px; border: 1px solid #a7f3d0; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger" style="padding: 12px 20px; margin-bottom: 20px; background: #fef2f2; color: #991b1b; border-radius: 10px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Stat Metric Cards -->
<div class="dashboard-cards" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 25px; gap: 16px;">
    <div class="stat-card" style="padding: 18px 20px;">
        <div class="stat-info">
            <h4 style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px;">Total Tagihan Periode Ini</h4>
            <h2 style="font-size: 1.35rem; font-weight: 700; color: #0f172a;">Rp <?= number_format($totalPotongan, 0, ',', '.') ?></h2>
            <small style="color: var(--primary); font-size: 0.75rem;"><i class="fas fa-calendar-alt"></i> Periode <?= date('F Y', strtotime(($periode ?? date('Y-m')) . '-01')) ?></small>
        </div>
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;"><i class="fas fa-file-invoice-dollar"></i></div>
    </div>
    
    <div class="stat-card" style="padding: 18px 20px;">
        <div class="stat-info">
            <h4 style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px;">Simpanan Wajib</h4>
            <h2 style="font-size: 1.35rem; font-weight: 700; color: #0f172a;">Rp <?= number_format($totalSimpananWajib, 0, ',', '.') ?></h2>
            <small style="color: #3b82f6; font-size: 0.75rem;"><i class="fas fa-piggy-bank"></i> <?= count($tagihan) ?> Tagihan</small>
        </div>
        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;"><i class="fas fa-piggy-bank"></i></div>
    </div>

    <div class="stat-card" style="padding: 18px 20px;">
        <div class="stat-info">
            <h4 style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px;">Angsuran Pinjaman</h4>
            <h2 style="font-size: 1.35rem; font-weight: 700; color: #0f172a;">Rp <?= number_format($totalAngsuran, 0, ',', '.') ?></h2>
            <small style="color: #8b5cf6; font-size: 0.75rem;"><i class="fas fa-hand-holding-usd"></i> Pokok & Jasa</small>
        </div>
        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;"><i class="fas fa-hand-holding-usd"></i></div>
    </div>

    <div class="stat-card" style="padding: 18px 20px;">
        <div class="stat-info">
            <h4 style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px;">Status Pembayaran</h4>
            <h2 style="font-size: 1.35rem; font-weight: 700; color: #0f172a;"><?= $totalLunas ?> <span style="font-size: 0.9rem; font-weight: normal; color: #64748b;">/ <?= count($tagihan) ?> Lunas</span></h2>
            <small style="color: #f59e0b; font-size: 0.75rem;"><i class="fas fa-clock"></i> <?= $totalPending ?> Menunggu Pelunasan</small>
        </div>
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;"><i class="fas fa-check-double"></i></div>
    </div>
</div>

<!-- Main Table Container -->
<div class="table-container glass-card" style="padding: 24px;">
    
    <!-- Unified Filter & Action Toolbar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1px solid #f1f5f9;">
        
        <!-- Left: Month Filter Form -->
        <form action="<?= base_url('admin/potongan') ?>" method="get" style="display: flex; align-items: center; gap: 10px;">
            <div style="position: relative;">
                <input type="month" name="periode" value="<?= $periode ?? date('Y-m') ?>" required 
                       style="padding: 8px 14px; font-size: 0.88rem; border: 1.5px solid #e2e8f0; border-radius: 8px; font-weight: 600; color: #1e293b; background: #f8fafc; outline: none; transition: 0.2s;">
            </div>
            <button type="submit" class="btn btn-sm btn-primary" style="padding: 8px 16px; font-size: 0.85rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>

        <!-- Right: Action Buttons Group -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
            
            <!-- Generate Button -->
            <button type="button" class="btn btn-sm btn-primary" onclick="bukaModal('modal-generate')" 
                    style="padding: 8px 16px; font-size: 0.85rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                <i class="fas fa-magic"></i> Generate Tagihan
                <?php if (($preview['akan_digenerate'] ?? 0) > 0): ?>
                    <span style="background: rgba(255,255,255,0.3); padding: 1px 6px; border-radius: 99px; font-size: 0.75rem; margin-left: 4px;"><?= $preview['akan_digenerate'] ?></span>
                <?php endif; ?>
            </button>

            <!-- Export Excel -->
            <a href="<?= base_url('admin/potongan/exportExcel?periode=' . ($periode ?? '')) ?>" class="btn btn-sm" 
               style="background: #10b981; color: white; padding: 8px 14px; font-size: 0.85rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-file-excel"></i> Excel
            </a>

            <!-- Export PDF -->
            <a href="<?= base_url('admin/potongan/exportPdf?periode=' . ($periode ?? '')) ?>" class="btn btn-sm" 
               style="background: #ef4444; color: white; padding: 8px 14px; font-size: 0.85rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-file-pdf"></i> PDF
            </a>

            <!-- Import CSV -->
            <button type="button" class="btn btn-sm" onclick="bukaModal('modal-import')" 
                    style="background: #0ea5e9; color: white; padding: 8px 14px; font-size: 0.85rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-file-import"></i> Import & Bayar
            </button>

            <!-- Send Mass Email -->
            <form action="<?= base_url('admin/potongan/sendEmailMassal') ?>" method="post" style="display: inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="periode" value="<?= $periode ?? '' ?>">
                <button type="submit" class="btn btn-sm" onclick="return confirm('Kirim email slip bukti potongan beserta lampiran PDF dan panduan login ke seluruh anggota aktif periode ini?')" 
                        style="background: #6366f1; color: white; padding: 8px 14px; font-size: 0.85rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer;">
                    <i class="fas fa-mail-bulk"></i> Kirim Email ke Semua
                </button>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="table-potongan" style="font-size: 0.88rem; width: 100%;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 90px;">ID Tagihan</th>
                    <th>NIP / NIK</th>
                    <th>Nama Anggota</th>
                    <th style="text-align: right;">Simpanan Wajib</th>
                    <th style="text-align: right;">Angsuran Pinjaman</th>
                    <th style="text-align: right;">Total Tagihan</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Tanggal Bayar</th>
                    <th style="text-align: center; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($tagihan as $row): ?>
                <tr>
                    <td style="text-align: center; color: #64748b;"><?= $no++ ?></td>
                    <td><span style="font-family: monospace; font-weight: 600; color: #475569;">#<?= $row['id'] ?? '' ?></span></td>
                    <td><span style="font-family: monospace; color: #64748b;"><?= esc($row['nik'] ?? '-') ?></span></td>
                    <td>
                        <strong style="color: #0f172a; font-size: 0.9rem;"><?= esc($row['nama'] ?? '') ?></strong>
                        <?php if(!empty($row['nama_instansi'])): ?>
                            <br><small style="color: #64748b; font-size: 0.75rem;"><i class="fas fa-building"></i> <?= esc($row['nama_instansi']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right; color: #3b82f6; font-weight: 500;">Rp <?= number_format($row['nominal_simpanan_wajib'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right; color: #8b5cf6; font-weight: 500;">Rp <?= number_format($row['nominal_angsuran'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: 700; color: #0f172a; font-size: 0.92rem;">Rp <?= number_format($row['total_tagihan'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: center;">
                        <?php if($row['status'] == 'Lunas'): ?>
                            <span class="badge" style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 99px; font-weight: 600; font-size: 0.78rem;">
                                <i class="fas fa-check-circle me-1"></i> Lunas
                            </span>
                        <?php elseif($row['status'] == 'Pending'): ?>
                            <span class="badge" style="background: #fef3c7; color: #b45309; padding: 5px 12px; border-radius: 99px; font-weight: 600; font-size: 0.78rem;">
                                <i class="fas fa-clock me-1"></i> Pending
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background: #fee2e2; color: #b91c1c; padding: 5px 12px; border-radius: 99px; font-weight: 600; font-size: 0.78rem;">
                                <i class="fas fa-times-circle me-1"></i> Gagal
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center; color: #64748b; font-size: 0.82rem;">
                        <?= $row['tanggal_bayar'] ? date('d/m/Y', strtotime($row['tanggal_bayar'])) : '<span style="color:#cbd5e1;">-</span>' ?>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: center;">
                            <a href="<?= base_url('admin/potongan/cetakBukti/' . idhash_encode($row['id'] ?? 0)) ?>" 
                               class="btn-action" target="_blank" title="Cetak Slip Bukti Potongan" 
                               style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; color: #475569; text-decoration: none; transition: 0.2s;">
                                <i class="fas fa-print"></i>
                            </a>
                            <form action="<?= base_url('admin/potongan/sendEmailSingle/' . idhash_encode($row['id'] ?? 0)) ?>" method="post" style="display: inline; margin: 0;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-action" title="Kirim Slip ke Email Anggota" onclick="return confirm('Kirim slip potongan gaji via email beserta lampiran PDF ke <?= esc($row['nama'] ?? 'anggota') ?>?')" 
                                        style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; cursor: pointer; transition: 0.2s;">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Generate Tagihan -->
<div class="modal-overlay" id="modal-generate">
    <div class="modal-content" style="max-width: 750px; text-align: left; padding: 25px; border-radius: 16px;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-generate')"></i>
        
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.25rem;">
                <i class="fas fa-magic"></i>
            </div>
            <div>
                <h3 style="margin: 0; color: #0f172a; font-size: 1.2rem; font-weight: 700;">Generate Tagihan Potongan Gaji</h3>
                <small style="color: #64748b;">Periode Tagihan: <strong><?= date('F Y', strtotime(($periode ?? date('Y-m')) . '-01')) ?></strong></small>
            </div>
        </div>

        <form action="<?= base_url('admin/potongan/generate') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="periode" value="<?= $periode ?? '' ?>">
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; text-align: center;">
                    <span style="font-size: 0.78rem; color: #64748b;">Total Anggota Aktif</span>
                    <h3 style="margin: 4px 0 0 0; color: #3b82f6; font-size: 1.3rem; font-weight: 700;"><?= $preview['total_anggota'] ?? 0 ?></h3>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; text-align: center;">
                    <span style="font-size: 0.78rem; color: #64748b;">Sudah Ada Tagihan</span>
                    <h3 style="margin: 4px 0 0 0; color: #10b981; font-size: 1.3rem; font-weight: 700;"><?= $preview['total_existing'] ?? 0 ?></h3>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; text-align: center;">
                    <span style="font-size: 0.78rem; color: #64748b;">Akan Dibuat Baru</span>
                    <h3 style="margin: 4px 0 0 0; color: #f59e0b; font-size: 1.3rem; font-weight: 700;"><?= $preview['akan_digenerate'] ?? 0 ?></h3>
                </div>
            </div>

            <?php if (($preview['akan_digenerate'] ?? 0) > 0): ?>
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; color: #b45309; font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i> Sistem akan membuat <strong><?= $preview['akan_digenerate'] ?></strong> data tagihan baru secara otomatis berdasarkan Simpanan Wajib & Angsuran berjalan.
                </div>
                
                <div class="table-responsive" style="max-height: 220px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 20px;">
                    <table class="table table-sm table-hover align-middle" style="font-size: 0.82rem; margin: 0;">
                        <thead style="position: sticky; top: 0; background: #f1f5f9; z-index: 1;">
                            <tr>
                                <th>No</th>
                                <th>NIP/NIK</th>
                                <th>Nama Anggota</th>
                                <th style="text-align: right;">Simp. Wajib</th>
                                <th style="text-align: right;">Angsuran</th>
                                <th style="text-align: right;">Total Tagihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($preview['list_generate'] as $rowPreview): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($rowPreview['nik'] ?? '') ?></td>
                                <td><strong><?= esc($rowPreview['nama'] ?? '') ?></strong></td>
                                <td style="text-align: right;">Rp <?= number_format($rowPreview['simpanan_wajib'] ?? 0, 0, ',', '.') ?></td>
                                <td style="text-align: right;">Rp <?= number_format($rowPreview['angsuran'] ?? 0, 0, ',', '.') ?></td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">Rp <?= number_format($rowPreview['total'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem; font-weight: 600; border-radius: 8px;">
                    <i class="fas fa-check-circle me-1"></i> Proses Generate Sekarang
                </button>
            <?php else: ?>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 20px; text-align: center; color: #15803d; margin-bottom: 15px;">
                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                    <strong style="font-size: 1rem;">Semua Tagihan Sudah Selesai Dibuat!</strong>
                    <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #166534;">Seluruh anggota aktif pada periode ini telah memiliki data tagihan potongan gaji.</p>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Modal Import & Bayar -->
<div class="modal-overlay" id="modal-import">
    <div class="modal-content" style="max-width: 600px; text-align: left; padding: 25px; border-radius: 16px;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-import')"></i>
        
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(14, 165, 233, 0.15); display: flex; align-items: center; justify-content: center; color: #0ea5e9; font-size: 1.25rem;">
                <i class="fas fa-file-import"></i>
            </div>
            <div>
                <h3 style="margin: 0; color: #0f172a; font-size: 1.2rem; font-weight: 700;">Import Pelunasan CSV</h3>
                <small style="color: #64748b;">Upload file CSV hasil potongan payroll dari Bendahara</small>
            </div>
        </div>

        <form action="<?= base_url('admin/potongan/importCsv') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 14px; margin-bottom: 18px; color: #0369a1; font-size: 0.85rem; line-height: 1.5;">
                <i class="fas fa-info-circle"></i> <strong>Ketentuan:</strong> Unggah file CSV yang telah diproses oleh Bendahara. Kolom pertama (ID Tagihan) akan diverifikasi dan status "Pending" akan otomatis diubah menjadi "Lunas".
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Pilih File CSV Pelunasan <span style="color:#ef4444;">*</span></label>
                <input type="file" name="file_csv" accept=".csv" required 
                       style="padding: 10px; font-size: 0.88rem; border: 1.5px dashed #cbd5e1; border-radius: 8px; width: 100%; background: #f8fafc; cursor: pointer;">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem; font-weight: 600; border-radius: 8px; background: #0ea5e9; border-color: #0ea5e9;">
                <i class="fas fa-upload me-1"></i> Proses Pelunasan Sekarang
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-potongan').DataTable({
        pageLength: 10
    });
});
</script>
<?= $this->endSection() ?>
