<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">
    <title>Aplikasi Kopkar Assyifa RSUD 45 Kuningan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary: #059669; /* Emerald 600 - Tema Medis RSUD */
            --primary-dark: #047857; /* Emerald 700 */
            --secondary: #f59e0b;
            --bg-color: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background-color: var(--bg-color); display: flex; justify-content: center; align-items: center; min-height: 100vh; }

        .app-container {
            width: 100%; max-width: 414px; height: 100vh; background-color: var(--bg-color);
            position: relative; overflow: hidden;
            display: flex; flex-direction: column;
        }

        .screen { display: none; flex-direction: column; height: 100%; width: 100%; animation: fadeIn 0.3s; }
        .screen.active-screen { display: flex; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .header { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; padding: 40px 20px 30px; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; flex-shrink: 0; }
        .header-top { display: flex; justify-content: space-between; align-items: center; }
        .header-title { display: flex; align-items: center; gap: 15px; font-size: 1.2rem; font-weight: 600; }
        .back-btn { color: white; font-size: 1.2rem; cursor: pointer; }

        .balance-card { background: white; border-radius: 15px; padding: 20px; margin: -20px 20px 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); position: relative; z-index: 10; display: flex; justify-content: space-between; align-items: center; }
        .balance-info h3 { color: var(--text-light); font-size: 0.85rem; margin-bottom: 5px; font-weight: normal; }
        .balance-info h1 { color: var(--text-dark); font-size: 1.5rem; }

        .main-content { flex: 1; overflow-y: auto; padding: 0 20px 100px; }
        .main-content::-webkit-scrollbar { display: none; }

        .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        .menu-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: var(--text-dark); gap: 8px; cursor: pointer; }
        .menu-icon { width: 50px; height: 50px; background: white; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.2rem; color: var(--primary); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .menu-item span { font-size: 0.75rem; font-weight: 500; text-align: center; }

        .section-title { font-size: 1rem; color: var(--text-dark); margin: 15px 0; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
        
        .list-card { background: white; border-radius: 12px; padding: 15px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.02); border: 1px solid var(--border); }
        .list-card-left h4 { color: var(--text-dark); font-size: 0.95rem; margin-bottom: 3px; }
        .list-card-left p { color: var(--text-light); font-size: 0.75rem; }
        .list-card-right { text-align: right; }
        .list-card-right h3 { color: var(--text-dark); font-size: 1rem; }
        .list-card-right p { font-size: 0.7rem; margin-top: 3px; }
        .badge-success { background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
        .badge-warning { background: #fef9c3; color: #854d0e; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }

        /* Modal Struk */
        .modal-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.7); display: none; justify-content: center; align-items: center; z-index: 100; border-radius: 25px; }
        .modal-overlay.active { display: flex; animation: fadeIn 0.2s; }
        .modal-content { background: white; width: 85%; border-radius: 15px; padding: 0; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .struk-header { background: #f8fafc; padding: 20px; text-align: center; border-bottom: 2px dashed #cbd5e1; }
        .struk-body { padding: 20px; font-family: monospace; font-size: 0.85rem; color: #334155; }
        .struk-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .btn-close-modal { width: 100%; padding: 15px; background: var(--primary); color: white; border: none; font-weight: bold; cursor: pointer; }

        .btn-outline { border: 1px solid var(--primary); color: var(--primary); padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; background: transparent; cursor: pointer; }

        .bottom-nav { position: absolute; bottom: 0; width: 100%; background: white; display: flex; justify-content: space-around; padding: 15px 0 25px; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; z-index: 50; transition: transform 0.3s; }
        .bottom-nav.hidden { transform: translateY(100%); }
        .nav-item { text-decoration: none; color: var(--text-light); display: flex; flex-direction: column; align-items: center; font-size: 0.75rem; gap: 5px; cursor: pointer; }
        .nav-item.active { color: var(--primary); }

        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-dark); font-weight: 500; }
        .input-group input, .input-group textarea { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; outline: none; font-family: inherit; }
        .input-group input:focus, .input-group textarea:focus { border-color: var(--primary); }
        .btn-primary { background: var(--primary); color: white; padding: 12px; border-radius: 8px; font-weight: bold; font-size: 1rem; width: 100%; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(5, 150, 105, 0.2); }
    </style>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#16a34a">
    <link rel="apple-touch-icon" href="/assets/img/icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

</head>
<body>

    <div class="app-container">
        
        <?= $this->renderSection('content') ?>

        <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
        <!-- BOTTOM NAV -->
        <div class="bottom-nav" id="main-bottom-nav">
            <a class="nav-item <?= uri_string() == 'mobile/dashboard' || uri_string() == '' ? 'active' : '' ?>" href="<?= base_url('mobile/dashboard') ?>"><i class="fas fa-home"></i><span>Beranda</span></a>
            <a class="nav-item <?= uri_string() == 'mobile/simpanan' ? 'active' : '' ?>" href="<?= base_url('mobile/simpanan') ?>"><i class="fas fa-book"></i><span>Simpanan</span></a>
            <a class="nav-item <?= uri_string() == 'mobile/pinjaman' ? 'active' : '' ?>" href="<?= base_url('mobile/pinjaman') ?>"><i class="fas fa-file-invoice-dollar"></i><span>Pinjaman</span></a>
            <a class="nav-item <?= uri_string() == 'mobile/profil' ? 'active' : '' ?>" href="<?= base_url('mobile/profil') ?>"><i class="fas fa-user"></i><span>Profil</span></a>
        </div>
        <?php endif; ?>
    </div>
    <script>
        // Konfigurasi CSRF Token untuk semua AJAX Request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
            }
        });
        async function handleLogin(e) {
            e.preventDefault();
            const nip = document.getElementById('loginNip').value;
            const pin = document.getElementById('loginPin').value;
            const errorEl = document.getElementById('loginError');
            try {
                const formData = new FormData();
                formData.append('nip', nip);
                formData.append('pin', pin);
                const response = await fetch('/auth/login', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content')
                    }
                });
                const data = await response.json();
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    errorEl.innerText = data.message;
                    errorEl.style.display = 'block';
                }
            } catch(err) {
                errorEl.innerText = "Terjadi kesalahan koneksi.";
                errorEl.style.display = 'block';
            }
        }
        function updateGreeting() {
            const greetingEl = document.getElementById('user-greeting');
            if (greetingEl) {
                const hour = new Date().getHours();
                let timeText = 'Selamat Pagi';
                if (hour >= 12 && hour < 15) timeText = 'Selamat Siang';
                else if (hour >= 15 && hour < 18) timeText = 'Selamat Sore';
                else if (hour >= 18 || hour < 4) timeText = 'Selamat Malam';
                greetingEl.innerText = timeText;
            }
        }
        updateGreeting();

        function switchScreen(screenId, updateHash = true) {
            document.querySelectorAll('.screen').forEach(s => s.classList.remove('active-screen'));
            // For screens inside dashboard.php or main.php
            const targetScreen = document.getElementById(screenId);
            if(targetScreen) {
                targetScreen.classList.add('active-screen');
                if (updateHash) {
                    window.location.hash = screenId;
                }
                // If opening notifications, mark as read
                if (screenId === 'screen-notifikasi') {
                    fetch('/mobile/read-notif', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
                        }
                    }).then(res => res.json()).then(data => {
                        // Hide badge if success
                        if(data.status === 'success') {
                            const badge = document.querySelector('.fa-bell').nextElementSibling;
                            if (badge && badge.tagName === 'SPAN') {
                                badge.style.display = 'none';
                            }
                        }
                    });
                }
            } else {
                // fallback to dashboard if not found
                window.location.href = '/mobile/dashboard';
            }
        }
        function filterRiwayat(kategori, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.style.background = 'white';
                b.style.color = 'var(--text-light)';
                b.style.border = '1px solid var(--border)';
            });
            btn.style.background = 'var(--primary)';
            btn.style.color = 'white';
            btn.style.border = 'none';
            document.querySelectorAll('.riwayat-item').forEach(item => {
                if (kategori === 'semua') {
                    item.style.display = 'flex';
                } else {
                    if (item.classList.contains('item-' + kategori)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        }
        function hitungSimulasi() {
            const nominal = parseFloat(document.getElementById('input-nominal-pinjaman').value);
            const tenor = parseInt(document.getElementById('input-tenor-pinjaman').value);
            const boxSimulasi = document.getElementById('box-simulasi');
            if (nominal > 0 && tenor > 0) {
                const pokok = nominal / tenor;
                const bunga = nominal * 0.01; // Asumsi jasa flat 1% per bulan
                const total = pokok + bunga;
                const formatRp = (angka) => 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                document.getElementById('simulasi-pokok').innerText = formatRp(pokok);
                document.getElementById('simulasi-bunga').innerText = formatRp(bunga);
                document.getElementById('simulasi-total').innerText = formatRp(total);
                boxSimulasi.style.display = 'block';
            } else {
                boxSimulasi.style.display = 'none';
            }
        }
        function bukaStruk() { document.getElementById('struk-modal').classList.add('active'); }
        function tutupStruk() {
            document.getElementById('struk-modal').classList.remove('active');
        }
        // FUNGSI CUSTOM ALERT
        function showAlert(title, message, isSuccess = true, callback = null) {
            const modal = document.getElementById('alert-modal');
            const header = document.getElementById('alert-header');
            const icon = document.getElementById('alert-icon');
            const titleEl = document.getElementById('alert-title');
            const msgEl = document.getElementById('alert-message');
            const btnClose = document.getElementById('btn-close-alert');
            titleEl.innerText = title;
            msgEl.innerText = message;
            if (isSuccess) {
                header.style.background = '#059669'; // var(--primary)
                icon.className = 'fas fa-check-circle';
            } else {
                header.style.background = '#ef4444'; // red
                icon.className = 'fas fa-exclamation-triangle';
            }
            modal.classList.add('active');
            btnClose.onclick = function() {
                modal.classList.remove('active');
                if(callback) callback();
            };
        }
        function tutupAlert() {
            document.getElementById('alert-modal').classList.remove('active');
        }
        async function submitTarikSimpanan(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('<?= base_url("tarik-simpanan") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content') }
                });
                const result = await response.json();
                if (result.status === 'success') {
                    showAlert('Berhasil!', result.message, true, function() {
                        window.location.reload();
                    });
                } else {
                    showAlert('Gagal!', result.message, false);
                }
            } catch (error) {
                showAlert('Error!', 'Terjadi kesalahan koneksi.', false);
            }
        }
        async function submitSetorSimpanan(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('<?= base_url("setor-simpanan") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content') }
                });
                const result = await response.json();
                if (result.status === 'success') {
                    showAlert('Berhasil!', result.message, true, function() {
                        window.location.reload();
                    });
                } else {
                    showAlert('Gagal!', result.message, false);
                }
            } catch (error) {
                showAlert('Error!', 'Terjadi kesalahan koneksi.', false);
            }
        }
        let currentDownloadTarget = '';
        function bukaModalPin(filename) {
            currentDownloadTarget = filename;
            document.getElementById('input-verify-pin').value = '';
            document.getElementById('pin-modal').classList.add('active');
        }
        function beliKasbon(produkId, namaProduk) {
            if(confirm(`Anda yakin ingin membeli ${namaProduk} menggunakan plafon kasbon Anda?`)) {
                fetch('<?= base_url("checkout-waserda") ?>', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content')
                    },
                    body: `produk_id=${produkId}`
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if(data.status === 'success') {
                        location.reload();
                    }
                });
            }
        }
        function tutupModalPin() {
            document.getElementById('pin-modal').classList.remove('active');
        }
        async function submitVerifyPin(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('<?= base_url("verify-pin") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content') }
                });
                const result = await response.json();
                if (result.status === 'success') {
                    tutupModalPin();
                    showAlert('Berhasil!', 'PIN diverifikasi. ' + currentDownloadTarget + ' sedang diunduh.', true, function() {
                        // Arahkan ke backend CI4 untuk mengunduh PDF asli (DomPDF / FPDF)
                        window.location.href = '<?= base_url("download-pdf") ?>?file=' + encodeURIComponent(currentDownloadTarget);
                    });
                } else {
                    showAlert('Gagal!', result.message, false);
                }
            } catch (error) {
                showAlert('Error!', 'Terjadi kesalahan koneksi.', false);
            }
        }
        async function submitAjukanPinjaman(e) {
            e.preventDefault();
            const form = document.getElementById('form-ajukan-pinjaman');
            const formData = new FormData(form);
            try {
                const response = await fetch('<?= base_url("ajukan-pinjaman") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content') }
                });
                const result = await response.json();
                if (result.status === 'success') {
                    showAlert('Berhasil!', result.message, true, function() {
                        window.location.reload();
                    });
                } else {
                    showAlert('Gagal!', result.message, false);
                }
            } catch (error) {
                showAlert('Gagal!', 'Terjadi kesalahan sistem.', false);
            }
        }
        // Initialize Hash Routing & Greetings
        window.addEventListener('DOMContentLoaded', () => {
            updateGreeting();
            // Set initial screen based on hash
            if (window.location.hash) {
                const initialScreenId = window.location.hash.substring(1); // remove '#'
                if (document.getElementById(initialScreenId)) {
                    switchScreen(initialScreenId, false);
                }
            }
        });
        // Handle browser back/forward buttons
        window.addEventListener('hashchange', () => {
            if (window.location.hash) {
                const screenId = window.location.hash.substring(1);
                if (document.getElementById(screenId)) {
                    switchScreen(screenId, false);
                }
            } else {
                switchScreen('screen-home', false);
            }
        });
    </script>
    <script>
        if ("serviceWorker" in navigator) {
            window.addEventListener("load", () => {
                navigator.serviceWorker.register("/sw.js")
                    .then(reg => console.log("Service Worker registered.", reg))
                    .catch(err => console.log("Service Worker registration failed:", err));
            });
        }
    </script>
    <!-- MIDTRANS SNAP JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= config('Midtrans')->clientKey ?? '' ?>"></script>
    <script>
        async function bayarAngsuran(idRef, nominal, keterangan) {
            try {
                // 1. Get Snap Token from Backend
                const formData = new FormData();
                formData.append('jenis', 'angsuran');
                formData.append('nominal', nominal);
                formData.append('id_ref', idRef);
                formData.append('keterangan', keterangan);
                const response = await fetch('<?= base_url("api/midtrans-token") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content') }
                });
                const result = await response.json();
                if (result.status === 'success' && result.token) {
                    // 2. Open Snap Popup
                    snap.pay(result.token, {
                        onSuccess: function(result){
                            showAlert('Berhasil!', 'Pembayaran berhasil. Terima kasih!', true, () => window.location.reload());
                        },
                        onPending: function(result){
                            showAlert('Pending!', 'Menunggu pembayaran diselesaikan.', true);
                        },
                        onError: function(result){
                            showAlert('Gagal!', 'Pembayaran gagal. Silakan coba lagi.', false);
                        },
                        onClose: function(){
                            // do nothing
                        }
                    });
                } else {
                    showAlert('Error!', result.message || 'Gagal generate token pembayaran', false);
                }
            } catch(e) {
                showAlert('Error!', 'Terjadi kesalahan sistem.', false);
                console.error(e);
            }
        }
    </script>
</body>
</html>