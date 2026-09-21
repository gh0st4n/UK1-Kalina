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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = "Token CSRF tidak valid.";
    } else {
        $nama_guru    = trim($_POST['nama_guru'] ?? '');
        $mata_pelajaran = trim($_POST['mata_pelajaran'] ?? '');

        if (empty($nama_guru)) {
            $error = "Nama guru wajib diisi.";
        } else {
           $query = "INSERT INTO guru (nama_guru, mapel) VALUES (:nama_guru, :mapel)";
$stmt = $db->prepare($query);
$stmt->bindParam(':nama_guru', $nama_guru);
$stmt->bindParam(':mapel', $mata_pelajaran);

            if ($stmt->execute()) {
                header("Location: guru_list.php?msg=added");
                exit();
            } else {
                $error = "Gagal menambahkan data guru.";
            }
        }
    }
}

include 'views/header.php';
?>

<div class="container my-5 d-flex justify-content-center">
    <div class="card shadow-sm border-0 rounded-3 style-card" style="width: 100%; max-width: 600px;">
        <!-- Header Card Biru Full Width -->
        <div class="card-header bg-primary text-white py-3 border-0 rounded-top-3">
            <h4 class="fw-bold mb-0">Tambah Data Guru</h4>
        </div>
        
        <div class="card-body p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="guru_tambah.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label for="nama_guru" class="form-label fw-semibold text-dark">Nama Guru</label>
                    <input type="text" class="form-control py-2" id="nama_guru" name="nama_guru" placeholder="Masukkan nama guru" required>
                </div>

                <div class="mb-4">
                    <label for="mata_pelajaran" class="form-label fw-semibold text-dark">Mata Pelajaran</label>
                    <input type="text" class="form-control py-2" id="mata_pelajaran" name="mata_pelajaran" placeholder="Masukkan mata pelajaran">
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <a href="guru_list.php" class="btn btn-secondary px-4 py-2 fw-semibold">Kembali</a>
                    <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>