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
$username  = $_SESSION['username'] ?? 'Pengguna';

// 1. Hitung Total Siswa
$query_siswa = "SELECT COUNT(*) as total FROM siswa";
$stmt_siswa = $db->prepare($query_siswa);
$stmt_siswa->execute();
$total_siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 2. Hitung Total Kelas
$query_kelas = "SELECT COUNT(*) as total FROM kelas";
$stmt_kelas = $db->prepare($query_kelas);
$stmt_kelas->execute();
$total_kelas = $stmt_kelas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 3. Hitung Total Guru
$query_guru = "SELECT COUNT(*) as total FROM guru";
$stmt_guru = $db->prepare($query_guru);
$stmt_guru->execute();
$total_guru = $stmt_guru->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 4. Hitung Siswa Hadir Hari Ini (Disempurnakan)
$query_absen = "SELECT COUNT(*) as total 
                FROM absensi 
                WHERE DATE(tanggal) = CURDATE() 
                AND (LOWER(status) = 'hadir' OR LOWER(keterangan) = 'hadir')";
$stmt_absen = $db->prepare($query_absen);
$stmt_absen->execute();
$total_hadir = $stmt_absen->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 5. Ambil 5 Siswa Terbaru
$query_recent = "SELECT siswa.*, kelas.nama_kelas 
                 FROM siswa 
                 LEFT JOIN kelas ON siswa.kelas_id = kelas.id 
                 ORDER BY siswa.id DESC LIMIT 5";
$stmt_recent = $db->prepare($query_recent);
$stmt_recent->execute();
$recent_siswa = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

include 'views/header.php';
?>

<div class="container my-4">
    <!-- Welcome Banner -->
    <div class="p-4 mb-4 bg-primary text-white rounded-3 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-1">
                    <i class="fa-solid fa-hand-wave me-2"></i>Selamat Datang, <?= htmlspecialchars(ucfirst($username)); ?>!
                </h3>
                <p class="mb-0 opacity-75">
                    Anda masuk sebagai <span class="badge bg-white text-primary text-uppercase fw-bold"><?= htmlspecialchars($user_role); ?></span>. 
                    <?php if ($user_role === 'admin'): ?>
                        Kelola data siswa, kelas, guru, dan absensi secara efisien di sini.
                    <?php else: ?>
                        Lihat informasi data siswa, kelas, dan pengajar terdaftar di sini.
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <i class="fa-solid fa-graduation-cap fa-5x opacity-25"></i>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Siswa -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-primary">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="fa-solid fa-user-graduate fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">TOTAL SISWA</span>
                        <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_siswa); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Kelas -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-success">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                        <i class="fa-solid fa-school fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">TOTAL KELAS</span>
                        <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_kelas); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Guru -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-warning">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3">
                        <i class="fa-solid fa-chalkboard-user fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">TOTAL GURU</span>
                        <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_guru); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Hadir Hari Ini -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-info">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-info-subtle text-info p-3 me-3">
                        <i class="fa-solid fa-clipboard-user fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">HADIR HARI INI</span>
                        <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_hadir); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Siswa Terbaru Diinput
                    </h6>
                    <a href="siswa_list.php" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold">
                        Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="py-3 px-4" width="200">NISN</th>
                                    <th class="py-3">Nama Siswa</th>
                                    <th class="py-3 px-4 text-end" width="200">Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($recent_siswa) > 0): ?>
                                    <?php foreach ($recent_siswa as $siswa): ?>
                                        <tr>
                                            <td class="px-4 fw-semibold text-muted"><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($siswa['nama'] ?? '-'); ?></div>
                                            </td>
                                            <td class="px-4 text-end">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
                                                    <?= htmlspecialchars($siswa['nama_kelas'] ?? 'Belum Ada Kelas'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">Belum ada data siswa.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>