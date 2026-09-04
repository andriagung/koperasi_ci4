<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-cash-register text-success me-2"></i>Kasir PPOB</h2>
        <p class="text-muted">Layanan pembayaran digital dan tagihan (Mock)</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('admin/ppob') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Manajemen
        </a>
    </div>
</div>

<div class="row">
    <!-- Form Kasir -->
    <div class="col-md-8">
        <div class="card glass-card">
            <div class="card-body">
                
    <form action="<?= base_url('admin/ppob/checkout') ?>" method="POST">
        <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold">1. Pilih Kategori Layanan</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($kategori as $k): ?>
                            <button type="button" class="btn btn-outline-primary btn-kategori" data-kategori="<?= $k ?? '' ?>">
                                <?= $k ?? '' ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4" id="section-produk" style="display:none;">
                        <label class="form-label fw-bold">2. Pilih Produk</label>
                        <select id="produk_id" name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                        </select>
                    </div>

                    <div class="mb-4" id="section-pelanggan" style="display:none;">
                        <label class="form-label fw-bold">3. Nomor Pelanggan / HP / Meteran</label>
                        <input type="text" id="no_pelanggan" name="no_pelanggan" class="form-control" placeholder="Masukkan nomor..." required>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ringkasan & Pembayaran -->
    <div class="col-md-4">
        <div class="card glass-card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Ringkasan Transaksi</h5>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="mb-3">
                    <p class="text-muted mb-1">Produk</p>
                    <h5 id="summary-produk">-</h5>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1">Total Tagihan / Harga</p>
                    <h3 id="summary-harga" class="text-success">Rp 0</h3>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tipe Pelanggan</label>
                    <select id="tipe_pelanggan" class="form-select">
                        <option value="umum">Umum (Tunai)</option>
                        <option value="anggota">Anggota Koperasi</option>
                    </select>
                </div>

                <div class="mb-3" id="div_anggota" style="display:none;">
                    <label class="form-label fw-bold">Pilih Anggota</label>
                    <select id="anggota_id" name="anggota_id" class="form-select">
                        <option value="">-- Cari Anggota --</option>
                        <?php foreach($anggota as $a): ?>
                            <option value="<?= $a['id'] ?? '' ?>"><?= $a['nomor_anggota'] ?? '' ?> - <?= $a['nama_lengkap'] ?? '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4" id="div_metode" style="display:none;">
                    <label class="form-label fw-bold">Metode Pembayaran</label>
                    <select id="metode" name="metode" class="form-select">
                        <option value="Tunai">Tunai</option>
                        <option value="Kasbon">Potong Gaji (Kasbon)</option>
                    </select>
                </div>

                <div class="mt-auto">
                    <button type="button" id="btn-bayar" class="btn btn-success w-100 py-3 fw-bold fs-5" disabled>
                        BAYAR SEKARANG
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let produkList = [];
    let selectedProduk = null;

    // 1. Kategori Click
    $('.btn-kategori').click(function() {
        $('.btn-kategori').removeClass('active');
        $(this).addClass('active');
        
        let kategori = $(this).data('kategori');
        
        // Fetch Produk via Ajax
        $.get('/admin/api/ppob/produk', { kategori: kategori }, function(res) {
            if (res.status === 'success') {
                produkList = res.data;
                let html = '<option value="">-- Pilih Produk --</option>';
                produkList.forEach(p => {
                    html += `<option value="${p.id}">[${p.provider}] ${p.nama_produk} - Rp ${parseInt(p.harga_jual).toLocaleString('id-ID')}</option>`;
                });
                $('#produk_id').html(html);
                $('#section-produk').fadeIn();
                $('#section-pelanggan').fadeOut();
                resetSummary();
            }
        });
    });

    // 2. Produk Change
    $('#produk_id').change(function() {
        let id = $(this).val();
        if (id) {
            selectedProduk = produkList.find(p => p.id == id);
            $('#section-pelanggan').fadeIn();
            
            $('#summary-produk').text(selectedProduk.nama_produk);
            $('#summary-harga').text('Rp ' + parseInt(selectedProduk.harga_jual).toLocaleString('id-ID'));
            checkReady();
        } else {
            $('#section-pelanggan').fadeOut();
            resetSummary();
        }
    });

    // 3. No Pelanggan Input
    $('#no_pelanggan').on('input', function() {
        checkReady();
    });

    // 4. Tipe Pelanggan Change
    $('#tipe_pelanggan').change(function() {
        if ($(this).val() === 'anggota') {
            $('#div_anggota').show();
            $('#div_metode').show();
        } else {
            $('#div_anggota').hide();
            $('#div_metode').hide();
            $('#anggota_id').val('');
            $('#metode').val('Tunai');
        }
        checkReady();
    });

    function resetSummary() {
        $('#summary-produk').text('-');
        $('#summary-harga').text('Rp 0');
        $('#no_pelanggan').val('');
        selectedProduk = null;
        checkReady();
    }

    function checkReady() {
        let ready = true;
        if (!selectedProduk) ready = false;
        if ($('#no_pelanggan').val().trim() === '') ready = false;
        
        if ($('#tipe_pelanggan').val() === 'anggota' && !$('#anggota_id').val()) {
            ready = false;
        }

        $('#btn-bayar').prop('disabled', !ready);
    }

    $('#btn-bayar').click(function() {
        let data = {
            produk_id: $('#produk_id').val(),
            no_pelanggan: $('#no_pelanggan').val(),
            tipe_pelanggan: $('#tipe_pelanggan').val(),
            anggota_id: $('#anggota_id').val(),
            metode: $('#tipe_pelanggan').val() === 'anggota' ? $('#metode').val() : 'Tunai'
        };

        Swal.fire({
            title: 'Proses Transaksi?',
            html: `Membayar <b>${selectedProduk.nama_produk}</b> ke nomor <b>${data.no_pelanggan}</b><br>Total: Rp ${parseInt(selectedProduk.harga_jual).toLocaleString('id-ID')}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Bayar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menghubungkan ke biller PPOB',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.post('/admin/api/ppob/checkout', data, function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Sukses!', 'Transaksi PPOB Berhasil', 'success').then(() => {
                            window.location.href = '/admin/ppob?tab=transaksi';
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

