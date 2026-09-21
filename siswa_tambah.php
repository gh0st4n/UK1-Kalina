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
    header("Location: siswa_list.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ambil daftar kelas untuk dropdown
$query_kelas = "SELECT * FROM kelas ORDER BY nama_kelas ASC";
$stmt_kelas = $db->prepare($query_kelas);
$stmt_kelas->execute();
$list_kelas = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = "Token CSRF tidak valid.";
    } else {
        $nisn      = trim($_POST['nisn'] ?? '');
        $nama      = trim($_POST['nama'] ?? '');
        $kelas_id  = trim($_POST['kelas_id'] ?? '');
        $alamat    = trim($_POST['alamat'] ?? '');
        $foto_nama = NULL;

        if (empty($nisn) || empty($nama) || empty($kelas_id)) {
            $error = "NISN, Nama Lengkap, dan Kelas wajib diisi.";
        } else {
            // Cek apakah NISN sudah terdaftar di database
            $check_query = "SELECT id FROM siswa WHERE nisn = :nisn";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':nisn', $nisn);
            $check_stmt->execute();

            if ($check_stmt->rowCount() > 0) {
                $error = "NISN sudah terdaftar! Gunakan NISN lain.";
            } else {
                // Proses Upload Foto (jika ada)
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath   = $_FILES['foto']['tmp_name'];
                    $fileName      = $_FILES['foto']['name'];
                    $fileSize      = $_FILES['foto']['size'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

                    if (in_array($fileExtension, $allowedExtensions)) {
                        if ($fileSize <= 2 * 1024 * 1024) { // Maksimal 2MB
                            $newFileName   = time() . '_' . uniqid() . '.' . $fileExtension;
                            $uploadFileDir = 'uploads/';

                            if (!is_dir($uploadFileDir)) {
                                mkdir($uploadFileDir, 0755, true);
                            }

                            $dest_path = $uploadFileDir . $newFileName;
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $foto_nama = $newFileName;
                            } else {
                                $error = "Gagal mengunggah foto siswa.";
                            }
                        } else {
                            $error = "Ukuran foto maksimal 2MB.";
                        }
                    } else {
                        $error = "Format foto hanya diperbolehkan JPG, JPEG, PNG, atau WEBP.";
                    }
                }

                // Simpan data jika tidak ada error upload
                if (empty($error)) {
                    $query = "INSERT INTO siswa (nisn, nama, kelas_id, alamat, foto) VALUES (:nisn, :nama, :kelas_id, :alamat, :foto)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':nisn', $nisn);
                    $stmt->bindParam(':nama', $nama);
                    $stmt->bindParam(':kelas_id', $kelas_id);
                    $stmt->bindParam(':alamat', $alamat);
                    $stmt->bindParam(':foto', $foto_nama);

                    if ($stmt->execute()) {
                        header("Location: siswa_list.php?msg=added");
                        exit();
                    } else {
                        $error = "Gagal menambahkan data siswa.";
                    }
                }
            }
        }
    }
}

include 'views/header.php';
?>

<div class="container my-5 d-flex justify-content-center">
    <div class="card shadow-sm border-0 rounded-3 style-card" style="width: 100%; max-width: 650px;">
        <!-- Header Card Biru Full Width -->
        <div class="card-header bg-primary text-white py-3 border-0 rounded-top-3">
            <h4 class="fw-bold mb-0">Tambah Data Siswa</h4>
        </div>
        
        <div class="card-body p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="siswa_tambah.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label for="nisn" class="form-label fw-semibold text-dark">NISN</label>
                    <input type="text" class="form-control py-2" id="nisn" name="nisn" value="<?= htmlspecialchars($_POST['nisn'] ?? ''); ?>" placeholder="Masukkan NISN siswa" required>
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold text-dark">Nama Lengkap</label>
                    <input type="text" class="form-control py-2" id="nama" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" placeholder="Masukkan nama siswa" required>
                </div>

                <div class="mb-3">
                    <label for="kelas_id" class="form-label fw-semibold text-dark">Kelas</label>
                    <select class="form-select py-2" id="kelas_id" name="kelas_id" required>
                        <option value="" disabled <?= empty($_POST['kelas_id']) ? 'selected' : ''; ?>>-- Pilih Kelas --</option>
                        <?php foreach ($list_kelas as $kelas): ?>
                            <option value="<?= $kelas['id']; ?>" <?= (($_POST['kelas_id'] ?? '') == $kelas['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kelas['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label fw-semibold text-dark">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat siswa"><?= htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="foto" class="form-label fw-semibold text-dark">Foto Siswa</label>
                    <input class="form-control" type="file" id="foto" name="foto" accept=".jpg, .jpeg, .png, .webp">
                    <div class="form-text">Format: JPG, JPEG, PNG, WEBP (Maksimal 2MB)</div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <a href="siswa_list.php" class="btn btn-secondary px-4 py-2 fw-semibold">Kembali</a>
                    <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>