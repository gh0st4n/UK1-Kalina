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
    header("Location: kelas_list.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: kelas_list.php");
    exit();
}

// Ambil data kelas berdasarkan ID
$query = "SELECT * FROM kelas WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$kelas = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {
    header("Location: kelas_list.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = "Token CSRF tidak valid.";
    } else {
        $nama_kelas = trim($_POST['nama_kelas'] ?? '');
        $wali_kelas = trim($_POST['wali_kelas'] ?? '');

        if (empty($nama_kelas)) {
            $error = "Nama kelas wajib diisi.";
        } else {
            $update_query = "UPDATE kelas SET nama_kelas = :nama_kelas, wali_kelas = :wali_kelas WHERE id = :id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':nama_kelas', $nama_kelas);
            $update_stmt->bindParam(':wali_kelas', $wali_kelas);
            $update_stmt->bindParam(':id', $id);

            if ($update_stmt->execute()) {
                header("Location: kelas_list.php?msg=updated");
                exit();
            } else {
                $error = "Gagal memperbarui data kelas.";
            }
        }
    }
}

include 'views/header.php';
?>

<div class="container my-5 d-flex justify-content-center">
    <div class="card shadow-sm border-0 rounded-3 style-card" style="width: 100%; max-width: 600px;">
        <!-- Header Card Kuning Full Width (Samakan dengan Edit Guru) -->
        <div class="card-header bg-warning text-dark py-3 border-0 rounded-top-3">
            <h4 class="fw-bold mb-0">Edit Data Kelas</h4>
        </div>
        
        <div class="card-body p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="kelas_edit.php?id=<?= htmlspecialchars($id); ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label for="nama_kelas" class="form-label fw-semibold text-dark">Nama Kelas</label>
                    <input type="text" class="form-control py-2" id="nama_kelas" name="nama_kelas" value="<?= htmlspecialchars($kelas['nama_kelas']); ?>" placeholder="Masukkan nama kelas" required>
                </div>

                <div class="mb-4">
                    <label for="wali_kelas" class="form-label fw-semibold text-dark">Wali Kelas</label>
                    <input type="text" class="form-control py-2" id="wali_kelas" name="wali_kelas" value="<?= htmlspecialchars($kelas['wali_kelas'] ?? ''); ?>" placeholder="Masukkan nama wali kelas">
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <a href="kelas_list.php" class="btn btn-secondary px-4 py-2 fw-semibold">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>