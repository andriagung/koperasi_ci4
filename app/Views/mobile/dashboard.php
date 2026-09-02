<?= $this->extend('mobile/layout/main') ?>
<?= $this->section('content') ?>
<!-- SCREEN 1: BERANDA -->
        <div id="screen-home" class="screen active-screen">
            <div class="header" style="padding-bottom: 40px;">
                <div class="header-top">
                    <div>
                        <h2 style="font-size: 1.2rem;">Kopkar Assyifa RSUD 45</h2>
                        <p style="font-size: 0.85rem; opacity: 0.9;">Halo, <?= esc($anggota['nama_lengkap']) ?> • NIP: <?= esc($anggota['nip']) ?></p>
                    </div>
                    <div onclick="switchScreen('screen-notifikasi')" style="position: relative; cursor: pointer;">
                        <i class="fas fa-bell" style="font-size: 1.3rem;"></i>
                        <?php 
                            $notifModel = new \App\Models\NotificationModel();
                            $unreadNotif = $notifModel->where('user_id', $anggota['id'])->where('user_type', 'Anggota')->where('is_read', 0)->countAllResults();
                            if ($unreadNotif > 0): 
                        ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; font-size: 0.6rem; font-weight: bold; width: 15px; height: 15px; display: flex; justify-content: center; align-items: center; border-radius: 50%; border: 2px solid var(--primary);"><?= $unreadNotif ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="balance-card">
                <div class="balance-info">
                    <h3>Total Aset Anda (Simpanan)</h3>
                    <h1>Rp <?= number_format($totalSimpanan, 0, ',', '.') ?></h1>
                </div>
                <i class="fas fa-chart-pie" style="font-size: 2rem; color: #e2e8f0;"></i>
            </div>

            <div class="main-content">
                <div class="menu-grid" style="grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div class="menu-item" onclick="window.location.href='/mobile/simpanan'">
                        <div class="menu-icon"><i class="fas fa-book"></i></div>
                        <span>Tabungan</span>
                    </div>
                    <div class="menu-item" onclick="window.location.href='/mobile/pinjaman'">
                        <div class="menu-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <span>Pinjaman</span>
                    </div>
                    <div class="menu-item" onclick="window.location.href='/mobile/waserda'">
                        <div class="menu-icon"><i class="fas fa-shopping-basket"></i></div>
                        <span>Waserda</span>
                    </div>
                    <div class="menu-item" onclick="switchScreen('screen-shu')">
                        <div class="menu-icon"><i class="fas fa-medal"></i></div>
                        <span>Info SHU</span>
                    </div>
                    <div class="menu-item" onclick="window.location.href='/mobile/profil'">
                        <div class="menu-icon"><i class="fas fa-id-card"></i></div>
                        <span>Kartu Digital</span>
                    </div>
                    <div class="menu-item" onclick="window.location.href='/mobile/pinjaman#screen-kalkulator'">
                        <div class="menu-icon"><i class="fas fa-calculator"></i></div>
                        <span>Kalkulator</span>
                    </div>
                </div>

                <div class="section-title">Tagihan & Kewajiban Terdekat</div>
                <?php if (!empty($pinjamanAktif)): ?>
                <div class="list-card" style="border-left: 4px solid var(--secondary);">
                    <div class="list-card-left">
                        <h4>Cicilan Pinjaman</h4>
                        <p>Jatuh tempo: <?= date('d M Y', strtotime('+1 month')) ?></p>
                    </div>
                    <div class="list-card-right">
                        <h3 style="color: #b45309;">Rp <?= number_format($pinjamanAktif['nominal_pengajuan']/12, 0, ',', '.') ?></h3>
                        <button class="btn-outline" style="margin-top:5px;" onclick="window.location.href='/mobile/pinjaman'">Bayar Cicilan</button>
                    </div>
                </div>
                <?php else: ?>
                <div class="list-card" style="border-left: 4px solid var(--primary); background: #f0fdf4;">
                    <div class="list-card-left">
                        <h4 style="color: var(--text-dark);">Tidak ada tagihan</h4>
                        <p>Kewajiban bulan ini sudah lunas</p>
                    </div>
                    <div class="list-card-right">
                        <i class="fas fa-check-circle" style="color: var(--primary); font-size: 1.5rem;"></i>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SCREEN 5: INFO SHU & MAKRK KOPERASI -->
        <div id="screen-shu" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-home')"></i>
                    <span>Kinerja & SHU</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 15px;">
                <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 20px; border-radius: 15px; margin-bottom: 15px;">
                    <p style="font-size: 0.85rem; opacity: 0.9;">Total Estimasi SHU Berjalan</p>
                    <h1 style="font-size: 1.8rem; margin: 5px 0;">Rp <?= number_format($detailShu['total_shu'], 0, ',', '.') ?></h1>
                    <?php if(!$detailShu['has_laba']): ?>
                    <p style="font-size: 0.75rem; opacity: 0.9; margin-top: 10px; background: rgba(255,255,255,0.2); padding: 8px; border-radius: 6px;">
                        <i class="fas fa-info-circle"></i> Koperasi belum membukukan laba berjalan positif. Nilai estimasi SHU sementara masih Rp 0, namun poin partisipasi Anda tetap tercatat di bawah.
                    </p>
                    <?php endif; ?>
                </div>

                <div class="section-title">Rincian Partisipasi Anda</div>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                    <div style="background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 0.75rem; color: var(--text-light); margin: 0;">Poin Modal (Simpanan)</p>
                            <h4 style="margin: 5px 0 0 0; color: var(--primary);">Rp <?= number_format($detailShu['poin_modal'], 0, ',', '.') ?></h4>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 0.7rem; color: var(--text-light); margin: 0;">Estimasi Jasa Modal</p>
                            <h5 style="margin: 3px 0 0 0; color: var(--text-dark);">Rp <?= number_format($detailShu['jasa_modal'], 0, ',', '.') ?></h5>
                        </div>
                    </div>

                    <div style="background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 0.75rem; color: var(--text-light); margin: 0;">Poin Usaha (Pinjaman+Waserda)</p>
                            <h4 style="margin: 5px 0 0 0; color: var(--secondary);">Rp <?= number_format($detailShu['poin_usaha'], 0, ',', '.') ?></h4>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 0.7rem; color: var(--text-light); margin: 0;">Estimasi Jasa Usaha</p>
                            <h5 style="margin: 3px 0 0 0; color: var(--text-dark);">Rp <?= number_format($detailShu['jasa_usaha'], 0, ',', '.') ?></h5>
                        </div>
                    </div>
                </div>

                <div class="section-title">Transparansi Kinerja Koperasi</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    <div style="background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--border);">
                        <i class="fas fa-users" style="color: var(--primary); font-size: 1.5rem; margin-bottom: 5px;"></i>
                        <p style="font-size: 0.7rem; color: var(--text-light);">Total Anggota</p>
                        <h4 style="color: var(--text-dark);">215 Orang</h4>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--border);">
                        <i class="fas fa-building" style="color: var(--primary); font-size: 1.5rem; margin-bottom: 5px;"></i>
                        <p style="font-size: 0.7rem; color: var(--text-light);">Total Aset</p>
                        <h4 style="color: var(--text-dark);">Rp 1.2 Miliar</h4>
                    </div>
                </div>
                
                <button class="btn-outline" style="width: 100%; border-style: dashed; padding: 12px; font-weight: bold;" onclick="bukaModalPin('LPJ_Koperasi_2025.pdf')">
                    <i class="fas fa-file-archive"></i> Unduh LPJ Tahun 2025 (PDF)
                </button>
            </div>
        </div>

        <!-- SCREEN 13: NOTIFIKASI -->
        <div id="screen-notifikasi" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-home')"></i>
                    <span>Notifikasi</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 15px;">
                <?php if(!empty($notifikasi)): ?>
                    <?php foreach($notifikasi as $notif): ?>
                        <div class="list-card" style="border-left: 4px solid var(--primary);">
                            <div class="list-card-left">
                                <h4 style="color: var(--text-dark); margin-bottom: 5px;"><?= esc($notif['judul']) ?></h4>
                                <p style="font-size: 0.8rem; color: var(--text-dark);"><?= esc($notif['pesan']) ?></p>
                                <p style="font-size: 0.7rem; color: var(--text-light); margin-top: 8px;"><?= date('d M Y, H:i', strtotime($notif['created_at'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; margin-top: 50px;">
                        <i class="fas fa-bell-slash" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <p style="color: var(--text-light); font-size: 0.9rem;">Belum ada notifikasi baru.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SCREEN 6: RIWAYAT TRANSAKSI -->
        <div id="screen-riwayat" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-home')"></i>
                    <span>Riwayat Transaksi</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 15px;">
                <div style="display: flex; gap: 10px; margin-bottom: 15px; overflow-x: auto; padding-bottom: 5px;">
                    <button class="filter-btn active-filter" onclick="filterRiwayat('semua', this)" style="background: var(--primary); color: white; border: none; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; white-space: nowrap; cursor: pointer;">Semua</button>
                    <button class="filter-btn" onclick="filterRiwayat('simpanan', this)" style="background: white; color: var(--text-light); border: 1px solid var(--border); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; white-space: nowrap; cursor: pointer;">Simpanan</button>
                    <button class="filter-btn" onclick="filterRiwayat('pinjaman', this)" style="background: white; color: var(--text-light); border: 1px solid var(--border); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; white-space: nowrap; cursor: pointer;">Pinjaman</button>
                    <button class="filter-btn" onclick="filterRiwayat('waserda', this)" style="background: white; color: var(--text-light); border: 1px solid var(--border); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; white-space: nowrap; cursor: pointer;">Waserda</button>
                </div>

                <?php if(!empty($riwayat)): ?>
                    <?php foreach($riwayat as $rw): ?>
                    <div class="list-card riwayat-item item-<?= strtolower($rw['kategori']) ?>">
                        <div class="list-card-left">
                            <h4><?= esc($rw['keterangan']) ?></h4>
                            <p><?= date('d M Y', strtotime($rw['created_at'])) ?> • <?= esc($rw['kategori']) ?></p>
                        </div>
                        <div class="list-card-right">
                            <h3 style="color: <?= $rw['jenis_transaksi'] === 'Masuk' ? '#16a34a' : '#ef4444' ?>;">
                                <?= $rw['jenis_transaksi'] === 'Masuk' ? '+' : '-' ?> Rp <?= number_format($rw['nominal'], 0, ',', '.') ?>
                            </h3>
                            <p>Sukses</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="font-size:0.85rem; color:var(--text-light); text-align:center;">Belum ada riwayat transaksi.</p>
                <?php endif; ?>
            </div>
        </div>

<?= $this->endSection() ?>
