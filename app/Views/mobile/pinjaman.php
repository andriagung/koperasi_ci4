<?= $this->extend('mobile/layout/main') ?>
<?= $this->section('content') ?>
<div id="screen-angsuran" class="screen active">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-home')"></i>
                    <span>Tabel Amortisasi Pinjaman</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 15px;">
                <div style="background: white; border-radius: 12px; padding: 15px; margin-bottom: 15px; border: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-size: 0.85rem; color: var(--text-light);">Sisa Pokok Hutang</span>
                        <span style="font-weight: bold; color: var(--text-dark);">Rp <?= number_format($sisaPokokHutang, 0, ',', '.') ?></span>
                    </div>
                    <button class="btn-outline" style="width: 100%; border-style: dashed;" onclick="bukaModalPin('E-Contract Pinjaman.pdf')">
                        <i class="fas fa-file-pdf"></i> Unduh E-Contract Perjanjian
                    </button>
                </div>
                <button class="btn-primary" style="margin-bottom: 20px;" onclick="switchScreen('screen-pengajuan-pinjaman')"><i class="fas fa-plus-circle"></i> Ajukan Pinjaman Baru</button>

                <div class="section-title">Jadwal Angsuran</div>
                
                <?php if(!empty($jadwalAngsuran)): foreach($jadwalAngsuran as $ja): ?>
                    <?php if($ja['status'] === 'Lunas'): ?>
                    <div class="list-card">
                        <div class="list-card-left">
                            <h4>Bulan <?= esc($ja['bulan_ke']) ?> (<?= date('M Y', strtotime($ja['jatuh_tempo'])) ?>)</h4>
                            <p>Pokok: <?= number_format($ja['pokok']/1000, 0) ?>rb | Jasa: <?= number_format($ja['jasa']/1000, 0) ?>rb</p>
                        </div>
                        <div class="list-card-right">
                            <div style="display:flex; justify-content:flex-end; gap:5px; margin-bottom:5px; align-items:center;">
                                <span class="badge-success" style="margin:0;">Lunas</span>
                                <i class="fas fa-download" style="color: #16a34a; cursor: pointer; padding: 5px; background: white; border-radius: 5px; border: 1px solid #bbf7d0;" onclick="bukaModalPin('Kuitansi_Angsuran_Bulan_<?= $ja['bulan_ke'] ?>.pdf')"></i>
                            </div>
                            <p>Dibayar <?= date('d M', strtotime($ja['tanggal_bayar'])) ?></p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="list-card" style="border-left: 4px solid var(--secondary);">
                        <div class="list-card-left">
                            <h4>Bulan <?= esc($ja['bulan_ke']) ?> (<?= date('M Y', strtotime($ja['jatuh_tempo'])) ?>)</h4>
                            <p>Pokok: <?= number_format($ja['pokok']/1000, 0) ?>rb | Jasa: <?= number_format($ja['jasa']/1000, 0) ?>rb</p>
                            <p style="color: #ef4444; font-size: 0.7rem; margin-top: 2px;">Jatuh Tempo: <?= date('d M Y', strtotime($ja['jatuh_tempo'])) ?></p>
                        </div>
                        <div class="list-card-right">
                            <div style="display:flex; justify-content:flex-end; gap:5px; margin-bottom:5px; align-items:center;">
                                <span class="badge-warning" style="margin:0;">Belum Lunas</span>
                                <i class="fas fa-download" style="color: #ca8a04; cursor: pointer; padding: 5px; background: white; border-radius: 5px; border: 1px solid #fef08a;" onclick="bukaModalPin('Tagihan_Angsuran_Bulan_<?= $ja['bulan_ke'] ?>.pdf')"></i>
                            </div>
                            <h3 style="margin-top: 5px; margin-bottom: 5px;">Rp <?= number_format($ja['pokok'] + $ja['jasa'], 0, ',', '.') ?></h3>
                            <button onclick="bayarAngsuran(<?= $ja['id'] ?>, <?= $ja['pokok'] + $ja['jasa'] ?>, 'Angsuran Pinjaman Bulan <?= $ja['bulan_ke'] ?>')" style="background: #1e3a8a; color: white; border: none; padding: 5px 10px; border-radius: 5px; font-size: 0.75rem; cursor: pointer;">
                                <i class="fas fa-credit-card"></i> Bayar Online
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; else: ?>
                    <p style="font-size:0.85rem; color:var(--text-light); text-align:center;">Tidak ada jadwal angsuran.</p>
                <?php endif; ?>
            </div>
        </div>
