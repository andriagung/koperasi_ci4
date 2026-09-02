<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <a href="<?= base_url('admin/shu') ?>" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
        <h2 class="page-title"><i class="fas fa-list-alt text-primary me-2"></i>Detail SHU Tahun <?= $periode['tahun'] ?? '' ?></h2>
        <p class="text-muted">Rincian Perhitungan SHU Per Anggota Koperasi</p>
    </div>
    <div class="col-md-6 text-end">
        <?php if ($periode['status'] == 'Dihitung'): ?>
            
    <?= csrf_field() ?>" method="POST" class="d-inline">
                <button type="submit" class="btn btn-info text-white" onclick="return confirm('Apakah Anda yakin menyetujui draf SHU ini? Setelah disetujui, SHU siap dibagikan.');">
                    <i class="fas fa-check-circle me-2"></i>Setujui Draf SHU
                </button>
            </form>
        <?php elseif ($periode['status'] == 'APPROVED'): ?>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalBagikan">
                <i class="fas fa-hand-holding-usd me-2"></i>Bagikan SHU
            </button>
        <?php else: ?>
            <span class="badge bg-success py-2 px-3 fs-6"><i class="fas fa-check-double me-2"></i>SHU Telah Dibagikan</span>
        <?php endif; ?>
    </div>
</div>

<!-- Informasi Global -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Total Laba Bersih</h6>
                <h3 class="mb-0">Rp <?= number_format($periode['total_shu'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Alokasi Jasa Modal</h6>
                <h3 class="mb-0">Rp <?= number_format($periode['total_jasa_modal'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Alokasi Jasa Anggota</h6>
                <h3 class="mb-0">Rp <?= number_format($periode['total_jasa_usaha'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <h6 class="card-title text-dark-50">Dana Internal (Cadangan, dll)</h6>
                <?php 
                $danaInternal = $periode['cadangan'] + $periode['dana_pendidikan'] + $periode['dana_sosial'] + $periode['dana_pengurus'];
                ?>
                <h3 class="mb-0">Rp <?= number_format($danaInternal ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card glass-card">
    <div class="card-header bg-white pb-0 border-0">
        <h5 class="mb-0">Distribusi SHU Anggota (Snapshot)</h5>
        <small class="text-muted">Perhitungan bersifat fixed sejak draf dibuat pada <?= date('d M Y H:i', strtotime($periode['created_at'])) ?></small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="table-shu-anggota">
                <thead class="bg-light">
                    <tr>
                        <th width="20%">Nama Anggota</th>
                        <th width="15%" class="text-end">Basis Modal</th>
                        <th width="15%" class="text-end">SHU Jasa Modal</th>
                        <th width="15%" class="text-end">Basis Transaksi</th>
                        <th width="15%" class="text-end">SHU Jasa Usaha</th>
                        <th width="20%" class="text-end">Total SHU Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($detail_anggota as $da): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= $da['nama_lengkap'] ?? '' ?></div>
                            <small class="text-muted"><?= $da['nomor_anggota'] ?? '' ?></small>
                        </td>
                        <td class="text-end text-muted">Rp <?= number_format($da['dasar_jasa_modal'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-info">Rp <?= number_format($da['shu_modal'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-muted">Rp <?= number_format($da['dasar_jasa_usaha'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-success">Rp <?= number_format($da['shu_usaha'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-primary fs-6">Rp <?= number_format($da['total_shu'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bagikan -->
<div class="modal fade" id="modalBagikan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        
    <?= csrf_field() ?>" method="POST">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Bagikan SHU Tahun <?= $periode['tahun'] ?? '' ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Aksi ini akan menyalurkan dana SHU kepada seluruh anggota sesuai dengan total yang telah disetujui, dan akan mencatat jurnal otomatis (Laba Ditahan pada Kas/Sukarela).
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Metode Penyaluran SHU</label>
                        <select name="disalurkan_ke" class="form-select" required>
                            <option value="Sukarela">Simpan ke Saldo Simpanan Sukarela Anggota</option>
                            <option value="Tunai">Pencairan Tunai (Kas Keluar)</option>
                        </select>
                        <div class="form-text mt-2">
                            * Standar operasional koperasi biasanya memasukkan SHU ke dalam Simpanan Sukarela agar tidak menguras *cashflow* secara mendadak.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-2"></i>Eksekusi Bagikan SHU</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#table-shu-anggota').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        }
    });
});
</script>
<?= $this->endSection() ?>

