<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<?php if(!$is_print): ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-boxes text-warning me-2"></i>Laporan Barang Mati (Slow-Moving)</h2>
        <p class="text-muted">Daftar produk yang tidak terjual dalam <?= $hari ?? '' ?> hari terakhir</p>
    </div>
</div>

<div class="card glass-card mb-4 no-print">
    <div class="card-body">
        
    <form action="" method="POST">
        <?= csrf_field() ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tampilkan barang mati dalam X hari terakhir</label>
                <select name="hari" class="form-select">
                    <option value="30" <?= $hari == 30 ? 'selected' : '' ?>>30 Hari Terakhir</option>
                    <option value="60" <?= $hari == 60 ? 'selected' : '' ?>>60 Hari Terakhir</option>
                    <option value="90" <?= $hari == 90 ? 'selected' : '' ?>>90 Hari Terakhir</option>
                    <option value="180" <?= $hari == 180 ? 'selected' : '' ?>>6 Bulan Terakhir</option>
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter me-1"></i>Filter</button>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><button type="submit" name="action" value="print" class="dropdown-item" formtarget="_blank"><i class="fas fa-print me-2 text-primary"></i>Cetak / PDF</button></li>
                        <li><button type="submit" name="action" value="excel" class="dropdown-item"><i class="fas fa-file-excel me-2 text-success"></i>Download Excel</button></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card <?= !$is_print ? 'glass-card' : '' ?> border-0 shadow-none">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table <?= $is_print ? 'data' : 'table-hover table-bordered' ?>" id="<?= !$is_print ? 'table-laporan' : '' ?>">
                <thead class="<?= !$is_print ? 'table-light' : '' ?>">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">SKU</th>
                        <th width="25%">Nama Produk</th>
                        <th width="10%" class="text-center">Sisa Stok</th>
                        <th width="15%" class="text-end">HPP</th>
                        <th width="15%" class="text-end">Total Nilai Mandek</th>
                        <th width="15%" class="text-center">Tgl Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totStok = 0;
                    $totNilai = 0;
                    foreach($data as $p): 
                        $nilaiMandek = $p['stok'] * $p['harga_beli'];
                        $totStok += $p['stok'];
                        $totNilai += $nilaiMandek;
                        
                        $isExpired = false;
                        $tglFormat = '-';
                        if ($p['tanggal_kadaluarsa']) {
                            $isExpired = $p['tanggal_kadaluarsa'] < date('Y-m-d');
                            $tglFormat = date('d/m/Y', strtotime($p['tanggal_kadaluarsa']));
                        }
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $p['sku'] ?? '' ?></td>
                        <td class="fw-bold"><?= $p['nama_produk'] ?? '' ?></td>
                        <td class="text-center <?= $p['stok'] > 10 ? 'text-danger fw-bold' : '' ?>"><?= $p['stok'] ?? '' ?></td>
                        <td class="text-end">Rp <?= number_format($p['harga_beli'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-danger">Rp <?= number_format($nilaiMandek ?? 0, 0, ',', '.') ?></td>
                        <td class="text-center">
                            <?php if($isExpired): ?>
                                <span class="badge bg-danger">Kedaluwarsa</span>
                            <?php else: ?>
                                <?= $tglFormat ?? '' ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">TOTAL KESELURUHAN BARANG MATI</th>
                        <th class="text-center text-danger"><?= $totStok ?? '' ?></th>
                        <th class="text-end">-</th>
                        <th class="text-end text-danger fs-6">Rp <?= number_format($totNilai ?? 0, 0, ',', '.') ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?php if(!$is_print): ?>
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-laporan').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