<div id="screen-pengajuan-pinjaman" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-angsuran')"></i>
                    <span>Pengajuan Pinjaman</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 20px;">
                <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                    <p style="font-size: 0.8rem; color: #0369a1;"><i class="fas fa-info-circle"></i> Sisa plafon maksimal pinjaman Anda saat ini adalah <strong>Rp 15.000.000</strong>.</p>
                </div>
                
                
    <?= csrf_field() ?>
                <div class="input-group">
                    <label>Nominal Pinjaman</label>
                    <input type="number" id="input-nominal-pinjaman" name="nominal" placeholder="Contoh: 5000000" oninput="hitungSimulasi()" required>
                </div>
                <div class="input-group">
                    <label>Tenor / Lama Pinjaman</label>
                    <select id="input-tenor-pinjaman" name="tenor" onchange="hitungSimulasi()" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; outline: none; background: white; font-family: inherit;" required>
                        <option value="">Pilih Tenor</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan</option>
                        <option value="24">24 Bulan</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Tujuan Pinjaman</label>
                    <select name="tujuan" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; outline: none; background: white; font-family: inherit;" required>
                        <option value="">Pilih Tujuan</option>
                        <option value="pendidikan">Pendidikan Anak</option>
                        <option value="kesehatan">Biaya Pengobatan</option>
                        <option value="renovasi">Renovasi Rumah</option>
                        <option value="lainnya">Lain-lain</option>
                    </select>
                </div>
                <div class="input-group" style="background:#f8fafc; padding:15px; border-radius:8px; border:1px dashed #cbd5e1; margin-bottom:15px;">
                    <h4 style="margin-top:0; margin-bottom:10px; font-size:0.9rem; color:#0f172a;"><i class="fas fa-shield-alt"></i> Data Analisis Kelayakan</h4>
                    <label style="font-size:0.85rem;">Penghasilan / Gaji Bulanan (Rp)</label>
                    <input type="number" name="penghasilan_bulanan" placeholder="Contoh: 5000000" style="margin-bottom:10px; width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;" required>
                    
                    <label style="font-size:0.85rem;">Cicilan di Tempat Lain (Jika Ada, Rp)</label>
                    <input type="number" name="cicilan_lainnya" placeholder="Contoh: 1500000" value="0" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                </div>
                <div class="input-group">
                    <label>Keterangan / Catatan</label>
                    <textarea rows="2" name="keterangan" placeholder="Tulis catatan jika diperlukan..."></textarea>
                </div>
                
                <div id="box-simulasi" style="display: none; background: #f8fafc; border: 1px solid var(--primary); border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <h4 style="font-size: 0.85rem; color: var(--primary); margin-bottom: 10px; border-bottom: 1px dashed var(--primary); padding-bottom: 5px;"><i class="fas fa-calculator"></i> Simulasi Angsuran (Jasa 1% / Bln)</h4>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-dark); margin-bottom: 5px;">
                        <span>Pokok Pinjaman:</span>
                        <span id="simulasi-pokok">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-dark); margin-bottom: 5px;">
                        <span>Jasa Koperasi:</span>
                        <span id="simulasi-bunga">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.95rem; color: var(--text-dark); font-weight: bold; margin-top: 10px; border-top: 1px solid var(--border); padding-top: 10px;">
                        <span>Angsuran per Bulan:</span>
                        <span id="simulasi-total" style="color: #ef4444;">Rp 0</span>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" style="margin-top: 10px; width:100%;">Kirim Pengajuan</button>
                </form>
            </div>
        </div>

