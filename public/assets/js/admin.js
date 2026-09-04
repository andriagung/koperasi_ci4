let globalConfirmCallback = null;
window.konfirmasiModal = function(message, callback, title = 'Konfirmasi', type = 'danger') {
    let icon = type === 'danger' ? '<i class="fas fa-exclamation-triangle"></i> ' : '<i class="fas fa-question-circle"></i> ';
    let color = type === 'danger' ? '#dc2626' : 'var(--primary)';
    
    $('#global-confirm-title').html(icon + title).css('color', color);
    $('#global-confirm-message').text(message);
    $('#global-confirm-yes').css('background', color);
    
    globalConfirmCallback = callback;
    bukaModal('global-confirm-modal');
};

window.alertModal = function(message, title = 'Informasi', type = 'info') {
    let icon = '<i class="fas fa-info-circle"></i> ';
    let titleColor = 'var(--primary)';
    
    if (type === 'success') {
        icon = '<i class="fas fa-check-circle text-success"></i> ';
        titleColor = '#16a34a';
    } else if (type === 'error') {
        icon = '<i class="fas fa-times-circle text-danger"></i> ';
        titleColor = '#dc2626';
    }
    
    $('#global-alert-title').html(icon + title).css('color', titleColor);
    $('#global-alert-message').text(message);
    bukaModal('global-alert-modal');
};

