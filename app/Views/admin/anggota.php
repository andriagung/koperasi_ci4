<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
            <div id="view-anggota" class="panel-view active">
                <div class="page-title">
                    Manajemen Master Anggota
                </div>
                <div class="table-container">
                    <div class="table-responsive">
                    <table id="tabelMasterAnggota" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th><th>NIP</th><th>Nama Lengkap</th><th>Divisi / Unit</th><th>No. HP</th><th>Status</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX Server-Side Processing -->
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

    <!-- MODAL IMPORT ANGGOTA -->
    <div class="modal-overlay" id="modal-import-anggota">
        <div class="modal-content modal-lg">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-import-anggota')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-file-csv"></i> Import Data Anggota</h3>
            <p style="font-size: 0.9rem; margin-bottom: 15px; color: var(--text-muted);">Unggah file CSV dengan kolom (header) berikut secara berurutan: <br><strong>NIP, Nama Lengkap, Divisi, No HP</strong></p>
            <form action="/admin/import-anggota" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>File CSV</label>
                    <input type="file" name="file_csv" accept=".csv" required style="padding: 10px 0; border: none;">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px; background: #0ea5e9;">Proses Import</button>
            </form>
            <div style="margin-top: 15px; text-align: center;">
                <a href="/admin/template-import-anggota" style="font-size: 0.85rem; color: #0ea5e9; text-decoration: none;"><i class="fas fa-download"></i> Download Template CSV</a>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH ANGGOTA -->
    <div class="modal-overlay" id="modal-tambah-anggota">
        <div class="modal-content" style="width: 1000px; max-width: 95%; max-height: 90vh; overflow-y: auto;">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-tambah-anggota')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Registrasi Anggota Baru</h3>
            <form action="/admin/tambah-anggota" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Nomor Induk Pegawai (NIP) *</label>
                        <input type="text" name="nip" placeholder="15 Digit">
                    </div>
                    <div class="form-group">
                        <label>NIK KTP *</label>
                        <input type="text" name="nik" placeholder="16 Digit" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" placeholder="Nama & Gelar" required>
                    </div>
                    <div class="form-group">
                        <label>Divisi / Poliklinik *</label>
                        <select name="divisi" required>
                            <option value="">Pilih Divisi</option>
                            <option value="IGD">IGD</option>
                            <option value="Poli Dalam">Poli Dalam</option>
                            <option value="Manajemen">Manajemen</option>
                            <option value="Farmasi">Farmasi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" placeholder="0812...">
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" placeholder="Kota">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir">
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin">
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="email@domain.com">
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" placeholder="Dokter/Perawat/Staff">
                    </div>
                    <div class="form-group">
                        <label>Bendahara Gaji (Instansi)</label>
                        <select name="bendahara_id">
                            <option value="">Pilih Bendahara (Jika Ada)</option>
                            <?php if(isset($bendahara_gaji)): ?>
                                <?php foreach($bendahara_gaji as $b): ?>
                                    <option value="<?= $b['id'] ?? '' ?>"><?= esc($b['nama_instansi'] ?? '') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Perkawinan</label>
                        <select name="status_perkawinan">
                            <option value="">Pilih</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai">Cerai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Foto Anggota</label>
                        <input type="file" name="foto" accept="image/*" style="padding: 8px 0; border: none;">
                    </div>
                </div>
                
                <h4 style="margin-top: 20px; margin-bottom: 10px; color: var(--text-dark);">Alamat Lengkap</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Jalan / Gang</label>
                        <textarea name="alamat" rows="2" placeholder="Nama jalan, nomor rumah"></textarea>
                    </div>
                    <div class="form-group">
                        <label>RT</label>
                        <input type="text" name="rt" placeholder="001">
                    </div>
                    <div class="form-group">
                        <label>RW</label>
                        <input type="text" name="rw" placeholder="002">
                    </div>
                    <div class="form-group">
                        <label>Desa / Kelurahan</label>
                        <input type="text" name="desa">
                    </div>
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan">
                    </div>
                    <div class="form-group">
                        <label>Kabupaten / Kota</label>
                        <input type="text" name="kabupaten">
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Kode Pos</label>
                        <input type="text" name="kode_pos">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>PIN Default Akun (6 Digit)</label>
                    <input type="password" value="123456" readonly style="background: #f1f5f9; cursor: not-allowed;">
                    <small style="color: var(--text-muted); display: block; margin-top: 5px;">PIN default ini akan digunakan anggota untuk login pertama kali.</small>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Simpan Data Anggota</button>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT ANGGOTA -->
    <div class="modal-overlay" id="modal-edit-anggota">
        <div class="modal-content" style="width: 1000px; max-width: 95%; max-height: 90vh; overflow-y: auto;">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-edit-anggota')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Master Anggota</h3>
            <form id="form-edit-anggota" action="" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Nomor Induk Pegawai (NIP)</label>
                        <input type="text" name="nip" id="edit_nip">
                    </div>
                    <div class="form-group">
                        <label>NIK KTP *</label>
                        <input type="text" name="nik" id="edit_nik" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" id="edit_nama" required>
                    </div>
                    <div class="form-group">
                        <label>Divisi / Poliklinik *</label>
                        <select name="divisi" id="edit_divisi" required>
                            <option value="">Pilih Divisi</option>
                            <option value="IGD">IGD</option>
                            <option value="Poli Dalam">Poli Dalam</option>
                            <option value="Manajemen">Manajemen</option>
                            <option value="Farmasi">Farmasi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Keanggotaan *</label>
                        <select name="status" id="edit_status" required>
                            <option value="Calon">Calon</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Keluar">Keluar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" id="edit_no_hp">
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="edit_tempat_lahir">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir">
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="edit_jenis_kelamin">
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email">
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" id="edit_pekerjaan">
                    </div>
                    <div class="form-group">
                        <label>Bendahara Gaji (Instansi)</label>
                        <select name="bendahara_id" id="edit_bendahara_id">
                            <option value="">Pilih Bendahara (Jika Ada)</option>
                            <?php if(isset($bendahara_gaji)): ?>
                                <?php foreach($bendahara_gaji as $b): ?>
                                    <option value="<?= $b['id'] ?? '' ?>"><?= esc($b['nama_instansi'] ?? '') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Perkawinan</label>
                        <select name="status_perkawinan" id="edit_status_perkawinan">
                            <option value="">Pilih</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai">Cerai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ganti Foto (Biarkan kosong jika tidak diganti)</label>
                        <input type="file" name="foto" accept="image/*" style="padding: 8px 0; border: none;">
                    </div>
                </div>

                <h4 style="margin-top: 20px; margin-bottom: 10px; color: var(--text-dark);">Alamat Lengkap</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Jalan / Gang</label>
                        <textarea name="alamat" id="edit_alamat" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>RT</label>
                        <input type="text" name="rt" id="edit_rt">
                    </div>
                    <div class="form-group">
                        <label>RW</label>
                        <input type="text" name="rw" id="edit_rw">
                    </div>
                    <div class="form-group">
                        <label>Desa / Kelurahan</label>
                        <input type="text" name="desa" id="edit_desa">
                    </div>
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan" id="edit_kecamatan">
                    </div>
                    <div class="form-group">
                        <label>Kabupaten / Kota</label>
                        <input type="text" name="kabupaten" id="edit_kabupaten">
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi" id="edit_provinsi">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Kode Pos</label>
                        <input type="text" name="kode_pos" id="edit_kode_pos">
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Perbarui Data Anggota</button>
            </form>
        </div>
    </div>

    <!-- MODAL CETAK KARTU ANGGOTA -->
    <div class="modal-overlay" id="modal-cetak-kartu">
        <div class="modal-content" style="width: 800px; max-width: 95%; text-align: center;">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-cetak-kartu')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);">Kartu Anggota Digital</h3>
            
            <div id="kartu-anggota-preview" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: 0 auto 20px auto; text-align: left; position: relative; overflow: hidden; max-width: 460px;">
                <!-- Decorative Circle -->
                <div style="position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                
                <h4 style="margin: 0 0 15px 0; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px;">
                    KOPKAR ASSYIFA RSUD 45
                </h4>
                
                <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 15px;">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; overflow: hidden;" id="kartu_foto_container">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 id="kartu_nama" style="margin: 0; font-size: 1.1rem; font-weight: 600;">-</h3>
                        <p id="kartu_nip" style="margin: 3px 0 0 0; font-size: 0.9rem; opacity: 0.9;">-</p>
                    </div>
                </div>
                
                <div style="font-size: 0.85rem; opacity: 0.9;">
                    <div style="margin-bottom: 5px;"><strong>Divisi:</strong> <span id="kartu_divisi">-</span></div>
                    <div><strong>Bergabung:</strong> <span id="kartu_tgl">-</span></div>
                </div>
            </div>
            
            <button class="btn-primary" onclick="printKartu()" style="width: 100%;"><i class="fas fa-print"></i> Cetak Kartu</button>
        </div>
    </div>

    <!-- MODAL DOKUMEN ANGGOTA -->
    <div class="modal-overlay" id="modal-dokumen-anggota">
        <div class="modal-content" style="width: 900px; max-width: 95%;">
            <i class="fas fa-times modal-close" onclick="tutupModal('modal-dokumen-anggota')"></i>
            <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-folder-open"></i> Dokumen Anggota</h3>
            <p id="dokumen_nama_anggota" style="font-weight:bold; margin-bottom: 15px;"></p>
            
            <!-- Form Upload -->
            <form action="/admin/upload-dokumen" method="POST" enctype="multipart/form-data" style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <?= csrf_field() ?>
                <input type="hidden" name="anggota_id" id="dokumen_anggota_id">
                
                <div class="form-group">
                    <label>Jenis Dokumen</label>
                    <select name="jenis_dokumen" required>
                        <option value="KTP">KTP</option>
                        <option value="KK">Kartu Keluarga (KK)</option>
                        <option value="Jaminan">Jaminan</option>
                        <option value="Surat Pernyataan">Surat Pernyataan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Dokumen (Opsional)</label>
                    <input type="text" name="nomor_dokumen" placeholder="Mis: NIK KTP">
                </div>
                <div class="form-group">
                    <label>File Upload (PDF/Image)</label>
                    <input type="file" name="file_dokumen" required style="padding: 8px 0; border: none;">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn-primary" style="width: 100%;"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>

            <table class="table-data" style="width: 100%; margin-top: 15px;">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Nomor</th>
                        <th>File</th>
                        <th>Tgl Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabelDokumenBody">
                    <tr><td colspan="5" style="text-align:center;">Memuat dokumen...</td></tr>
                </tbody>
            </table>
        </div>
        </div>
    </div>

