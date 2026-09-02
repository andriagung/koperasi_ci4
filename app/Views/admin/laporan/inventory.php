<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<?php if(!$is_print): ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-boxes text-primary me-2"></i>Laporan Inventory Waserda</h2>
        <p class="text-muted">Data sisa stok, HPP, dan status persediaan</p>
    </div>
</div>

<div class="card glass-card mb-4 no-print">
    <div class="card-body">
        
    <?= csrf_field() ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <button type="submit" name="action" value="filter" class="btn btn-primary me-2"><i class="fas fa-sync me-1"></i>Refresh Data</button>
                
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
                        <th width="20%">Nama Produk</th>
                        <th width="12%" class="text-center">Sisa Stok</th>
                        <th width="12%" class="text-center">Stok Minimum</th>
                        <th width="13%" class="text-end">HPP / Beli</th>
                        <th width="13%" class="text-end">Harga Jual</th>
                        <th width="10%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totValuasi = 0;
                    foreach($data as $p): 
                        $status = ($p['stok'] <= $p['stok_minimum']) ? 'KRITIS' : 'AMAN';
                        $totValuasi += ($p['stok'] * $p['harga_beli']);
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $p['sku'] ?? '' ?></td>
                        <td class="fw-bold"><?= $p['nama_produk'] ?? '' ?></td>
                        <td class="text-center fw-bold <?= $status == 'KRITIS' ? 'text-danger' : 'text-success' ?>"><?= $p['stok'] ?? '' ?></td>
                        <td class="text-center text-muted"><?= $p['stok_minimum'] ?? '' ?></td>
                        <td class="text-end">Rp <?= number_format($p['harga_beli'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp <?= number_format($p['harga_jual'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-center">
                            <?php if(!$is_print): ?>
                                <span class="badge <?= $status == 'KRITIS' ? 'bg-danger' : 'bg-success' ?>"><?= $status ?? '' ?></span>
                            <?php else: ?>
                                <?= $status ?? '' ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">ESTIMASI VALUASI (HPP x STOK)</th>
                        <th colspan="3" class="text-start fs-6 text-primary">Rp <?= number_format($totValuasi ?? 0, 0, ',', '.') ?></th>
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
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url("admin/ajax-laporan-inventory") ?>",
            type: "POST",
            data: function (d) {
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
                d.tgl_awal = "<?= esc($awal ?? '') ?>";
                d.tgl_akhir = "<?= esc($akhir ?? '') ?>";
                d.status = "<?= esc($status ?? '') ?>";
            }
        },
        columns: [
            {
                data: null, 
                orderable: false, 
                searchable: false, 
                className: 'text-center',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'sku'},
            {data: 'nama_produk'},
            {
                data: 'stok',
                className: 'text-center fw-bold',
                render: function(data, type, row) {
                    if (data <= row.stok_minimum) {
                        return '<span class="text-danger">' + data + '</span>';
                    }
                    return data;
                }
            },
            {data: 'stok_minimum', className: 'text-center'},
            {
                data: 'harga_beli',
                className: 'text-end text-danger',
                render: function(data) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(data); }
            },
            {
                data: 'harga_jual',
                className: 'text-end text-success',
                render: function(data) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(data); }
            },
            {
                data: 'stok',
                className: 'text-center',
                render: function(data, type, row) {
                    if (data <= row.stok_minimum) {
                        return '<span class="badge bg-danger">KRITIS</span>';
                    }
                    return '<span class="badge bg-success">AMAN</span>';
                }
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

