<?php
// Jalankan session agar data login pengguna terbaca di seluruh halaman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Deteksi nama file halaman yang sedang diakses saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil role pengguna saat ini untuk pengecekan hak akses
$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Manajemen Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Navbar Tema Biru (bg-primary) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fa-solid fa-school me-2"></i>Data Siswa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'index.php') ? 'active fw-bold text-white' : ''; ?>" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (in_array($current_page, ['siswa_list.php', 'siswa_tambah.php', 'siswa_edit.php', 'siswa_detail.php'])) ? 'active fw-bold text-white' : ''; ?>" href="siswa_list.php">
                            <?= ($user_role === 'admin') ? 'Data Siswa' : 'Jadwal Harian'; ?>
                        </a>
                    </li>

                    <!-- Menu Data Kelas hanya tampil jika role bernilai 'admin' -->
                    <?php if ($user_role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (in_array($current_page, ['kelas_list.php', 'kelas_tambah.php', 'kelas_edit.php'])) ? 'active fw-bold text-white' : ''; ?>" href="kelas_list.php">Data Kelas</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link <?= (in_array($current_page, ['guru_list.php', 'guru_tambah.php', 'guru_edit.php'])) ? 'active fw-bold text-white' : ''; ?>" href="guru_list.php">Data Guru</a>
                    </li>

                    <!-- Menu Khusus Siswa untuk melihat absensi pribadi -->
                    <?php if ($user_role === 'siswa'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'absensi_siswa.php') ? 'active fw-bold text-white' : ''; ?>" href="absensi_siswa.php">
                                <i class="fa-solid fa-clipboard-user me-1"></i> Absensi Saya
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Menu Absensi (Dropdown) hanya tampil jika role bernilai 'admin' -->
                    <?php if ($user_role === 'admin'): ?>
                        <li class="nav-item dropdown">
                        <a class="nav-link <?= ($current_page == 'absensi_rekap.php') ? 'active fw-bold text-white' : ''; ?>" href="absensi_rekap.php">    
                        Rekap Absensi
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Menu Laporan Data hanya tampil jika role bernilai 'admin' -->
                    <?php if ($user_role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'laporan.php') ? 'active fw-bold text-white' : ''; ?>" href="laporan.php">Laporan Data</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="d-flex align-items-center text-white ms-auto">
                        <span class="me-3 small">
                            <i class="fa-solid fa-user me-1"></i>
                            <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User'); ?> 
                            <span class="badge bg-white text-primary fw-bold text-uppercase px-2 py-1 ms-1">
                                <?= htmlspecialchars($_SESSION['user_role'] ?? $_SESSION['role'] ?? 'USER'); ?>
                            </span>
                        </span>
                        <a href="logout.php" class="btn btn-sm btn-outline-light" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline-light ms-auto">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 container py-4">