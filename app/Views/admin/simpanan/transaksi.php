<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
    Transaksi Simpanan (Setoran & Penarikan)
    <div style="display:flex; gap:10px;">
        <button class="btn-primary" style="background:#f59e0b; padding:8px 12px;" onclick="bukaModal('modal-koreksi-simpanan')"><i class="fas fa-edit"></i> Koreksi Saldo</button>
        <button class="btn-primary" style="background:#3b82f6; padding:8px 12px;" onclick="bukaModal('modal-transfer-simpanan')"><i class="fas fa-exchange-alt"></i> Transfer Saldo</button>
    </div>
</div>
<?php if(session()->getFlashdata('message')): ?>
    <div class="alert alert-success" style="padding:15px; margin-bottom:20px; background-color:#d1fae5; color:#065f46; border-radius:6px;">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" style="padding:15px; margin-bottom:20px; background-color:#fee2e2; color:#991b1b; border-radius:6px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 2.2fr; gap:20px; align-items: start;">
    <!-- Form Transaksi -->
    <div class="table-container" style="margin-bottom:0;">
        <h4 style="margin-bottom:15px; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">Input Transaksi</h4>
        <form action="/admin/simpanan/proses" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Jenis Transaksi</label>
                <div style="display:flex; gap:15px; margin-top:5px;">
                    <label style="display:flex; align-items:center; gap:5px;"><input type="radio" name="jenis_transaksi" value="setoran" checked style="width:auto;"> Setoran (Masuk)</label>
                    <label style="display:flex; align-items:center; gap:5px;"><input type="radio" name="jenis_transaksi" value="penarikan" style="width:auto;"> Penarikan (Keluar)</label>
                </div>
            </div>
            
            <div class="form-group mt-3">
                <label>Tanggal Transaksi</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Anggota</label>
                <select name="anggota_id" required class="select2">
                    <option value="">Pilih Anggota</option>
                    <?php foreach($anggota as $a): ?>
                        <option value="<?= idhash_encode($a['id'] ?? '') ?>"><?= esc($a['nama_lengkap'] ?? '') ?> (<?= esc($a['nip'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Jenis Simpanan</label>
                <select name="jenis_simpanan_id" required>
                    <option value="">Pilih Jenis</option>
                    <?php foreach($jenis_simpanan as $j): ?>
                        <option value="<?= idhash_encode($j['id'] ?? '') ?>"><?= esc($j['nama'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nominal (Rp)</label>
                <input type="number" name="nominal" required min="1000">
            </div>
            
            <div class="form-group">
                <label>Metode Pembayaran</label>
                <select name="metode_pembayaran" id="metode_pembayaran" onchange="toggleMetode()">
                    <option value="Tunai">Tunai</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                </select>
            </div>
            
            <div class="form-group" id="kas_div">
                <label>Kas Tujuan / Asal (Tunai)</label>
                <select name="kas_id">
                    <?php foreach($kas as $k): ?>
                        <option value="<?= idhash_encode($k['id'] ?? '') ?>"><?= esc($k['nama'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="bank_div" style="display:none;">
                <label>Rekening Bank (Transfer)</label>
                <select name="bank_id">
                    <?php foreach($bank as $b): ?>
                        <option value="<?= idhash_encode($b['id'] ?? '') ?>"><?= esc($b['nama_bank'] ?? '') ?> - <?= esc($b['nomor_rekening'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                $('#tabelTransaksi').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "<?= base_url('admin/simpanan/datatablesTransaksi') ?>",
                        type: "POST",
                        data: function (d) {
                            d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
                        }
                    },
                    columns: [
                        {data: 'tanggal'},
                        {data: 'nomor_transaksi'},
                        {data: 'nama_lengkap'},
                        {data: 'jenis', orderable: false, searchable: false},
                        {data: 'nominal'},
                        {data: 'aksi', orderable: false, searchable: false}
                    ],
                    order: [[0, 'desc']],
                    "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json" }
                });
            });
            function toggleMetode() {
                var metode = document.getElementById('metode_pembayaran').value;
                if(metode === 'Tunai') {
                    document.getElementById('kas_div').style.display = 'block';
                    document.getElementById('bank_div').style.display = 'none';
                } else {
                    document.getElementById('kas_div').style.display = 'none';
                    document.getElementById('bank_div').style.display = 'block';
                }
            }
            </script>
            
            <div class="form-group">
                <label>Keterangan Tambahan</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan opsional..."></textarea>
            </div>
            
            <button type="submit" class="btn-primary" style="width:100%; margin-top:15px; padding:12px;">Proses Transaksi</button>
        </form>
    </div>
    
    <!-- Histori Transaksi -->
    <div class="table-container" style="margin-bottom:0;">
        <h4 style="margin-bottom:15px; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">Histori Transaksi Terakhir</h4>
        <div style="overflow-x:auto;">
            <table id="tabelTransaksi" class="display" style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:2px solid #e2e8f0;">
                        <th style="padding:8px;">Tanggal</th>
                        <th style="padding:8px;">Nomor</th>
                        <th style="padding:8px;">Anggota</th>
                        <th style="padding:8px;">Jenis</th>
                        <th style="padding:8px;">Nominal</th>
                        <th style="padding:8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL KOREKSI SIMPANAN -->
<div class="modal-overlay" id="modal-koreksi-simpanan">
    <div class="modal-content" style="width: 800px; max-width: 95%;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-koreksi-simpanan')"></i>
        <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-edit"></i> Koreksi Saldo Simpanan</h3>
        <form action="/admin/simpanan/koreksi" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Anggota</label>
                <select name="anggota_id" required class="select2">
                    <option value="">Pilih Anggota</option>
                    <?php foreach($anggota as $a): ?>
                        <option value="<?= idhash_encode($a['id'] ?? '') ?>"><?= esc($a['nama_lengkap'] ?? '') ?> (<?= esc($a['nip'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>Jenis Simpanan</label>
                    <select name="jenis_simpanan" required>
                        <option value="Pokok">Pokok</option>
                        <option value="Wajib">Wajib</option>
                        <option value="Sukarela">Sukarela</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipe Koreksi</label>
                    <select name="tipe" required>
                        <option value="Tambah">Tambah Saldo</option>
                        <option value="Kurang">Kurangi Saldo</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-top:10px;">
                <label>Nominal (Rp)</label>
                <input type="number" name="nominal" placeholder="0" required min="1">
            </div>
            <div class="form-group">
                <label>Keterangan / Alasan</label>
                <textarea name="keterangan" rows="2" required></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Proses Koreksi</button>
        </form>
    </div>
</div>

<!-- MODAL TRANSFER SIMPANAN -->
<div class="modal-overlay" id="modal-transfer-simpanan">
    <div class="modal-content" style="width: 800px; max-width: 95%;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-transfer-simpanan')"></i>
        <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-exchange-alt"></i> Transfer Saldo Simpanan</h3>
        <form action="/admin/simpanan/transfer" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Anggota</label>
                <select name="anggota_id" required class="select2">
                    <option value="">Pilih Anggota</option>
                    <?php foreach($anggota as $a): ?>
                        <option value="<?= idhash_encode($a['id'] ?? '') ?>"><?= esc($a['nama_lengkap'] ?? '') ?> (<?= esc($a['nip'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>Dari Simpanan</label>
                    <select name="dari_simpanan" required>
                        <option value="Sukarela">Sukarela</option>
                        <option value="Pokok">Pokok</option>
                        <option value="Wajib">Wajib</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ke Simpanan</label>
                    <select name="ke_simpanan" required>
                        <option value="Wajib">Wajib</option>
                        <option value="Pokok">Pokok</option>
                        <option value="Sukarela">Sukarela</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 10px;">
                <label>Nominal (Rp)</label>
                <input type="number" name="nominal" placeholder="0" required min="1">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Proses Transfer</button>
        </form>
    </div>
</div>

<!-- Tambahkan script select2 jika diperlukan -->
<?= $this->endSection() ?>