$(document).ready(function() {
    // Setup event listener untuk tombol konfirmasi global
    $('#global-confirm-yes').on('click', function() {
        if (globalConfirmCallback) {
            globalConfirmCallback();
        }
        tutupModal('global-confirm-modal');
    });

    // Konfigurasi CSRF Token untuk semua AJAX Request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
        }
    });

    if (window.AppConfig.flashMessage) {
        alertModal(window.AppConfig.flashMessage, 'Sukses', 'success');
    }
    
    if (window.AppConfig.flashError) {
        alertModal(window.AppConfig.flashError, 'Error', 'error');
    }

    // ==========================================
    // CHART.JS INITIALIZATION (FASE 8)
    // ==========================================
    
    // 1. Arus Kas Bulanan (Line Chart)
    const ctxArusKas = document.getElementById('arusKasChart');
    if (ctxArusKas) {
        new Chart(ctxArusKas.getContext('2d'), {
            type: 'line',
            data: {
                labels: window.AppConfig.chartArusKas.labels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: window.AppConfig.chartArusKas.pendapatan,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pengeluaran',
                        data: window.AppConfig.chartArusKas.pengeluaran,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // 2. Komposisi Aset Koperasi (Doughnut Chart)
    const ctxAset = document.getElementById('asetChart');
    if (ctxAset) {
        new Chart(ctxAset.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Kas & Bank', 'Piutang Anggota', 'Persediaan Barang'],
                datasets: [{
                    data: [
                        window.AppConfig.neraca.kas,
                        window.AppConfig.neraca.piutang,
                        window.AppConfig.neraca.persediaanBarang
                    ],
                    backgroundColor: ['#2563eb', '#f59e0b', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // 3. Top 5 Produk Waserda (Bar Chart)
    const ctxTopWaserda = document.getElementById('topWaserdaChart');
    if (ctxTopWaserda) {
        new Chart(ctxTopWaserda.getContext('2d'), {
            type: 'bar',
            data: {
                labels: window.AppConfig.topWaserda.labels,
                datasets: [{
                    label: 'Qty Terjual',
                    data: window.AppConfig.topWaserda.data,
                    backgroundColor: '#0ea5e9',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // ==========================================

    // Inisialisasi DataTables
    window.dataTableOptions = {
        "destroy": true,
        "processing": true,
        "pageLength": 10, 
        "lengthMenu": [[10, 25, 50, 75, 100, -1], [10, 25, 50, 75, 100, "Semua"]],
        "lengthChange": true, 
        "dom": '<"dt-action-bar"<"dt-export-buttons"B><"dt-custom-buttons">><"dt-top"lf>rt<"dt-bottom"ip><"clear">',
        "buttons": [
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-export' },
            { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-export' },
            { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn-export' }
        ],
        "language": {
            "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "lengthMenu": "Tampilkan _MENU_ data",
            "loadingRecords": "Sedang memuat...",
            "processing": "Sedang memproses...",
            "search": "",
            "searchPlaceholder": "Cari data...",
            "zeroRecords": "Tidak ditemukan data yang sesuai",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    };
    
    if ($.fn.DataTable) {

    // Simpan Pinjam Tables
    if ($('#tabel-simpanan').length) {
        $('#tabel-simpanan').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-simpanan", "type": "POST" }
        }));
    }
    if ($('#tabel-penarikan').length) {
        $('#tabel-penarikan').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-penarikan", "type": "POST" }
        }));
    }
    if ($('#tabelMasterAnggota').length) {
        window.tableAnggota = $('#tabelMasterAnggota').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-anggota", "type": "POST" },
            "columns": [
                {
                    data: null, 
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: "nip"},
                {data: "nama_lengkap"},
                {data: "divisi"},
                {data: "no_hp"},
                {data: "status_badge"},
                {data: "aksi", orderable: false, searchable: false}
            ]
        }));
    }
    if ($('#tabelAnggota').length) {
        window.tableDashboardAnggota = $('#tabelAnggota').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-dashboard-anggota", "type": "POST" }
        }));
    }
    if ($('#tabel-pinjaman').length) {
        $('#tabel-pinjaman').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-pinjaman", "type": "POST" }
        }));
    }

    // Waserda Tables
    if ($('#tabel-waserda-produk').length) {
        $('#tabel-waserda-produk').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-waserda-produk", "type": "POST" }
        }));
    }
    if ($('#tabel-waserda-po').length) {
        $('#tabel-waserda-po').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-waserda-po", "type": "POST" }
        }));
    }
    if ($('#tabel-waserda-transaksi').length) {
        $('#tabel-waserda-transaksi').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-waserda-transaksi", "type": "POST" }
        }));
    }
    if ($('#tabel-waserda-po').length) {
        $('#tabel-waserda-po').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-waserda-po", "type": "POST" }
        }));
    }
    if ($('#tabel-stock-opname').length) {
        $('#tabel-stock-opname').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-stock-opname", "type": "POST" }
        }));
    }

    // Akuntansi Tables
    if ($('#tabel-coa').length) {
        initDataTable('#tabel-coa', '/admin/akuntansi/ajaxDaftarCoa');
    }

    if ($('#tabel-jurnal').length) {
        initDataTable('#tabel-jurnal', '/admin/akuntansi/ajaxJurnalUmum');
    }
    
    // Kas Transaksi Datatable
    if ($('#tabel-kas').length) {
        initDataTable('#tabel-kas', '/admin/akuntansi/ajaxDaftarKas');
    }
    
    // Form Kas Submit
    if ($('#form-kas').length) {
        $('#form-kas').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '/admin/akuntansi/simpanKas',
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        alert(res.message);
                        closeKasModal();
                        $('#tabel-kas').DataTable().ajax.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function(err) {
                    alert('Gagal menyimpan transaksi kas');
                }
            });
        });
    }

    // Pengaturan Tables
    if ($('#tabel-admin-users').length) {
        $('#tabel-admin-users').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-admin-users", "type": "POST" }
        }));
    }
    if ($('#tabel-audit-trail').length) {
        $('#tabel-audit-trail').DataTable($.extend(true, {}, window.dataTableOptions, {
            "serverSide": true,
            "ajax": { "url": "/admin/ajax-audit-trail", "type": "POST" },
            "order": [[ 0, "desc" ]]
        }));
    }
    
    // tabelPenagihan initialized in view penagihan.php with explicit CSRF
        
        if ($('#tabel-shu-simulasi').length) {
            $('#tabel-shu-simulasi').DataTable(window.dataTableOptions);
        }
        if ($('#tabel-riwayat-shu').length) {
            $('#tabel-riwayat-shu').DataTable(window.dataTableOptions);
        }

        // Filter Dropdown
        $('#filterStatus').on('change', function () {
            if (typeof window.tableDashboardAnggota !== 'undefined') {
                window.tableDashboardAnggota.columns(5).search(this.value).draw();
            } else if (typeof window.tableAnggota !== 'undefined') {
                window.tableAnggota.columns(5).search(this.value).draw();
            }
        });

        // DataTables Row Detail
        $('#tabelAnggota tbody, #tabelMasterAnggota tbody, #tabelPenagihan tbody').on('click', '.btn-detail', function () {
            var table = null;
            var tableId = $(this).closest('table').attr('id');
            
            if (tableId === 'tabelAnggota' && typeof window.tableDashboardAnggota !== 'undefined') {
                table = window.tableDashboardAnggota;
            } else if (tableId === 'tabelMasterAnggota' && typeof window.tableAnggota !== 'undefined') {
                table = window.tableAnggota;
            } else if (tableId === 'tabelPenagihan' && typeof window.tablePenagihan !== 'undefined') {
                table = window.tablePenagihan;
            }
            if (!table) return;

            var tr = $(this).closest('tr');
            var row = table.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                var d = row.data();
                var html = '';
                
                if (tableId === 'tabelPenagihan') {
                    html = '<div style="padding:20px; background:#fef2f2; border-radius:8px; border:1px solid #fca5a5; margin:5px 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">' +
                           '<h4 style="margin-bottom:15px; color:#991b1b; border-bottom:1px solid #fca5a5; padding-bottom:10px;"><i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i> Detail Penagihan Anggota</h4>' +
                           '<table style="width:100%; border-collapse:collapse; font-size:0.9rem;">' +
                           '<tr><td style="padding:8px 0; width:180px; color:#7f1d1d;">NIP</td><td>: <strong>' + d[7] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#7f1d1d;">Nama Lengkap</td><td>: <strong>' + d[8] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#7f1d1d;">Nomor HP</td><td>: <strong>' + d[9] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#7f1d1d;">Total Tunggakan</td><td>: <strong style="color:#b91c1c;">' + d[10] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#7f1d1d;">Jumlah Angsuran Nunggak</td><td>: <strong>' + d[11] + ' Kali</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#7f1d1d;">Hari Keterlambatan</td><td>: <strong>' + d[12] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#7f1d1d;">Kolektibilitas</td><td>: <strong>' + d[13] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#7f1d1d;">Jatuh Tempo Terlama</td><td>: <strong>' + d[14] + '</strong></td></tr>' +
                           '</table></div>';
                } else {
                    var cleanName = typeof d[1] === 'string' ? d[1].replace(/(<([^>]+)>)/gi, "") : d[1];
                    html = '<div style="padding:20px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; margin:5px 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">' +
                           '<h4 style="margin-bottom:15px; color:#0f172a; border-bottom:1px solid #e2e8f0; padding-bottom:10px;"><i class="fas fa-id-card" style="color:var(--primary); margin-right:8px;"></i> Profil Detail Anggota</h4>' +
                           '<table style="width:100%; border-collapse:collapse; font-size:0.9rem;">' +
                           '<tr><td style="padding:8px 0; width:150px; color:#64748b;">NIP</td><td>: <strong style="color:#0f172a;">' + d[0] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#64748b;">Nama Lengkap</td><td>: <strong style="color:#0f172a;">' + cleanName + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#64748b;">Divisi</td><td>: <strong style="color:#0f172a;">' + d[2] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#64748b;">Nomor HP</td><td>: <strong style="color:#0f172a;">' + d[3] + '</strong></td></tr>' +
                           '<tr><td style="padding:8px 0; color:#64748b;">Status Akun</td><td>: ' + d[4] + '</td></tr>' +
                           '<tr><td style="padding:8px 0; color:#64748b;">Kolektibilitas</td><td>: ' + d[5] + '</td></tr>' +
                           '</table></div>';
                }
                
                row.child(html).show();
                tr.addClass('shown');
            }
        });
        
        // Inisialisasi Generik untuk semua tabel dengan class .datatable yang belum terinisialisasi
        if ($('.datatable').length) {
            $('.datatable').not('.dataTable').DataTable(window.dataTableOptions);
        }
    }
});

