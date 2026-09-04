<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-title">
    Rekonsiliasi Bank
</div>

<div class="panel-view active">
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        
    <form action="" method="GET">
        <?= csrf_field() ?>
            <div style="flex: 1;">
                <label style="display: block; font-size: 14px; margin-bottom: 5px;">Pilih Rekening Bank</label>
                <select name="bank_id" class="form-control" required onchange="this.form.submit()">
                    <option value="">-- Pilih Rekening Bank --</option>
                    <?php foreach($bankList as $b): ?>
                        <option value="<?= $b['id'] ?? '' ?>" <?= $bank_id == $b['id'] ? 'selected' : '' ?>><?= esc($b['nama_bank'] ?? '') ?> - <?= esc($b['nomor_rekening'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if($bank): ?>
    <div style="display: flex; gap: 20px;">
        <div style="flex: 1; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
            <h4 style="color: #64748b; margin-bottom: 10px;">Saldo Menurut Sistem Koperasi</h4>
            <div style="font-size: 2rem; font-weight: bold; color: #1d4ed8;">Rp <?= number_format($bank['saldo'] ?? 0, 2, ',', '.') ?></div>
            <p style="margin-top: 10px; font-size: 0.9rem; color: #94a3b8;">Sistem mencatat seluruh mutasi operasional</p>
        </div>
        
        <div style="flex: 1; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
            <h4 style="color: #64748b; margin-bottom: 10px;">Saldo Menurut Rekening Koran</h4>
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <span style="font-size: 1.5rem; font-weight: bold;">Rp</span>
                <input type="number" id="saldo_fisik" class="form-control" style="font-size: 1.5rem; font-weight: bold; width: 250px; text-align: right;" oninput="hitungSelisih()">
            </div>
            <p style="margin-top: 10px; font-size: 0.9rem; color: #94a3b8;">Masukkan nominal akhir di Rekening Koran</p>
        </div>
    </div>
    
    <div id="hasil-rekonsiliasi" style="margin-top: 20px; padding: 20px; border-radius: 8px; background-color: #f8fafc; text-align: center; border: 1px solid #e2e8f0; display: none;">
        <h3 id="teks-selisih" style="margin-bottom: 10px;"></h3>
        <p id="pesan-rekonsiliasi" style="color: #64748b;"></p>
    </div>
    
    <script>
    function hitungSelisih() {
        let saldoSistem = <?= $bank['saldo'] ?? '' ?>;
        let inputFisik = document.getElementById('saldo_fisik').value;
        let hasilDiv = document.getElementById('hasil-rekonsiliasi');
        let teksSelisih = document.getElementById('teks-selisih');
        let pesan = document.getElementById('pesan-rekonsiliasi');
        
        if (!inputFisik) {
            hasilDiv.style.display = 'none';
            return;
        }
        
        let saldoFisik = parseFloat(inputFisik);
        let selisih = saldoFisik - saldoSistem;
        
        hasilDiv.style.display = 'block';
        
        let formatSelisih = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(Math.abs(selisih));
        
        if (selisih === 0) {
            teksSelisih.innerHTML = '<span style="color: #166534;"><i class="fas fa-check-circle"></i> Saldo MATCH (Sesuai)</span>';
            pesan.innerText = 'Tidak ada selisih antara sistem dan rekening koran. Rekonsiliasi selesai.';
            hasilDiv.style.backgroundColor = '#ecfdf5';
            hasilDiv.style.borderColor = '#10b981';
        } else if (selisih > 0) {
            teksSelisih.innerHTML = '<span style="color: #0284c7;"><i class="fas fa-arrow-up"></i> Selisih Positif: ' + formatSelisih + '</span>';
            pesan.innerText = 'Ada dana masuk di rekening koran yang belum tercatat di sistem (Contoh: Bunga Bank). Tambahkan Jurnal Manual.';
            hasilDiv.style.backgroundColor = '#f0f9ff';
            hasilDiv.style.borderColor = '#38bdf8';
        } else {
            teksSelisih.innerHTML = '<span style="color: #dc2626;"><i class="fas fa-arrow-down"></i> Selisih Negatif: ' + formatSelisih + '</span>';
            pesan.innerText = 'Ada dana keluar di rekening koran yang belum tercatat di sistem (Contoh: Biaya Admin Bank). Tambahkan Jurnal Manual.';
            hasilDiv.style.backgroundColor = '#fef2f2';
            hasilDiv.style.borderColor = '#f87171';
        }
    }
    </script>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

