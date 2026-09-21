<?php
session_start();
require_once 'classes/database.php';
require_once 'classes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');
if ($user_role !== 'admin') {
    header("Location: guru_list.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        die("Token CSRF tidak valid.");
    }

    $id = $_POST['id'] ?? '';
    if (!empty($id)) {
        $query = "DELETE FROM guru WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header("Location: guru_list.php?msg=deleted");
exit();