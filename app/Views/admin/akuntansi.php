<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<div id="view-laporan" class="panel-view active">
                <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
                    Laporan Laba Rugi & Sisa Hasil Usaha (SHU)
                    <div>
                        <button class="btn-primary" id="btn-mode-pengurus" onclick="toggleModeLaporan('pengurus')" style="background: var(--primary);"><i class="fas fa-user-tie"></i> Mode Pengurus Awam</button>
                        <button class="btn-primary" id="btn-mode-akuntan" onclick="toggleModeLaporan('akuntan')" style="background: white; color: var(--primary); border: 1px solid var(--primary);"><i class="fas fa-calculator"></i> Mode Akuntan (PSAK)</button>
                        <button class="btn-primary" style="background: #16a34a; margin-left: 10px;" onclick="distribusiSHU()"><i class="fas fa-share-all"></i> Distribusi SHU</button>
                    </div>
                </div>
                
                <!-- ============================================== -->
                <!-- MODE PENGURUS AWAM (BAHASA MUDAH & LABA RUGI DI ATAS) -->
                <!-- ============================================== -->
                <div id="laporan-pengurus">
                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 10px; margin-bottom: 15px;"><i class="fas fa-file-invoice-dollar"></i> Ringkasan Hasil Usaha (Laba/Rugi)</h3>
                    
                    <div class="dashboard-cards" style="grid-template-columns: repeat(3, 1fr);">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4 style="font-size:0.9rem;">Total Uang Masuk Koperasi</h4>
                                <h2>Rp <?= number_format($labaRugi['totalPendapatan'] ?? 0, 0, ',', '.') ?></h2>
                                <small style="color:var(--text-muted);">Jasa Pinjaman: Rp <?= number_format($labaRugi['pendapatanJasa'] ?? 0, 0, ',', '.') ?></small><br>
                                <small style="color:var(--text-muted);">Untung Jualan Waserda: Rp <?= number_format($labaRugi['labaKotorWaserda'] ?? 0, 0, ',', '.') ?></small>
                            </div>
                            <div class="stat-icon icon-blue"><i class="fas fa-arrow-trend-up"></i></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4>Pengeluaran & Biaya Operasional</h4>
                                <h2>Rp <?= number_format($labaRugi['bebanOperasional'] ?? 0, 0, ',', '.') ?></h2>
                                <small style="color:var(--text-muted);">Gaji, Listrik, Beli Stok, dll (Sesuai Pengaturan)</small><br>&nbsp;
                            </div>
                            <div class="stat-icon icon-red"><i class="fas fa-arrow-trend-down"></i></div>
                        </div>
                        <div class="stat-card" style="border: 2px solid var(--primary); background: #f0fdf4;">
                            <div class="stat-info">
                                <h4>Keuntungan Bersih (SHU)</h4>
                                <h2 style="color: var(--primary);">Rp <?= number_format($labaRugi['shuBersih'] ?? 0, 0, ',', '.') ?></h2>
                                <small style="color:#16a34a; font-weight:bold;">Uang yang siap dibagikan!</small><br>&nbsp;
                            </div>
                            <div class="stat-icon icon-green"><i class="fas fa-sack-dollar"></i></div>
                        </div>
                    </div>

                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 30px; margin-bottom: 15px;">
                        <i class="fas fa-balance-scale"></i> Kesehatan Finansial Koperasi 
                        <?php if($neraca['totalAktiva'] == $neraca['totalPasiva']): ?>
                            <span class="badge bg-success" style="margin-left: 10px;"><i class="fas fa-check-circle"></i> DATA VALID & SEIMBANG</span>
                        <?php else: ?>
                            <span class="badge bg-warning" style="margin-left: 10px;">DATA TIDAK SEIMBANG</span>
                        <?php endif; ?>
                    </h3>
                    <div style="display: flex; gap: 20px;">
                        <!-- HARTA KOPERASI -->
                        <div class="table-container" style="flex: 1; border-top: 4px solid var(--primary);">
                            <h4 style="color: var(--primary); margin-bottom: 10px;">HARTA KOPERASI (Uang yang Kita Punya)</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Uang Tunai (Di Kas & Bank)</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['kas'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Uang yang Masih Dipinjam Anggota</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['piutang'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #f8fafc; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: var(--primary);">TOTAL HARTA KITA</td>
                                        <td style="padding: 15px; color: var(--primary); text-align: right;">Rp <?= number_format($neraca['totalAktiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- SUMBER DANA KOPERASI -->
                        <div class="table-container" style="flex: 1; border-top: 4px solid #ef4444;">
                            <h4 style="color: #ef4444; margin-bottom: 10px;">SUMBER DANA (Asal Usul Harta Kita)</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                            Titipan Anggota Koperasi (Sukarela)<br>
                                            <small style="color:var(--text-muted);">Dianggap UTANG karena bisa ditarik kapan saja</small>
                                        </td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; vertical-align: top;">Rp <?= number_format($neraca['kewajiban'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                            Modal Inti (Simpanan Pokok & Wajib)<br>
                                            <small style="color:var(--text-muted);">Modal asli yang tidak bisa ditarik</small>
                                        </td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; vertical-align: top;">Rp <?= number_format($neraca['modalPokokWajib'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Keuntungan (SHU) Tahun Ini</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #16a34a;">+ Rp <?= number_format($neraca['labaBerjalan'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #f8fafc; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: #ef4444;">TOTAL SUMBER DANA</td>
                                        <td style="padding: 15px; color: #ef4444; text-align: right;">Rp <?= number_format($neraca['totalPasiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- End Mode Pengurus -->

                <!-- ============================================== -->
                <!-- MODE AKUNTAN (STANDAR PSAK KOPERASI & NERACA DI ATAS) -->
                <!-- ============================================== -->
                <div id="laporan-akuntan" style="display: none;">
                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 10px; margin-bottom: 15px;"><i class="fas fa-balance-scale"></i> NERACA (Balance Sheet) per <?= date('d M Y') ?></h3>
                    <div style="display: flex; gap: 20px;">
                        <!-- AKTIVA -->
                        <div class="table-container" style="flex: 1;">
                            <h4 style="color: var(--primary); margin-bottom: 10px;">AKTIVA</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Aktiva Lancar</td></tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Kas & Setara Kas</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['kas'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Piutang Pinjaman Anggota</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['piutang'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #e0f2fe; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: var(--primary);">TOTAL AKTIVA</td>
                                        <td style="padding: 15px; color: var(--primary); text-align: right;">Rp <?= number_format($neraca['totalAktiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- PASIVA -->
                        <div class="table-container" style="flex: 1;">
                            <h4 style="color: #ef4444; margin-bottom: 10px;">PASIVA</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Kewajiban</td></tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Simpanan Sukarela Anggota</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['kewajiban'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Ekuitas</td></tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Simpanan Pokok & Wajib</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['modalPokokWajib'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Sisa Hasil Usaha (SHU) Berjalan</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #16a34a;">Rp <?= number_format($neraca['labaBerjalan'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #fee2e2; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: #ef4444;">TOTAL PASIVA</td>
                                        <td style="padding: 15px; color: #ef4444; text-align: right;">Rp <?= number_format($neraca['totalPasiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 30px; margin-bottom: 15px;"><i class="fas fa-file-invoice-dollar"></i> LABA RUGI (Perhitungan Hasil Usaha)</h3>
                    <div class="table-container">
                        <table class="display" style="width:100%; border-collapse: collapse;">
                            <tbody>
                                <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Pendapatan Operasional</td></tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Pendapatan Jasa Pinjaman</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($labaRugi['pendapatanJasa'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Pendapatan Kotor Penjualan Waserda</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($labaRugi['penjualanWaserda'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">(HPP Penjualan Waserda)</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color:#ef4444;">(Rp <?= number_format($labaRugi['penjualanWaserda'] - $labaRugi['labaKotorWaserda'], 0, ',', '.') ?>)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">Total Laba Kotor</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">Rp <?= number_format($labaRugi['totalPendapatan'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Beban Operasional</td></tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Beban Operasional Umum & Administrasi</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color:#ef4444;">(Rp <?= number_format($labaRugi['bebanOperasional'] ?? 0, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr style="background: #e0f2fe; font-weight: bold; font-size: 1.1rem;">
                                    <td style="padding: 15px; color: var(--primary);">SISA HASIL USAHA (SHU) BERSIH</td>
                                    <td style="padding: 15px; color: var(--primary); text-align: right;">Rp <?= number_format($labaRugi['shuBersih'] ?? 0, 0, ',', '.') ?></td>
            <div id="view-laporan" class="panel-view active">
                <div class="page-title" style="display: flex; justify-content: space-between; align-items: center;">
                    Laporan Laba Rugi & Sisa Hasil Usaha (SHU)
                    <div>
                        <button class="btn-primary" id="btn-mode-pengurus" onclick="toggleModeLaporan('pengurus')" style="background: var(--primary);"><i class="fas fa-user-tie"></i> Mode Pengurus Awam</button>
                        <button class="btn-primary" id="btn-mode-akuntan" onclick="toggleModeLaporan('akuntan')" style="background: white; color: var(--primary); border: 1px solid var(--primary);"><i class="fas fa-calculator"></i> Mode Akuntan (PSAK)</button>
                        <button class="btn-primary" style="background: #16a34a; margin-left: 10px;" onclick="distribusiSHU()"><i class="fas fa-share-all"></i> Distribusi SHU</button>
                    </div>
                </div>
                
                <!-- ============================================== -->
                <!-- MODE PENGURUS AWAM (BAHASA MUDAH & LABA RUGI DI ATAS) -->
                <!-- ============================================== -->
                <div id="laporan-pengurus">
                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 10px; margin-bottom: 15px;"><i class="fas fa-file-invoice-dollar"></i> Ringkasan Hasil Usaha (Laba/Rugi)</h3>
                    
                    <div class="dashboard-cards" style="grid-template-columns: repeat(3, 1fr);">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4 style="font-size:0.9rem;">Total Uang Masuk Koperasi</h4>
                                <h2>Rp <?= number_format($labaRugi['totalPendapatan'] ?? 0, 0, ',', '.') ?></h2>
                                <small style="color:var(--text-muted);">Jasa Pinjaman: Rp <?= number_format($labaRugi['pendapatanJasa'] ?? 0, 0, ',', '.') ?></small><br>
                                <small style="color:var(--text-muted);">Untung Jualan Waserda: Rp <?= number_format($labaRugi['labaKotorWaserda'] ?? 0, 0, ',', '.') ?></small>
                            </div>
                            <div class="stat-icon icon-blue"><i class="fas fa-arrow-trend-up"></i></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4>Pengeluaran & Biaya Operasional</h4>
                                <h2>Rp <?= number_format($labaRugi['bebanOperasional'] ?? 0, 0, ',', '.') ?></h2>
                                <small style="color:var(--text-muted);">Gaji, Listrik, Beli Stok, dll (Sesuai Pengaturan)</small><br>&nbsp;
                            </div>
                            <div class="stat-icon icon-red"><i class="fas fa-arrow-trend-down"></i></div>
                        </div>
                        <div class="stat-card" style="border: 2px solid var(--primary); background: #f0fdf4;">
                            <div class="stat-info">
                                <h4>Keuntungan Bersih (SHU)</h4>
                                <h2 style="color: var(--primary);">Rp <?= number_format($labaRugi['shuBersih'] ?? 0, 0, ',', '.') ?></h2>
                                <small style="color:#16a34a; font-weight:bold;">Uang yang siap dibagikan!</small><br>&nbsp;
                            </div>
                            <div class="stat-icon icon-green"><i class="fas fa-sack-dollar"></i></div>
                        </div>
                    </div>

                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 30px; margin-bottom: 15px;">
                        <i class="fas fa-balance-scale"></i> Kesehatan Finansial Koperasi 
                        <?php if($neraca['totalAktiva'] == $neraca['totalPasiva']): ?>
                            <span class="badge bg-success" style="margin-left: 10px;"><i class="fas fa-check-circle"></i> DATA VALID & SEIMBANG</span>
                        <?php else: ?>
                            <span class="badge bg-warning" style="margin-left: 10px;">DATA TIDAK SEIMBANG</span>
                        <?php endif; ?>
                    </h3>
                    <div style="display: flex; gap: 20px;">
                        <!-- HARTA KOPERASI -->
                        <div class="table-container" style="flex: 1; border-top: 4px solid var(--primary);">
                            <h4 style="color: var(--primary); margin-bottom: 10px;">HARTA KOPERASI (Uang yang Kita Punya)</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Uang Tunai (Di Kas & Bank)</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['kas'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Uang yang Masih Dipinjam Anggota</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['piutang'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #f8fafc; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: var(--primary);">TOTAL HARTA KITA</td>
                                        <td style="padding: 15px; color: var(--primary); text-align: right;">Rp <?= number_format($neraca['totalAktiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- SUMBER DANA KOPERASI -->
                        <div class="table-container" style="flex: 1; border-top: 4px solid #ef4444;">
                            <h4 style="color: #ef4444; margin-bottom: 10px;">SUMBER DANA (Asal Usul Harta Kita)</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                            Titipan Anggota Koperasi (Sukarela)<br>
                                            <small style="color:var(--text-muted);">Dianggap UTANG karena bisa ditarik kapan saja</small>
                                        </td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; vertical-align: top;">Rp <?= number_format($neraca['kewajiban'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                            Modal Inti (Simpanan Pokok & Wajib)<br>
                                            <small style="color:var(--text-muted);">Modal asli yang tidak bisa ditarik</small>
                                        </td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; vertical-align: top;">Rp <?= number_format($neraca['modalPokokWajib'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Keuntungan (SHU) Tahun Ini</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #16a34a;">+ Rp <?= number_format($neraca['labaBerjalan'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #f8fafc; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: #ef4444;">TOTAL SUMBER DANA</td>
                                        <td style="padding: 15px; color: #ef4444; text-align: right;">Rp <?= number_format($neraca['totalPasiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- End Mode Pengurus -->

                <!-- ============================================== -->
                <!-- MODE AKUNTAN (STANDAR PSAK KOPERASI & NERACA DI ATAS) -->
                <!-- ============================================== -->
                <div id="laporan-akuntan" style="display: none;">
                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 10px; margin-bottom: 15px;"><i class="fas fa-balance-scale"></i> NERACA (Balance Sheet) per <?= date('d M Y') ?></h3>
                    <div style="display: flex; gap: 20px;">
                        <!-- AKTIVA -->
                        <div class="table-container" style="flex: 1;">
                            <h4 style="color: var(--primary); margin-bottom: 10px;">AKTIVA</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Aktiva Lancar</td></tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Kas & Setara Kas</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['kas'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Piutang Pinjaman Anggota</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['piutang'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #e0f2fe; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: var(--primary);">TOTAL AKTIVA</td>
                                        <td style="padding: 15px; color: var(--primary); text-align: right;">Rp <?= number_format($neraca['totalAktiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- PASIVA -->
                        <div class="table-container" style="flex: 1;">
                            <h4 style="color: #ef4444; margin-bottom: 10px;">PASIVA</h4>
                            <table class="display" style="width:100%; border-collapse: collapse;">
                                <tbody>
                                    <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Kewajiban</td></tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Simpanan Sukarela Anggota</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['kewajiban'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Ekuitas</td></tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Simpanan Pokok & Wajib</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($neraca['modalPokokWajib'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Sisa Hasil Usaha (SHU) Berjalan</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #16a34a;">Rp <?= number_format($neraca['labaBerjalan'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="background: #fee2e2; font-weight: bold; font-size: 1.1rem;">
                                        <td style="padding: 15px; color: #ef4444;">TOTAL PASIVA</td>
                                        <td style="padding: 15px; color: #ef4444; text-align: right;">Rp <?= number_format($neraca['totalPasiva'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h3 style="font-size: 1.1rem; color: #0f172a; margin-top: 30px; margin-bottom: 15px;"><i class="fas fa-file-invoice-dollar"></i> LABA RUGI (Perhitungan Hasil Usaha)</h3>
                    <div class="table-container">
                        <table class="display" style="width:100%; border-collapse: collapse;">
                            <tbody>
                                <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Pendapatan Operasional</td></tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Pendapatan Jasa Pinjaman</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($labaRugi['pendapatanJasa'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Pendapatan Kotor Penjualan Waserda</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp <?= number_format($labaRugi['penjualanWaserda'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">(HPP Penjualan Waserda)</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color:#ef4444;">(Rp <?= number_format($labaRugi['penjualanWaserda'] - $labaRugi['labaKotorWaserda'], 0, ',', '.') ?>)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">Total Laba Kotor</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">Rp <?= number_format($labaRugi['totalPendapatan'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr><td colspan="2" style="padding: 5px 10px; font-weight: bold; background: #f8fafc;">Beban Operasional</td></tr>
                                <tr>
                                    <td style="padding: 10px 10px 10px 20px; border-bottom: 1px solid #e2e8f0;">Beban Operasional Umum & Administrasi</td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color:#ef4444;">(Rp <?= number_format($labaRugi['bebanOperasional'] ?? 0, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr style="background: #e0f2fe; font-weight: bold; font-size: 1.1rem;">
                                    <td style="padding: 15px; color: var(--primary);">SISA HASIL USAHA (SHU) BERSIH</td>
                                    <td style="padding: 15px; color: var(--primary); text-align: right;">Rp <?= number_format($labaRugi['shuBersih'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> <!-- End Mode Akuntan -->

                <!-- Simulasi Pembagian Tetap Muncul di Kedua Mode -->
                <div class="table-container" style="margin-top: 30px;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 15px;"><i class="fas fa-chart-pie"></i> Rencana Alokasi Pembagian SHU</h3>
                    <div class="table-responsive">
                    <table class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Kategori / Divisi</th>
                                <th>Persentase</th>
                                <th>Estimasi Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Dana Cadangan Koperasi</td><td>25%</td><td>Rp <?= number_format($labaRugi['shuBersih'] * 0.25, 0, ',', '.') ?></td></tr>
                            <tr><td>Jasa Anggota (Simpanan & Pinjaman)</td><td>40%</td><td>Rp <?= number_format($labaRugi['shuBersih'] * 0.40, 0, ',', '.') ?></td></tr>
                            <tr><td>Dana Pengurus & Karyawan</td><td>15%</td><td>Rp <?= number_format($labaRugi['shuBersih'] * 0.15, 0, ',', '.') ?></td></tr>
                            <tr><td>Dana Pendidikan & Sosial</td><td>10%</td><td>Rp <?= number_format($labaRugi['shuBersih'] * 0.10, 0, ',', '.') ?></td></tr>
                            <tr><td>Dana Pembangunan Daerah Kerja</td><td>10%</td><td>Rp <?= number_format($labaRugi['shuBersih'] * 0.10, 0, ',', '.') ?></td></tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
<?= $this->endSection() ?>
