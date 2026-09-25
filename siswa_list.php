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

// Logika penentuan role
$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');

include 'views/header.php';

// Fitur Pencarian khusus Admin
$search = $_GET['search'] ?? '';

if ($user_role === 'admin') {
    // Query untuk Admin: Ambil Data Siswa
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

} else {
    // -------------------------------------------------------------
    // QUERY UNTUK USER/SISWA: Dapatkan Data Guru Dinamis dari Database
    // -------------------------------------------------------------
    $query_guru = "SELECT * FROM guru";
    $stmt_guru = $db->prepare($query_guru);
    $stmt_guru->execute();
    $data_guru_raw = $stmt_guru->fetchAll(PDO::FETCH_ASSOC);

    // Pemetaan Mata Pelajaran ke Nama Guru
    $mapel_to_guru = [];
    foreach ($data_guru_raw as $g) {
        $nama_mapel = $g['mata_pelajaran'] ?? $g['mapel'] ?? null;
        if (!empty($nama_mapel) && !empty($g['nama_guru'])) {
            $mapel_to_guru[trim($nama_mapel)] = $g['nama_guru'];
        }
    }

    // Array Jadwal Harian
    $jadwal_raw = [
        // SENIN
        ['hari' => 'Senin', 'jam' => '06:30 - 09:00', 'mapel' => 'Pemrograman Web', 'ruang' => 'Lab Utama'],
        ['hari' => 'Senin', 'jam' => '09:00 - 09:45', 'mapel' => 'Istirahat Pertama', 'ruang' => 'Area Sekolah'],
        ['hari' => 'Senin', 'jam' => '09:45 - 11:45', 'mapel' => 'Bahasa Indonesia', 'ruang' => 'Ruang 32'],
        ['hari' => 'Senin', 'jam' => '11:45 - 12:45', 'mapel' => 'Istirahat Kedua / ISOMA', 'ruang' => 'Masjid / Kantin'],
        ['hari' => 'Senin', 'jam' => '12:45 - 15:00', 'mapel' => 'Matematika', 'ruang' => 'Ruang 32'],

        // SELASA
        ['hari' => 'Selasa', 'jam' => '06:30 - 09:00', 'mapel' => 'Pemrograman Berorientasi Objek', 'ruang' => 'Lab Utama'],
        ['hari' => 'Selasa', 'jam' => '09:00 - 09:45', 'mapel' => 'Istirahat Pertama', 'ruang' => 'Area Sekolah'],
        ['hari' => 'Selasa', 'jam' => '09:45 - 11:45', 'mapel' => 'Desain UI / UX', 'ruang' => 'Lab Utama'],
        ['hari' => 'Selasa', 'jam' => '11:45 - 12:45', 'mapel' => 'Istirahat Kedua / ISOMA', 'ruang' => 'Masjid / Kantin'],
        ['hari' => 'Selasa', 'jam' => '12:45 - 15:00', 'mapel' => 'PJOK', 'ruang' => 'Lapangan Indoor / Outdoor'],

        // RABU
        ['hari' => 'Rabu', 'jam' => '06:30 - 09:00', 'mapel' => 'Web Security', 'ruang' => 'Lab Utama'],
        ['hari' => 'Rabu', 'jam' => '09:00 - 09:45', 'mapel' => 'Istirahat Pertama', 'ruang' => 'Area Sekolah'],
        ['hari' => 'Rabu', 'jam' => '09:45 - 11:45', 'mapel' => 'Database', 'ruang' => 'Lab Utama'],
        ['hari' => 'Rabu', 'jam' => '11:45 - 12:45', 'mapel' => 'Istirahat Kedua / ISOMA', 'ruang' => 'Masjid / Kantin'],
        ['hari' => 'Rabu', 'jam' => '12:45 - 15:00', 'mapel' => 'B. Inggris', 'ruang' => 'Ruang 32'],

        // KAMIS
        ['hari' => 'Kamis', 'jam' => '06:30 - 09:00', 'mapel' => 'Pendidikan Agama', 'ruang' => 'Ruang 32'],
        ['hari' => 'Kamis', 'jam' => '09:00 - 09:45', 'mapel' => 'Istirahat Pertama', 'ruang' => 'Area Sekolah'],
        ['hari' => 'Kamis', 'jam' => '09:45 - 11:45', 'mapel' => 'Pemrograman Web', 'ruang' => 'Lab Utama'],
        ['hari' => 'Kamis', 'jam' => '11:45 - 12:45', 'mapel' => 'Istirahat Kedua / ISOMA', 'ruang' => 'Masjid / Kantin'],
        ['hari' => 'Kamis', 'jam' => '12:45 - 15:00', 'mapel' => 'Database', 'ruang' => 'Lab Utama'],

        // JUMAT
        ['hari' => 'Jumat', 'jam' => '06:30 - 09:00', 'mapel' => 'Matematika', 'ruang' => 'Ruang 32'],
        ['hari' => 'Jumat', 'jam' => '09:00 - 09:45', 'mapel' => 'Istirahat Pertama', 'ruang' => 'Area Sekolah'],
        ['hari' => 'Jumat', 'jam' => '09:45 - 11:45', 'mapel' => 'B. Inggris', 'ruang' => 'Ruang 32'],
        ['hari' => 'Jumat', 'jam' => '11:45 - 12:45', 'mapel' => 'Istirahat Kedua / Solat Jumat', 'ruang' => 'Masjid'],
        ['hari' => 'Jumat', 'jam' => '12:45 - 15:00', 'mapel' => 'Desain UI / UX', 'ruang' => 'Lab Utama']
    ];

    // Hubungkan nama guru secara otomatis dari database
    $jadwal_harian = [];
    foreach ($jadwal_raw as $item) {
        if (strpos(strtolower($item['mapel']), 'istirahat') !== false) {
            $item['guru'] = '-';
        } else {
            $item['guru'] = $mapel_to_guru[$item['mapel']] ?? 'Belum Ditentukan';
        }
        $jadwal_harian[] = $item;
    }
}

