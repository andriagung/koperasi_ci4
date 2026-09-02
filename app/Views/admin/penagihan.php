<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
            <div id="view-penagihan" class="panel-view active">
                <div class="page-title">
                    Manajemen Penagihan & Aging
                </div>
                
                <div class="dashboard-cards" style="grid-template-columns: repeat(3, 1fr);">
                    <div class="stat-card">
                        <div class="stat-info"><h4>Total Tagihan Jatuh Tempo</h4><h2>Rp <?= number_format($totalTagihan ?? 0, 0, ',', '.') ?></h2></div>
                        <div class="stat-icon icon-orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info"><h4>Tagihan Macet (> 90 Hari)</h4><h2>Rp <?= number_format($totalMacetParah ?? 0, 0, ',', '.') ?></h2></div>
                        <div class="stat-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info"><h4>Anggota Perlu Dihubungi</h4><h2><?= esc($anggotaPerluDihubungi ?? '') ?> Orang</h2></div>
                        <div class="stat-icon icon-blue"><i class="fas fa-phone-alt"></i></div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3 style="font-size: 1.1rem; color: #0f172a;">Daftar Tagihan Jatuh Tempo (Aging)</h3>
                    </div>
                    <div class="table-responsive">
                    <table id="tabelPenagihan" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Anggota</th>
                                <th>Sisa Pinjaman (Tunggakan)</th>
                                <th>Hari Keterlambatan</th>
                                <th>Kolektibilitas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX Server-Side -->
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#tabelPenagihan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('admin/ajax-penagihan') ?>",
            type: "POST",
            data: function (d) {
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
            }
        },
        columns: [
            { orderable: false, searchable: false },
            { orderable: false },
            { orderable: false },
            { orderable: false },
            { orderable: false },
            { orderable: false, searchable: false },
            { orderable: false, searchable: false }
        ],
        initComplete: function() {
            let filterDiv = $('#tabelPenagihan_filter');
            filterDiv.css({
                'display': 'flex',
                'align-items': 'center',
                'justify-content': 'flex-end',
                'gap': '12px'
            });
            let broadcastBtn = $(`
                <button type="button" class="btn btn-primary" style="background:#0ea5e9; border-color:#0ea5e9; padding: 8px 16px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; border-radius: 99px; font-weight: 600; white-space: nowrap; box-shadow: 0 2px 6px rgba(14, 165, 233, 0.2);" onclick="alert('Fitur Broadcast WhatsApp Otomatis Siap Digunakan')">
                    <i class="fab fa-whatsapp"></i> Broadcast Tagihan
                </button>
            `);
            filterDiv.append(broadcastBtn);
        }
    });
});
</script>
<?= $this->endSection() ?>
