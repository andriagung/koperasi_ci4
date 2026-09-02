<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">
    <title>Admin Panel - Kopkar Assyifa RSUD 45</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-hospital"></i> Kopkar Assyifa</h2>
            <p>Admin Workspace</p>
        </div>
        <div class="nav-menu">
            <!-- Setiap menu sudah diberi event onclick untuk memanggil view yang sesuai -->
            <div class="nav-item active" onclick="switchView('view-dashboard', this)">
                <i class="fas fa-chart-line"></i> Dashboard
            </div>
            <div class="nav-item" onclick="switchView('view-anggota', this)">
                <i class="fas fa-users"></i> Data Anggota
            </div>
            <div class="nav-item" onclick="switchView('view-simpan-pinjam', this)">
                <i class="fas fa-hand-holding-dollar"></i> Simpan Pinjam
            </div>
            <div class="nav-item" onclick="switchView('view-penagihan', this)">
                <i class="fas fa-file-invoice-dollar"></i> Penagihan & Aging
            </div>
            <div class="nav-item" onclick="switchView('view-waserda', this)">
                <i class="fas fa-cash-register"></i> Kasir Waserda
            </div>
            <div class="nav-item" onclick="switchView('view-gudang', this)">
                <i class="fas fa-warehouse"></i> Gudang & Stok
            </div>
            <div class="nav-item" onclick="switchView('view-po', this)">
                <i class="fas fa-truck-loading"></i> Purchase Order
            </div>
            <div class="nav-item" onclick="switchView('view-laporan', this)">
                <i class="fas fa-file-invoice"></i> Laporan Keuangan
            </div>
            <div class="nav-item" onclick="switchView('view-akuntansi', this)">
                <i class="fas fa-book-journal-whills"></i> Akuntansi & Jurnal
            </div>
            <div class="nav-item" onclick="switchView('view-riwayat', this)">
                <i class="fas fa-history"></i> Riwayat Transaksi
            </div>
            <a href="<?= base_url('admin/riwayat') ?>" class="nav-item <?= (isset($currentMenu) && $currentMenu == 'riwayat') ? 'active' : '' ?>">
                <i class="fas fa-history"></i> Log Aktivitas
            </a>
            <a href="<?= base_url('admin/analitik') ?>" class="nav-item <?= (isset($currentMenu) && $currentMenu == 'analitik') ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Analitik & BI
            </a>
            <div class="nav-item" onclick="switchView('view-audit', this)">
                <i class="fas fa-shield-alt"></i> Keamanan & Audit
            </div>
            <div class="nav-item" onclick="switchView('view-pengaturan', this)">
                <i class="fas fa-cog"></i> Pengaturan
            </div>
        </div>
        <div class="admin-profile">
            <div class="admin-avatar">AA</div>
            <div>
                <h4 style="font-size: 0.9rem; color: white;">Agung Andri</h4>
                <p style="font-size: 0.75rem; color: #94a3b8;">Super Administrator</p>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center;">
                <i class="fas fa-bars toggle-sidebar-btn" onclick="toggleSidebar()"></i>
                <div class="topbar-search">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" placeholder="Cari NIP, Nama, atau Transaksi...">
                </div>
            </div>
            <div class="topbar-actions">
                <i class="fas fa-envelope"></i>
                <i class="fas fa-bell"></i>
                <span style="border-left: 1px solid var(--border); margin: 0 10px;"></span>
                <i class="fas fa-sign-out-alt"></i>
            </div>
        </div>

        <div class="content-area">
            
            <!-- 1. VIEW DASHBOARD -->
