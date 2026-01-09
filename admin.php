<?php
// 1. Paksa PHP nampilin error di layar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>=== Debugging Tambah Admin ===</h3>";

// Data dari gambar panel lo
$host = "sql107.infinityfree.com"; 
$user = "if0_40643505"; 
$pass = "Z5Hm4zxgdB8SWL"; 
$db   = "if0_40643505_tugas_test"; 

try {
    echo "Langkah 1: Mencoba koneksi database... ";
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("<b style='color:red'>GAGAL!</b> Error: " . $conn->connect_error);
    }
    echo "<b style='color:green'>OK</b><br>";

    // Data admin baru
    $username_baru = "admin11";
    $password_plain = "admin123";
    
    echo "Langkah 2: Proses hashing password... ";
    $password_hashed = password_hash($password_plain, PASSWORD_BCRYPT);
    echo "<b style='color:green'>OK</b><br>";

    echo "Langkah 3: Cek apakah tabel 'users' ada... ";
    $check_table = $conn->query("SHOW TABLES LIKE 'users'");
    if ($check_table->num_rows == 0) {
        die("<b style='color:red'>GAGAL!</b> Tabel 'users' belum ada di database. Silakan import SQL dulu.");
    }
    echo "<b style='color:green'>OK</b><br>";

    echo "Langkah 4: Memasukkan data ke database... ";
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
    
    if (!$stmt) {
        die("<b style='color:red'>GAGAL!</b> Error Prepare: " . $conn->error);
    }

    $stmt->bind_param("ss", $username_baru, $password_hashed);
    
    if ($stmt->execute()) {
        echo "<b style='color:green'>BERHASIL!</b><br><br>";
        echo "<b>Username:</b> $username_baru <br>";
        echo "<b>Password:</b> $password_plain <br>";
        echo "<p style='color:blue'>Sekarang coba login di index.php</p>";
    } else {
        echo "<b style='color:red'>GAGAL!</b> Mungkin username '$username_baru' sudah ada? Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo "<br><b style='color:red'>ERROR TERDETEKSI:</b> " . $e->getMessage();
}
?>