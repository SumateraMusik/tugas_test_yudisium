<?php
// 1. AKTIFKAN DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. OB_START BIAR REDIRECT LANCAR
ob_start();

require_once 'config/security.php';
require_once 'config/db.php';

// Ambil data prodi buat dropdown
$prodis = $conn->query("SELECT * FROM prodi");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifikasi CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_token($token)) {
        die("CSRF Token Invalid!");
    }

    $user = $conn->real_escape_string(trim($_POST['username']));
    
    // Gunakan password_hash bawaan PHP (BCrypt)
    $pass = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); 
    
    $prodi = $_POST['id_prodi'];

    // Cek apakah username sudah ada biar gak duplikat
    $cek = $conn->query("SELECT id_user FROM users WHERE username = '$user'");
    if ($cek->num_rows > 0) {
        $error = "Username/NPM tersebut sudah terdaftar!";
    } else {
        $sql = "INSERT INTO users (username, password, role, id_prodi) VALUES ('$user', '$pass', 'mahasiswa', '$prodi')";
        
        if ($conn->query($sql)) {
            header("Location: index.php?msg=Registrasi Berhasil, Silahkan Login");
            exit;
        } else {
            $error = "Gagal daftar database: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa - Portal Wisuda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-md border-b-8 border-emerald-600">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl mb-4 shadow-inner">
                <i class="fas fa-user-plus text-3xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">Daftar Akun</h2>
            <p class="text-sm text-slate-400 font-medium mt-1">Lengkapi data untuk mendaftar wisuda</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="font-bold"><?= h($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= get_token(); ?>">
            
            <div class="group">
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-1 tracking-widest group-focus-within:text-emerald-600 transition-colors">Nomor Pokok Mahasiswa (NPM)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input type="text" name="username" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition-all font-medium text-slate-700" placeholder="Contoh: 202101001" required>
                </div>
            </div>

            <div class="group">
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-1 tracking-widest group-focus-within:text-emerald-600 transition-colors">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300">
                        <i class="fas fa-key"></i>
                    </span>
                    <input type="password" name="password" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition-all font-medium text-slate-700" placeholder="••••••••" required>
                </div>
            </div>

            <div class="group">
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-1 tracking-widest group-focus-within:text-emerald-600 transition-colors">Program Studi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300">
                        <i class="fas fa-graduation-cap"></i>
                    </span>
                    <select name="id_prodi" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition-all font-medium text-slate-700 appearance-none" required>
                        <option value="">-- Pilih Prodi Anda --</option>
                        <?php while($p = $prodis->fetch_assoc()): ?>
                            <option value="<?= $p['id_prodi'] ?>"><?= h($p['nama_prodi']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-300 pointer-events-none">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 text-white font-black py-4 rounded-2xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2 uppercase tracking-tighter">
                <i class="fas fa-paper-plane"></i>
                Daftar Akun Sekarang
            </button>
        </form>
        
        <div class="mt-10 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-400 font-medium">
                Sudah terdaftar? 
                <a href="index.php" class="text-emerald-600 font-black hover:underline ml-1">Masuk ke Portal</a>
            </p>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>