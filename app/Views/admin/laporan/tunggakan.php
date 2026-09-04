<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="panel-view active">
    <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
        <span><?= $judul ?? '' ?></span>
    </div>
    
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <form action="" method="POST">
        <?= csrf_field() ?>
            <div>
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Batas Jatuh Tempo (Hingga)</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $akhir ?? '' ?>" required>
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter Laporan</button>
            </div>
        </form>
    </div>

    <div class="alert alert-warning" style="background:#fffbeb; color:#b45309; padding:15px; border-radius:8px; margin-bottom:20px; border-left:4px solid #f59e0b;">
        <i class="fas fa-exclamation-triangle"></i> Menampilkan data angsuran yang belum dibayar dan sudah melewati tanggal jatuh tempo hingga <b><?= date('d/m/Y', strtotime($akhir)) ?></b>.
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="display" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">No. Pinjaman</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">NIP / Nama Anggota</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Angsuran Ke</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Jatuh Tempo</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: right;">Total Tagihan (Rp)</th>
                        <th style="padding: 12px; border-bottom: 2px solid #cbd5e1; text-align: center;">Hari Keterlambatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalTunggakan = 0;
                    if(empty($data)): ?>
                        <tr><td colspan="6" style="text-align: center; padding: 20px;">Tidak ada data tunggakan.</td></tr>
                    <?php else:
                        foreach ($data as $row): 
                            $tagihan = $row['jumlah_bayar'];
                            $totalTunggakan += $tagihan;
                            
                            $jatuhTempo = new DateTime($row['tanggal_jatuh_tempo']);
                            $hariIni = new DateTime($akhir);
                            $selisih = $hariIni->diff($jatuhTempo)->days;
                    ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= $row['nomor_pinjaman'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                            <b><?= $row['nip'] ?? '' ?></b><br>
                            <?= $row['nama_lengkap'] ?? '' ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Ke-<?= $row['angsuran_ke'] ?? '' ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #dc2626; font-weight: bold;">
                            <?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">
                            <?= number_format($tagihan ?? 0, 0, ',', '.') ?>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            <span style="background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 12px; font-size: 12px;">
                                <?= $selisih ?? '' ?> Hari
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #fef2f2;">
                        <td colspan="4" style="padding: 12px; text-align: right;">TOTAL TUNGGAKAN</td>
                        <td style="padding: 12px; text-align: right; color: #b91c1c;">Rp <?= number_format($totalTunggakan ?? 0, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

