<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
            <div id="view-simpan-pinjam" class="panel-view active">
                <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
                    Antrean Pinjaman & Penarikan
                    <div style="display:flex; gap:10px;">
                        <button class="btn-primary" style="background:#f59e0b;" onclick="bukaModal('modal-koreksi-simpanan')"><i class="fas fa-edit"></i> Koreksi Saldo</button>
                        <button class="btn-primary" style="background:#3b82f6;" onclick="bukaModal('modal-transfer-simpanan')"><i class="fas fa-exchange-alt"></i> Transfer Saldo</button>
                        <button class="btn-primary" style="background:#8b5cf6;" onclick="bukaModal('modal-simulasi-pinjaman')"><i class="fas fa-calculator"></i> Kalkulator Simulasi Pinjaman</button>
                    </div>
                </div>
                
                <h3 style="font-size: 1.1rem; color: #0f172a; margin-bottom: 15px;">Persetujuan Pinjaman Baru</h3>
                <div class="table-container" style="margin-bottom: 30px;">
                    <div class="table-responsive">
                    <table id="tabel-simpanan" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>NIP</th>
                                <th>Nama Anggota</th>
                                <th>Jenis</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>

                <h3 style="font-size: 1.1rem; color: #0f172a; margin-bottom: 15px;">Persetujuan Tarik Simpanan Sukarela</h3>
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabel-penarikan" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>NIP</th>
                                <th>Nama Anggota</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>

                <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 30px; margin-bottom: 15px;">Verifikasi Setoran Simpanan (Top-Up)</h3>
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabel-setoran" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>NIP</th>
                                <th>Nama Anggota</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- VIEW PENAGIHAN & AGING (FASE 6) -->
            <div id="view-penagihan" class="panel-view active">
                <div class="page-title">Sistem Penagihan & Aging Tunggakan</div>
                <p style="margin-bottom: 20px; color: var(--text-light);">Pantau jadwal tagihan angsuran bulan berjalan dan kelola kredit macet (Aging).</p>
                
                <h3 style="font-size: 1.1rem; color: #0f172a; margin-bottom: 15px;"><i class="fas fa-calendar-check" style="color:var(--primary);"></i> Jadwal Tagihan Bulan Ini</h3>
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabel-pinjaman" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tgl Pengajuan</th>
                                <th>NIP</th>
                                <th>Nama Anggota</th>
                                <th>Nominal</th>
                                <th>Tenor</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>

                <h3 style="font-size: 1.1rem; color: #dc2626; margin-top: 30px; margin-bottom: 15px;"><i class="fas fa-exclamation-circle"></i> Aging Tunggakan (Kredit Macet)</h3>
                <div class="table-container" style="border-top: 4px solid #ef4444;">
                    <div class="table-responsive">
                    <table class="dataTable display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Jatuh Tempo</th><th>Anggota</th><th>Angsuran Ke</th><th>Total Tagihan</th><th>Keterlambatan</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tagihanMacet as $m): ?>
                            <tr>
                                <td><span style="color:#dc2626; font-weight:bold;"><?= date('d/m/Y', strtotime($m['jatuh_tempo'])) ?></span></td>
                                <td><strong><?= esc($m['nama_lengkap'] ?? '') ?></strong><br><small><?= esc($m['nip'] ?? '') ?></small></td>
                                <td>Bulan ke-<?= $m['bulan_ke'] ?? '' ?></td>
                                <td style="color:#0f172a; font-weight:bold;">Rp <?= number_format($m['pokok'] + $m['jasa'], 0, ',', '.') ?></td>
                                <td><span class="badge bg-warning" style="color:#9a3412;"><i class="fas fa-clock"></i> <?= $m['hari_terlambat'] ?? '' ?> Hari</span></td>
                                <td class="action-btns">
                                    <button onclick="approve('angsuran', <?= $m['id'] ?? '' ?>)" class="btn-sm btn-success" title="Lunasi Sekarang"><i class="fas fa-check-double"></i> Lunasi</button>
                                    <button class="btn-sm" style="background:#475569; color:white;" onclick="alert('Kirim Pesan Penagihan ke: <?= esc($m['nip'] ?? '') ?>')"><i class="fab fa-whatsapp"></i> Tagih</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- 4. VIEW KASIR WASERDA -->

            <!-- MODAL SIMULASI PINJAMAN -->
            <div class="modal-overlay" id="modal-simulasi-pinjaman">
                <div class="modal-content" style="max-width: 800px;">
                    <i class="fas fa-times modal-close" onclick="tutupModal('modal-simulasi-pinjaman')"></i>
                    <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-calculator"></i> Kalkulator Simulasi Pinjaman</h3>
                    <div class="form-group">
                        <label>Nominal Pinjaman (Rp)</label>
                        <input type="number" id="simulasi_nominal" placeholder="Contoh: 10000000">
                    </div>
                    <div class="form-group">
                        <label>Tenor (Bulan)</label>
                        <select id="simulasi_tenor">
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                            <option value="24">24 Bulan</option>
                            <option value="36">36 Bulan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Suku Bunga (% Flat per bulan)</label>
                        <input type="number" id="simulasi_bunga" value="2" step="0.1">
                    </div>
                    <button class="btn-primary" onclick="hitungSimulasi()" style="width: 100%; margin-top: 10px;">Hitung Cicilan</button>
                    
                    <div id="hasil_simulasi" style="display: none; margin-top: 20px; padding: 15px; background: #f1f5f9; border-radius: 8px;">
                        <h4 style="margin-bottom: 10px; color: #0f172a;">Hasil Simulasi:</h4>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Pokok:</span> <strong id="hasil_pokok">Rp 0</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Bunga:</span> <strong id="hasil_bunga">Rp 0</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid #cbd5e1; padding-top: 5px; margin-top: 5px;">
                            <span>Total Cicilan per Bulan:</span> <strong id="hasil_total" style="color: var(--primary);">Rp 0</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL RINCIAN CICILAN -->
            <div class="modal-overlay" id="modal-rincian-cicilan">
                <div class="modal-content" style="max-width: 800px;">
                    <i class="fas fa-times modal-close" onclick="tutupModal('modal-rincian-cicilan')"></i>
                    <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-list"></i> Rincian Jadwal Cicilan</h3>
                    <div class="table-responsive">
                        <table class="display dataTable" style="width:100%" id="tabel-rincian-cicilan">
                            <thead>
                                <tr>
                                    <th>Bulan Ke</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Pokok</th>
                                    <th>Bunga</th>
                                    <th>Total Tagihan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="body-rincian-cicilan">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MODAL KOREKSI SIMPANAN -->
            <div class="modal-overlay" id="modal-koreksi-simpanan">
                <div class="modal-content" style="max-width: 800px;">
                    <i class="fas fa-times modal-close" onclick="tutupModal('modal-koreksi-simpanan')"></i>
                    <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-edit"></i> Koreksi Saldo Simpanan</h3>
                    
    <form action="" method="GET">
        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Anggota ID</label>
                            <input type="number" name="anggota_id" placeholder="ID Anggota" required>
                        </div>
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
                        <div class="form-group">
                            <label>Nominal (Rp)</label>
                            <input type="number" name="nominal" placeholder="0" required>
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
                <div class="modal-content" style="max-width: 800px;">
                    <i class="fas fa-times modal-close" onclick="tutupModal('modal-transfer-simpanan')"></i>
                    <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-exchange-alt"></i> Transfer Saldo Simpanan</h3>
                    
    <form action="" method="GET">
        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Anggota ID</label>
                            <input type="number" name="anggota_id" placeholder="ID Anggota" required>
                        </div>
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
                        <div class="form-group">
                            <label>Nominal (Rp)</label>
                            <input type="number" name="nominal" placeholder="0" required>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Proses Transfer</button>
                    </form>
                </div>
            </div>

<?= $this->endSection() ?>

