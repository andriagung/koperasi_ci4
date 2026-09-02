<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card glass-card">
            <div class="card-header">
                <h4 class="card-title">Kalkulasi & Pembagian SHU</h4>
            </div>
            <div class="card-body">
    <?php if(session()->getFlashdata('error')): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    
    <?= csrf_field() ?>
        <select name="tahun" class="form-control" style="max-width: 200px;">
            <?php for($i=date('Y'); $i>=date('Y')-5; $i--): ?>
                <option value="<?= $i ?? '' ?>" <?= $tahun == $i ? 'selected' : '' ?>>Tahun Buku <?= $i ?? '' ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn-primary">Kalkulasi Simulasi</button>
    </form>

    <?php if(!$simulasi['has_laba']): ?>
        <div style="background: #fffbeb; color: #b45309; padding: 15px; border-radius: 4px; border-left: 4px solid #f59e0b; margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i> <?= esc($simulasi['message'] ?? '') ?> 
            <br>Tabel di bawah ini hanya menampilkan <strong>Proyeksi Poin Partisipasi</strong> anggota berjalan.
        </div>
    <?php endif; ?>
        
        <?php if($isDitutup): ?>
            <div style="background: #dcfce7; color: #16a34a; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; text-align: center;">
                <i class="fas fa-check-circle"></i> Tahun Buku <?= $tahun ?? '' ?> sudah ditutup dan SHU telah dibagikan. <br>
                <a href="<?= base_url('admin/shu/detail?tahun='.$tahun) ?>" style="color: #15803d; text-decoration: underline; font-size: 0.9em;">Lihat Detail Distribusi</a>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid var(--primary);">
                <p style="color: #64748b; margin: 0; font-size: 13px;">Total Laba Bersih</p>
                <h3 style="margin: 5px 0 0 0; color: #0f172a;">Rp <?= number_format($simulasi['laba_bersih'] ?? 0, 0, ',', '.') ?></h3>
            </div>
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <p style="color: #64748b; margin: 0; font-size: 13px;">Dana Cadangan (25%)</p>
                <h3 style="margin: 5px 0 0 0; color: #0f172a;">Rp <?= number_format($simulasi['dana_cadangan'] ?? 0, 0, ',', '.') ?></h3>
            </div>
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #10b981;">
                <p style="color: #64748b; margin: 0; font-size: 13px;">Dana Jasa Modal (30%)</p>
                <h3 style="margin: 5px 0 0 0; color: #0f172a;">Rp <?= number_format($simulasi['dana_jasa_modal'] ?? 0, 0, ',', '.') ?></h3>
            </div>
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <p style="color: #64748b; margin: 0; font-size: 13px;">Dana Jasa Usaha (45%)</p>
                <h3 style="margin: 5px 0 0 0; color: #0f172a;">Rp <?= number_format($simulasi['dana_jasa_usaha'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>

        <h4 style="margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom:10px;">Simulasi Distribusi Anggota</h4>
        <table class="table" style="font-size: 13px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th>NIP</th>
                    <th>Nama Anggota</th>
                    <th style="text-align: right; background: #e0f2fe;">Poin Modal</th>
                    <th style="text-align: right; background: #e0f2fe;">Poin Usaha</th>
                    <th style="text-align: right;">Est. Jasa Modal</th>
                    <th style="text-align: right;">Est. Jasa Usaha</th>
                    <th style="text-align: right; font-weight: bold;">Total Estimasi SHU</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($simulasi['distribusi'] as $d): ?>
                <tr>
                    <td><?= esc($d['nip'] ?? '') ?></td>
                    <td><?= esc($d['nama_lengkap'] ?? '') ?></td>
                    <td style="text-align: right; background: #f0f9ff;">Rp <?= number_format($d['poin_modal'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right; background: #f0f9ff;">Rp <?= number_format($d['poin_usaha'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right;">Rp <?= number_format($d['jasa_modal'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right;">Rp <?= number_format($d['jasa_usaha'] ?? 0, 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: bold; color: var(--primary);">
                        Rp <?= number_format($d['total_shu'] ?? 0, 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if(!$isDitutup): ?>
            <div style="margin-top: 30px; text-align: center;">
                
    <?= csrf_field() ?>" onsubmit="return confirm('APAKAH ANDA YAKIN? Proses Tutup Buku akan secara permanen membagikan nominal ini ke Simpanan Sukarela anggota dan menjurnal Laba Rugi ke SHU Tahun Berjalan. Tindakan ini tidak dapat dibatalkan.')">
                    <input type="hidden" name="tahun" value="<?= $tahun ?? '' ?>">
                    <button type="submit" class="btn btn-danger" <?= !$simulasi['has_laba'] ? 'disabled' : '' ?> style="padding: 12px 30px; border-radius: 8px; font-weight: bold; font-size: 16px;">
                        <i class="fas fa-lock"></i> Eksekusi Tutup Buku <?= $tahun ?? '' ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

