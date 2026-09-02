<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    <a href="/admin/pinjaman/pengajuan" class="btn-primary" style="background:var(--text-muted); margin-right:15px; text-decoration:none;"><i class="fas fa-arrow-left"></i> Kembali</a>
    Jadwal & Pembayaran Angsuran
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

<div style="display:grid; grid-template-columns:1fr 3fr; gap:20px;">
    <!-- Info Ringkas -->
    <div class="panel-view active" style="padding:20px;">
        <h4 style="margin-bottom:15px; border-bottom:1px solid #e2e8f0; padding-bottom:5px;">Info Pinjaman</h4>
        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Nama Anggota:</p>
        <p style="font-weight:bold; margin-top:0; margin-bottom:15px;"><?= esc($pinjaman['nama_lengkap'] ?? '') ?></p>
        
        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Nominal Pinjaman:</p>
        <p style="font-weight:bold; font-size:1.1rem; color:var(--primary); margin-top:0; margin-bottom:15px;">Rp <?= number_format($pinjaman['nominal_pengajuan'] ?? 0, 0, ',', '.') ?></p>
        
        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Sisa Pokok:</p>
        <p style="font-weight:bold; color:#dc2626; margin-top:0; margin-bottom:15px;">Rp <?= number_format($pinjaman['sisa_pokok'] ?? 0, 0, ',', '.') ?></p>
        
        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Tenor:</p>
        <p style="font-weight:bold; margin-top:0; margin-bottom:15px;"><?= esc($pinjaman['tenor_bulan'] ?? '') ?> Bulan</p>
        
        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Status Pinjaman:</p>
        <div style="margin-bottom:20px;">
            <span class="status-badge <?= $pinjaman['status_pengajuan'] == 'PAID' ? 'status-approved' : ($pinjaman['status_pengajuan'] == 'ACTIVE' ? 'status-pending' : '') ?>"><?= esc($pinjaman['status_pengajuan'] ?? '') ?></span>
        </div>
        
        <a href="#" class="btn-primary" style="display:block; text-align:center; text-decoration:none;"><i class="fas fa-print"></i> Cetak Jadwal</a>
    </div>
    
    <!-- Jadwal Angsuran Tabel -->
    <div class="panel-view active" style="padding:20px;">
        <h4 style="margin-bottom:15px;">Daftar Angsuran bulanan</h4>
        
        <div style="overflow-x:auto;">
            <table class="display" style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                <thead>
                    <tr style="background:#f1f5f9; border-bottom:1px solid #cbd5e1;">
                        <th style="padding:10px;">Ke</th>
                        <th style="padding:10px;">Jatuh Tempo</th>
                        <th style="padding:10px; text-align:right;">Pokok</th>
                        <th style="padding:10px; text-align:right;">Bunga</th>
                        <th style="padding:10px; text-align:right;">Total</th>
                        <th style="padding:10px; text-align:center;">Status</th>
                        <th style="padding:10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $today = date('Y-m-d');
                    foreach($jadwal as $j): 
                        $isOverdue = ($j['status'] == 'Belum Lunas' && $j['jatuh_tempo'] < $today);
                        $rowBg = $isOverdue ? '#fef2f2' : '';
                        $textColor = $isOverdue ? '#991b1b' : '';
                    ?>
                    <tr style="border-bottom:1px solid #e2e8f0; background:<?= $rowBg ?? '' ?>; color:<?= $textColor ?? '' ?>;">
                        <td style="padding:10px; font-weight:bold;"><?= $j['angsuran_ke'] ?? '' ?></td>
                        <td style="padding:10px;"><?= date('d/m/Y', strtotime($j['jatuh_tempo'])) ?>
                            <?php if($isOverdue): ?><br><small style="color:#dc2626; font-weight:bold;">TERLAMBAT</small><?php endif; ?>
                        </td>
                        <td style="padding:10px; text-align:right;">Rp <?= number_format($j['pokok'] ?? 0, 0, ',', '.') ?></td>
                        <td style="padding:10px; text-align:right;">Rp <?= number_format($j['bunga'] ?? 0, 0, ',', '.') ?></td>
                        <td style="padding:10px; text-align:right; font-weight:600;">Rp <?= number_format($j['total_angsuran'] ?? 0, 0, ',', '.') ?></td>
                        <td style="padding:10px; text-align:center;">
                            <?php if($j['status'] == 'Lunas'): ?>
                                <span style="background:#d1fae5; color:#065f46; padding:2px 6px; border-radius:4px; font-size:0.75rem;">LUNAS</span>
                            <?php else: ?>
                                <span style="background:#e2e8f0; color:#475569; padding:2px 6px; border-radius:4px; font-size:0.75rem;">BELUM</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:10px;">
                            <?php if($j['status'] == 'Belum Lunas'): ?>
                                <button class="btn-action" style="background:#16a34a; color:white; font-size:0.8rem;" onclick='bayar(<?= json_encode($j) ?>, <?= $isOverdue ? "true" : "false" ?>)'>Bayar</button>
                            <?php else: ?>
                                <button class="btn-action" style="background:#0ea5e9; color:white; font-size:0.8rem;"><i class="fas fa-print"></i> Struk</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bayar Angsuran -->
