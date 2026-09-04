<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<?php if(!$is_print): ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-users text-primary me-2"></i>Laporan Anggota</h2>
        <p class="text-muted">Data pertumbuhan dan keanggotaan Koperasi</p>
    </div>
</div>

<div class="card glass-card mb-4 no-print">
    <div class="card-body">
        
    <form action="" method="GET">
        <?= csrf_field() ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai (Bergabung)</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $awal ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $akhir ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status Anggota</label>
                <select name="status" class="form-select">
                    <option value="Semua" <?= $status == 'Semua' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="Aktif" <?= $status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Nonaktif" <?= $status == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
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
                        <th width="15%">No. Anggota</th>
                        <th width="25%">Nama Lengkap</th>
                        <th width="15%">NIK</th>
                        <th width="10%">L/P</th>
                        <th width="15%">Tgl Bergabung</th>
                        <th width="15%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($data as $a): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="fw-bold"><?= $a['nomor_anggota'] ?? '' ?></td>
                        <td><?= $a['nama_lengkap'] ?? '' ?></td>
                        <td><?= $a['nik'] ?? '' ?></td>
                        <td class="text-center"><?= $a['jenis_kelamin'] ?? '' ?></td>
                        <td><?= $a['tanggal_masuk'] ? date('d/m/Y', strtotime($a['tanggal_masuk'])) : '-' ?></td>
                        <td class="text-center">
                            <?php if(!$is_print): ?>
                                <span class="badge <?= $a['status'] == 'Aktif' ? 'bg-success' : 'bg-danger' ?>"><?= $a['status'] ?? '' ?></span>
                            <?php else: ?>
                                <?= $a['status'] ?? '' ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($data)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">Tidak ada data anggota pada filter ini</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
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
            url: "<?= base_url('admin/ajax-laporan-anggota') ?>",
            type: "POST",
            data: function (d) {
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
                d.status = "<?= esc($status ?? 'Semua') ?>";
                d.tgl_awal = "<?= esc($awal ?? '') ?>";
                d.tgl_akhir = "<?= esc($akhir ?? '') ?>";
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
            {data: 'nomor_anggota'},
            {data: 'nama_lengkap'},
            {data: 'nik'},
            {data: 'jenis_kelamin', className: 'text-center'},
            {
                data: 'tanggal_masuk', 
                render: function(data) {
                    if (!data) return '-';
                    var d = new Date(data);
                    return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth()+1)).slice(-2) + '/' + d.getFullYear();
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    var cls = data == 'Aktif' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' + cls + '">' + data + '</span>';
                }
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

