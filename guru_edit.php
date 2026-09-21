<?php
session_start();
require_once 'classes/database.php';
require_once 'classes/auth.php';
require_once 'classes/guru.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Cek apakah user sudah login
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Cek hak akses admin
$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');
if ($user_role !== 'admin') {
    header("Location: guru_list.php");
    exit();
}

$guruObj = new Guru($db);
$error = '';

// Ambil ID guru dari URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: guru_list.php");
    exit();
}

// Ambil data guru berdasarkan ID
$guru = $guruObj->getGuruById($id);

if (!$guru) {
    header("Location: guru_list.php");
    exit();
}

// Proses Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_guru = trim($_POST['nama_guru']);
    $mapel     = trim($_POST['mapel']);

    if (!empty($nama_guru) && !empty($mapel)) {
        if ($guruObj->updateGuru($id, $nama_guru, $mapel)) {
            header("Location: guru_list.php?status=updated");
            exit();
        } else {
            $error = "Gagal memperbarui data guru.";
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}

// Panggil Header untuk menampilkan Navbar atas
include 'views/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <!-- Header Card warna kuning -->
                <div class="card-header bg-warning py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">Edit Data Guru</h5>
                </div>
                
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mb-3" role="alert">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="guru_edit.php?id=<?= $id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="nama_guru" class="form-label fw-semibold text-secondary">Nama Guru</label>
                            <input type="text" class="form-control" id="nama_guru" name="nama_guru" value="<?= htmlspecialchars($guru['nama_guru'] ?? $guru['nama'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="mapel" class="form-label fw-semibold text-secondary">Mata Pelajaran</label>
                            <input type="text" class="form-control" id="mapel" name="mapel" value="<?= htmlspecialchars($guru['mapel'] ?? $guru['mata_pelajaran'] ?? ''); ?>" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="guru_list.php" class="btn btn-secondary px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Panggil Footer
include 'views/footer.php'; 
?>