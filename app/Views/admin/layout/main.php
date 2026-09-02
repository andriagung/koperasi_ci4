<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">
    <title>Admin Panel - Kopkar Assyifa RSUD 45</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <style>
        :root {
            --primary: #059669;
            /* Emerald 600 */
            --primary-light: #10b981;
            /* Emerald 500 */
            --primary-dark: #047857;
            /* Emerald 700 */
            --sidebar-bg: #047857;
            /* Disamakan dengan tema mobile */
            --sidebar-hover: #059669;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #334155;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            height: 100vh;
            overflow: hidden;
        }

        body {
            display: flex;
            background-color: var(--bg-color);
            color: var(--text-main);
            height: 100vh;
            overflow: hidden;
        }

        /* Modern Slim Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #334155;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .nav-menu {
            padding: 20px 0;
            flex: 1;
        }

        .nav-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #cbd5e1;
            text-decoration: none;
            transition: 0.2s;
            cursor: pointer;
        }

        .nav-item:hover,
        .nav-item.active {
            background-color: var(--sidebar-hover);
            color: white;
            border-left: 4px solid var(--primary-light);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        .admin-profile {
            padding: 20px;
            border-top: 1px solid #334155;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
        }

        /* DROPDOWN MENU */
        .nav-dropdown {
            background-color: rgba(0, 0, 0, 0.1);
            display: none;
        }

        .nav-dropdown.show {
            display: block;
        }

        .nav-dropdown .nav-item {
            padding: 10px 25px 10px 45px;
            font-size: 0.85rem;
            border-left: none;
        }

        .nav-dropdown .nav-item:hover,
        .nav-dropdown .nav-item.active {
            border-left: none;
            background-color: var(--sidebar-hover);
            color: white;
            font-weight: bold;
        }

        .nav-item-has-children {
            justify-content: space-between;
            cursor: pointer;
        }

        .nav-item-has-children>div {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-item-has-children i.fa-chevron-down {
            font-size: 0.8rem;
            transition: transform 0.3s;
            width: auto;
            text-align: right;
            margin-right: 15px;
        }

        .nav-item-has-children.open i.fa-chevron-down {
            transform: rotate(180deg);
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .topbar {
            background-color: var(--card-bg);
            height: 70px;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .topbar-search {
            display: flex;
            align-items: center;
            background: var(--bg-color);
            padding: 8px 15px;
            border-radius: 8px;
            width: 300px;
        }

        .topbar-search input {
            border: none;
            background: transparent;
            outline: none;
            margin-left: 10px;
            width: 100%;
            color: var(--text-main);
        }

        .topbar-actions {
            display: flex;
            gap: 20px;
            font-size: 1.2rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .content-area {
            padding: 30px;
            overflow-y: auto;
            height: calc(100vh - 70px);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
        }

        /* WIDGET CARDS */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .stat-info h4 {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-info h2 {
            font-size: 1.4rem;
            color: var(--text-main);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.6rem;
        }

        .icon-blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-orange {
            background: #fef3c7;
            color: #d97706;
        }

        .icon-red {
            background: #fee2e2;
            color: #dc2626;
        }

        /* DATATABLES */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: 25px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-group select {
            padding: 10px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            font-size: 0.9rem;
        }

        table.dataTable {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px !important;
        }

        table.dataTable thead th {
            background-color: #f8fafc;
            color: #475569;
            padding: 10px 12px;
            border-bottom: 2px solid var(--border) !important;
            font-size: 0.8rem;
            text-transform: uppercase;
            text-align: left;
            font-weight: 600;
        }

        table.dataTable tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            font-size: 0.85rem;
            vertical-align: middle;
        }

        table.dataTable tbody tr {
            transition: background-color 0.2s;
        }

        table.dataTable tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* DataTables Custom Controls */
        .dt-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dt-action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed var(--border);
        }

        .dt-export-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dt-custom-buttons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .dataTables_wrapper .dataTables_filter {
            text-align: left;
            position: relative;
        }

        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            font-size: 0;
            color: transparent;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border);
            padding: 9px 15px 9px 35px;
            border-radius: 99px;
            outline: none;
            margin-left: 0;
            width: 260px;
            font-size: 0.9rem;
            background: #fff url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="%2394a3b8" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat 14px center;
            transition: 0.2s;
            color: var(--text-main);
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            width: 300px;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder {
            color: #94a3b8;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin: 0 2px;
            border-radius: 6px;
            border: 1px solid var(--border);
            cursor: pointer;
            color: var(--text-main) !important;
            transition: 0.2s;
            background: white;
            font-size: 0.8rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9;
            color: var(--primary) !important;
            border-color: var(--border);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary);
            font-weight: bold;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Export Buttons Emerald Style */
        button.dt-button.btn-export,
        a.dt-button.btn-export,
        div.dt-button.btn-export {
            background: var(--primary) !important;
            color: #ffffff !important;
            border: none !important;
            padding: 8px 15px !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            margin-right: 8px !important;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
        }

        button.dt-button.btn-export i,
        a.dt-button.btn-export i {
            margin-right: 6px;
            color: #ffffff;
        }

        button.dt-button.btn-export:hover,
        a.dt-button.btn-export:hover,
        div.dt-button.btn-export:hover {
            background: var(--primary-dark) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.3);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }

        .bg-success {
            background: #dcfce7;
            color: #166534;
        }

        .bg-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .bg-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-btns {
            display: flex;
            gap: 4px;
            align-items: center;
            justify-content: center;
        }

        .btn-action {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background: #f1f5f9;
            color: var(--text-muted);
            cursor: pointer;
            transition: 0.2s;
            border: none;
            font-size: 0.75rem;
            padding: 0;
        }

        .btn-action:hover {
            background: var(--primary);
            color: white;
        }

        .btn-action.delete:hover {
            background: #ef4444;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-success {
            background: #10b981;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        /* SPA Hiding */
        .panel-view {
            display: none;
        }

        .panel-view.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* In-Page Navigation Tabs */
        .nav-tabs {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 25px;
            padding-bottom: 0;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .nav-tabs::-webkit-scrollbar {
            display: none;
        }

        .nav-tab-btn {
            padding: 12px 20px;
            border: none;
            background: transparent;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: 0.2s;
            white-space: nowrap;
        }

        .nav-tab-btn:hover {
            color: var(--primary);
            background: #f8fafc;
            border-radius: 8px 8px 0 0;
        }

        .nav-tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 600;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.3s;
        }

        .tab-pane.active {
            display: block;
        }

        /* Placeholder UI for Dummy Pages */
        .dummy-content {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .dummy-content i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .dummy-content h3 {
            color: #0f172a;
            margin-bottom: 10px;
        }

        /* Modals */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            z-index: 9999;
            overflow-y: auto;
            padding: 30px 15px;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: block;
            animation: modalFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-overlay .modal-content {
            background: white;
            padding: 30px;
            border-radius: 16px;
            width: 800px;
            max-width: 100%;
            position: relative;
            margin: 0 auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-overlay .modal-content.modal-lg {
            width: 700px;
        }

        .modal-overlay .modal-content.modal-xl {
            width: 900px;
        }

        .modal-overlay .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 8px;
            color: var(--text-main);
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            font-size: 0.95rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .grid-2-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Responsive UI */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            z-index: 1040;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        .toggle-sidebar-btn {
            display: none;
            font-size: 1.4rem;
            cursor: pointer;
            color: var(--text-main);
            margin-right: 15px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                height: 100vh;
                left: -260px;
                z-index: 1050;
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.1);
            }

            .sidebar.active-sidebar {
                left: 0;
            }

            .toggle-sidebar-btn {
                display: block;
            }

            .topbar {
                padding: 0 20px;
            }

            .content-area {
                padding: 20px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .topbar-search {
                display: none;
            }

            .page-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .grid-2-col {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php
    $uri = service('uri');
    $seg2 = $uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : '';
    $seg3 = $uri->getTotalSegments() >= 3 ? $uri->getSegment(3) : '';
    $status_get = service('request')->getGet('status') ?? '';
    ?>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-hospital"></i> Kopkar Assyifa</h2>
            <p>Admin Workspace</p>
        </div>
        <div class="nav-menu">
            <?php $role = session()->get('role') ?? 'Super Admin'; ?>

            <a href="<?= base_url('admin/dashboard') ?>"
                class="nav-item <?= ($seg2 == '' || $seg2 == 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>

            <?php if (in_array($role, ['Super Admin', 'Admin', 'Pengurus', 'Manajer', 'Bendahara', 'Petugas Kredit'])): ?>
                <!-- KEANGGOTAAN -->
                <div class="nav-item nav-item-has-children <?= (in_array($seg2, ['anggota', 'bendahara'])) ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-users"></i> Keanggotaan</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= (in_array($seg2, ['anggota', 'bendahara'])) ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/anggota') ?>" class="nav-item <?= ($seg2 == 'anggota' && $seg3 == '') ? 'active' : '' ?>">Data Anggota</a>
                    <a href="<?= base_url('admin/anggota/kartu') ?>" class="nav-item <?= ($seg3 == 'kartu') ? 'active' : '' ?>">Kartu Anggota</a>
                    <?php if (in_array($role, ['Super Admin', 'Admin', 'Bendahara', 'Pengurus'])): ?>
                    <a href="<?= base_url('admin/bendahara') ?>" class="nav-item <?= ($seg2 == 'bendahara') ? 'active' : '' ?>">Bendahara Gaji</a>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

            <?php if (in_array($role, ['Super Admin', 'Admin', 'Teller', 'Petugas Kredit', 'Manajer'])): ?>
                <!-- PPOB -->
                <div class="nav-item nav-item-has-children <?= ($seg2 == 'ppob') ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-mobile-alt"></i> Layanan PPOB</div>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="nav-dropdown <?= ($seg2 == 'ppob') ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/ppob/kasir') ?>" class="nav-item <?= ($seg2 == 'ppob' && $seg3 == 'kasir') ? 'active' : '' ?>">Kasir PPOB</a>
                    <a href="<?= base_url('admin/ppob') ?>" class="nav-item <?= ($seg2 == 'ppob' && empty($seg3)) ? 'active' : '' ?>">Produk & Transaksi</a>
                </div>

                <!-- SIMPAN PINJAM -->
                <div class="nav-item nav-item-has-children <?= ($seg2 == 'simpanan') ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-piggy-bank"></i> Simpanan</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= ($seg2 == 'simpanan') ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/simpanan/jenis') ?>" class="nav-item <?= ($seg3 == 'jenis') ? 'active' : '' ?>">Jenis Simpanan</a>
                    <a href="<?= base_url('admin/simpanan/transaksi') ?>" class="nav-item <?= ($seg3 == 'transaksi') ? 'active' : '' ?>">Setor / Tarik</a>
                    <a href="<?= base_url('admin/simpanan/mutasi') ?>" class="nav-item <?= ($seg3 == 'mutasi') ? 'active' : '' ?>">Mutasi Simpanan</a>
                    <a href="<?= base_url('admin/simpanan/buku') ?>" class="nav-item <?= ($seg3 == 'buku') ? 'active' : '' ?>">Buku Simpanan</a>
                </div>

                <!-- PINJAMAN -->
                <div class="nav-item nav-item-has-children <?= ($seg2 == 'pinjaman' || $seg2 == 'penagihan') ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-hand-holding-dollar"></i> Pinjaman</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= ($seg2 == 'pinjaman' || $seg2 == 'penagihan') ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/pinjaman/produk') ?>" class="nav-item <?= ($seg3 == 'produk') ? 'active' : '' ?>">Produk Pinjaman</a>
                    <a href="<?= base_url('admin/pinjaman/pengajuan') ?>" class="nav-item <?= ($seg3 == 'pengajuan' && $status_get == '') ? 'active' : '' ?>">Pengajuan</a>
                    <a href="<?= base_url('admin/pinjaman/pengajuan?status=submitted') ?>" class="nav-item <?= ($seg3 == 'pengajuan' && $status_get == 'submitted') ? 'active' : '' ?>">Approval</a>
                    <a href="<?= base_url('admin/pinjaman/pengajuan?status=approved') ?>" class="nav-item <?= ($seg3 == 'pengajuan' && $status_get == 'approved') ? 'active' : '' ?>">Pencairan</a>
                    <a href="<?= base_url('admin/pinjaman/pengajuan?status=active') ?>" class="nav-item <?= ($seg3 == 'pengajuan' && $status_get == 'active') ? 'active' : '' ?>">Jadwal Angsuran</a>
                    <a href="<?= base_url('admin/penagihan') ?>" class="nav-item <?= ($seg2 == 'penagihan') ? 'active' : '' ?>">Tunggakan & Aging</a>
                    <a href="<?= base_url('admin/pinjaman/restrukturisasi') ?>" class="nav-item <?= ($seg3 == 'restrukturisasi') ? 'active' : '' ?>">Restrukturisasi</a>
                </div>
            <?php endif; ?>

            <?php if (in_array($role, ['Super Admin', 'Admin', 'Bendahara', 'Akuntansi'])): ?>
                <!-- POTONGAN GAJI -->
                <a href="<?= base_url('admin/potongan') ?>"
                    class="nav-item <?= ($seg2 == 'potongan') ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Potongan Gaji
                </a>
            <?php endif; ?>

            <?php if (in_array($role, ['Super Admin', 'Admin', 'Kasir', 'Gudang', 'Akuntansi'])): ?>
                <!-- WARSERDA -->
                <div class="nav-item nav-item-has-children <?= (in_array($seg2, ['waserda', 'po']) || ($seg2 == 'gudang' && in_array($seg3, ['supplier', 'retur']))) ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-store"></i> WARSerDA</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= (in_array($seg2, ['waserda', 'po']) || ($seg2 == 'gudang' && in_array($seg3, ['supplier', 'retur']))) ? 'show' : '' ?>">
                    <?php if (in_array($role, ['Super Admin', 'Admin', 'Kasir', 'Akuntansi'])): ?>
                        <a href="<?= base_url('admin/waserda') ?>" class="nav-item <?= ($seg2 == 'waserda') ? 'active' : '' ?>">POS / Kasir</a>
                        <a href="<?= base_url('admin/waserda#produk') ?>" class="nav-item">Produk</a>
                    <?php endif; ?>
                    <?php if (in_array($role, ['Super Admin', 'Admin', 'Gudang'])): ?>
                        <a href="<?= base_url('admin/gudang#supplier') ?>" class="nav-item">Supplier</a>
                    <?php endif; ?>
                    <?php if (in_array($role, ['Super Admin', 'Admin', 'Akuntansi', 'Gudang'])): ?>
                        <a href="<?= base_url('admin/po') ?>" class="nav-item <?= ($seg2 == 'po') ? 'active' : '' ?>">Pembelian (PO)</a>
                        <a href="<?= base_url('admin/laporan/waserda') ?>" class="nav-item">Penjualan</a>
                    <?php endif; ?>
                    <?php if (in_array($role, ['Super Admin', 'Admin', 'Gudang'])): ?>
                        <a href="<?= base_url('admin/gudang#retur') ?>" class="nav-item">Retur Penjualan</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (in_array($role, ['Super Admin', 'Admin', 'Gudang'])): ?>
                <!-- INVENTORY -->
                <div class="nav-item nav-item-has-children <?= ($seg2 == 'inventory' || ($seg2 == 'gudang' && !in_array($seg3, ['supplier', 'retur']))) ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-boxes"></i> Inventory</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= ($seg2 == 'inventory' || ($seg2 == 'gudang' && !in_array($seg3, ['supplier', 'retur']))) ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/inventory/lokasi') ?>" class="nav-item <?= ($seg3 == 'lokasi') ? 'active' : '' ?>">Stok & Lokasi</a>
                    <a href="<?= base_url('admin/inventory/kartu-stok') ?>" class="nav-item <?= ($seg3 == 'kartu-stok') ? 'active' : '' ?>">Kartu Stok</a>
                    <a href="<?= base_url('admin/inventory/transfer') ?>" class="nav-item <?= ($seg3 == 'transfer') ? 'active' : '' ?>">Transfer Stok</a>
                    <a href="<?= base_url('admin/inventory/opname') ?>" class="nav-item <?= ($seg3 == 'opname') ? 'active' : '' ?>">Stock Opname</a>
                </div>
            <?php endif; ?>

            <?php if (in_array($role, ['Super Admin', 'Admin'])): ?>
                <!-- KEUANGAN -->
                <div class="nav-item nav-item-has-children <?= (in_array($seg2, ['keuangan', 'akuntansi', 'jurnal'])) ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-wallet"></i> Keuangan</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= (in_array($seg2, ['keuangan', 'akuntansi', 'jurnal'])) ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/keuangan/kas') ?>" class="nav-item <?= ($seg3 == 'kas') ? 'active' : '' ?>">Kas</a>
                    <a href="<?= base_url('admin/keuangan/bank') ?>" class="nav-item <?= ($seg3 == 'bank') ? 'active' : '' ?>">Bank</a>
                    <?php if (in_array($role, ['Super Admin', 'Admin', 'Akuntansi'])): ?>
                    <a href="<?= base_url('admin/keuangan/rekonsiliasi') ?>" class="nav-item <?= ($seg3 == 'rekonsiliasi') ? 'active' : '' ?>">Rekonsiliasi</a>
                    <a href="<?= base_url('admin/akuntansi/coa') ?>" class="nav-item <?= ($seg3 == 'coa') ? 'active' : '' ?>">Chart of Account</a>
                    <a href="<?= base_url('admin/akuntansi/jurnal') ?>" class="nav-item <?= ($seg3 == 'jurnal' || $seg2 == 'jurnal') ? 'active' : '' ?>">Jurnal Umum</a>
                    <a href="<?= base_url('admin/akuntansi/buku-besar') ?>" class="nav-item <?= ($seg3 == 'buku-besar') ? 'active' : '' ?>">Buku Besar</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (in_array($role, ['Super Admin', 'Pengurus', 'Manajer'])): ?>
                <!-- SHU -->
                <div class="nav-item nav-item-has-children <?= ($seg2 == 'shu') ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-hand-holding-usd"></i> Pembagian SHU</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= ($seg2 == 'shu') ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/shu') ?>" class="nav-item <?= ($seg2 == 'shu' && $seg3 == '') ? 'active' : '' ?>">Periode & Kalkulasi</a>
                    <a href="<?= base_url('admin/shu/detail') ?>" class="nav-item <?= ($seg3 == 'detail') ? 'active' : '' ?>">Detail Distribusi</a>
                </div>
            <?php endif; ?>

            <?php if (in_array($role, ['Super Admin', 'Admin', 'Pengurus', 'Manajer', 'Akuntansi'])): ?>
                <!-- LAPORAN -->
                <div class="nav-item nav-item-has-children <?= ($seg2 == 'laporan') ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-print"></i> Laporan</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= ($seg2 == 'laporan') ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/laporan/bulanan') ?>" class="nav-item <?= ($seg3 == 'bulanan') ? 'active' : '' ?>">Laporan Bulanan</a>
                    <a href="<?= base_url('admin/laporan/anggota') ?>" class="nav-item <?= ($seg3 == 'anggota') ? 'active' : '' ?>">Laporan Anggota</a>
                    <a href="<?= base_url('admin/laporan/simpanan') ?>" class="nav-item <?= ($seg3 == 'simpanan') ? 'active' : '' ?>">Laporan Simpanan</a>
                    <a href="<?= base_url('admin/laporan/pinjaman') ?>" class="nav-item <?= ($seg3 == 'pinjaman') ? 'active' : '' ?>">Laporan Pinjaman</a>
                    <a href="<?= base_url('admin/laporan/rat') ?>" class="nav-item <?= ($seg3 == 'rat') ? 'active' : '' ?>">Laporan RAT Akhir Tahun</a>
                    <a href="<?= base_url('admin/laporan/tunggakanPinjaman') ?>" class="nav-item <?= ($seg3 == 'tunggakanPinjaman') ? 'active' : '' ?>">Laporan Tunggakan</a>
                    <a href="<?= base_url('admin/laporan/waserda') ?>" class="nav-item <?= ($seg3 == 'waserda') ? 'active' : '' ?>">Laporan WARSerDA</a>
                    <a href="<?= base_url('admin/laporan/penjualanHarian') ?>" class="nav-item <?= ($seg3 == 'penjualanHarian') ? 'active' : '' ?>">Penjualan Harian</a>
                    <a href="<?= base_url('admin/laporan/produkTerlaris') ?>" class="nav-item <?= ($seg3 == 'produkTerlaris') ? 'active' : '' ?>">Produk Terlaris</a>
                    <a href="<?= base_url('admin/laporan/slow_moving') ?>" class="nav-item <?= ($seg3 == 'slow_moving') ? 'active' : '' ?>">Barang Mati (Slow-Moving)</a>
                    <a href="<?= base_url('admin/laporan/inventory') ?>" class="nav-item <?= ($seg3 == 'inventory') ? 'active' : '' ?>">Laporan Inventory</a>
                    <a href="<?= base_url('admin/laporan/aruskas') ?>" class="nav-item <?= ($seg3 == 'aruskas') ? 'active' : '' ?>">Arus Kas</a>
                    <a href="<?= base_url('admin/laporan/daftar-potongan') ?>" class="nav-item <?= ($seg3 == 'daftar-potongan') ? 'active' : '' ?>">Daftar Potongan</a>
                    <a href="<?= base_url('admin/akuntansi/laba-rugi') ?>" class="nav-item <?= ($seg3 == 'laba-rugi') ? 'active' : '' ?>">Laba Rugi</a>
                    <a href="<?= base_url('admin/akuntansi/neraca') ?>" class="nav-item <?= ($seg3 == 'neraca') ? 'active' : '' ?>">Neraca</a>
                    <a href="<?= base_url('admin/akuntansi/neraca-saldo') ?>" class="nav-item <?= ($seg3 == 'neraca-saldo') ? 'active' : '' ?>">Neraca Saldo</a>
                    <a href="<?= base_url('admin/akuntansi/buku-besar') ?>" class="nav-item <?= ($seg3 == 'buku-besar' && $seg2 == 'akuntansi') ? 'active' : '' ?>">Buku Besar</a>
                </div>
            <?php endif; ?>

            <?php if ($role === 'Super Admin'): ?>
                <!-- SISTEM -->
                <div class="nav-item nav-item-has-children <?= (in_array($seg2, ['pengaturan', 'analitik', 'audit'])) ? 'open' : '' ?>" onclick="toggleDropdown(this)">
                    <div><i class="fas fa-cogs"></i> Sistem</div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="nav-dropdown <?= (in_array($seg2, ['pengaturan', 'analitik', 'audit'])) ? 'show' : '' ?>">
                    <a href="<?= base_url('admin/pengaturan/user') ?>" class="nav-item <?= ($seg2 == 'pengaturan' && $seg3 == 'user') ? 'active' : '' ?>">Manajemen User</a>
                    <a href="<?= base_url('admin/analitik') ?>" class="nav-item <?= ($seg2 == 'analitik') ? 'active' : '' ?>">Analitik & BI</a>
                    <a href="<?= base_url('admin/audit') ?>" class="nav-item <?= ($seg2 == 'audit') ? 'active' : '' ?>">Audit Trail</a>
                    <a href="<?= base_url('admin/pengaturan') ?>" class="nav-item <?= ($seg2 == 'pengaturan' && $seg3 != 'user') ? 'active' : '' ?>">Pengaturan Sistem</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="admin-profile">
            <div class="admin-avatar"><?= substr(session()->get('nama_lengkap') ?? 'A', 0, 1) ?></div>
            <div>
                <h4 style="font-size: 0.9rem; color: white;"><?= esc(session()->get('nama_lengkap') ?? 'Admin') ?></h4>
                <p style="font-size: 0.75rem; color: #94a3b8;"><?= esc(session()->get('role') ?? 'Super Admin') ?></p>
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
            <div class="topbar-actions" style="position: relative;">
                <i class="fas fa-envelope" title="Pesan"></i>
                <div style="position: relative; display: inline-block; cursor: pointer;"
                    onclick="document.getElementById('notif-dropdown').classList.toggle('show')">
                    <i class="fas fa-bell" title="Notifikasi"></i>
                    <!-- Badge Notif, diisi dinamis via AJAX -->
                    <span id="notif-badge"
                        style="display:none; position: absolute; top: -5px; right: -8px; background: #ef4444; color: white; font-size: 0.6rem; font-weight: bold; padding: 2px 5px; border-radius: 10px;">0</span>
                </div>

                <!-- Dropdown Notif -->
                <div id="notif-dropdown"
                    style="display: none; position: absolute; top: 35px; right: 40px; background: white; border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 250px; z-index: 100;">
                    <div
                        style="padding: 10px; border-bottom: 1px solid var(--border); font-size: 0.85rem; font-weight: bold; color: var(--text-main);">
                        Notifikasi Terbaru</div>
                    <div id="notif-list" style="max-height: 200px; overflow-y: auto;">
                        <div style="padding: 10px; text-align: center; font-size: 0.8rem; color: var(--text-muted);">
                            Memuat...</div>
                    </div>
                </div>
                <style>
                    #notif-dropdown.show {
                        display: block !important;
                    }
                </style>

                <span style="border-left: 1px solid var(--border); margin: 0 10px;"></span>
                <a href="<?= base_url('admin/logout') ?>" style="color: inherit; text-decoration: none;" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="content-area">
            <!-- Menampilkan Contextual Help -->
            <?= $this->include('admin/layout/contextual_help') ?>
            
            <?= $this->renderSection('content') ?>
        </div> <!-- Tutup content-area -->
    </div> <!-- Tutup main-content -->

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Scripts: jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        // Dropdown toggle logic
        function toggleDropdown(element) {
            element.classList.toggle('open');
            var dropdown = element.nextElementSibling;
            dropdown.classList.toggle('show');
        }

        // Toggle Sidebar on mobile
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active-sidebar');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }
    </script>

    <!-- Script: Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php
    $topLabels = [];
    $topData = [];
    if (isset($topWaserda) && is_array($topWaserda)) {
        foreach ($topWaserda as $top) {
            $topLabels[] = $top['nama_produk'];
            $topData[] = $top['total_terjual'];
        }
    }
    $totalPersediaan = 0;
    if (isset($waserda) && is_array($waserda)) {
        foreach ($waserda as $w) {
            $totalPersediaan += ($w['stok'] * $w['harga_beli']);
        }
    }
    ?>
    <script>
        window.AppConfig = {
            flashMessage: <?= json_encode(session()->getFlashdata('message') ?? '') ?>,
            flashError: <?= json_encode(session()->getFlashdata('error') ?? '') ?>,
            chartArusKas: {
                labels: <?= isset($chartArusKas) ? json_encode($chartArusKas['labels'] ?? []) : "[]" ?>,
                pendapatan: <?= isset($chartArusKas) ? json_encode($chartArusKas['pendapatan'] ?? []) : "[]" ?>,
                pengeluaran: <?= isset($chartArusKas) ? json_encode($chartArusKas['pengeluaran'] ?? []) : "[]" ?>
            },
            neraca: {
                kas: <?= isset($neraca) ? ($neraca['kas'] ?? 0) : 0 ?>,
                piutang: <?= isset($neraca) ? ($neraca['piutang'] ?? 0) : 0 ?>,
                persediaanBarang: <?= $totalPersediaan ?? '' ?>
            },
            topWaserda: {
                labels: <?= isset($topLabels) ? json_encode($topLabels) : "[]" ?>,
                data: <?= isset($topData) ? json_encode($topData) : "[]" ?>
            }
        };

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        // Script untuk Notifikasi Otomatis
        function fetchNotif() {
            $.get('<?= base_url('admin/dashboard/get_notif') ?>', function (res) {
                if (res.status === 'success') {
                    let html = '';
                    let count = res.data.length;

                    if (count > 0) {
                        $('#notif-badge').text(count).show();
                        res.data.forEach(item => {
                            let icon = item.type === 'pinjaman' ? '<i class="fas fa-hand-holding-dollar text-primary"></i>' : '<i class="fas fa-exclamation-triangle text-danger"></i>';
                            html += `<div style="padding: 10px; border-bottom: 1px solid var(--border); font-size: 0.8rem;">
                                        <div style="margin-bottom: 3px;">${icon} <strong>${item.title}</strong></div>
                                        <div style="color: var(--text-muted);">${item.msg}</div>
                                     </div>`;
                        });
                    } else {
                        $('#notif-badge').hide();
                        html = '<div style="padding: 10px; text-align: center; font-size: 0.8rem; color: var(--text-muted);">Tidak ada notifikasi baru</div>';
                    }
                    $('#notif-list').html(html);
                }
            }, 'json');
        }

        // Cek notif saat halaman dimuat, lalu tiap 60 detik
        $(document).ready(function () {
            fetchNotif();
            setInterval(fetchNotif, 60000);

            // Klik di luar dropdown untuk menutup
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.topbar-actions').length) {
                    $('#notif-dropdown').removeClass('show');
                }
            });
        });
    </script>

    <!-- GLOBAL CONFIRM MODAL -->
    <div class="modal-overlay" id="global-confirm-modal">
        <div class="modal-content" style="width: 800px; max-width: 95%; text-align: center; padding: 25px;">
            <i class="fas fa-times modal-close" onclick="tutupModal('global-confirm-modal')"></i>
            <h3 id="global-confirm-title" style="margin-bottom: 15px; color: #dc2626; font-size: 1.3rem; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> Konfirmasi
            </h3>
            <p id="global-confirm-message" style="margin-bottom: 25px; color: var(--text-main); font-size: 0.95rem; line-height: 1.5;"></p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" class="btn-primary" id="global-confirm-yes" style="background: #dc2626; padding: 10px 25px; font-size: 0.9rem; border: none; border-radius: 8px; color: white;">Ya, Proses</button>
                <button type="button" class="btn-sm" style="background: #64748b; color: white; padding: 10px 25px; font-size: 0.9rem; border: none; border-radius: 8px;" onclick="tutupModal('global-confirm-modal')">Batal</button>
            </div>
        </div>
    </div>

    <!-- GLOBAL ALERT MODAL -->
    <div class="modal-overlay" id="global-alert-modal">
        <div class="modal-content" style="width: 800px; max-width: 95%; text-align: center; padding: 25px;">
            <i class="fas fa-times modal-close" onclick="tutupModal('global-alert-modal')"></i>
            <h3 id="global-alert-title" style="margin-bottom: 15px; color: var(--primary); font-size: 1.3rem; font-weight: 600;">
                <i class="fas fa-info-circle"></i> Informasi
            </h3>
            <p id="global-alert-message" style="margin-bottom: 25px; color: var(--text-main); font-size: 0.95rem; line-height: 1.5;"></p>
            <div style="display: flex; justify-content: center;">
                <button type="button" class="btn-primary" onclick="tutupModal('global-alert-modal')" style="padding: 10px 35px; font-size: 0.9rem; border: none; border-radius: 8px; color: white; background: var(--primary);">OK</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
    
    <script>
    // Global AJAX setup for CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    });

    // Global DataTables Default Configuration (Bahasa Indonesia)
    if (typeof $.fn.dataTable !== 'undefined') {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ total entri)",
                "infoThousands": ".",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Sedang memuat data...",
                "processing": "Sedang memproses...",
                "search": "Cari:",
                "searchPlaceholder": "Ketik kata kunci...",
                "zeroRecords": "Tidak ditemukan data yang cocok",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "aria": {
                    "sortAscending": ": aktifkan untuk mengurutkan kolom naik",
                    "sortDescending": ": aktifkan untuk mengurutkan kolom turun"
                }
            }
        });
    }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>

</html>