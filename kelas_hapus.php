<?php
session_start();
require_once 'classes/database.php';
require_once 'classes/auth.php';
require_once 'classes/kelas.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');

if (!$auth->isLoggedIn() || $user_role !== 'admin') {
    header("Location: kelas_list.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Akses ditolak! Token CSRF tidak valid.");
    }

    $id = $_POST['id'] ?? null;
    if ($id) {
        $kelasObj = new Kelas($db);
        $kelasObj->delete($id);
    }
}

header("Location: kelas_list.php");
exit();
?>