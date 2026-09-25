<?php
session_start();
require_once 'classes/database.php';
require_once 'classes/auth.php';

$database = new Database();
$db = $database->getConnection();

// Cek permission admin
if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['nisn'])) {
    $nisn = trim($_GET['nisn']);
    $nama = trim($_GET['nama']);
    
    // Gunakan NISN sebagai username dan password default
    $username = $nisn;
    $password_default = password_hash($nisn, PASSWORD_BCRYPT);
    $role = 'user'; // Role untuk siswa

    // 1. Cek apakah akun dengan NISN ini sudah pernah dibuat
    $check_stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
    $check_stmt->execute([':username' => $username]);

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = "Akun untuk NISN " . $nisn . " sudah ada!";
    } else {
        // 2. Insert ke tabel users
        $insert_stmt = $db->prepare("INSERT INTO users (username, password, nama, role) VALUES (:username, :password, :nama, :role)");
        $saved = $insert_stmt->execute([
            ':username' => $username,
            ':password' => $password_default,
            ':nama'     => $nama,
            ':role'     => $role
        ]);

        if ($saved) {
            $_SESSION['success'] = "Akun login siswa (" . $nama . ") berhasil dibuat! Username & Password: " . $nisn;
        } else {
            $_SESSION['error'] = "Gagal membuat akun siswa.";
        }
    }
}

header("Location: siswa_list.php");
exit();