<?php
require_once 'config/security.php';
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Portal Yudisium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border-t-8 border-emerald-600">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl mb-4">
                <i class="fas fa-university text-3xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Portal Wisuda</h2>
            <p class="text-sm text-slate-500 font-medium">Silakan masuk ke akun Anda</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= h($_SESSION['error']); unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= get_token(); ?>">
            
            <div>
                <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2 ml-1">Username / NPM</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" placeholder="Masukkan username..." required>
                </div>
            </div>

            <div>
                <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2 ml-1">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1 active:scale-95">
                MASUK SEKARANG
            </button>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500 font-medium">
                Belum terdaftar? 
                <a href="register.php" class="text-emerald-600 font-bold hover:underline">Buat Akun Mahasiswa</a>
            </p>
        </div>
    </div>
</body>
</html>