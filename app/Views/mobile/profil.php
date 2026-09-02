<?= $this->extend('mobile/layout/main') ?>
<?= $this->section('content') ?>

        <!-- SCREEN 8: PROFIL -->
        <div id="screen-profil" class="screen active-screen">
            <div class="header" style="padding-bottom: 50px; text-align: center; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                <div class="header-title" style="justify-content: center;">
                    <span>Profil Saya</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 0;">
                <div style="background: white; border-radius: 16px; padding: 20px; margin: -35px 0 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); position: relative; z-index: 10; text-align: center; border: 1px solid var(--border);">
                    <div style="width: 85px; height: 85px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 50%; margin: 0 auto 12px; display: flex; justify-content: center; align-items: center; border: 4px solid white; box-shadow: 0 4px 10px rgba(5,150,105,0.2);">
                        <i class="fas fa-user-nurse" style="font-size: 2.5rem; color: white;"></i>
                    </div>
                    <h2 style="color: var(--text-dark); font-size: 1.25rem; font-weight: 700; margin-bottom: 2px;"><?= esc($anggota['nama_lengkap']) ?></h2>
                    <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 6px;"><?= esc($anggota['divisi']) ?></p>
                    
                    <div style="display: flex; justify-content: center; gap: 8px; align-items: center; margin-top: 8px;">
                        <span style="background: #f1f5f9; color: var(--text-dark); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; font-family: monospace;">NIP: <?= esc($anggota['nip']) ?></span>
                        <span class="badge-success" style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem;">Aktif</span>
                    </div>

                    <button onclick="switchScreen('screen-edit-profil')" style="margin-top: 15px; background: var(--bg-color); color: var(--text-dark); border: 1px solid var(--border); padding: 8px 18px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: 0.2s;">
                        <i class="fas fa-user-edit" style="color: var(--primary); margin-right: 5px;"></i> Edit Informasi Profil
                    </button>
                </div>

                <!-- RINGKASAN KEANGGOTAAN -->
                <div class="section-title" style="margin-top: 5px;">Ringkasan Keanggotaan</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                    <div style="background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <p style="font-size: 0.75rem; color: var(--text-light); margin-bottom: 4px;">Total Simpanan</p>
                        <h4 style="font-size: 0.95rem; color: var(--primary); font-weight: 700;">Rp <?= number_format($totalSimpanan ?? 0, 0, ',', '.') ?></h4>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <p style="font-size: 0.75rem; color: var(--text-light); margin-bottom: 4px;">Pinjaman Aktif</p>
                        <h4 style="font-size: 0.95rem; color: <?= !empty($pinjamanAktif) ? '#ef4444' : 'var(--text-dark)' ?>; font-weight: 700;"><?= !empty($pinjamanAktif) ? 'Rp ' . number_format($pinjamanAktif['nominal_pengajuan'], 0, ',', '.') : 'Tidak Ada' ?></h4>
                    </div>
                </div>

                <!-- PENGATURAN -->
                <div class="section-title">Pengaturan & Informasi</div>
                <div style="background: white; border-radius: 14px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.02); margin-bottom: 25px;">
                    <div onclick="switchScreen('screen-ubah-pin')" style="padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 12px; color: var(--text-dark); font-size: 0.9rem; font-weight: 500;">
                            <i class="fas fa-shield-alt" style="color: var(--primary); width: 20px;"></i>
                            <span>Ubah PIN Keamanan</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: var(--text-light); font-size: 0.8rem;"></i>
                    </div>
                    <div onclick="switchScreen('screen-bantuan')" style="padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 12px; color: var(--text-dark); font-size: 0.9rem; font-weight: 500;">
                            <i class="fas fa-headset" style="color: #3b82f6; width: 20px;"></i>
                            <span>Bantuan & CS Koperasi</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: var(--text-light); font-size: 0.8rem;"></i>
                    </div>
                    <div onclick="switchScreen('screen-tentang')" style="padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 12px; color: var(--text-dark); font-size: 0.9rem; font-weight: 500;">
                            <i class="fas fa-info-circle" style="color: #f59e0b; width: 20px;"></i>
                            <span>Tentang Aplikasi (v1.0.0)</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: var(--text-light); font-size: 0.8rem;"></i>
                    </div>
                </div>

                <!-- BUTTON LOGOUT SUNGGUHAN -->
                <button onclick="handleLogout()" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 14px; border-radius: 12px; font-weight: bold; font-size: 0.95rem; width: 100%; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 2px 5px rgba(220,38,38,0.05); transition: 0.2s;">
                    <i class="fas fa-sign-out-alt"></i> Keluar dari Akun (Logout)
                </button>
            </div>
        </div>

        <!-- SCREEN 9: LENGKAPI PROFIL -->
        <div id="screen-edit-profil" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-profil')"></i>
                    <span>Lengkapi Profil</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 20px;">
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" value="<?= esc($anggota['nama_lengkap']) ?>" disabled style="background: #f1f5f9;">
                </div>
                <div class="input-group">
                    <label>NIP</label>
                    <input type="text" value="<?= esc($anggota['nip']) ?>" disabled style="background: #f1f5f9;">
                </div>
                <div class="input-group">
                    <label>Jabatan / Divisi</label>
                    <input type="text" value="<?= esc($anggota['divisi']) ?>" disabled style="background: #f1f5f9;">
                </div>
                <div class="input-group">
                    <label>No. Handphone (WhatsApp)</label>
                    <input type="tel" value="<?= esc($anggota['no_hp']) ?>">
                </div>
                <div class="input-group">
                    <label>Alamat Lengkap</label>
                    <textarea rows="3"><?= esc($anggota['alamat'] ?? '') ?></textarea>
                </div>
                
                <button class="btn-primary" style="margin-top: 15px;">Simpan Perubahan</button>
            </div>
        </div>

        <!-- SCREEN 10: UBAH PIN -->
        <div id="screen-ubah-pin" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-profil')"></i>
                    <span>Ubah PIN Keamanan</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 20px;">
                <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                    <p style="font-size: 0.8rem; color: #0369a1;"><i class="fas fa-info-circle"></i> PIN digunakan untuk otorisasi setiap transaksi dan unduh dokumen. Jaga kerahasiaan PIN Anda.</p>
                </div>

                <div class="input-group">
                    <label>PIN Saat Ini</label>
                    <input type="password" placeholder="* * * * * *" maxlength="6" style="text-align: center; letter-spacing: 5px;">
                </div>
                <div class="input-group">
                    <label>PIN Baru</label>
                    <input type="password" placeholder="* * * * * *" maxlength="6" style="text-align: center; letter-spacing: 5px;">
                </div>
                <div class="input-group">
                    <label>Konfirmasi PIN Baru</label>
                    <input type="password" placeholder="* * * * * *" maxlength="6" style="text-align: center; letter-spacing: 5px;">
                </div>
                
                <button class="btn-primary" style="margin-top: 15px;">Perbarui PIN</button>
            </div>
        </div>

        <!-- SCREEN 11: BANTUAN & CS -->
        <div id="screen-bantuan" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-profil')"></i>
                    <span>Bantuan Koperasi</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 15px;">
                <div style="text-align: center; margin-bottom: 25px;">
                    <i class="fas fa-headset" style="font-size: 4rem; color: var(--primary); margin-bottom: 10px;"></i>
                    <h3 style="color: var(--text-dark);">Pusat Bantuan Anggota</h3>
                    <p style="font-size: 0.85rem; color: var(--text-light);">Jam Operasional: Senin - Jumat (08.00 - 15.00)</p>
                </div>

                <div class="list-card" style="border-left: 4px solid #25D366; cursor: pointer;">
                    <div class="list-card-left">
                        <h4>Admin Simpan Pinjam</h4>
                        <p>Tanya seputar saldo & angsuran</p>
                    </div>
                    <div class="list-card-right">
                        <i class="fab fa-whatsapp" style="color: #25D366; font-size: 1.5rem;"></i>
                    </div>
                </div>
                
                <div class="list-card" style="border-left: 4px solid #25D366; cursor: pointer;">
                    <div class="list-card-left">
                        <h4>Admin Waserda</h4>
                        <p>Ketersediaan barang & komplain</p>
                    </div>
                    <div class="list-card-right">
                        <i class="fab fa-whatsapp" style="color: #25D366; font-size: 1.5rem;"></i>
                    </div>
                </div>

                <div class="section-title">FAQ Singkat</div>
                <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 10px;">
                    <h4 style="font-size: 0.9rem; margin-bottom: 5px;">Kapan SHU dibagikan?</h4>
                    <p style="font-size: 0.8rem; color: var(--text-light);">SHU dibagikan setahun sekali maksimal 1 bulan setelah RAT disahkan.</p>
                </div>
                <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 10px;">
                    <h4 style="font-size: 0.9rem; margin-bottom: 5px;">Berapa limit kasbon saya?</h4>
                    <p style="font-size: 0.8rem; color: var(--text-light);">Maksimal 50% dari total simpanan aktif Anda.</p>
                </div>
            </div>
        </div>

        <!-- SCREEN 12: TENTANG APLIKASI -->
        <div id="screen-tentang" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-profil')"></i>
                    <span>Tentang Aplikasi</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 40px; text-align: center;">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" style="width: 100px; margin-bottom: 20px;">
                <h2 style="color: var(--text-dark);">Kopkar Assyifa</h2>
                <p style="color: var(--text-light); margin-bottom: 20px;">RSUD 45 Kuningan</p>
                
                <span class="badge-success" style="font-size: 0.8rem; padding: 5px 15px; border-radius: 20px;">Versi 1.0.0 (BETA)</span>
                
                <div style="margin-top: 40px; text-align: left; background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
                    <h4 style="margin-bottom: 10px; font-size: 0.9rem;">Dikembangkan oleh:</h4>
                    <p style="font-size: 0.8rem; color: var(--text-light); margin-bottom: 5px;">Tim IT RSUD 45 Kuningan</p>
                    <p style="font-size: 0.8rem; color: var(--text-light); margin-bottom: 15px;">© 2026 Hak Cipta Dilindungi</p>
                    
                    <a href="#" style="color: var(--primary); font-size: 0.8rem; display: block; margin-bottom: 5px; text-decoration: none;">Syarat & Ketentuan Layanan</a>
                    <a href="#" style="color: var(--primary); font-size: 0.8rem; display: block; text-decoration: none;">Kebijakan Privasi Data</a>
                </div>
            </div>
        </div>

        <!-- SCREEN: KARTU DIGITAL -->
        <div id="screen-kartu" class="screen">
            <div class="header" style="padding-bottom: 20px;">
                <div class="header-title">
                    <i class="fas fa-arrow-left back-btn" onclick="switchScreen('screen-profil')"></i>
                    <span>Kartu Anggota Digital</span>
                </div>
            </div>
            <div class="main-content" style="padding-top: 15px; display: flex; flex-direction: column; align-items: center;">
                <div style="width: 100%; max-width: 350px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 20px; padding: 20px; position: relative; overflow: hidden; box-shadow: 0 10px 20px rgba(5, 150, 105, 0.3);">
                    <!-- Ornamen background -->
                    <i class="fas fa-hospital" style="position: absolute; right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.1; transform: rotate(-15deg);"></i>
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem; letter-spacing: 1px;">KOPKAR ASSYIFA</h3>
                            <p style="margin: 0; font-size: 0.7rem; opacity: 0.8;">RSUD 45 KUNINGAN</p>
                        </div>
                        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" style="height: 40px; background: white; padding: 5px; border-radius: 8px;">
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <p style="margin: 0; font-size: 0.75rem; opacity: 0.8; text-transform: uppercase;">Nomor Induk Pegawai</p>
                        <h2 style="margin: 0; font-size: 1.6rem; letter-spacing: 2px; font-family: monospace;"><?= esc($anggota['nip'] ?? '000000') ?></h2>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div>
                            <p style="margin: 0; font-size: 0.75rem; opacity: 0.8; text-transform: uppercase;">Nama Anggota</p>
                            <h4 style="margin: 0; font-size: 1.1rem; text-transform: uppercase;"><?= esc($anggota['nama_lengkap'] ?? 'Nama Anggota') ?></h4>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin: 0; font-size: 0.75rem; opacity: 0.8; text-transform: uppercase;">Berlaku Sejak</p>
                            <h4 style="margin: 0; font-size: 1rem;"><?= date('Y') ?></h4>
                        </div>
                    </div>
                </div>

                <div style="background: white; width: 100%; border-radius: 12px; padding: 20px; margin-top: 25px; text-align: center; border: 1px solid var(--border);">
                    <p style="font-size: 0.85rem; color: var(--text-dark); margin-bottom: 15px;">QR Code Integrasi Absensi / Kantin</p>
                    <img src="<?= base_url('mobile/qr-code') ?>" alt="QR Code" style="width: 150px; height: 150px; display: block; margin: 0 auto;">
                    <p style="font-size: 0.75rem; color: var(--text-light); margin-top: 15px;">(Tunjukkan kode ini saat transaksi offline di Waserda)</p>
                </div>
            </div>
        </div>

<?= $this->endSection() ?>
