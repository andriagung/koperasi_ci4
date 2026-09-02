<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    <a href="/admin/anggota" class="btn-primary" style="background:var(--text-muted); margin-right:15px; text-decoration:none;"><i class="fas fa-arrow-left"></i> Kembali</a>
    Detail Anggota: <?= esc($anggota['nama_lengkap']) ?> (<?= esc($anggota['nomor_anggota']) ?>)
</div>

<?php if(session()->getFlashdata('message')): ?>
    <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background-color: #d1fae5; color: #065f46; border-radius: 6px;">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #fee2e2; color: #991b1b; border-radius: 6px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <!-- Bagian Kiri: Profil Singkat -->
    <div class="panel-view active" style="padding: 20px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <?php if(!empty($anggota['foto'])): ?>
                <img src="/uploads/anggota/<?= esc($anggota['foto']) ?>" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary);">
            <?php else: ?>
                <div style="width: 150px; height: 150px; border-radius: 50%; background: #e2e8f0; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #94a3b8;">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
            <h3 style="margin-top: 15px;"><?= esc($anggota['nama_lengkap']) ?></h3>
            <p style="color: var(--text-muted);"><?= esc($anggota['nomor_anggota']) ?></p>
            
            <div style="margin-top:10px;">
                <span class="status-badge <?= $anggota['status'] == 'Aktif' ? 'status-approved' : 'status-rejected' ?>">
                    <?= esc($anggota['status']) ?>
                </span>
            </div>
            
            <div style="margin-top: 20px; text-align: left;">
                <p><strong>NIP:</strong> <?= esc($anggota['nip']) ?></p>
                <p><strong>Divisi:</strong> <?= esc($anggota['divisi']) ?></p>
                <p><strong>No. HP:</strong> <?= esc($anggota['no_hp']) ?></p>
                <p><strong>Bergabung:</strong> <?= !empty($anggota['tanggal_masuk']) ? date('d M Y', strtotime($anggota['tanggal_masuk'])) : '-' ?></p>
            </div>
            
            <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
                <a href="#" class="btn-primary" style="text-decoration:none;"><i class="fas fa-id-card"></i> Cetak Kartu</a>
            </div>
        </div>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        
        <h4>Saldo Simpanan</h4>
        <table style="width: 100%; margin-top: 10px;">
            <?php 
            $totalSimpanan = 0;
            foreach($saldo_simpanan as $saldo): 
                $totalSimpanan += $saldo['saldo'];
            ?>
            <tr>
                <td style="padding: 5px 0; color: var(--text-muted);"><?= esc($saldo['jenis_simpanan']) ?></td>
                <td style="padding: 5px 0; text-align: right; font-weight: 600;">Rp <?= number_format($saldo['saldo'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="border-top: 1px dashed #cbd5e1;">
                <td style="padding: 5px 0; font-weight: bold;">Total Saldo</td>
                <td style="padding: 5px 0; text-align: right; font-weight: bold; color: var(--primary);">Rp <?= number_format($totalSimpanan, 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Bagian Kanan: Tab Data -->
    <div class="panel-view active" style="padding: 20px;">
        <div style="border-bottom: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; gap: 20px;">
            <a href="#tab-keluarga" class="tab-link active" onclick="switchTab(event, 'tab-keluarga')" style="padding: 10px 0; font-weight: 600; text-decoration:none; color:var(--primary); border-bottom: 2px solid var(--primary);">Data Keluarga / Ahli Waris</a>
            <a href="#tab-histori" class="tab-link" onclick="switchTab(event, 'tab-histori')" style="padding: 10px 0; font-weight: 600; text-decoration:none; color:var(--text-muted);">Histori Transaksi</a>
            <a href="#tab-status" class="tab-link" onclick="switchTab(event, 'tab-status')" style="padding: 10px 0; font-weight: 600; text-decoration:none; color:var(--text-muted);">Ubah Status</a>
        </div>
        
        <!-- Tab Keluarga -->
        <div id="tab-keluarga" class="tab-content" style="display: block;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0;">Anggota Keluarga</h4>
                <button class="btn-primary" onclick="bukaModal('modal-keluarga')"><i class="fas fa-plus"></i> Tambah</button>
            </div>
            
            <table class="display" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #cbd5e1;">Nama</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #cbd5e1;">Hubungan</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #cbd5e1;">Ahli Waris</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #cbd5e1;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($keluarga)): ?>
                    <tr>
                        <td colspan="4" style="padding: 15px; text-align: center; color: var(--text-muted);">Belum ada data keluarga.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($keluarga as $k): ?>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;"><?= esc($k['nama']) ?></td>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;"><?= esc($k['hubungan']) ?></td>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                <?php if($k['is_ahli_waris']): ?>
                                    <span class="status-badge status-approved">Ya</span>
                                <?php else: ?>
                                    <span class="status-badge" style="background:#f1f5f9; color:#64748b;">Tidak</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                <button class="btn-action edit" onclick="editKeluarga(<?= htmlspecialchars(json_encode($k), ENT_QUOTES, 'UTF-8') ?>)" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn-action delete" onclick="hapusKeluarga(<?= $k['id'] ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Tab Histori -->
        <div id="tab-histori" class="tab-content" style="display: none;">
            <h4>Histori Transaksi Terakhir</h4>
            <table class="display" style="width:100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #cbd5e1;">Tanggal</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #cbd5e1;">Nomor Transaksi</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #cbd5e1;">Jenis</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 1px solid #cbd5e1;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($histori_simpanan)): ?>
                    <tr>
                        <td colspan="4" style="padding: 15px; text-align: center; color: var(--text-muted);">Belum ada histori transaksi.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($histori_simpanan as $h): ?>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;"><?= date('d/m/Y', strtotime($h['tanggal'])) ?></td>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;"><?= esc($h['nomor_transaksi']) ?></td>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-transform: capitalize;"><?= esc($h['jenis_transaksi']) ?></td>
                            <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                <?php if($h['jenis_transaksi'] == 'setoran'): ?>
                                    <span style="color: #16a34a;">+ Rp <?= number_format($h['nominal'], 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span style="color: #dc2626;">- Rp <?= number_format($h['nominal'], 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Tab Status -->
        <div id="tab-status" class="tab-content" style="display: none;">
            <h4>Ubah Status Anggota</h4>
            <p style="color: var(--text-muted); margin-bottom: 15px;">Mengubah status anggota dapat mempengaruhi hak akses, pembuatan pinjaman, dan perhitungan SHU.</p>
            
            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div class="form-group">
                    <label>Pilih Status Baru</label>
                    <select id="ubah_status_val" class="form-control" style="max-width: 300px; padding: 8px;">
                        <option value="Aktif" <?= $anggota['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Cuti" <?= $anggota['status'] == 'Cuti' ? 'selected' : '' ?>>Cuti</option>
                        <option value="Tidak Aktif" <?= $anggota['status'] == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                        <option value="Keluar" <?= $anggota['status'] == 'Keluar' ? 'selected' : '' ?>>Keluar (Resign)</option>
                    </select>
                </div>
                <button class="btn-primary" onclick="simpanStatus()" style="margin-top: 10px;">Simpan Perubahan Status</button>
            </div>
        </div>
        
    </div>
</div>

<!-- Modal Keluarga -->
<div class="modal-overlay" id="modal-keluarga">
    <div class="modal-content" style="max-width: 500px;">
        <i class="fas fa-times modal-close" onclick="tutupModal('modal-keluarga')"></i>
        <h3 style="margin-bottom: 20px; color: var(--primary);" id="modal-keluarga-title">Tambah Data Keluarga</h3>
        <form action="/admin/anggota/simpanKeluarga" method="POST">
            <input type="hidden" name="anggota_id" value="<?= $anggota['id'] ?>">
            <input type="hidden" name="id" id="keluarga_id">
            
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama" id="keluarga_nama" required>
            </div>
            
            <div class="form-group">
                <label>Hubungan *</label>
                <select name="hubungan" id="keluarga_hubungan" required>
                    <option value="">Pilih</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Orang Tua">Orang Tua</option>
                    <option value="Saudara Kandung">Saudara Kandung</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="keluarga_tanggal_lahir">
            </div>
            
            <div class="form-group">
                <label>Nomor HP</label>
                <input type="text" name="no_hp" id="keluarga_no_hp">
            </div>
            
            <div class="form-group">
                <label>Alamat (Biarkan kosong jika sama)</label>
                <textarea name="alamat" id="keluarga_alamat" rows="2"></textarea>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_ahli_waris" id="keluarga_is_ahli_waris" value="1" style="width: auto;">
                    <span>Jadikan sebagai Ahli Waris Utama</span>
                </label>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px;">Simpan Data</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function switchTab(e, tabId) {
    e.preventDefault();
    document.querySelectorAll('.tab-link').forEach(el => {
        el.classList.remove('active');
        el.style.color = 'var(--text-muted)';
        el.style.borderBottom = 'none';
    });
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    
    e.currentTarget.classList.add('active');
    e.currentTarget.style.color = 'var(--primary)';
    e.currentTarget.style.borderBottom = '2px solid var(--primary)';
    document.getElementById(tabId).style.display = 'block';
}

function editKeluarga(data) {
    document.getElementById('modal-keluarga-title').innerText = 'Edit Data Keluarga';
    document.getElementById('keluarga_id').value = data.id;
    document.getElementById('keluarga_nama').value = data.nama;
    document.getElementById('keluarga_hubungan').value = data.hubungan;
    document.getElementById('keluarga_tanggal_lahir').value = data.tanggal_lahir;
    document.getElementById('keluarga_no_hp').value = data.no_hp;
    document.getElementById('keluarga_alamat').value = data.alamat;
    document.getElementById('keluarga_is_ahli_waris').checked = (data.is_ahli_waris == 1);
    bukaModal('modal-keluarga');
}

function hapusKeluarga(id) {
    Swal.fire({
        title: 'Hapus data ini?',
        text: "Data keluarga tidak dapat dikembalikan",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/anggota/hapusKeluarga/' + id, {method: 'POST'})
            .then(r => r.json())
            .then(res => {
                if(res.status == 'success') location.reload();
            });
        }
    });
}

function simpanStatus() {
    let status = document.getElementById('ubah_status_val').value;
    Swal.fire({
        title: 'Ubah Status Anggota?',
        text: "Anda akan mengubah status menjadi: " + status,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ubah'
    }).then((result) => {
        if(result.isConfirmed) {
            let formData = new FormData();
            formData.append('status', status);
            fetch('/admin/anggota/ubahStatus/<?= $anggota['id'] ?>', {
                method: 'POST',
                body: formData
            }).then(r => r.json())
            .then(res => {
                if(res.status == 'success') {
                    Swal.fire('Berhasil', 'Status anggota telah diubah', 'success').then(()=>location.reload());
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>