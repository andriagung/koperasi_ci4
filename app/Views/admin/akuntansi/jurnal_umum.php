<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-book text-primary me-2"></i>Jurnal Umum</h2>
        <p class="text-muted">Daftar Transaksi Akuntansi</p>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-primary" onclick="$('#modalTambahJurnal').modal('show')">
            <i class="fas fa-plus me-2"></i>Tambah Jurnal Manual
        </button>
    </div>
</div>

<div class="card glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="table-jurnal">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nomor Bukti</th>
                        <th>Keterangan</th>
                        <th>Total Debit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Jurnal -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Detail Jurnal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Nomor Bukti:</strong> <span id="det-nobukti"></span><br>
                        <strong>Tanggal:</strong> <span id="det-tanggal"></span>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Keterangan:</strong> <br><span id="det-keterangan"></span>
                    </div>
                </div>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr class="bg-light">
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Keterangan</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody id="det-body"></tbody>
                    <tfoot class="fw-bold bg-light">
                        <tr>
                            <td colspan="3" class="text-end">Total</td>
                            <td class="text-end" id="det-totdebit"></td>
                            <td class="text-end" id="det-totkredit"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jurnal Manual -->
<div class="modal fade" id="modalTambahJurnal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        
    <?= csrf_field() ?>" method="POST" id="formTambahJurnal">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurnal Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label>Nomor Bukti</label>
                            <input type="text" name="nomor_bukti" class="form-control" value="JU-<?= date('YmdHis') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label>Keterangan Umum</label>
                            <input type="text" name="keterangan" class="form-control" required placeholder="Contoh: Koreksi Piutang">
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-2 border-bottom pb-2">Rincian Akun (Debit & Kredit harus seimbang)</h6>
                    <table class="table table-sm" id="table-input-jurnal">
                        <thead>
                            <tr>
                                <th width="35%">Akun COA</th>
                                <th width="25%">Debit</th>
                                <th width="25%">Kredit</th>
                                <th width="10%">Keterangan</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1 -->
                            <tr>
                                <td>
                                    <select name="akun_id[]" class="form-select select2" required>
                                        <option value="">Pilih Akun...</option>
                                        <?php foreach ($coas as $c): ?>
                                            <option value="<?= $c['id'] ?? '' ?>"><?= $c['kode_akun'] ?? '' ?> - <?= $c['nama_akun'] ?? '' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="debit[]" class="form-control input-debit money-format" value="0"></td>
                                <td><input type="text" name="kredit[]" class="form-control input-kredit money-format" value="0"></td>
                                <td><input type="text" name="ket_detail[]" class="form-control"></td>
                                <td></td>
                            </tr>
                            <!-- Row 2 -->
                            <tr>
                                <td>
                                    <select name="akun_id[]" class="form-select select2" required>
                                        <option value="">Pilih Akun...</option>
                                        <?php foreach ($coas as $c): ?>
                                            <option value="<?= $c['id'] ?? '' ?>"><?= $c['kode_akun'] ?? '' ?> - <?= $c['nama_akun'] ?? '' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="debit[]" class="form-control input-debit money-format" value="0"></td>
                                <td><input type="text" name="kredit[]" class="form-control input-kredit money-format" value="0"></td>
                                <td><input type="text" name="ket_detail[]" class="form-control"></td>
                                <td><button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-row"><i class="fas fa-plus me-1"></i>Tambah Baris</button></td>
                                <td><strong><span id="tot-debit">0</span></strong></td>
                                <td><strong><span id="tot-kredit">0</span></strong></td>
                                <td colspan="2"><span id="lbl-balance" class="badge bg-success">SEIMBANG</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-submit-jurnal">Simpan Jurnal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    let dt = $('#table-jurnal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('admin/akuntansi/ajax-jurnal') ?>",
            type: "POST",
            data: function (d) {
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
            }
        },
        columns: [
            {data: 'tanggal'},
            {data: 'nomor_bukti'},
            {data: 'keterangan'},
            {data: 'total_debit'},
            {data: 'aksi', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']]
    });
    
    // Init Mask & Select2
    function initPlugins() {
        $('.money-format').mask('000.000.000.000.000', {reverse: true});
    }
    initPlugins();
    
    // Add Row
    $('#btn-add-row').click(function() {
        let options = $('.select2').first().html();
        let tr = `
            <tr>
                <td><select name="akun_id[]" class="form-select select2" required>${options}</select></td>
                <td><input type="text" name="debit[]" class="form-control input-debit money-format" value="0"></td>
                <td><input type="text" name="kredit[]" class="form-control input-kredit money-format" value="0"></td>
                <td><input type="text" name="ket_detail[]" class="form-control"></td>
                <td><button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="fas fa-times"></i></button></td>
            </tr>
        `;
        $('#table-input-jurnal tbody').append(tr);
        initPlugins();
    });
    
    // Remove Row
    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        hitungTotal();
    });
    
    // Hitung Total on keyup
    $(document).on('keyup', '.input-debit, .input-kredit', function() {
        hitungTotal();
    });
    
    function parseRupiah(str) {
        if(!str) return 0;
        return parseFloat(str.replace(/\./g, '')) || 0;
    }
    
    function formatRupiah(num) {
        return num.toLocaleString('id-ID');
    }
    
    function hitungTotal() {
        let totDebit = 0;
        let totKredit = 0;
        $('.input-debit').each(function(){ totDebit += parseRupiah($(this).val()); });
        $('.input-kredit').each(function(){ totKredit += parseRupiah($(this).val()); });
        
        $('#tot-debit').text(formatRupiah(totDebit));
        $('#tot-kredit').text(formatRupiah(totKredit));
        
        if (totDebit === totKredit && totDebit > 0) {
            $('#lbl-balance').removeClass('bg-danger').addClass('bg-success').text('SEIMBANG');
            $('#btn-submit-jurnal').prop('disabled', false);
        } else {
            $('#lbl-balance').removeClass('bg-success').addClass('bg-danger').text('TIDAK SEIMBANG');
            $('#btn-submit-jurnal').prop('disabled', true);
        }
    }
    
    // Submit AJAX
    $('#formTambahJurnal').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalTambahJurnal').modal('hide');
                    dt.ajax.reload();
                    $('#formTambahJurnal')[0].reset();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    });
});

function lihatJurnal(id) {
    $.get('<?= base_url('admin/akuntansi/detail-jurnal') ?>/' + id, function(res) {
        if (res.status === 'success') {
            $('#det-nobukti').text(res.header.nomor_bukti);
            $('#det-tanggal').text(res.header.tanggal);
            $('#det-keterangan').text(res.header.keterangan);
            
            let tbody = '';
            let tDebit = 0;
            let tKredit = 0;
            
            res.details.forEach(d => {
                let deb = parseFloat(d.debit);
                let kre = parseFloat(d.kredit);
                tDebit += deb;
                tKredit += kre;
                tbody += `<tr>
                    <td>${d.kode_akun}</td>
                    <td>${d.nama_akun}</td>
                    <td>${d.keterangan || '-'}</td>
                    <td class="text-end">${deb.toLocaleString('id-ID')}</td>
                    <td class="text-end">${kre.toLocaleString('id-ID')}</td>
                </tr>`;
            });
            $('#det-body').html(tbody);
            $('#det-totdebit').text(tDebit.toLocaleString('id-ID'));
            $('#det-totkredit').text(tKredit.toLocaleString('id-ID'));
            
            $('#modalDetail').modal('show');
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
}
</script>
<?= $this->endSection() ?>

