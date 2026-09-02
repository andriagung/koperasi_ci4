<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title">Chart of Accounts (CoA)</h4>
            </div>
            <div class="card-body">
    <p style="color: #666; margin-bottom: 20px;">Daftar Akun Standar Akuntansi Koperasi</p>
    
    <table class="table" style="font-size: 14px;">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="width: 100px;">Kode Akun</th>
                <th>Nama Akun</th>
                <th style="width: 150px;">Tipe Akun</th>
                <th style="width: 150px;">Saldo Normal</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($akun)): ?>
                <tr><td colspan="4" style="text-align:center;">Data CoA tidak ditemukan.</td></tr>
            <?php else: foreach($akun as $a): ?>
                <tr>
                    <td><strong><?= esc($a['kode_akun'] ?? '') ?></strong></td>
                    <td><?= esc($a['nama_akun'] ?? '') ?></td>
                    <td><span class="badge" style="background:#3b82f6; color:#fff; padding:3px 8px; border-radius:4px; font-size:12px;"><?= esc($a['tipe_akun'] ?? '') ?></span></td>
                    <td><?= esc($a['saldo_normal'] ?? '') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
