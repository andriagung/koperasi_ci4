<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div id="view-bendahara" class="panel-view active">
    <div class="page-title">
        Manajemen Bendahara Gaji
    </div>
    
    <div class="table-container">
        <?php if (session()->getFlashdata('message')) : ?>
            <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
        <?php endif; ?>



        <div class="table-responsive">
            <table class="display" style="width:100%" id="table-bendahara">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Instansi / Kategori</th>
                        <th>Jml Anggota</th>
                        <th>Email</th>
                        <th>Akun Login (Role Bendahara)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded by DataTables -->
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('admin/bendahara/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Bendahara Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Instansi / Bendahara</label>
                        <input type="text" name="nama_instansi" class="form-control" placeholder="Misal: RS 45 Kuningan" required>
                    </div>
                    <div class="mb-3">
                        <label>Email Tujuan Tagihan</label>
                        <input type="email" name="email" class="form-control" placeholder="Email bendahara" required>
                    </div>
                    <div class="mb-3">
                        <label>Pilih Akun Login (Opsional)</label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Tidak Terhubung --</option>
                            <?php foreach($admin_users as $adm): ?>
                                <option value="<?= $adm['id'] ?? '' ?>"><?= esc($adm['name'] ?? '') ?> (@<?= esc($adm['username'] ?? '') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Jika belum ada, Anda bisa membuatnya nanti di menu Pengaturan > Admin Users (dengan role Bendahara).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('admin/bendahara/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit_id" value="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Bendahara Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Instansi / Bendahara</label>
                        <input type="text" name="nama_instansi" id="edit_nama_instansi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email Tujuan Tagihan</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Pilih Akun Login (Opsional)</label>
                        <select name="user_id" id="edit_user_id" class="form-select">
                            <option value="">-- Tidak Terhubung --</option>
                            <?php foreach($admin_users as $adm): ?>
                                <option value="<?= $adm['id'] ?? '' ?>"><?= esc($adm['name'] ?? '') ?> (@<?= esc($adm['username'] ?? '') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Akun admin dengan role "Bendahara" yang dapat login untuk melihat tagihan ini.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-bendahara').on('init.dt', function() {
        var customBtns = `
            <div style="display:inline-flex; gap:10px;">
                <button class="btn-sm btn-primary" style="padding: 8px 15px; font-size: 0.85rem;" onclick="new bootstrap.Modal(document.getElementById('modalTambah')).show()"><i class="fas fa-plus"></i> Tambah Bendahara</button>
            </div>
        `;
        $('.dt-custom-buttons').append(customBtns);
    });

    $('#table-bendahara').DataTable($.extend(true, {}, (typeof window.dataTableOptions !== 'undefined' ? window.dataTableOptions : {}), {
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('admin/ajax-bendahara') ?>",
            type: "POST",
            data: function (d) {
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
            }
        },
        columns: [
            {
                data: null, 
                orderable: false, 
                searchable: false, 
                className: 'text-center align-middle',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'nama_instansi',
                className: 'align-middle',
                render: function(data) {
                    return '<strong class="text-dark">' + data + '</strong>';
                }
            },
            {
                data: 'jumlah_anggota',
                searchable: false,
                className: 'align-middle text-center',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + ' Anggota</span>';
                }
            },
            {
                data: 'email',
                className: 'align-middle',
                render: function(data) {
                    return data ? '<a href="mailto:'+data+'" class="text-decoration-none text-primary"><i class="fas fa-envelope me-1"></i>'+data+'</a>' : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'admin_nama',
                className: 'align-middle text-center'
            },
            {
                data: 'aksi', 
                orderable: false, 
                searchable: false,
                className: 'align-middle text-center'
            }
        ],
        drawCallback: function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover'
                });
            });
        }
    }));
});

function editBendahara(data) {
    document.getElementById('edit_id').value = data.hash_id;
    document.getElementById('edit_nama_instansi').value = data.nama_instansi;
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_user_id').value = data.user_id ? data.user_id : '';
    
    var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();
}
</script>
<?= $this->endSection() ?>
