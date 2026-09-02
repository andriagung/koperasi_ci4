<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div id="view-audit" class="panel-view active">
                <div class="page-title">Pusat Keamanan & Audit Trail</div>
                <p style="margin-bottom: 20px; color: var(--text-light);">Log aktivitas sistem digunakan untuk mendeteksi tindakan pengguna dan admin. Anda dapat mengekspor log ini ke PDF/Excel.</p>
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabel-audit-trail" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Tipe User</th>
                                <th>Username</th>
                                <th>Aksi / Modul</th>
                                <th>IP Address</th>
                            <th>User Agent</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>
<?= $this->endSection() ?>