<div class="modal-overlay" id="modal-bayar">
    <div class="modal-content" style="max-width: 800px;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-bayar')"></i>
        <h3 style="margin-bottom:20px; color:var(--primary);">Pembayaran Angsuran Ke-<span id="lbl_angsuran_ke"></span></h3>
        
        
    <?= csrf_field() ?>
            <input type="hidden" name="jadwal_id" id="bayar_jadwal_id">
            
            <div style="background:#f1f5f9; padding:15px; border-radius:6px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span>Jatuh Tempo:</span> <strong id="lbl_jatuh_tempo"></strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span>Pokok:</span> <strong id="lbl_pokok"></strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span>Bunga:</span> <strong id="lbl_bunga"></strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:1.1rem; border-top:1px dashed #cbd5e1; padding-top:5px; margin-top:5px;">
                    <span>Total Tagihan:</span> <strong id="lbl_total" style="color:var(--primary);"></strong>
                </div>
            </div>
            
            <div class="form-group">
                <label>Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group" id="container_denda" style="display:none;">
                <label style="color:#dc2626;">Denda Keterlambatan (Rp)</label>
                <input type="number" name="denda_dibayar" id="bayar_denda" value="0" style="border-color:#fca5a5; background:#fef2f2;">
                <small style="color:#dc2626;">Angsuran ini melewati jatuh tempo, silakan isi denda jika dikenakan.</small>
            </div>
            
            <div class="form-group">
                <label>Total Dibayar (Tagihan + Denda) Rp</label>
                <input type="number" name="nominal_bayar" id="bayar_nominal" required readonly style="background:#e2e8f0; font-weight:bold;">
            </div>
            
            <div class="form-group">
                <label>Metode Pembayaran</label>
                <select name="metode_pembayaran" id="metode_pembayaran_angsuran" onchange="toggleMetodeAngsuran()" required>
                    <option value="Tunai">Tunai</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                </select>
            </div>

            <div class="form-group" id="kas_div_angsuran">
                <label>Kas Tujuan (Tunai)</label>
                <select name="kas_id">
                    <?php foreach($kas as $k): ?>
                        <option value="<?= $k['id'] ?? '' ?>"><?= esc($k['nama'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="bank_div_angsuran" style="display:none;">
                <label>Rekening Bank (Transfer)</label>
                <select name="bank_id">
                    <?php if(!empty($bank)) { foreach($bank as $b): ?>
                        <option value="<?= $b['id'] ?? '' ?>"><?= esc($b['nama_bank'] ?? '') ?> - <?= esc($b['nomor_rekening'] ?? '') ?></option>
                    <?php endforeach; } ?>
                </select>
            </div>
            
            <script>
            function toggleMetodeAngsuran() {
                var metode = document.getElementById('metode_pembayaran_angsuran').value;
                if(metode === 'Tunai') {
                    document.getElementById('kas_div_angsuran').style.display = 'block';
                    document.getElementById('bank_div_angsuran').style.display = 'none';
                } else {
                    document.getElementById('kas_div_angsuran').style.display = 'none';
                    document.getElementById('bank_div_angsuran').style.display = 'block';
                }
            }
            </script>
            
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Proses Pembayaran</button>
        </form>
    </div>
</div>

<script>
function bayar(jadwal, isOverdue) {
    document.getElementById('lbl_angsuran_ke').innerText = jadwal.angsuran_ke;
    document.getElementById('bayar_jadwal_id').value = jadwal.id;
    
    // Format label
    document.getElementById('lbl_jatuh_tempo').innerText = jadwal.jatuh_tempo;
    document.getElementById('lbl_pokok').innerText = new Intl.NumberFormat('id-ID').format(jadwal.pokok);
    document.getElementById('lbl_bunga').innerText = new Intl.NumberFormat('id-ID').format(jadwal.bunga);
    document.getElementById('lbl_total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(jadwal.total_angsuran);
    
    document.getElementById('bayar_nominal').value = jadwal.total_angsuran;
    
    // Handle denda
    let dendaContainer = document.getElementById('container_denda');
    let inputDenda = document.getElementById('bayar_denda');
    
    if(isOverdue) {
        dendaContainer.style.display = 'block';
        inputDenda.value = '10000'; // Default denda 10rb
    } else {
        dendaContainer.style.display = 'none';
        inputDenda.value = '0';
    }
    
    // Auto update nominal bayar when denda change
    inputDenda.oninput = function() {
        let d = parseInt(this.value) || 0;
        let t = parseInt(jadwal.total_angsuran);
        document.getElementById('bayar_nominal').value = t + d;
    };
    
    // Trigger oninput
    inputDenda.oninput();
    
    bukaModal('modal-bayar');
}
</script>
<?= $this->endSection() ?>