$msg = $_GET['msg'] ?? '';
?>

<div class="container-fluid px-4 mt-4 mb-5">
    <!-- Notifikasi Alert khusus Admin -->
    <?php if ($user_role === 'admin'): ?>
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
    <?php endif; ?>

    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center p-3 mb-4 bg-white rounded-3 shadow-sm border-start border-4 border-primary">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid <?= ($user_role === 'admin') ? 'fa-users' : 'fa-calendar-days'; ?> text-primary me-2"></i>
                <?= ($user_role === 'admin') ? 'Daftar Data Siswa' : 'Jadwal Harian Siswa'; ?>
            </h4>
            <p class="text-muted mb-0 small">
                <?= ($user_role === 'admin') ? 'Kelola informasi seluruh data siswa terdaftar' : 'Informasi jadwal mata pelajaran harian'; ?>
            </p>
        </div>
        <?php if ($user_role === 'admin'): ?>
            <a href="siswa_tambah.php" class="btn btn-primary px-3 shadow-sm"><i class="fa-solid fa-user-plus me-1"></i> Tambah Siswa</a>
        <?php endif; ?>
    </div>

    <!-- Search Bar (Hanya tampil untuk Admin) -->
    <?php if ($user_role === 'admin'): ?>
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="siswa_list.php" class="row g-2">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0" 
                                   placeholder="Cari berdasarkan NISN, Nama, atau Kelas..." 
                                   value="<?= htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Card Table Container -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                
                <?php if ($user_role === 'admin'): ?>
                    <!-- TAMPILAN UNTUK ADMIN: TABEL DATA SISWA -->
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
                                <th class="py-3">Terakhir Diperbarui</th>
                                <th width="240" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data_siswa) > 0): ?>
                                <?php $no = 1; foreach ($data_siswa as $row): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                        <td class="text-center">
                                            <?php $foto = !empty($row['foto']) && file_exists('uploads/' . $row['foto']) ? 'uploads/' . $row['foto'] : 'assets/img/default-avatar.png'; ?>
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
                                        <td class="text-secondary small">
                                            <?php 
                                                $waktu = $row['updated_at'] ?? $row['created_at'] ?? null;
                                                echo !empty($waktu) ? '<i class="fa-regular fa-clock me-1 text-muted"></i>' . date('d M Y, H:i', strtotime($waktu)) : '-';
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1 align-items-center justify-content-center">
                                                <!-- TOMBOL DETAIL MEMBUKA MODAL -->
                                                <button type="button" class="btn btn-info btn-sm text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetailSiswa<?= $row['id']; ?>">
                                                    <i class="fa-solid fa-eye"></i> Detail
                                                </button>

                                                <a href="siswa_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm text-white shadow-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                                                
                                                <form action="siswa_hapus.php" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm"><i class="fa-solid fa-trash"></i> Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-user-slash fa-2x mb-2 d-block"></i>
                                        Data siswa tidak ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                <?php else: ?>
                    <!-- TAMPILAN UNTUK SISWA/USER: TABEL JADWAL HARIAN -->
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th width="60" class="text-center py-3">No</th>
                                <th width="120" class="py-3">Hari</th>
                                <th width="170" class="py-3">Jam Pelajaran</th>
                                <th class="py-3">Mata Pelajaran</th>
                                <th class="py-3">Guru Pengampu</th>
                                <th class="py-3">Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($jadwal_harian) > 0): ?>
                                <?php 
                                // Mapping warna badge untuk setiap hari
                                $warna_hari = [
                                    'Senin'  => 'bg-primary',
                                    'Selasa' => 'bg-success',
                                    'Rabu'   => 'bg-info text-dark',
                                    'Kamis'  => 'bg-warning text-dark',
                                    'Jumat'  => 'bg-danger'
                                ];

                                $no = 1; 
                                foreach ($jadwal_harian as $row): 
                                    $is_istirahat = strpos(strtolower($row['mapel']), 'istirahat') !== false;
                                    $badge_class = $warna_hari[$row['hari']] ?? 'bg-secondary';
                                ?>
                                    <tr class="<?= $is_istirahat ? 'table-warning' : ''; ?>">
                                        <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                        <td>
                                            <span class="badge <?= $badge_class; ?> px-3 py-2 fw-semibold">
                                                <?= htmlspecialchars($row['hari']); ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            <i class="fa-regular fa-clock me-1 text-primary"></i>
                                            <?= htmlspecialchars($row['jam']); ?>
                                        </td>
                                        <td class="fw-bold <?= $is_istirahat ? 'text-warning-emphasis' : 'text-primary'; ?>">
                                            <?= htmlspecialchars($row['mapel']); ?>
                                        </td>
                                        <td class="text-secondary"><?= htmlspecialchars($row['guru']); ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                                <?= htmlspecialchars($row['ruang']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block"></i>
                                        Jadwal harian tidak ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Navigation Back -->
    <div class="mt-4">
        <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-2"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DETAIL SISWA (DITARUH DI LUAR TABEL) -->
<!-- ========================================== -->
<?php if ($user_role === 'admin' && !empty($data_siswa)): ?>
    <?php foreach ($data_siswa as $row): ?>
        <?php $foto = !empty($row['foto']) && file_exists('uploads/' . $row['foto']) ? 'uploads/' . $row['foto'] : 'assets/img/default-avatar.png'; ?>
        <div class="modal fade" id="modalDetailSiswa<?= $row['id']; ?>" tabindex="-1" aria-labelledby="labelModalSiswa<?= $row['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="labelModalSiswa<?= $row['id']; ?>">
                            <i class="fa-solid fa-id-card me-2"></i>Detail Data Siswa
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <img src="<?= $foto; ?>" alt="Foto Siswa" class="rounded border shadow-sm img-fluid" style="max-height: 250px; object-fit: cover;">
                        </div>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="130" class="fw-bold text-secondary">NISN</td>
                                <td width="10">:</td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nisn']); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-secondary">Nama Lengkap</td>
                                <td>:</td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-secondary">Kelas</td>
                                <td>:</td>
                                <td><span class="badge bg-primary px-2 py-1"><?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?></span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-secondary">Wali Kelas</td>
                                <td>:</td>
                                <td class="text-dark"><?= htmlspecialchars($row['wali_kelas'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-secondary">Alamat</td>
                                <td>:</td>
                                <td class="text-dark"><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'views/footer.php'; ?>