<script>
function hitungSimulasi() {
    let nominalStr = document.getElementById('input-nominal-pinjaman').value;
    let tenorStr = document.getElementById('input-tenor-pinjaman').value;
    
    let box = document.getElementById('box-simulasi');
    
    if (nominalStr && tenorStr) {
        let nominal = parseFloat(nominalStr);
        let tenor = parseInt(tenorStr);
        
        let formData = new FormData();
        formData.append('nominal', nominal);
        formData.append('tenor', tenor);
        formData.append('bunga', 1); // Bunga 1%
        
        fetch('/mobile/pinjaman/simulasi', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                let pokok = res.data.pokok;
                let bunga = res.data.bunga;
                let total = res.data.total_per_bulan;
                
                document.getElementById('simulasi-pokok').innerText = 'Rp ' + Math.round(pokok).toLocaleString('id-ID');
                document.getElementById('simulasi-bunga').innerText = 'Rp ' + Math.round(bunga).toLocaleString('id-ID');
                document.getElementById('simulasi-total').innerText = 'Rp ' + Math.round(total).toLocaleString('id-ID');
                
                box.style.display = 'block';
            }
        });
    } else {
        box.style.display = 'none';
    }
}
</script>

<div id="screen-kalkulator" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-home')"></i>
                    <span>Kalkulator Pinjaman</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 20px;">
                <div style="background: white; border-radius: 12px; padding: 20px; border: 1px solid var(--border); box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <div class="input-group">
                        <label>Nominal Pinjaman (Rp)</label>
                        <input type="number" id="input-nominal-pinjaman" placeholder="Contoh: 10000000" min="500000" step="500000">
                    </div>
                    <div class="input-group">
                        <label>Tenor Pinjaman (Bulan)</label>
                        <select id="input-tenor-pinjaman" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; outline: none; background: white;">
                            <option value="6">6 Bulan</option>
                            <option value="12" selected>12 Bulan</option>
                            <option value="24">24 Bulan</option>
                            <option value="36">36 Bulan</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Bunga / Jasa (Flat per bulan)</label>
                        <input type="text" value="1% (Sesuai Ketentuan)" disabled style="background: #f1f5f9; cursor: not-allowed;">
                    </div>
                    
                    <button type="button" class="btn-primary" onclick="hitungSimulasi()">Hitung Simulasi</button>
                </div>

                <div id="box-simulasi" style="display: none; margin-top: 25px;">
                    <div class="section-title">Hasil Simulasi Angsuran</div>
                    <div class="list-card" style="border-left: 4px solid var(--primary); background: #f0fdf4;">
                        <div class="list-card-left">
                            <h4>Angsuran Pokok</h4>
                            <p>Per bulan</p>
                        </div>
                        <div class="list-card-right">
                            <h3 id="simulasi-pokok" style="color: var(--text-dark);">Rp 0</h3>
                        </div>
                    </div>
                    <div class="list-card" style="border-left: 4px solid var(--secondary); background: #fffbeb;">
                        <div class="list-card-left">
                            <h4>Bunga / Jasa</h4>
                            <p>Per bulan</p>
                        </div>
                        <div class="list-card-right">
                            <h3 id="simulasi-bunga" style="color: var(--text-dark);">Rp 0</h3>
                        </div>
                    </div>
                    <div class="list-card" style="border-left: 4px solid #3b82f6; background: #eff6ff;">
                        <div class="list-card-left">
                            <h4 style="color: var(--primary-dark);">TOTAL ANGSURAN</h4>
                            <p style="color: var(--primary);">Yang harus dibayar / bln</p>
                        </div>
                        <div class="list-card-right">
                            <h3 id="simulasi-total" style="color: #1d4ed8; font-size: 1.2rem;">Rp 0</h3>
                        </div>
                    </div>
                    
                    <button class="btn-outline" style="width: 100%; border-style: dashed; padding: 12px; margin-top: 10px;" onclick="switchScreen('screen-pengajuan-pinjaman')">
                        <i class="fas fa-hand-holding-dollar"></i> Lanjut Ajukan Pinjaman
                    </button>
                </div>
            </div>
        </div>
<?= $this->endSection() ?>

