<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pemotongan Gaji (Payroll) - Koperasi As-Syifa</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .email-container {
            max-width: 620px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 30px 25px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .email-header p {
            margin: 0;
            font-size: 12px;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px 25px;
        }
        .greeting {
            font-size: 15px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .greeting strong {
            color: #0f172a;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .summary-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .summary-row.total {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1.5px dashed #cbd5e1;
            font-size: 15px;
            font-weight: 700;
            color: #059669;
        }
        .loan-progress-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 15px 18px;
            margin-bottom: 25px;
        }
        .loan-progress-box h4 {
            margin: 0 0 6px 0;
            color: #1e40af;
            font-size: 13px;
            font-weight: 700;
        }
        .loan-progress-box p {
            margin: 0;
            color: #1e3a8a;
            font-size: 12px;
            line-height: 1.5;
        }
        .login-guide-box {
            background: #fdf4ff;
            border: 1px solid #f0abfc;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 25px;
        }
        .login-guide-box h4 {
            margin: 0 0 8px 0;
            color: #86198f;
            font-size: 14px;
            font-weight: 700;
        }
        .login-guide-box ul {
            margin: 0;
            padding-left: 18px;
            font-size: 12px;
            color: #701a75;
            line-height: 1.6;
        }
        .login-btn {
            display: inline-block;
            background: #059669;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 12px;
        }
        .attachment-note {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 12px;
            color: #92400e;
            margin-bottom: 25px;
        }
        .email-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 25px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>KOPERASI AS-SYIFA RSUD 45 KUNINGAN</h1>
            <p>Jl. Jend. Sudirman No. 68 Kuningan | Telp: (0232) 871885-159</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Yth. Bapak/Ibu <strong><?= esc($anggota['nama_lengkap'] ?? $tagihan['nama'] ?? 'Anggota') ?></strong>,<br>
                Unit Kerja: <strong><?= esc($anggota['divisi'] ?? $tagihan['nama_instansi'] ?? 'RSUD 45 Kuningan') ?></strong>
                <p style="margin: 8px 0 0 0; color: #64748b; font-size: 13px;">
                    Berikut adalah rincian pemotongan gaji (payroll deduction) Anda untuk periode <strong><?= date('F Y', strtotime(($tagihan['periode'] ?? date('Y-m')) . '-01')) ?></strong>:
                </p>
            </div>

            <!-- Summary Card -->
            <div class="summary-card">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">No. Anggota / NIP</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0f172a;"><?= esc($anggota['nip'] ?? $tagihan['nik'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Setor Simpanan Wajib</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #3b82f6;">Rp <?= number_format($tagihan['nominal_simpanan_wajib'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php if (!empty($tagihan['nominal_simpanan_sukarela']) && $tagihan['nominal_simpanan_sukarela'] > 0): ?>
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Setor Simpanan Sukarela (M-Suka)</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #3b82f6;">Rp <?= number_format($tagihan['nominal_simpanan_sukarela'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Angsuran Pinjaman (Pokok + Jasa)</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #8b5cf6;">Rp <?= number_format($tagihan['nominal_angsuran'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Iuran Dana Sosial</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #64748b;">Rp <?= number_format($tagihan['dana_sosial'] ?? 7500, 0, ',', '.') ?></td>
                    </tr>
                    <tr style="border-top: 1.5px dashed #cbd5e1;">
                        <td style="padding: 10px 0 4px 0; font-weight: 700; color: #0f172a; font-size: 14px;">TOTAL POTONGAN BULAN INI</td>
                        <td style="padding: 10px 0 4px 0; text-align: right; font-weight: 700; color: #059669; font-size: 15px;">
                            Rp <?= number_format(($tagihan['total_tagihan'] ?? 0) + ($tagihan['dana_sosial'] ?? 7500), 0, ',', '.') ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Loan Progress Info Box -->
            <?php if (!empty($pinjaman_aktif)): ?>
            <div class="loan-progress-box">
                <h4>📊 Status Pinjaman Berjalan Anda</h4>
                <p>
                    • <strong>Plafon Pinjaman:</strong> Rp <?= number_format($pinjaman_aktif['nominal_pengajuan'] ?? 0, 0, ',', '.') ?><br>
                    • <strong>Masa Berjalan:</strong> Angsuran Ke-<strong><?= $pinjaman_aktif['angsuran_ke'] ?? 1 ?></strong> dari <strong><?= $pinjaman_aktif['tenor_bulan'] ?? 12 ?> Bulan</strong><br>
                    • <strong>Sisa Tenor:</strong> <?= max(0, ($pinjaman_aktif['tenor_bulan'] ?? 12) - ($pinjaman_aktif['angsuran_ke'] ?? 1)) ?> Bulan lagi<br>
                    • <strong>Sisa Pokok Pinjaman:</strong> Rp <?= number_format($pinjaman_aktif['sisa_pokok'] ?? 0, 0, ',', '.') ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- Attachment Note -->
            <div class="attachment-note">
                📎 <strong>Lampiran Bukti Resmi:</strong> File resmi <em>Slip Bukti Potongan Anggota (PDF)</em> telah dilampirkan bersama email ini. Anda dapat mengunduh atau mencetaknya sebagai arsip pribadi.
            </div>

            <!-- Member Portal Login Guide -->
            <div class="login-guide-box">
                <h4>📱 Akses Portal Anggota & Aplikasi Mobile</h4>
                <p style="margin: 0 0 8px 0; font-size: 12px; color: #701a75;">
                    Anda dapat memantau saldo simpanan, sisa pinjaman, riwayat mutasi, belanja WASERDA, dan kartu anggota digital secara realtime melalui portal:
                </p>
                <ul>
                    <li><strong>Link Portal:</strong> <a href="<?= base_url() ?>" target="_blank" style="color: #86198f; font-weight: bold;"><?= base_url() ?></a></li>
                    <li><strong>Username:</strong> Nomor NIP / NIK Anda (<code><?= esc($anggota['nip'] ?? $tagihan['nik'] ?? 'NIP') ?></code>)</li>
                    <li><strong>PIN / Password:</strong> Masukkan PIN default Anda saat pertama kali mendaftar</li>
                </ul>
                <div style="text-align: center;">
                    <a href="<?= base_url('login') ?>" class="login-btn" target="_blank">Buka Portal Anggota Sekarang &rarr;</a>
                </div>
            </div>

            <p style="font-size: 12px; color: #64748b; margin-top: 20px; line-height: 1.5;">
                Apabila terdapat perbedaan atau pertanyaan seputar potongan gaji ini, silakan menghubungi kantor sekretariat Koperasi As-Syifa RSUD 45 Kuningan atau Bendahara unit kerja Anda.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            Email ini dibuat dan dikirimkan secara otomatis oleh Sistem Informasi Manajemen Koperasi As-Syifa RSUD 45 Kuningan.<br>
            &copy; <?= date('Y') ?> Kopkar Assyifa RSUD 45. All rights reserved.
        </div>
    </div>
</body>
</html>
