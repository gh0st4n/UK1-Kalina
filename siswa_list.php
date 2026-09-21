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

// Inisialisasi CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Samakan logika penentuan role dengan header.php
$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');

include 'views/header.php';

// Logika Pencarian & Query Data Siswa + Join Kelas
$search = $_GET['search'] ?? '';
$query = "SELECT siswa.*, kelas.nama_kelas, kelas.wali_kelas 
          FROM siswa 
          LEFT JOIN kelas ON siswa.kelas_id = kelas.id ";

if (!empty($search)) {
    $query .= "WHERE siswa.nisn LIKE :search OR siswa.nama LIKE :search OR kelas.nama_kelas LIKE :search ";
}

$query .= "ORDER BY siswa.nama ASC";

$stmt = $db->prepare($query);

if (!empty($search)) {
    $searchTerm = "%{$search}%";
    $stmt->bindParam(':search', $searchTerm);
}

$stmt->execute();
$data_siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = $_GET['msg'] ?? '';
?>

<div class="container-fluid px-4 mt-4 mb-5">
    <!-- Notifikasi Alert -->
    <?php if ($msg === 'added'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>Data siswa berhasil ditambahkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>Data siswa berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>Data siswa berhasil dihapus!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center p-3 mb-4 bg-white rounded-3 shadow-sm border-start border-4 border-primary">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-users text-primary me-2"></i>Daftar Data Siswa</h4>
            <p class="text-muted mb-0 small">Kelola informasi seluruh data siswa terdaftar</p>
        </div>
        <?php if ($user_role === 'admin'): ?>
            <a href="siswa_tambah.php" class="btn btn-primary px-3 shadow-sm"><i class="fa-solid fa-user-plus me-1"></i> Tambah Siswa</a>
        <?php endif; ?>
    </div>

    <!-- Search Bar -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="siswa_list.php" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari berdasarkan NISN, Nama, atau Kelas..." value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Table Container -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th width="50" class="text-center py-3">No</th>
                            <th width="70" class="text-center py-3">Foto</th>
                            <th class="py-3">NISN</th>
                            <th class="py-3">Nama Siswa</th>
                            <th class="py-3">Kelas</th>
                            <th class="py-3">Wali Kelas</th>
                            <th class="py-3">Alamat</th>
                            
                            <!-- Terakhir Diperbarui khusus Admin -->
                            <?php if ($user_role === 'admin'): ?>
                                <th class="py-3">Terakhir Diperbarui</th>
                            <?php endif; ?>

                            <th width="240" class="text-center py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_siswa) > 0): ?>
                            <?php $no = 1; foreach ($data_siswa as $row): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $foto = !empty($row['foto']) && file_exists('uploads/' . $row['foto']) ? 'uploads/' . $row['foto'] : 'assets/img/default-avatar.png'; 
                                        ?>
                                        <img src="<?= $foto; ?>" alt="Foto" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['nisn']); ?></span></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary small"><?= htmlspecialchars($row['wali_kelas'] ?? '-'); ?></td>
                                    <td class="text-secondary small"><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>

                                    <!-- Terakhir Diperbarui khusus Admin -->
                                    <?php if ($user_role === 'admin'): ?>
                                        <td class="text-secondary small">
                                            <?php 
                                                $waktu = $row['updated_at'] ?? $row['created_at'] ?? null;
                                                if (!empty($waktu)) {
                                                    echo '<i class="fa-regular fa-clock me-1 text-muted"></i>' . date('d M Y, H:i', strtotime($waktu));
                                                } else {
                                                    echo '<span class="text-muted">-</span>';
                                                }
                                            ?>
                                        </td>
                                    <?php endif; ?>

                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1 align-items-center justify-content-center">
                                            <a href="siswa_detail.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm text-white shadow-sm">
                                                <i class="fa-solid fa-eye"></i> Detail
                                            </a>
                                            
                                            <?php if ($user_role === 'admin'): ?>
                                                <a href="siswa_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm text-white shadow-sm">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </a>
                                                
                                                <form action="siswa_hapus.php" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                                                        <i class="fa-solid fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <!-- Colspan 9 untuk admin, 8 untuk user/siswa -->
                                <td colspan="<?= ($user_role === 'admin') ? '9' : '8'; ?>" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-user-slash fa-2x mb-2 d-block"></i>
                                    Data siswa tidak ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Navigation Back -->
    <div class="mt-4">
        <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-2"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
    </div>
</div>

<?php include 'views/footer.php'; ?>