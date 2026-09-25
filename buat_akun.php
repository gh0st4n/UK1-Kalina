<?php
session_start();
require_once 'classes/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // 1. Ambil semua siswa yang belum memiliki akun di tabel users
    $query = "SELECT s.id, s.nisn, s.nama 
              FROM siswa s 
              LEFT JOIN users u ON s.id = u.siswa_id 
              WHERE u.id IS NULL";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach ($siswa_list as $siswa) {
        if (!empty($siswa['nisn'])) {
            $username = trim($siswa['nisn']);
            
            // Generate Hash Password resmi via PHP (Password = NISN)
            $password_hash = password_hash($username, PASSWORD_BCRYPT);

            $insert = "INSERT INTO users (username, password, role, nama_lengkap, siswa_id) 
                       VALUES (:username, :password, 'siswa', :nama_lengkap, :siswa_id)";
                       
            $stmt_insert = $db->prepare($insert);
            $stmt_insert->bindParam(':username', $username);
            $stmt_insert->bindParam(':password', $password_hash);
            $stmt_insert->bindParam(':nama_lengkap', $siswa['nama']);
            $stmt_insert->bindParam(':siswa_id', $siswa['id']);
            
            if ($stmt_insert->execute()) {
                $count++;
                echo "✅ Berhasil membuat akun: <b>" . htmlspecialchars($siswa['nama']) . "</b> (Username: " . htmlspecialchars($username) . ")<br>";
            }
        }
    }

    echo "<hr><h3>Selesai! Total $count akun siswa berhasil dibuat.</h3>";
    echo "<a href='login.php'>Klik di sini untuk Kembali ke Halaman Login</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>