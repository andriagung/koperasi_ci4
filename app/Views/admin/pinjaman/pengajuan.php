<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title"><?= esc($title ?? '') ?></div>

<?php if(session()->getFlashdata('message')): ?>
    <div class="alert alert-success" style="padding:15px; margin-bottom:20px; background-color:#d1fae5; color:#065f46; border-radius:6px;">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>

<div class="card glass-card table-container" style="padding: 25px;">
    <div style="display:flex; justify-content:space-between; margin-bottom:15px; align-items: center;">
        <h4 style="margin:0; font-size: 1.1rem; color: #0f172a; font-weight: 600;"><?= esc($status_filter === 'active' ? 'Pinjaman Aktif' : ($status_filter === 'approved' ? 'Pencairan Pinjaman' : ($status_filter === 'submitted' ? 'Menunggu Verifikasi & Approval' : 'Semua Daftar Pengajuan Pinjaman'))) ?></h4>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm align-middle" id="table-pengajuan" style="width:100%; border-collapse:collapse; text-align:left; font-size: 13px;">
            <thead>
                <tr style="background:#f1f5f9;">
                    <th style="padding:10px;">Tgl Ajukan</th>
                    <th style="padding:10px;">Nomor Anggota</th>
                    <th style="padding:10px;">Nama</th>
                    <th style="padding:10px;">Nominal</th>
                    <th style="padding:10px;">Tenor</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pengajuan Baru -->
<div class="modal-overlay" id="modal-ajukan">
    <div class="modal-content" style="max-width:800px; max-height:90vh; overflow-y:auto; border-radius: 16px; padding: 25px;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-ajukan')"></i>
        <h3 style="margin-bottom:20px; color:var(--primary); font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-signature"></i> Form Pengajuan Pinjaman Baru
        </h3>
        
        
    <form action="<?= base_url('admin/pinjaman/simpanPengajuan') ?>" method="POST">
        <?= csrf_field() ?>
            <h5 style="border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 15px; color: #334155; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-info-circle text-primary"></i> Informasi Pinjaman
            </h5>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 5px;">Anggota Pemohon <span style="color: #ef4444;">*</span></label>
                    <select name="anggota_id" required class="select2" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                        <option value="">Pilih Anggota</option>
                        <?php foreach($anggota as $a): ?>
                            <option value="<?= $a['id'] ?? '' ?>"><?= esc($a['nama_lengkap'] ?? '') ?> (<?= esc($a['nip'] ?? '') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 5px;">Produk Pinjaman <span style="color: #ef4444;">*</span></label>
                    <select name="produk_id" required style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                        <?php foreach($produk as $pr): ?>
                            <option value="<?= $pr['id'] ?? '' ?>"><?= esc($pr['nama'] ?? '') ?> (Max <?= $pr['tenor_max'] ?? '' ?> Bln)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 5px;">Plafon Pinjaman (Rp) <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="nominal_pengajuan" required min="100000" placeholder="Contoh: 5000000" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
                <div class="form-group">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 5px;">Tenor (Bulan) <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="tenor_bulan" required min="1" max="120" placeholder="Contoh: 12" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-bullseye" style="color: var(--primary);"></i> Tujuan Pinjaman <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="tujuan_pinjaman" required placeholder="Contoh: Biaya pendidikan anak, Renovasi rumah, Modal usaha..." style="width: 100%; font-size: 0.85rem; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff;">
                    <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px; display: block;">Tuliskan secara singkat dan jelas peruntukan dana pinjaman</small>
                </div>
            </div>
            
            <h5 style="border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; margin-top: 20px; margin-bottom: 15px; color: #334155; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-chart-pie text-primary"></i> Data Analisis Finansial (DSR)
            </h5>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 5px;">Estimasi Pendapatan Bulanan Bersih (Rp) <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="pendapatan_bulanan" required value="0" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
                <div class="form-group">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 5px;">Estimasi Pengeluaran Bulanan (Rp) <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="pengeluaran_bulanan" required value="0" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label style="font-size: 0.85rem; font-weight: 500; margin-bottom: 5px;">Angsuran Lain di Luar Koperasi (KPR, Leasing, dll) per Bulan (Rp)</label>
                    <input type="number" name="angsuran_lain" value="0" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
            </div>
            
            <button type="submit" class="btn-primary" style="width:100%; margin-top:20px; padding: 12px; font-size: 0.95rem; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-paper-plane me-1"></i> Submit Pengajuan & Analisis
            </button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-pengajuan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('admin/pinjaman/ajax-pengajuan') ?>",
            type: "POST",
            data: function (d) {
                d.status_filter = "<?= esc($status_filter ?? '') ?>";
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
            }
        },
        columns: [
            {data: 'created_at'},
            {data: 'nip'},
            {data: 'nama_lengkap'},
            {data: 'nominal_pengajuan'},
            {data: 'tenor_bulan'},
            {data: 'status_badge'},
            {data: 'aksi', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        initComplete: function() {
            let filterDiv = $('#table-pengajuan_filter');
            filterDiv.css({
                'display': 'flex',
                'align-items': 'center',
                'justify-content': 'flex-end',
                'gap': '12px'
            });
            let addBtn = $(`
                <button type="button" class="btn-primary" style="padding: 8px 16px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; border-radius: 99px; font-weight: 600; white-space: nowrap; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);" onclick="bukaModal('modal-ajukan')">
                    <i class="fas fa-plus"></i> Pengajuan Baru
                </button>
            `);
            filterDiv.append(addBtn);
        }
    });
});
</script>
<?= $this->endSection() ?>
