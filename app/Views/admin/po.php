<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div id="view-po" class="panel-view active">
                <div class="page-title">Purchase Order (Restock)</div>
                <div class="table-container">
                    <button class="btn-primary" style="margin-bottom: 15px;" onclick="bukaModal('modal-tambah-po')"><i class="fas fa-plus"></i> Buat PO Baru</button>
                    <div class="table-responsive">
                        <table id="tabel-waserda-po" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal PO</th>
                                <th>No. PO</th>
                                <th>Supplier</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Total Tagihan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>
<?= $this->include('admin/waserda_modals') ?>
<?= $this->endSection() ?>
