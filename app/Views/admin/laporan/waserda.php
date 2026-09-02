<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<?php if(!$is_print): ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-shopping-cart text-primary me-2"></i>Laporan Penjualan Waserda</h2>
        <p class="text-muted">Data performa penjualan dan margin per produk</p>
    </div>
</div>

<div class="card glass-card mb-4 no-print">
    <div class="card-body">
        
    <?= csrf_field() ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $awal ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $akhir ?? '' ?>">
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
                        <th width="10%" class="text-center">Terjual</th>
                        <th width="15%" class="text-end">Total HPP</th>
                        <th width="15%" class="text-end">Total Omset</th>
                        <th width="15%" class="text-end">Margin Laba</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totQty = 0;
                    $totHpp = 0;
                    $totOmset = 0;
                    foreach($data as $p): 
                        $margin = $p['total_omset'] - $p['total_hpp'];
                        $totQty += $p['total_qty'];
                        $totHpp += $p['total_hpp'];
                        $totOmset += $p['total_omset'];
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $p['sku'] ?? '' ?></td>
                        <td class="fw-bold"><?= $p['nama_produk'] ?? '' ?></td>
                        <td class="text-center"><?= $p['total_qty'] ?? '' ?></td>
                        <td class="text-end">Rp <?= number_format($p['total_hpp'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-success">Rp <?= number_format($p['total_omset'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-primary fw-bold">Rp <?= number_format($margin ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">TOTAL KESELURUHAN</th>
                        <th class="text-center"><?= $totQty ?? '' ?></th>
                        <th class="text-end">Rp <?= number_format($totHpp ?? 0, 0, ',', '.') ?></th>
                        <th class="text-end text-success fs-6">Rp <?= number_format($totOmset ?? 0, 0, ',', '.') ?></th>
                        <th class="text-end text-primary fs-6">Rp <?= number_format($totOmset - $totHpp, 0, ',', '.') ?></th>
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
            url: "<?= base_url("admin/ajax-laporan-waserda") ?>",
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
            {data: 'total_qty', className: 'text-center'},
            {
                data: 'total_hpp',
                className: 'text-end text-danger',
                render: function(data) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(data); }
            },
            {
                data: 'total_omset',
                className: 'text-end fw-bold text-success',
                render: function(data) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(data); }
            },
            {
                data: 'total_omset',
                className: 'text-end text-success fw-bold',
                render: function(data, type, row) { 
                    var margin = data - row.total_hpp;
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(margin);
                }
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