// Sidebar Toggle for Mobile
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active-sidebar');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
}

// Script untuk pindah antar menu
function switchView(viewId, element) {
    // Sembunyikan semua konten panel
    document.querySelectorAll('.panel-view').forEach(panel => {
        panel.classList.remove('active');
    });
    
    // Tampilkan panel yang diklik
    document.getElementById(viewId).classList.add('active');

    // Reset warna highlight di sidebar
    document.querySelectorAll('.nav-item').forEach(nav => {
        nav.classList.remove('active');
    });
    
    // Beri warna highlight pada menu yang sedang aktif
    element.classList.add('active');
}

// Modal Functions
function bukaModal(id) { document.getElementById(id).classList.add('active'); }
function tutupModal(id) { document.getElementById(id).classList.remove('active'); }

// Fitur Buku Besar (Filter DataTables Jurnal)
function lihatBukuBesar(kodeAkun) {
    if ($.fn.DataTable) {
        var tableJurnal = $('#view-akuntansi .table-container:nth-child(2) table').DataTable();
        tableJurnal.search(kodeAkun).draw();
        alert('Buku Besar difilter untuk Akun: ' + kodeAkun);
    }
}

// Action Functions
function approve(type, id) {
    let msg = 'Yakin ingin menyetujui pengajuan ini?';
    if(type === 'angsuran') msg = 'Proses pelunasan tagihan ini? (Otomatis mencatat Jurnal Kas)';
    
    if(confirm(msg)) {
        $.post(`/admin/approve-${type}/${id}`, function(res) {
            if(res.status === 'success') {
                if(type === 'angsuran') alert('Pembayaran berhasil diproses!');
                else alert('Berhasil disetujui!');
                
                location.reload();
            } else {
                alert('Gagal memproses aksi.');
            }
        });
    }
}

