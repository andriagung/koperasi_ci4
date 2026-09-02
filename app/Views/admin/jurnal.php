<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<!-- 5B. VIEW AKUNTANSI & BUKU BESAR -->
            <div id="view-akuntansi" class="panel-view active">
                <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
                    Akuntansi & Jurnal Umum (Fase 3)
                </div>
                
                <div class="alert" style="background-color: #e0f2fe; color: #0284c7; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0284c7;">
                    <i class="fas fa-info-circle"></i> <strong>Modul Double-Entry Accounting</strong>: Ini adalah pondasi Buku Besar Koperasi. Seluruh transaksi operasional akan direkam ke dalam bentuk Jurnal (Debit & Kredit) agar terhubung langsung dengan Laporan Keuangan tanpa ada selisih uang.
                </div>

                <div style="display: flex; gap: 20px;">
                    <!-- TABEL COA -->
                    <div class="table-container" style="flex: 1;">
                        <div class="table-header" style="justify-content: space-between;">
                            <h3>Daftar Akun (Chart of Accounts)</h3>
                            <button class="btn-primary" onclick="alert('Fitur tambah COA segera hadir')"><i class="fas fa-plus"></i> Akun Baru</button>
                        </div>
                        <div class="table-responsive">
                            <table id="tabel-coa" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Kategori</th>
                                        <th>Saldo Normal</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABEL JURNAL -->
                    <div class="table-container" style="flex: 1.5;">
                        <div class="table-header" style="justify-content: space-between;">
                            <h3>Buku Jurnal Umum</h3>
                            <div>
                                <button class="btn-primary" style="background:#0ea5e9; margin-right:5px;" onclick="window.location.href='/admin/akuntansi/export-jurnal'"><i class="fas fa-file-csv"></i> Export CSV</button>
                                <button class="btn-primary" style="background:#16a34a;" onclick="alert('Fitur input Jurnal Manual segera hadir')"><i class="fas fa-pen"></i> Jurnal Manual</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="tabel-jurnal" class="display" style="width:100%">


                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. Bukti</th>
                                        <th>Akun</th>
                                        <th>Posisi</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<?= $this->endSection() ?>