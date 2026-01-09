<?php
ob_start();
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

// --- AMBIL DATA REAL DARI DATABASE ---
$res_mhs = $conn->query("SELECT COUNT(*) as total FROM yudisium");
$total_mhs = $res_mhs->fetch_assoc()['total'] ?? 0;

$res_valid = $conn->query("SELECT COUNT(*) as total FROM yudisium WHERE status_validasi = 'valid'");
$total_valid = $res_valid->fetch_assoc()['total'] ?? 0;

$res_nina = $conn->query("SELECT COUNT(*) as total FROM yudisium WHERE nina IS NULL OR nina = ''");
$total_nina = $res_nina->fetch_assoc()['total'] ?? 0;

$query_recent = "SELECT y.nama_mahasiswa, p.nama_prodi, y.status_validasi 
                 FROM yudisium y 
                 LEFT JOIN prodi p ON y.id_prodi = p.id_prodi 
                 ORDER BY y.id_yudisium DESC LIMIT 5";
$recent_mhs = $conn->query($query_recent);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Kendali - Portal Wisuda</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">

    <aside class="w-64 bg-white min-h-screen border-r border-slate-200 hidden md:block shadow-sm flex-shrink-0">
        <div class="p-8 border-b border-slate-100 text-center">
            <h1 class="text-xl font-black flex items-center justify-center gap-2 text-emerald-700 tracking-tighter">
                <i class="fas fa-university text-2xl"></i> PORTAL WISUDA
            </h1>
        </div>
        <nav class="p-4 space-y-1">
            <p class="text-[10px] text-slate-400 uppercase font-black px-4 mb-2 tracking-widest">Main Navigation</p>
            
            <a href="dashboard.php" class="flex items-center gap-3 p-3 bg-emerald-50 text-emerald-700 rounded-xl font-bold transition shadow-sm border border-emerald-100">
                <i class="fas fa-th-large w-5"></i> Ringkasan
            </a>
            
            <?php if ($role === 'admin'): ?>
            <a href="admin_users.php" class="flex items-center gap-3 p-3 text-slate-500 hover:bg-slate-50 hover:text-emerald-600 rounded-xl transition font-medium">
                <i class="fas fa-users-cog w-5"></i> Kelola Staf
            </a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $role === 'staf_prodi'): ?>
            <a href="prodi_yudisium.php" class="flex items-center gap-3 p-3 text-slate-500 hover:bg-slate-50 hover:text-emerald-600 rounded-xl transition font-medium">
                <i class="fas fa-user-graduate w-5"></i> Validasi Data
            </a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $role === 'staf_baa'): ?>
            <a href="baa_nina.php" class="flex items-center gap-3 p-3 text-slate-500 hover:bg-slate-50 hover:text-emerald-600 rounded-xl transition font-medium">
                <i class="fas fa-stamp w-5"></i> Input NINA
            </a>
            <?php endif; ?>

            <div class="pt-6 border-t border-slate-100 mt-6">
                <a href="cetak_laporan.php" target="_blank" class="flex items-center gap-3 p-3 text-red-500 hover:bg-red-50 hover:rounded-xl transition font-bold">
                    <i class="fas fa-print w-5"></i> Unduh Laporan
                </a>
                <a href="logout.php" class="flex items-center gap-3 p-3 text-slate-400 hover:text-slate-700 transition mt-2 font-medium">
                    <i class="fas fa-power-off w-5"></i> Keluar Sistem
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-1">
        <header class="bg-white p-6 flex justify-between items-center px-10 border-b border-slate-100">
            <div class="text-slate-400 font-semibold text-xs uppercase tracking-widest">
                Update Terakhir: <?= date('d M Y') ?>
            </div>
            <div class="flex items-center gap-5">
                <div class="text-right">
                    <p class="text-sm font-black text-slate-800 tracking-tight"><?= h($username) ?></p>
                    <p class="text-[9px] bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full font-black uppercase inline-block border border-emerald-200"><?= strtoupper($role) ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white font-black shadow-lg shadow-emerald-200">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="p-10">
            <div class="bg-gradient-to-br from-emerald-600 to-teal-800 p-10 rounded-3xl text-white shadow-2xl shadow-emerald-200 mb-10 relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-4xl font-black tracking-tighter">Selamat Datang, <?= h($username) ?>!</h2>
                    <p class="mt-2 text-emerald-50 opacity-90 font-medium">Sistem pemrosesan data kelulusan terintegrasi. Pantau status validasi mahasiswa di sini.</p>
                </div>
                <i class="fas fa-university absolute -right-6 -bottom-6 text-[12rem] text-white/10 rotate-12"></i>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Pendaftar</p>
                            <p class="text-4xl font-black mt-2 text-slate-800"><?= number_format($total_mhs) ?></p>
                        </div>
                        <div class="p-4 bg-blue-50 text-blue-500 rounded-2xl"><i class="fas fa-user-friends text-2xl"></i></div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Tervalidasi</p>
                            <p class="text-4xl font-black mt-2 text-emerald-600"><?= number_format($total_valid) ?></p>
                        </div>
                        <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl"><i class="fas fa-check-circle text-2xl"></i></div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Tunda NINA</p>
                            <p class="text-4xl font-black mt-2 text-orange-500"><?= number_format($total_nina) ?></p>
                        </div>
                        <div class="p-4 bg-orange-50 text-orange-500 rounded-2xl"><i class="fas fa-clock text-2xl"></i></div>
                    </div>
                </div>
            </div>

            <div class="mt-10 bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-black text-slate-800 flex items-center gap-3 text-lg">
                        <i class="fas fa-history text-emerald-600"></i> Aktivitas Terbaru
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white text-slate-400 text-[10px] uppercase font-black tracking-widest">
                            <tr>
                                <th class="p-6">Mahasiswa</th>
                                <th class="p-6">Program Studi</th>
                                <th class="p-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-600 divide-y divide-slate-50">
                            <?php if ($recent_mhs->num_rows > 0): ?>
                                <?php while($row = $recent_mhs->fetch_assoc()): ?>
                                <tr class="hover:bg-emerald-50/30 transition-colors">
                                    <td class="p-6 font-bold text-slate-700"><?= h($row['nama_mahasiswa']) ?></td>
                                    <td class="p-6 font-medium text-slate-400"><?= h($row['nama_prodi'] ?? 'N/A') ?></td>
                                    <td class="p-6 text-center">
                                        <?php if($row['status_validasi'] === 'valid'): ?>
                                            <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-[9px] font-black tracking-tighter uppercase">VERIFIED</span>
                                        <?php else: ?>
                                            <span class="px-4 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-[9px] font-black tracking-tighter uppercase">ON PROCESS</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="p-20 text-center italic text-slate-300">
                                        <i class="fas fa-box-open block text-4xl mb-4 opacity-10"></i>
                                        Tidak ada data pendaftaran terbaru.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js');
            });
        }
    </script>
</body>
</html>