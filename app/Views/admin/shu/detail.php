<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title">Detail Distribusi SHU Tahun <?= $tahun ?? '' ?></h4>
            </div>
            <div class="card-body">
    <?php if(session()->getFlashdata('message')): ?>
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    
    <form action="<?= base_url('admin/shu/bagikan') ?>" method="POST">
        <?= csrf_field() ?>
        <select name="tahun" class="form-control" style="max-width: 200px;">
            <?php for($i=date('Y'); $i>=date('Y')-5; $i--): ?>
                <option value="<?= $i ?? '' ?>" <?= $tahun == $i ? 'selected' : '' ?>>Tahun Buku <?= $i ?? '' ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn-primary">Lihat Detail</button>
    </form>

    <?php if(!$header): ?>
        <div style="text-align: center; padding: 30px; border: 1px dashed #ccc; border-radius: 8px;">
            <i class="fas fa-info-circle" style="font-size: 2rem; color: #64748b; margin-bottom:10px;"></i>
            <p>Tahun buku <?= $tahun ?? '' ?> belum ditutup. Tidak ada detail distribusi SHU yang tersimpan.</p>
        </div>
    <?php else: ?>
        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
            <p style="margin: 0; color: #475569;">
                Status: <strong style="color: #16a34a;"><?= esc($header['status'] ?? '') ?></strong><br>
                Tanggal Eksekusi: <strong><?= date('d M Y', strtotime($header['created_at'])) ?></strong><br>
                Total Laba Bersih: <strong style="font-size: 1.1em;">Rp <?= number_format($header['total_shu'] ?? 0, 0, ',', '.') ?></strong>
            </p>
        </div>

        <table class="table" style="font-size: 13px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th>NIP</th>
                    <th>Nama Anggota</th>
                    <th style="text-align: right;">Jasa Modal</th>
                    <th style="text-align: right;">Jasa Usaha</th>
                    <th style="text-align: right;">Total Diterima</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detail as $d): ?>
                <tr>
                    <td><?= esc($d['nip'] ?? '') ?></td>
                    <td><?= esc($d['nama_lengkap'] ?? '') ?></td>
                    <td style="text-align: right;">Rp <?= number_format($d['shu_modal'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right;">Rp <?= number_format($d['shu_usaha'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp <?= number_format($d['total_shu'] ?? 0, 0, ',', '.') ?>
                    </td>
                    <td>
                        <span class="badge" style="background:#10b981; color:#fff; padding:3px 8px; border-radius:4px; font-size:11px;">
                            <?= esc($d['status'] ?? '') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