function reject(type, id) {
    if(confirm('Yakin ingin menolak pengajuan ini?')) {
        $.post(`/admin/reject-${type}/${id}`, function(res) {
            if(res.status === 'success') {
                alert('Berhasil ditolak!');
                location.reload();
            } else {
                alert('Gagal menolak pengajuan.');
            }
        });
    }
}

// CRUD Anggota
function editAnggotaModal(data) {
    $('#edit_nip').val(data.nip);
    $('#edit_nama').val(data.nama_lengkap);
    $('#edit_divisi').val(data.divisi);
    $('#edit_status').val(data.status);
    $('#edit_no_hp').val(data.no_hp);
    $('#edit_tempat_lahir').val(data.tempat_lahir);
    $('#edit_tanggal_lahir').val(data.tanggal_lahir);
    $('#edit_jenis_kelamin').val(data.jenis_kelamin);
    $('#edit_alamat').val(data.alamat);
    $('#edit_email').val(data.email);
    $('#edit_pekerjaan').val(data.pekerjaan);
    $('#edit_status_perkawinan').val(data.status_perkawinan);
    
    $('#form-edit-anggota').attr('action', `/admin/edit-anggota/${data.hash_id}`);
    bukaModal('modal-edit-anggota');
}

function cetakKartu(data) {
    $('#kartu_nama').text(data.nama_lengkap.toUpperCase());
    $('#kartu_nip').text(data.nip);
    $('#kartu_divisi').text(data.divisi);
    $('#kartu_tgl').text(data.tanggal_bergabung || '-');
    bukaModal('modal-cetak-kartu');
}

function printKartu() {
    var content = document.getElementById('kartu-anggota-preview').outerHTML;
    var win = window.open('', '_blank', 'width=800,height=600');
    win.document.write('<html><head><title>Cetak Kartu Anggota</title>');
    win.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">');
    win.document.write('<style>body { font-family: "Inter", sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f8fafc; } :root { --primary: #059669; --primary-dark: #047857; } </style>');
    win.document.write('</head><body>');
    win.document.write(content);
    win.document.write('</body></html>');
    win.document.close();
    win.setTimeout(function() {
        win.print();
    }, 500);
}

function updatePOModal(id, status, produk_id, jumlah) {
    $('#form-update-po').attr('action', `/admin/po/update-status/${id}`);
    $('#update_po_status').val(status);
    $('#update_po_produk_id').val(produk_id);
    $('#update_po_jumlah').val(jumlah);
    bukaModal('modal-update-po');
}

