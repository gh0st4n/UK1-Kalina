<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Cek apakah user sudah login
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Ambil ID siswa dari parameter URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: siswa_list.php");
    exit();
}

// Query untuk mengambil data siswa beserta nama kelas & wali kelas dari tabel kelas
$query = "SELECT siswa.*, kelas.nama_kelas, kelas.wali_kelas 
          FROM siswa 
          LEFT JOIN kelas ON siswa.kelas_id = kelas.id 
          WHERE siswa.id = :id LIMIT 1";

$stmt = $db->prepare($query);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data siswa tidak ditemukan, kembalikan ke list
if (!$siswa) {
    header("Location: siswa_list.php");
    exit();
}

// Gunakan require_once agar header (navbar) hanya dimuat SATU kali
require_once 'views/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Data Siswa</h5>
                    <a href="siswa_list.php" class="btn btn-light btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <img src="uploads/<?= htmlspecialchars($siswa['foto'] ?? 'default.png') ?>" 
                                 alt="Foto Siswa" 
                                 class="img-fluid rounded border shadow-sm" 
                                 style="max-height: 250px; object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/150';">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">NISN</th>
                                    <td>: <?= htmlspecialchars($siswa['nisn'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td>: <strong><?= htmlspecialchars($siswa['nama'] ?? '-') ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>: <?= htmlspecialchars($siswa['nama_kelas'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Wali Kelas</th>
                                    <td>: <?= htmlspecialchars($siswa['wali_kelas'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: <?= htmlspecialchars($siswa['alamat'] ?? '-') ?></td>
                                </tr>
                            </table>
                            <?php if ($auth->getRole() === 'admin'): ?>
                                <div class="mt-3">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Gunakan require_once juga untuk footer
require_once 'views/footer.php'; 
?>