<!-- Modal Cetak Kartu -->
<div class="modal" id="modal-cetak-kartu">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Cetak Kartu Anggota</h3>
            <button class="close-btn" onclick="tutupModal('modal-cetak-kartu')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="text-align: center; background: #f3f4f6; padding: 20px;">
            <div id="kartu-anggota-preview" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border-radius: 12px; padding: 20px; text-align: left; position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; margin: 0 auto;">
                <div style="display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 10px; margin-bottom: 15px;">
                    <i class="fas fa-hospital-user" style="font-size: 24px; margin-right: 15px;"></i>
                    <div>
                        <h4 style="margin: 0; font-size: 16px;">KOPERASI KARYAWAN MEDIS RSUD</h4>
                        <p style="margin: 0; font-size: 10px; opacity: 0.8;">KARTU TANDA ANGGOTA</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div id="kartu_foto_container" style="width: 80px; height: 100px; background: rgba(255,255,255,0.2); border-radius: 6px; display: flex; justify-content: center; align-items: center; font-size: 30px; overflow: hidden;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 id="kartu_nama" style="margin: 0 0 5px 0; font-size: 18px;">Nama Lengkap</h3>
                        <p id="kartu_nip" style="margin: 0 0 5px 0; font-size: 12px; opacity: 0.9;">NIP: -</p>
                        <p style="margin: 0 0 2px 0; font-size: 11px;">Divisi: <strong id="kartu_divisi">Divisi</strong></p>
                        <p style="margin: 0; font-size: 11px;">Tgl Bergabung: <strong id="kartu_tgl">2023-01-01</strong></p>
                    </div>
                </div>
                
                <div style="position: absolute; bottom: 15px; right: 15px; opacity: 0.2; font-size: 40px;">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding-top: 15px; text-align: right;">
            <button type="button" class="btn btn-secondary" onclick="tutupModal('modal-cetak-kartu')">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="printKartu()"><i class="fas fa-print"></i> Cetak</button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#tabelMasterAnggota').on('init.dt', function() {
        var customBtns = `
            <div style="display:inline-flex; gap:10px;">
                <button class="btn-sm" style="background:#0ea5e9; padding: 8px 15px; font-size: 0.85rem;" onclick="bukaModal('modal-import-anggota')"><i class="fas fa-file-import"></i> Import CSV</button>
                <button class="btn-sm btn-primary" style="padding: 8px 15px; font-size: 0.85rem;" onclick="bukaModal('modal-tambah-anggota')"><i class="fas fa-user-plus"></i> Tambah Anggota</button>
            </div>
        `;
        $('.dt-custom-buttons').append(customBtns);
    });
});