function editSupplierModal(id, kode, nama, kontak, npwp, bank, rek, alamat) {
    $('#form-edit-supplier').attr('action', `/admin/gudang/edit-supplier/${id}`);
    $('#edit_supplier_kode').val(kode);
    $('#edit_supplier_nama').val(nama);
    $('#edit_supplier_kontak').val(kontak);
    $('#edit_supplier_npwp').val(npwp);
    $('#edit_supplier_bank').val(bank);
    $('#edit_supplier_rek').val(rek);
    $('#edit_supplier_alamat').val(alamat);
    bukaModal('modal-edit-supplier');
}

function editKategoriModal(id, nama, deskripsi) {
    $('#form-edit-kategori').attr('action', `/admin/waserda/edit-kategori/${id}`);
    $('#edit_kategori_nama').val(nama);
    $('#edit_kategori_deskripsi').val(deskripsi);
    bukaModal('modal-edit-kategori');
}

function hapusAnggota(id) {
    konfirmasiModal('Yakin ingin menghapus anggota ini? Seluruh data simpanan dan pinjamannya dapat terpengaruh!', function() {
        $.post(`/admin/hapus-anggota/${id}`, function(res) {
            if(res.status === 'success') {
                alertModal('Anggota berhasil dihapus.', 'Sukses', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alertModal('Gagal menghapus anggota.', 'Error', 'error');
            }
        });
    }, 'Hapus Anggota', 'danger');
}

function resetPinAnggota(id) {
    konfirmasiModal('Yakin ingin mereset PIN anggota ini ke 123456?', function() {
        $.post(`/admin/reset-pin-anggota/${id}`, function(res) {
            if(res.status === 'success') {
                alertModal('PIN berhasil direset ke 123456.', 'Sukses', 'success');
            } else {
                alertModal('Gagal mereset PIN.', 'Error', 'error');
            }
        });
    }, 'Reset PIN', 'danger');
}

// CRUD Promo/Produk Waserda
function editPromo(id, nama, harga_normal, harga_promo, ikon, is_active, harga_beli, stok, stok_minimum) {
    $('#edit_promo_nama').val(nama);
    $('#edit_promo_normal').val(harga_normal);
    $('#edit_promo_harga').val(harga_promo);
    $('#edit_promo_ikon').val(ikon);
    $('#edit_promo_beli').val(harga_beli);
    $('#edit_promo_stok').val(stok);
    $('#edit_promo_stokmin').val(stok_minimum);
    if(is_active) {
        $('#edit_promo_active').prop('checked', true);
    } else {
        $('#edit_promo_active').prop('checked', false);
    }
    $('#form-edit-promo').attr('action', `/admin/edit-produk/${id}`);
    bukaModal('modal-edit-promo');
}

function hapusPromo(id) {
    if(confirm('Yakin ingin menghapus produk/promo ini?')) {
        $.post(`/admin/hapus-produk/${id}`, function(res) {
            if(res.status === 'success') {
                alert('Produk berhasil dihapus.');
                location.reload();
            } else {
                alert('Gagal menghapus produk.');
            }
        });
    }
}

// CRUD Admin Users
function editAdmin(id, nama, username, role) {
    $('#edit_admin_nama').val(nama);
    $('#edit_admin_username').val(username);
    $('#edit_admin_role').val(role);
    $('#form-edit-admin').attr('action', `/admin/edit-admin/${id}`);
    bukaModal('modal-edit-admin');
}

function hapusAdmin(id) {
    if(confirm('Yakin ingin menghapus akses admin ini?')) {
        $.post(`/admin/hapus-admin/${id}`, function(res) {
            if(res.status === 'success') {
                alert('Admin berhasil dihapus.');
                location.reload();
            } else {
                alert('Gagal menghapus admin.');
            }
        });
    }
}

// POS System Logic
let posKeranjang = [];
let posTotal = 0;

function renderKeranjang() {
    let html = '';
    posTotal = 0;
    posKeranjang.forEach((item, index) => {
        let subtotal = item.harga * item.qty;
        posTotal += subtotal;
        html += `
            <tr>
                <td>${item.nama}</td>
                <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td><input type="number" value="${item.qty}" min="1" onchange="updateQty(${index}, this.value)" style="width: 60px; padding: 5px;"></td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td class="action-btns"><button class="btn-action delete" onclick="hapusDariKeranjang(${index})"><i class="fas fa-times"></i></button></td>
            </tr>
        `;
    });
    $('#posKeranjang').html(html);
    $('#posTotalSpan').text(`Rp ${posTotal.toLocaleString('id-ID')}`);
}

function handleBarcodeScan(e) {
    if (e.key === 'Enter') {
        let barcode = e.target.value;
        if (!barcode) return;
        $.get('/admin/waserda/cari-barcode/' + barcode, function(res) {
            if (res.status === 'success') {
                let p = res.data;
                tambahKeKeranjangData(p.id, p.nama_produk, p.harga_promo > 0 ? p.harga_promo : p.harga_normal, p.harga_beli, p.stok);
                e.target.value = '';
            } else {
                alert('Produk tidak ditemukan atau tidak aktif.');
            }
        });
    }
}

function tambahKeKeranjangData(id, nama, harga, hargabeli, stok) {
    let existing = posKeranjang.find(i => i.id == id);
    if(existing) {
        if(existing.qty + 1 > stok) {
            alert("Stok tidak mencukupi!"); return;
        }
        existing.qty += 1;
    } else {
        if(stok < 1) {
            alert("Stok habis!"); return;
        }
        posKeranjang.push({id: id, nama: nama, harga: parseInt(harga), hargabeli: parseInt(hargabeli), stok: parseInt(stok), qty: 1});
    }
    renderKeranjang();
}

function tambahKeKeranjang() {
    let select = document.getElementById('posProdukSelect');
    if(!select.value) { alert("Pilih produk terlebih dahulu!"); return; }
    
    let id = select.value;
    let nama = select.options[select.selectedIndex].getAttribute('data-nama');
    let harga = parseInt(select.options[select.selectedIndex].getAttribute('data-harga'));
    let hargabeli = parseInt(select.options[select.selectedIndex].getAttribute('data-hargabeli')) || 0;
    let stok = parseInt(select.options[select.selectedIndex].getAttribute('data-stok')) || 0;
    
    tambahKeKeranjangData(id, nama, harga, hargabeli, stok);
}

function updateQty(index, qty) {
    let item = posKeranjang[index];
    qty = parseInt(qty) || 1;
    if(qty > item.stok) {
        alert("Stok hanya tersedia " + item.stok);
        qty = item.stok;
    }
    posKeranjang[index].qty = qty;
    renderKeranjang();
}

function hapusDariKeranjang(index) {
    posKeranjang.splice(index, 1);
    renderKeranjang();
}

function checkoutPOS(metode) {
    if(posKeranjang.length === 0) {
        alert("Keranjang masih kosong!"); return;
    }
    let anggota_id = $('#posAnggota').val();
    if(metode === 'kasbon' && !anggota_id) {
        alert("Kasbon diwajibkan untuk memilih Anggota pembeli!"); return;
    }
    
    if(confirm(`Yakin memproses pembayaran ini via ${metode.toUpperCase()} senilai Rp ${posTotal.toLocaleString('id-ID')}?`)) {
        $.post('/admin/checkout-kasir', {
            metode: metode,
            anggota_id: anggota_id,
            total: posTotal,
            items: posKeranjang
        }, function(res) {
            if(res.status === 'success') {
                alert("Pembayaran berhasil dicatat!");
                posKeranjang = [];
                $('#posAnggota').val('');
                renderKeranjang();
            } else {
                alert("Terjadi kesalahan.");
            }
        });
    }
}

function distribusiSHU() {
    if(confirm('Aksi ini akan mencatat pembagian SHU (Jasa Anggota) ke saldo Simpanan seluruh anggota secara otomatis dan proporsional. Yakin ingin mendistribusikan SHU Tahun ini?')) {
        alert('Proses distribusi SHU berhasil dijalankan! (Mode Simulasi)');
        location.reload();
    }
}

function toggleModeLaporan(mode) {
    if (mode === 'pengurus') {
        $('#laporan-pengurus').show();
        $('#laporan-akuntan').hide();
        $('#btn-mode-pengurus').css({'background': 'var(--primary)', 'color': 'white'});
        $('#btn-mode-akuntan').css({'background': 'white', 'color': 'var(--primary)'});
    } else {
        $('#laporan-pengurus').hide();
        $('#laporan-akuntan').show();
        $('#btn-mode-akuntan').css({'background': 'var(--primary)', 'color': 'white'});
        $('#btn-mode-pengurus').css({'background': 'white', 'color': 'var(--primary)'});
    }
}

// Waserda Produk Functions
function editProdukModal(id, nama, beli, normal, promo, stok, stok_min, is_active, tgl_kadaluarsa) {
    $('#form-edit-promo').attr('action', '/admin/edit-produk/' + id);
    $('#edit_promo_nama').val(nama);
    $('#edit_promo_beli').val(beli);
    $('#edit_promo_normal').val(normal);
    $('#edit_promo_harga').val(promo);
    $('#edit_promo_stok').val(stok);
    $('#edit_promo_stokmin').val(stok_min);
    $('#edit_promo_kadaluarsa').val(tgl_kadaluarsa);
    
    if (is_active == 1) {
        $('#edit_promo_active').prop('checked', true);
    } else {
        $('#edit_promo_active').prop('checked', false);
    }
    
    bukaModal('modal-edit-promo');
}

function hapusProduk(id) {
    if (confirm('Yakin ingin menghapus produk ini?')) {
        // Create a form to send POST request
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/hapus-produk/' + id;
        
        // Add CSRF token
        var csrfName = $('meta[name="X-CSRF-TOKEN"]').attr('content'); 
        document.body.appendChild(form);
        form.submit();
    }
}

// Fitur Simulasi Pinjaman
function hitungSimulasi() {
    var nominal = parseFloat($('#simulasi_nominal').val());
    var tenor = parseInt($('#simulasi_tenor').val());
    var bungaPercent = parseFloat($('#simulasi_bunga').val());
    
    if (isNaN(nominal) || nominal <= 0) {
        alert("Nominal pinjaman tidak valid");
        return;
    }
    
    var pokok = nominal / tenor;
    var bunga = nominal * (bungaPercent / 100);
    var total = pokok + bunga;
    
    $('#hasil_pokok').text('Rp ' + Math.round(pokok).toLocaleString('id-ID'));
    $('#hasil_bunga').text('Rp ' + Math.round(bunga).toLocaleString('id-ID'));
    $('#hasil_total').text('Rp ' + Math.round(total).toLocaleString('id-ID'));
    
    $('#hasil_simulasi').slideDown();
}

function lihatRincianCicilan(pinjaman_id) {
    // Tampilkan loading di tabel
    $('#body-rincian-cicilan').html('<tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>');
    bukaModal('modal-rincian-cicilan');
    
    $.get('/admin/rincian-cicilan/' + pinjaman_id, function(res) {
        if(res.status === 'success') {
            var html = '';
            res.data.forEach(function(row) {
                var total = parseFloat(row.pokok) + parseFloat(row.bunga);
                var statusBadge = '';
                if(row.status_bayar === 'Lunas') {
                    statusBadge = '<span class="badge bg-success">Lunas</span>';
                } else if(row.status_bayar === 'Terlambat') {
                    statusBadge = '<span class="badge bg-danger">Terlambat</span>';
                } else {
                    statusBadge = '<span class="badge bg-warning">Belum Bayar</span>';
                }
                
                html += '<tr>';
                html += '<td>' + row.bulan_ke + '</td>';
                html += '<td>' + row.jatuh_tempo + '</td>';
                html += '<td>Rp ' + Math.round(row.pokok).toLocaleString('id-ID') + '</td>';
                html += '<td>Rp ' + Math.round(row.bunga).toLocaleString('id-ID') + '</td>';
                html += '<td>Rp ' + Math.round(total).toLocaleString('id-ID') + '</td>';
                html += '<td>' + statusBadge + '</td>';
                html += '</tr>';
            });
            
            if(res.data.length === 0) {
                html = '<tr><td colspan="6" style="text-align:center;">Tidak ada data jadwal cicilan.</td></tr>';
            }
            
            $('#body-rincian-cicilan').html(html);
        } else {
            $('#body-rincian-cicilan').html('<tr><td colspan="6" style="text-align:center;color:red;">Gagal memuat data</td></tr>');
        }
    });
}
