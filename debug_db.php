<?php
// Aktifkan semua laporan error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>=== Debugging Koneksi Database ===</h2>";

// Data dari gambar yang lo kasih
$host = "sql107.infinityfree.com"; 
$user = "if0_40643505"; 
$pass = "Z5Hm4zxgdB8SWL"; 
$db   = "if0_40643505_tugas_test"; 

echo "<b>Mencoba koneksi ke:</b> $host <br>";
echo "<b>User:</b> $user <br>";
echo "<b>Database:</b> $db <br><br>";

try {
    // Cek apakah ekstensi mysqli terinstall
    if (!extension_loaded('mysqli')) {
        die("Error: Ekstensi MySQLi tidak terpasang di server ini!");
    }

    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("<span style='color:red'>Koneksi Gagal: </span>" . $conn->connect_error);
    }

    echo "<span style='color:green; font-weight:bold;'>KONEKSI BERHASIL!</span><br>";
    echo "Server Info: " . $conn->server_info . "<br>";
    
    // Cek apakah tabel users ada
    $check_table = $conn->query("SHOW TABLES LIKE 'users'");
    if ($check_table->num_rows > 0) {
        echo "Tabel 'users' ditemukan. Sistem siap digunakan!";
    } else {
        echo "<span style='color:orange'>Peringatan:</span> Koneksi ok, tapi tabel belum di-import (kosong).";
    }

} catch (Exception $e) {
    echo "<span style='color:red; font-weight:bold;'>ERROR TERDETEKSI:</span><br>";
    echo $e->getMessage();
}
?>