function editAnggotaModal(data) {
    document.getElementById('form-edit-anggota').action = '/admin/edit-anggota/' + data.id;
    
    // Set values
    $('#edit_nip').val(data.nip);
    $('#edit_nik').val(data.nik);
    $('#edit_nama').val(data.nama_lengkap);
    $('#edit_divisi').val(data.divisi);
    $('#edit_status').val(data.status);
    $('#edit_no_hp').val(data.no_hp);
    $('#edit_tempat_lahir').val(data.tempat_lahir);
    $('#edit_tanggal_lahir').val(data.tanggal_lahir);
    $('#edit_jenis_kelamin').val(data.jenis_kelamin);
    $('#edit_email').val(data.email);
    $('#edit_pekerjaan').val(data.pekerjaan);
    $('#edit_bendahara_id').val(data.bendahara_id);
    $('#edit_status_perkawinan').val(data.status_perkawinan);
    
    // Alamat
    $('#edit_alamat').val(data.alamat);
    $('#edit_rt').val(data.rt);
    $('#edit_rw').val(data.rw);
    $('#edit_desa').val(data.desa);
    $('#edit_kecamatan').val(data.kecamatan);
    $('#edit_kabupaten').val(data.kabupaten);
    $('#edit_provinsi').val(data.provinsi);
    $('#edit_kode_pos').val(data.kode_pos);

    bukaModal('modal-edit-anggota');
}

