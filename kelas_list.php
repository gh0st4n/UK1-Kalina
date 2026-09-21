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

// Query Data Kelas
$query = "SELECT * FROM kelas ORDER BY nama_kelas ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$data_kelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = $_GET['msg'] ?? '';
?>

<div class="container-fluid px-4 mt-4 mb-5">
    <!-- Notifikasi Alert -->
    <?php if ($msg === 'added'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>Data kelas berhasil ditambahkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>Data kelas berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>Data kelas berhasil dihapus!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center p-3 mb-4 bg-white rounded-3 shadow-sm border-start border-4 border-primary">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-school text-primary me-2"></i>Data Kelas</h4>
            <p class="text-muted mb-0 small">Daftar kelas dan jurusan yang terdaftar dalam sistem.</p>
        </div>
        <?php if ($user_role === 'admin'): ?>
            <a href="kelas_tambah.php" class="btn btn-primary px-3 shadow-sm"><i class="fa-solid fa-plus me-1"></i> Tambah Kelas Baru</a>
        <?php endif; ?>
    </div>

    <!-- Card Table Container -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th width="50" class="text-center py-3">No</th>
                            <th class="py-3">Nama Kelas</th>
                            <th class="py-3">Wali Kelas / Keterangan</th>
                            
                            <!-- Terakhir Diperbarui khusus Admin -->
                            <?php if ($user_role === 'admin'): ?>
                                <th class="py-3">Terakhir Diperbarui</th>
                            <?php endif; ?>

                            <?php if ($user_role === 'admin'): ?>
                                <th width="200" class="text-center py-3">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_kelas) > 0): ?>
                            <?php $no = 1; foreach ($data_kelas as $row): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6 fw-normal">
                                            <i class="fa-solid fa-chalkboard me-1"></i> <?= htmlspecialchars($row['nama_kelas']); ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary"><?= htmlspecialchars($row['wali_kelas'] ?? '-'); ?></td>
                                    
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

                                    <?php if ($user_role === 'admin'): ?>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1 align-items-center justify-content-center">
                                                <a href="kelas_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm text-white shadow-sm">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </a>
                                                
                                                <form action="kelas_hapus.php" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kelas ini?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                                                        <i class="fa-solid fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <!-- Colspan 5 untuk Admin (No, Nama Kelas, Wali Kelas, Terakhir Diperbarui, Aksi), Colspan 3 untuk User (No, Nama Kelas, Wali Kelas) -->
                                <td colspan="<?= ($user_role === 'admin') ? '5' : '3'; ?>" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                                    Data kelas belum tersedia.
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