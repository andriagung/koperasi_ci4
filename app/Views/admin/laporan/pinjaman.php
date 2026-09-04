<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<?php if(!$is_print): ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-hand-holding-usd text-primary me-2"></i>Laporan Pinjaman</h2>
        <p class="text-muted">Data pinjaman dan kolektibilitas anggota</p>
    </div>
</div>

<div class="card glass-card mb-4 no-print">
    <div class="card-body">
        
    <form action="" method="GET">
        <?= csrf_field() ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Status Pinjaman</label>
                <select name="status" class="form-select">
                    <option value="Semua" <?= $status == 'Semua' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="ACTIVE" <?= $status == 'ACTIVE' ? 'selected' : '' ?>>Aktif / Berjalan (Outstanding)</option>
                    <option value="PAID" <?= $status == 'PAID' ? 'selected' : '' ?>>Lunas</option>
                    <option value="Menunggu Persetujuan" <?= $status == 'Menunggu Persetujuan' ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                </select>
            </div>
            <div class="col-md-8">
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
                        <th width="15%">No. Pinjaman</th>
                        <th width="20%">Anggota</th>
                        <th width="12%">Tgl Pengajuan</th>
                        <th width="12%" class="text-end">Plafon Pinjaman</th>
                        <th width="12%" class="text-end">Terbayar</th>
                        <th width="12%" class="text-end">Sisa (Outstanding)</th>
                        <th width="12%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totPlafon = 0;
                    $totSisa = 0;
                    foreach($data as $p): 
                        $totPlafon += $p['jumlah_pinjaman'];
                        $totSisa += $p['sisa_pinjaman'];
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="fw-bold"><?= $p['nomor_pinjaman'] ?? '' ?></td>
                        <td>
                            <?= $p['nama_lengkap'] ?? '' ?><br>
                            <?php if(!$is_print): ?><small class="text-muted"><?= $p['nomor_anggota'] ?? '' ?></small><?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($p['tanggal_pengajuan'])) ?></td>
                        <td class="text-end">Rp <?= number_format($p['jumlah_pinjaman'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-success">Rp <?= number_format($p['total_dibayar'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-danger fw-bold">Rp <?= number_format($p['sisa_pinjaman'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-center">
                            <?php if(!$is_print): ?>
                                <?php
                                $badge = 'bg-secondary';
                                if($p['status'] == 'ACTIVE') $badge = 'bg-primary';
                                if($p['status'] == 'PAID') $badge = 'bg-success';
                                ?>
                                <span class="badge <?= $badge ?? '' ?>"><?= $p['status'] ?? '' ?></span>
                            <?php else: ?>
                                <?= $p['status'] ?? '' ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">TOTAL</th>
                        <th class="text-end fs-6">Rp <?= number_format($totPlafon ?? 0, 0, ',', '.') ?></th>
                        <th class="text-end fs-6"></th>
                        <th class="text-end text-danger fs-6">Rp <?= number_format($totSisa ?? 0, 0, ',', '.') ?></th>
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
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url("admin/ajax-laporan-pinjaman") ?>",
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
            {data: 'nomor_pinjaman'},
            {data: 'nama_lengkap'},
            {
                data: 'tanggal_pengajuan', 
                render: function(data) {
                    if (!data) return '-';
                    var d = new Date(data);
                    return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth()+1)).slice(-2) + '/' + d.getFullYear();
                }
            },
            {
                data: 'jumlah_pinjaman',
                className: 'text-end',
                render: function(data) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(data); }
            },
            {
                data: 'total_dibayar',
                className: 'text-end text-success',
                render: function(data) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(data || 0); }
            },
            {
                data: 'sisa_pinjaman',
                className: 'text-end text-danger fw-bold',
                render: function(data, type, row) { 
                    var sisa = data;
                    if (sisa == null) {
                        sisa = row.jumlah_pinjaman - (row.total_dibayar || 0);
                    }
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(sisa);
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    var badge = 'bg-secondary';
                    if (data == 'ACTIVE') badge = 'bg-primary';
                    else if (data == 'PAID') badge = 'bg-success';
                    else if (data == 'DEFAULT') badge = 'bg-danger';
                    return '<span class="badge ' + badge + '">' + data + '</span>';
                }
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

