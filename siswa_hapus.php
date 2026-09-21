<?php
session_start();
require_once 'classes/database.php';
require_once 'classes/auth.php';
require_once 'classes/siswa.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$siswaObj = new Siswa($db);

$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? $auth->getRole() ?? '');

// Proteksi: Hanya Admin yang sudah login
if (!$auth->isLoggedIn() || $user_role !== 'admin') {
    header("Location: siswa_list.php");
    exit();
}

// Validasi Metode POST dan Token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("Akses ditolak! Token CSRF tidak valid.");
    }

    $id = $_POST['id'] ?? null;
    if ($id) {
        $siswaObj->delete($id);
    }
}

header("Location: siswa_list.php?msg=deleted");
exit();
?>