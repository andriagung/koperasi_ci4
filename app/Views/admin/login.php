<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Koperasi Assyifa RSUD 45</title>
    <!-- Tailwind CSS (CDN for quick styling since admin uses it based on other views) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border-t-4 border-emerald-600">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Panel Admin</h1>
            <p class="text-slate-500 text-sm mt-1">Kopkar Assyifa RSUD 45 Kuningan</p>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 border border-red-200">
                <i class="fas fa-exclamation-circle mr-2"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-lg text-sm mb-4 border border-emerald-200">
                <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="/admin/login" method="POST">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="block text-slate-700 text-sm font-bold mb-2">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-slate-400"></i>
                    </div>
                    <input type="text" name="username" class="w-full pl-10 pr-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Masukkan Username Admin" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-slate-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-400"></i>
                    </div>
                    <input type="password" name="password" class="w-full pl-10 pr-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Masukkan Password" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk Dashboard
            </button>
        </form>
        
        <div class="mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200 text-sm text-blue-800">
            <p class="font-bold mb-2"><i class="fas fa-info-circle mr-1"></i> Akun Testing UAT (Password: 123456):</p>
            <div class="grid grid-cols-2 gap-1 text-xs">
                <div>&bull; superadmin <span class="text-slate-500">(Super Admin)</span></div>
                <div>&bull; admin_biasa <span class="text-slate-500">(Admin)</span></div>
                <div>&bull; teller <span class="text-slate-500">(Teller)</span></div>
                <div>&bull; kasir <span class="text-slate-500">(Kasir)</span></div>
                <div>&bull; keuangan <span class="text-slate-500">(Keuangan)</span></div>
                <div>&bull; kredit <span class="text-slate-500">(Kredit)</span></div>
                <div>&bull; akuntansi <span class="text-slate-500">(Akuntansi)</span></div>
                <div>&bull; gudang <span class="text-slate-500">(Gudang)</span></div>
                <div>&bull; pengurus <span class="text-slate-500">(Pengurus)</span></div>
                <div>&bull; manajer <span class="text-slate-500">(Manajer)</span></div>
                <div>&bull; bendahara <span class="text-slate-500">(Bendahara)</span></div>
            </div>
        </div>
        
        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> Koperasi Karyawan Assyifa RSUD 45 Kuningan
        </div>
    </div>

</body>
</html>
