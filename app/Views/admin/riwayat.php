<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<!-- 7. VIEW RIWAYAT TRANSAKSI -->
            <div id="view-riwayat" class="panel-view active">
                <div class="page-title">Riwayat Transaksi Anggota</div>
                <div class="table-container">
                    <div class="table-responsive">
                    <table class="dataTable display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Anggota</th>
                                <th>Kategori</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($semuaRiwayat as $r): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                <td><strong><?= esc($r['nama_lengkap'] ?? '') ?></strong><br><small><?= esc($r['nip'] ?? '') ?></small></td>
                                <td><span class="badge bg-primary" style="background:#0284c7; color:white;"><?= esc($r['kategori'] ?? '') ?></span></td>
                                <td>
                                    <?php if($r['jenis_transaksi'] === 'Masuk'): ?>
                                        <span style="color:#16a34a; font-weight:bold;"><i class="fas fa-arrow-down"></i> Masuk</span>
                                    <?php else: ?>
                                    <?php endif; ?>
                                </td>
                                <td><strong>Rp <?= number_format($r['nominal'] ?? 0, 0, ',', '.') ?></strong></td>
                                <td><?= esc($r['keterangan'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

    <?= $this->endSection() ?>
