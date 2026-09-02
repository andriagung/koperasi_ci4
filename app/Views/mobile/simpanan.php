<?= $this->extend('mobile/layout/main') ?>
<?= $this->section('content') ?>
<div id="screen-mutasi" class="screen active">
    <div class="header" style="padding-bottom: 20px;">
        <div class="header-title">
            <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-home')"></i>
            <span>Buku Tabungan Digital</span>
        </div>
    </div>
    <div class="main-content" style="padding-top: 15px;">
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1; background: white; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid var(--border);">
                <p style="font-size: 0.75rem; color: var(--text-light);">Simpanan Wajib</p>
                <h4 style="color: var(--primary);">Rp <?= number_format($simpananWajib, 0, ',', '.') ?></h4>
            </div>
            <div style="flex: 1; background: white; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid var(--border);">
                <p style="font-size: 0.75rem; color: var(--text-light);">Simp. Sukarela</p>
                <h4 style="color: var(--primary);">Rp <?= number_format($simpananSukarela, 0, ',', '.') ?></h4>
            </div>
        </div>
        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button class="btn-primary" style="flex: 1; background: white; color: var(--primary); border: 1px solid var(--primary); font-size: 0.85rem;" onclick="switchScreen('screen-setor-simpanan')"><i class="fas fa-piggy-bank"></i> Setor Simpanan</button>
            <button class="btn-primary" style="flex: 1; background: white; color: var(--primary); border: 1px solid var(--primary); font-size: 0.85rem;" onclick="switchScreen('screen-tarik-simpanan')"><i class="fas fa-hand-holding-usd"></i> Tarik Simpanan</button>
        </div>

        <?php if(!empty($penarikan_pending) || !empty($setoran_pending) || !empty($pinjamanPending)): ?>
            <div class="section-title">Status Pengajuan (Pending)</div>
            
            <!-- Setoran Pending -->
            <?php if(!empty($setoran_pending)): foreach($setoran_pending as $s): ?>
            <div class="list-card" style="border-left: 4px solid var(--primary); background: #f0fdf4;">
                <div class="list-card-left">
                    <h4 style="color: var(--text-dark); margin-bottom: 5px;"><i class="fas fa-clock" style="color: var(--primary); margin-right: 5px;"></i>Verifikasi Setoran</h4>
                    <p style="font-size: 0.8rem; color: var(--text-dark);">Top-Up via <?= esc($s['metode_pembayaran'] ?? '-') ?></p>
                    <p style="font-size: 0.7rem; color: var(--text-light); margin-top: 8px;"><?= date('d M Y - H:i', strtotime($s['tanggal'] ?? $s['created_at'] ?? 'now')) ?></p>
                </div>
                <div class="list-card-right">
                    <h3 style="color: var(--primary);">+ Rp <?= number_format($s['nominal'], 0, ',', '.') ?></h3>
                    <span class="badge-warning" style="display:inline-block; margin-top:5px;">Pending</span>
                </div>
            </div>
            <?php endforeach; endif; ?>

            <!-- Penarikan Pending -->
            <?php if(!empty($penarikan_pending)): foreach($penarikan_pending as $p): ?>
            <div class="list-card" style="border-left: 4px solid var(--secondary); background: #fefce8;">
                <div class="list-card-left">
                    <h4 style="color: var(--text-dark); margin-bottom: 5px;"><i class="fas fa-clock" style="color: #ca8a04; margin-right: 5px;"></i>Verifikasi Penarikan</h4>
                    <p style="font-size: 0.8rem; color: var(--text-dark);">Tarik ke <?= esc($p['metode_pembayaran'] ?? '-') ?></p>
                    <p style="font-size: 0.7rem; color: var(--text-light); margin-top: 8px;"><?= date('d M Y - H:i', strtotime($p['tanggal'] ?? $p['created_at'] ?? 'now')) ?></p>
                </div>
                <div class="list-card-right">
                    <h3 style="color: #b45309;">- Rp <?= number_format($p['nominal'], 0, ',', '.') ?></h3>
                    <span class="badge-warning" style="display:inline-block; margin-top:5px;">Pending</span>
                </div>
            </div>
            <?php endforeach; endif; ?>

            <!-- Pinjaman Pending -->
            <?php if(!empty($pinjamanPending)): foreach($pinjamanPending as $pj): ?>
            <div class="list-card" style="border-left: 4px solid #ef4444; background: #fef2f2;">
                <div class="list-card-left">
                    <h4 style="color: var(--text-dark); margin-bottom: 5px;"><i class="fas fa-clock" style="color: #ef4444; margin-right: 5px;"></i>Pengajuan Pinjaman</h4>
                    <p style="font-size: 0.8rem; color: var(--text-dark);">Tujuan: <?= esc($pj['tujuan'] ?? '') ?></p>
                    <p style="font-size: 0.7rem; color: var(--text-light); margin-top: 8px;"><?= date('d M Y - H:i', strtotime($pj['tanggal_pengajuan'])) ?></p>
                </div>
                <div class="list-card-right">
                    <h3 style="color: #b91c1c;">Rp <?= number_format($pj['nominal_pengajuan'], 0, ',', '.') ?></h3>
                    <div style="display:flex; justify-content:flex-end; gap:5px; margin-top:5px; align-items:center;">
                        <span class="badge-warning" style="background:#fecaca; color:#7f1d1d; margin:0;">Pending</span>
                        <i class="fas fa-download" style="color: #ef4444; cursor: pointer; padding: 5px; background: white; border-radius: 5px; border: 1px solid #fecaca;" onclick="bukaModalPin('Bukti_Pengajuan_Pinjaman.pdf')"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        <?php endif; ?>

        <div class="section-title">
            Mutasi Terakhir 
            <i class="fas fa-download" style="color: var(--primary); font-size: 1.1rem; cursor: pointer;" onclick="bukaModalPin('Mutasi_Buku_Tabungan.pdf')"></i>
        </div>
        
        <?php if(!empty($riwayat)): ?>
            <?php foreach($riwayat as $rw): ?>
            <div class="list-card">
                <div class="list-card-left">
                    <h4><?= esc($rw['keterangan']) ?></h4>
                    <p><?= date('d M Y • H:i', strtotime($rw['created_at'])) ?> WIB</p>
                </div>
                <div class="list-card-right">
                    <h3 style="color: <?= $rw['jenis_transaksi'] === 'Masuk' ? '#16a34a' : '#ef4444' ?>;">
                        <?= $rw['jenis_transaksi'] === 'Masuk' ? '+' : '-' ?> Rp <?= number_format($rw['nominal'], 0, ',', '.') ?>
                    </h3>
                    <p><?= esc($rw['kategori']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="font-size:0.85rem; color:var(--text-light); text-align:center;">Belum ada riwayat mutasi.</p>
        <?php endif; ?>
    </div>
</div>

<div id="screen-setor-simpanan" class="screen">
    <div class="header" style="padding-bottom: 20px;">
        <div class="header-title">
            <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-mutasi')"></i>
            <span>Setor Simpanan Sukarela</span>
        </div>
    </div>
    <div class="main-content" style="padding-top: 20px;">
        
        <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <p style="font-size: 0.85rem; color: #0369a1;"><i class="fas fa-info-circle"></i> Silakan transfer dana ke rekening Koperasi di bawah ini, lalu isi form konfirmasi.</p>
            <div style="margin-top: 10px; background: white; padding: 10px; border-radius: 5px;">
                <p style="font-weight: bold; font-size: 0.9rem;">Bank BJB: 0011-2233-4455</p>
                <p style="font-size: 0.8rem; color: var(--text-light);">a.n Koperasi Karyawan Assyifa</p>
            </div>
        </div>
        
        
    <?= csrf_field() ?>
            <div class="input-group">
                <label>Nominal Transfer / Setoran</label>
                <input type="number" name="nominal" placeholder="Contoh: 150000" required min="10000">
            </div>
            <div class="input-group">
                <label>Bank Pengirim Anda</label>
                <input type="text" name="bank_pengirim" placeholder="Contoh: BCA - Agung Andri" required>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 15px;">Konfirmasi Setoran</button>
        </form>
    </div>
</div>

<div id="screen-tarik-simpanan" class="screen">
    <div class="header" style="padding-bottom: 20px;">
        <div class="header-title">
            <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-mutasi')"></i>
            <span>Tarik Simpanan Sukarela</span>
        </div>
    </div>
    <div class="main-content" style="padding-top: 20px;">
        <div style="background: var(--primary); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center; box-shadow: 0 4px 10px rgba(5,150,105,0.2);">
            <p style="font-size: 0.85rem; opacity: 0.9;">Saldo Sukarela Tersedia</p>
            <h2 style="font-size: 1.8rem; margin-top: 5px;">Rp <?= number_format($sukarelaSaldo = ($simpanan[1]['saldo'] ?? 0), 0, ',', '.') ?></h2>
        </div>
        
        
    <?= csrf_field() ?>
            <div class="input-group">
                <label>Nominal Penarikan</label>
                <input type="number" id="input-nominal-tarik" name="nominal" placeholder="Maks. Rp <?= number_format($sukarelaSaldo, 0, ',', '.') ?>" required>
            </div>
            <div class="input-group">
                <label>Rekening Pencairan</label>
                <div style="padding: 12px; border: 1px solid var(--primary); border-radius: 8px; background: #f0fdf4; color: var(--text-dark);">
                    <p style="font-weight: bold; font-size: 0.95rem;">Bank BJB (Rek. Penggajian RSUD)</p>
                    <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 3px;">001122334455 - a.n <?= esc($anggota['nama_lengkap']) ?></p>
                </div>
                <input type="hidden" name="bank_pencairan" value="BJB - 001122334455">
            </div>
            <div class="input-group">
                <label>Masukkan PIN Konfirmasi</label>
                <input type="password" name="pin_konfirmasi" id="input-pin-tarik" placeholder="******" required>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 15px;">Konfirmasi Penarikan</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