function bukaDokumen(id, nama) {
    $('#dokumen_anggota_id').val(id);
    $('#dokumen_nama_anggota').text('Atas Nama: ' + nama);
    loadDokumen(id);
    bukaModal('modal-dokumen-anggota');
}

function loadDokumen(anggota_id) {
    $('#tabelDokumenBody').html('<tr><td colspan="5" style="text-align:center;">Memuat dokumen...</td></tr>');
    $.get('/admin/dokumen-anggota/' + anggota_id, function(res) {
        var html = '';
        if(res.length > 0) {
            res.forEach(function(d) {
                html += `<tr>
                    <td>${d.jenis_dokumen}</td>
                    <td>${d.nomor_dokumen || '-'}</td>
                    <td><a href="/uploads/dokumen/${d.file}" target="_blank" style="color:var(--primary);"><i class="fas fa-file"></i> Lihat File</a></td>
                    <td>${d.tanggal_upload}</td>
                    <td><button type="button" class="btn-action delete" onclick="hapusDokumen(${d.id}, ${anggota_id})" title="Hapus"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
        } else {
            html = '<tr><td colspan="5" style="text-align:center;">Belum ada dokumen</td></tr>';
        }
        $('#tabelDokumenBody').html(html);
    });
}

function hapusDokumen(id, anggota_id) {
    konfirmasiModal('Hapus dokumen ini?', function() {
        $.post('/admin/hapus-dokumen/' + id, function(res) {
            if(res.status == 'success') {
                alertModal('Dokumen berhasil dihapus.', 'Sukses', 'success');
                loadDokumen(anggota_id);
            } else {
                alertModal('Gagal menghapus dokumen.', 'Error', 'error');
            }
        });
    }, 'Hapus Dokumen', 'danger');
}



function cetakKartu(data) {
    $('#kartu_nama').text(data.nama_lengkap);
    $('#kartu_nip').text('NIP: ' + (data.nip ? data.nip : '-'));
    $('#kartu_divisi').text(data.divisi);
    $('#kartu_tgl').text(data.tanggal_masuk ? data.tanggal_masuk : '-');
    if(data.foto) {
        $('#kartu_foto_container').html('<img src="/uploads/anggota/'+data.foto+'" style="width:100%; height:100%; object-fit:cover;">');
    } else {
        $('#kartu_foto_container').html('<i class="fas fa-user"></i>');
    }
    bukaModal('modal-cetak-kartu');
}

function printKartu() {
    var content = document.getElementById('kartu-anggota-preview').innerHTML;
    var printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <html><head><title>Cetak Kartu Anggota</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #fff; }
            .kartu { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border-radius: 12px; padding: 25px; max-width: 460px; width: 100%; position: relative; overflow: hidden; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        </style>
        </head><body><div class="kartu">${content}</div>
        <script>window.onload = function() { window.print(); window.close(); }<\/script>
        </body></html>
    `);
    printWindow.document.close();
}
</script>
<?= $this->endSection() ?>