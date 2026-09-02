<?= $this->extend('mobile/layout/main') ?>
<?= $this->section('content') ?>
<div id="screen-waserda" class="screen active">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-home')"></i>
                    <span>Kredit Waserda</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 20px;">
                <div style="background: var(--primary); color: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; text-align: center; box-shadow: 0 5px 15px rgba(2, 132, 199, 0.3);">
                    <p style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">Plafon Kasbon Tersedia</p>
                    <h1 style="font-size: 2rem;">Rp <?= number_format($plafonWaserda, 0, ',', '.') ?></h1>
                    <p style="font-size: 0.7rem; opacity: 0.8; margin-top: 10px;">Limit disesuaikan dengan 50% Total Simpanan Anda</p>
                </div>

                <div class="section-title">Riwayat Belanja (Bulan Ini)</div>
                <?php if(!empty($riwayatWaserda)): ?>
                    <?php foreach($riwayatWaserda as $rw): ?>
                    <div class="list-card">
                        <div class="list-card-left">
                            <h4><?= esc($rw['keterangan']) ?></h4>
                            <p><?= date('d M Y', strtotime($rw['created_at'])) ?> • Kasbon</p>
                        </div>
                        <div class="list-card-right">
                            <h3>Rp <?= number_format($rw['nominal'], 0, ',', '.') ?></h3>
                            <button class="btn-outline" style="margin-top:5px;" onclick="bukaStruk()">Lihat Struk</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="font-size:0.85rem; color:var(--text-light); text-align:center; margin-bottom:20px;">Belum ada riwayat belanja bulan ini.</p>
                <?php endif; ?>

                <div class="section-title">Promo Spesial Minggu Ini</div>
                <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
                    <?php if(!empty($promoWaserda)): ?>
                        <?php foreach($promoWaserda as $promo): ?>
                        <div style="min-width: 140px; background: white; border-radius: 12px; padding: 10px; border: 1px solid var(--border); text-align: center;">
                            <div style="height: 80px; background: #fef08a; border-radius: 8px; margin-bottom: 8px; display: flex; justify-content: center; align-items: center;">
                                <i class="<?= esc($promo['ikon']) ?>" style="font-size: 2rem; color: #ca8a04;"></i>
                            </div>
                            <h4 style="font-size: 0.8rem; color: var(--text-dark); margin-bottom: 3px;"><?= esc($promo['nama_produk']) ?></h4>
                            <p style="font-size: 0.7rem; text-decoration: line-through; color: var(--text-light);">Rp <?= number_format($promo['harga_normal'], 0, ',', '.') ?></p>
                            <p style="font-size: 0.85rem; color: #ef4444; font-weight: bold;">Rp <?= number_format($promo['harga_promo'], 0, ',', '.') ?></p>
                            <button onclick="beliKasbon('<?= idhash_encode($promo['id']) ?>', '<?= esc($promo['nama_produk']) ?>')" style="margin-top: 8px; width: 100%; background: var(--primary); color: white; border: none; padding: 5px; border-radius: 6px; font-size: 0.75rem; cursor: pointer;">Beli Kasbon</button>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 0.8rem; color: var(--text-light);">Belum ada promo saat ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<div id="screen-scan" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <span>Scan QR Waserda</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 30px; display: flex; flex-direction: column; align-items: center;">
                <p style="text-align: center; color: var(--text-dark); margin-bottom: 20px;">Arahkan kamera ke QR Code di Kasir Waserda untuk membayar menggunakan saldo/kasbon Koperasi.</p>
                <div style="width: 250px; height: 250px; border: 4px dashed var(--primary); border-radius: 20px; position: relative; margin-bottom: 30px; display: flex; justify-content: center; align-items: center; background: #e2e8f0;">
                    <i class="fas fa-qrcode" style="font-size: 5rem; color: var(--text-light); opacity: 0.5;"></i>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: #ef4444; box-shadow: 0 0 10px #ef4444; animation: scanLine 2s infinite;"></div>
                </div>
                <style>
                    @keyframes scanLine {
                        0% { top: 10%; }
                        50% { top: 90%; }
                        100% { top: 10%; }
                    }
                </style>
                <button style="background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; font-size: 1rem; width: 100%; box-shadow: 0 4px 6px rgba(5, 150, 105, 0.2); cursor: pointer;">Unggah dari Galeri</button>
            </div>
        </div>
<?= $this->endSection() ?>
