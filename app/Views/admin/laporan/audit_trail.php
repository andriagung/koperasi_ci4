<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span><?= $judul ?? '' ?></span>
    </div>
    
    <div class="alert alert-info" style="background:#e0f2fe; color:#0369a1; padding:15px; border-radius:8px; margin-bottom:20px; border-left:4px solid #0284c7;">
        <i class="fas fa-shield-alt"></i> Menampilkan rekam jejak sistem (Audit Trail) untuk keperluan keamanan dan pemantauan aktivitas user (Admin/Petugas).
    </div>

    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <form action="" method="POST">
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
                <button type="submit" class="btn-primary">Filter Log</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="display" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; width: 150px;">Waktu Sistem</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; width: 100px;">User ID</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; width: 150px;">Aksi / Modul</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Deskripsi Detail</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; width: 150px;">Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">Tidak ada rekaman audit pada periode ini.</td></tr>
                    <?php else:
                        foreach ($data as $row): 
                    ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 0.9em;"><?= $row['created_at'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">User-<?= $row['user_id'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                            <span style="background: #e0e7ff; color: #4338ca; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85em;">
                                <?= $row['action'] ?? '' ?>
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $row['description'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 0.85em; font-family: monospace;"><?= $row['ip_address'] ?? 'N/A' ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

