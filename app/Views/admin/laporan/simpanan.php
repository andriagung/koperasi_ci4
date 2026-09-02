<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<?php if(!$is_print): ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-wallet text-primary me-2"></i>Laporan Simpanan</h2>
        <p class="text-muted">Data mutasi transaksi simpanan anggota</p>
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
                        <th width="12%">Tanggal</th>
                        <th width="20%">Anggota</th>
                        <th width="15%">Jenis Simpanan</th>
                        <th width="15%" class="text-end">Setoran (Masuk)</th>
                        <th width="15%" class="text-end">Penarikan (Keluar)</th>
                        <th width="18%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    $totSetor = 0; 
                    $totTarik = 0;
                    foreach($data as $t): 
                        $totSetor += $t['kredit'];
                        $totTarik += $t['debit'];
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                        <td>
                            <span class="fw-bold"><?= $t['nama_lengkap'] ?? '' ?></span><br>
                            <?php if(!$is_print): ?><small class="text-muted"><?= $t['nomor_anggota'] ?? '' ?></small><?php endif; ?>
                        </td>
                        <td><?= $t['jenis_simpanan'] ?? '' ?></td>
                        <td class="text-end text-success">Rp <?= number_format($t['kredit'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-danger">Rp <?= number_format($t['debit'] ?? 0, 0, ',', '.') ?></td>
                        <td><?= $t['keterangan'] ?? '' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">TOTAL MUTASI</th>
                        <th class="text-end text-success fs-6">Rp <?= number_format($totSetor ?? 0, 0, ',', '.') ?></th>
                        <th class="text-end text-danger fs-6">Rp <?= number_format($totTarik ?? 0, 0, ',', '.') ?></th>
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
            url: "<?= base_url("admin/ajax-laporan-simpanan") ?>",
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
            {
                data: 'tanggal', 
                render: function(data) {
                    if (!data) return '-';
                    var d = new Date(data);
                    return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth()+1)).slice(-2) + '/' + d.getFullYear();
                }
            },
            {
                data: 'nomor_anggota',
                render: function(data, type, row) {
                    return '<strong>' + (row.nomor_anggota || '') + '</strong><br><small class="text-muted">' + (row.nama_lengkap || '') + '</small>';
                }
            },
            {data: 'keterangan'},
            {
                data: 'nominal',
                className: 'text-end text-success',
                render: function(data, type, row) {
                    return (row.jenis_transaksi.toLowerCase() == 'setoran') ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data) : '-';
                }
            },
            {
                data: 'nominal',
                className: 'text-end text-danger',
                render: function(data, type, row) {
                    return (row.jenis_transaksi.toLowerCase() == 'tarik' || row.jenis_transaksi.toLowerCase() == 'penarikan') ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data) : '-';
                }
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

