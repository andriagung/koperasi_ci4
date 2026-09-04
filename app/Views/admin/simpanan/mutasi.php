<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-list-alt text-primary me-2"></i><?= $title ?? '' ?></h2>
        <p class="text-muted">Data mutasi transaksi simpanan anggota</p>
    </div>
</div>

<div class="card glass-card mb-4">
    <div class="card-body">
        
        <form action="<?= base_url('admin/simpanan/mutasi') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $awal ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $akhir ?? '' ?>">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card glass-card border-0 shadow-none">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="table-mutasi">
                <thead class="table-light">
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

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-mutasi').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('admin/simpanan/datatablesMutasi') ?>",
            type: "POST",
            data: function (d) {
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
                d.tgl_awal = $('input[name="tgl_awal"]').val();
                d.tgl_akhir = $('input[name="tgl_akhir"]').val();
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
            {data: 'tanggal'},
            {data: 'anggota', orderable: false, searchable: false},
            {data: 'nama_simpanan'},
            {data: 'setor', className: 'text-end text-success', orderable: false, searchable: false},
            {data: 'tarik', className: 'text-end text-danger', orderable: false, searchable: false},
            {data: 'keterangan'}
        ],
        order: [[1, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
    });
});
</script>
<?= $this->endSection() ?>

