<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><i class="fas fa-balance-scale text-primary me-2"></i>Neraca Saldo</h2>
        <p class="text-muted">Ringkasan Saldo Akhir Seluruh Akun</p>
    </div>
</div>

<div class="card glass-card mb-4">
    <div class="card-body">
        
    <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 10)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php for($i=date('Y')-2; $i<=date('Y'); $i++): ?>
                            <option value="<?= $i ?? '' ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?? '' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Filter Laporan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card glass-card">
    <div class="card-header bg-white pb-0 border-0 text-center">
        <h4>NERACA SALDO</h4>
        <p class="text-muted mb-0">Periode Berakhir: <?= date('t', strtotime("$tahun-$bulan-01")) ?> <?= date('F', mktime(0, 0, 0, $bulan, 10)) ?> <?= $tahun ?? '' ?></p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="15%">Kode Akun</th>
                        <th width="45%">Nama Akun</th>
                        <th width="20%" class="text-end">Debit</th>
                        <th width="20%" class="text-end">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($neracaSaldo)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada data untuk periode ini</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($neracaSaldo as $ns): ?>
                        <tr>
                            <td><?= $ns['kode_akun'] ?? '' ?></td>
                            <td><?= $ns['nama_akun'] ?? '' ?></td>
                            <td class="text-end"><?= $ns['debit'] == 0 ? '-' : number_format($ns['debit'] ?? 0, 0, ',', '.') ?></td>
                            <td class="text-end"><?= $ns['kredit'] == 0 ? '-' : number_format($ns['kredit'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end fs-5">TOTAL</td>
                        <td class="text-end fs-5 <?= $totalDebit == $totalKredit ? 'text-success' : 'text-danger' ?>"><?= number_format($totalDebit ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end fs-5 <?= $totalDebit == $totalKredit ? 'text-success' : 'text-danger' ?>"><?= number_format($totalKredit ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php if ($totalDebit != $totalKredit && ($totalDebit > 0 || $totalKredit > 0)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-danger">Peringatan: Neraca Saldo Tidak Seimbang! Ada selisih Rp <?= number_format(abs($totalDebit - $totalKredit), 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

