<?php
session_start();
require_once 'classes/database.php';
require_once 'classes/auth.php';

$database = new Database();
$db =$database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_role  = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');
$username   =$_SESSION['username'] ?? 'Pengguna';
$siswa_data =$_SESSION['siswa_data'] ?? null;
$siswa_id   =$siswa_data['id'] ?? null;

// Variabel default statistik
$total_siswa = 0;
$total_kelas = 0;
$total_guru = 0;
$total_hadir = 0;
$total_izin_sakit = 0;
$jumlah_mapel = 0;
$recent_data = [];

if ($user_role === 'siswa') {
    // Jika ID siswa belum ada di session, cari berdasarkan username atau nisn di tabel siswa
    $nisn_siswa = $siswa_data['nisn'] ?? $username ?? null;

    if (!$siswa_id &&$nisn_siswa) {
        $q_cari =$db->prepare("SELECT id FROM siswa WHERE nisn = ? OR nama = ? LIMIT 1");
        $q_cari->execute([$nisn_siswa,$username]);
        $res_cari =$q_cari->fetch(PDO::FETCH_ASSOC);
        if ($res_cari) {
            $siswa_id =$res_cari['id'];
        }
    }

    // 1. Hitung Total Hadir Siswa Ini (Hanya menggunakan id_siswa)
    $q_hadir =$db->prepare("SELECT COUNT(*) as total FROM absensi WHERE id_siswa = ? AND (LOWER(status) = 'hadir' OR LOWER(keterangan) = 'hadir')");
    $q_hadir->execute([$siswa_id]);
    $total_hadir =$q_hadir->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 2. Hitung Total Izin/Sakit Siswa Ini
    $q_izin =$db->prepare("SELECT COUNT(*) as total FROM absensi WHERE id_siswa = ? AND (LOWER(status) IN ('izin', 'sakit') OR LOWER(keterangan) IN ('izin', 'sakit'))");
    $q_izin->execute([$siswa_id]);
    $total_izin_sakit =$q_izin->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 3. Jumlah Mapel Ditetapkan 10
    $jumlah_mapel = 10;

    // 4. Ambil Riwayat Absensi Terakhir Siswa Ini
    $q_riwayat =$db->prepare("SELECT * FROM absensi WHERE id_siswa = ? ORDER BY tanggal DESC, jam_masuk DESC LIMIT 5");
    $q_riwayat->execute([$siswa_id]);
    $recent_data =$q_riwayat->fetchAll(PDO::FETCH_ASSOC);

} else {
    // === DATA UNTUK ADMIN / GURU ===
    $total_siswa =$db->query("SELECT COUNT(*) as total FROM siswa")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $total_kelas =$db->query("SELECT COUNT(*) as total FROM kelas")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $total_guru =$db->query("SELECT COUNT(*) as total FROM guru")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $q_absen_hari_ini =$db->query("SELECT COUNT(*) as total FROM absensi WHERE DATE(tanggal) = CURDATE() AND (LOWER(status) = 'hadir' OR LOWER(keterangan) = 'hadir')");
    $total_hadir =$q_absen_hari_ini->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $q_recent =$db->query("SELECT siswa.*, kelas.nama_kelas FROM siswa LEFT JOIN kelas ON siswa.kelas_id = kelas.id ORDER BY siswa.id DESC LIMIT 5");
    $recent_data =$q_recent->fetchAll(PDO::FETCH_ASSOC);
}

include 'views/header.php';
?>

<div class="container my-4">
    <!-- Welcome Banner -->
    <div class="p-4 mb-4 bg-primary text-white rounded-3 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center">
            <?php if ($user_role === 'siswa' && !empty($siswa_data)): ?>
                <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
                    <?php 
                        $foto_path = (!empty($siswa_data['foto']) && file_exists('uploads/' .$siswa_data['foto'])) 
                                   ? 'uploads/' . $siswa_data['foto'] 
                                   : 'https://via.placeholder.com/150';
                    ?>
                    <img src="<?= htmlspecialchars($foto_path); ?>" 
                         alt="Foto Profil" 
                         class="rounded-circle border border-3 border-white shadow-sm" 
                         style="width: 90px; height: 90px; object-fit: cover;">
                </div>
                <div class="col-md-10">
                    <h3 class="fw-bold mb-1">
                        Selamat Datang, <?= htmlspecialchars($siswa_data['nama'] ?? $username); ?>!
                    </h3>
                    <p class="mb-1 opacity-90 fs-6">
                        <i class="fa-solid fa-id-card me-1"></i> NISN: <strong><?= htmlspecialchars($siswa_data['nisn'] ?? '-'); ?></strong>
                        <span class="mx-2">|</span>
                        <i class="fa-solid fa-school me-1"></i> Kelas: <strong><?= htmlspecialchars($siswa_data['nama_kelas'] ?? 'Belum ada kelas'); ?></strong>
                    </p>
                    <p class="mb-0 opacity-75 small">
                        Anda masuk sebagai <span class="badge bg-white text-primary text-uppercase fw-bold">SISWA</span>. Pantau status absensi dan data sekolah Anda di sini.
                    </p>
                </div>
            <?php else: ?>
                <div class="col-md-8">
                    <h3 class="fw-bold mb-1">
                        <i class="fa-solid fa-hand-wave me-2"></i>Selamat Datang, <?= htmlspecialchars(ucfirst($username)); ?>!
                    </h3>
                    <p class="mb-0 opacity-75">
                        Anda masuk sebagai <span class="badge bg-white text-primary text-uppercase fw-bold"><?= htmlspecialchars($user_role); ?></span>. 
                        Kelola data dan informasi sekolah dengan mudah melalui panel ini.
                    </p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="fa-solid fa-graduation-cap fa-5x opacity-25"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <?php if ($user_role === 'siswa'): ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-success">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-user-check fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">TOTAL HADIR</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_hadir); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-warning">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-file-medical text-warning fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">IZIN / SAKIT</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_izin_sakit); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-primary">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-book-open fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">JUMLAH MAPEL</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($jumlah_mapel); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-primary">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-user-graduate fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">TOTAL SISWA</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_siswa); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-success">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-school fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">TOTAL KELAS</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_kelas); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-warning">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-chalkboard-user fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">TOTAL GURU</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_guru); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-info">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-clipboard-user fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold">HADIR HARI INI</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_hadir); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- MAIN CONTENT TABLE -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <?php if ($user_role === 'siswa'): ?>
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Riwayat Kehadiran Terbaru Anda
                        <?php else: ?>
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Siswa Terbaru Diinput
                        <?php endif; ?>
                    </h6>
                    <?php if ($user_role === 'siswa'): ?>
                        <a href="absensi_siswa.php" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold">
                            Lihat Semua Absensi <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    <?php else: ?>
                        <a href="siswa_list.php" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold">
                            Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary">
                                <?php if ($user_role === 'siswa'): ?>
                                    <tr>
                                        <th class="py-3 px-4">Tanggal</th>
                                        <th class="py-3">Jam</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3 px-4 text-end">Keterangan / Bukti</th>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th class="py-3 px-4" width="200">NISN</th>
                                        <th class="py-3">Nama Siswa</th>
                                        <th class="py-3 px-4 text-end" width="200">Kelas</th>
                                    </tr>
                                <?php endif; ?>
                            </thead>
                            <tbody>
                                <?php if (count($recent_data) > 0): ?>
                                    <?php foreach ($recent_data as$row): ?>
                                        <?php if ($user_role === 'siswa'): ?>
                                            <tr>
                                                <td class="px-4 fw-semibold text-muted"><?= htmlspecialchars($row['tanggal'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($row['jam_masuk'] ?? '-'); ?></td>
                                                <td>
                                                    <?php 
                                                        $status = $row['status'] ?? 'Hadir';$badgeBg = 'bg-success';
                                                        if (strtolower($status) == 'izin')$badgeBg = 'bg-info text-dark';
                                                        if (strtolower($status) == 'sakit')$badgeBg = 'bg-warning text-dark';
                                                        if (strtolower($status) == 'alfa')$badgeBg = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?= $badgeBg; ?> px-3 py-2 text-uppercase fw-bold">
                                                        <?= htmlspecialchars($status); ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 text-end">
                                                    <?php 
                                                        $st =$row['status'] ?? 'Hadir';
                                                        $ket =$row['keterangan'] ?? '';
                                                        $target_folder = '';$btn_class = 'btn-outline-primary';
                                                        $icon_class = 'fa-file-image';$label_btn = 'Lihat Bukti';

                                                        if ($st == 'Hadir') {
                                                            $target_folder = 'uploads/selfie/';$btn_class = 'btn-outline-success';
                                                            $icon_class = 'fa-camera';$label_btn = 'Lihat Foto Selfie';
                                                        } elseif ($st == 'Sakit' || $st == 'Izin') {$target_folder = 'uploads/surat dokter/';
                                                            $btn_class = ($st == 'Sakit') ? 'btn-outline-warning text-dark' : 'btn-outline-info text-dark';
                                                            $icon_class = ($st == 'Sakit') ? 'fa-file-medical' : 'fa-envelope-open-text';
                                                            $label_btn = ($st == 'Sakit') ? 'Lihat Surat Dokter' : 'Lihat Surat Izin';
                                                        }

                                                        $full_path = $target_folder .$ket;
                                                    ?>

                                                    <?php if (!empty($target_folder) && !empty($ket) && file_exists(__DIR__ . '/' .$full_path)): ?>
                                                        <button type="button" class="btn <?= $btn_class; ?> btn-sm fw-semibold" onclick="lihatBukti('<?= $full_path; ?>', '<?=$st; ?>', '<?= $row['latitude'] ?? ''; ?>', '<?=$row['longitude'] ?? ''; ?>')">
                                                            <i class="fa-solid <?= $icon_class; ?> me-1"></i> <?= $label_btn; ?>
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted"><?= htmlspecialchars($ket ?: '-'); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td class="px-4 fw-semibold text-muted"><?= htmlspecialchars($row['nisn'] ?? '-'); ?></td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama'] ?? '-'); ?></div>
                                                </td>
                                                <td class="px-4 text-end">
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
                                                        <?= htmlspecialchars($row['nama_kelas'] ?? 'Belum Ada Kelas'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            <?php if ($user_role === 'siswa'): ?>
                                                Belum ada data riwayat absensi.
                                            <?php else: ?>
                                                Belum ada data siswa.
                                            <?php endif; ?>
                                        </td>
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

<!-- Modal Pratinjau Bukti -->
<div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="modalBuktiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalBuktiLabel">
                    <i id="modalIcon" class="fa-solid fa-file-lines me-2"></i> 
                    <span id="modalTitleText">Pratinjau Bukti</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <div id="buktiContent"></div>
                <div id="lokasiContent" class="mt-3 text-muted small fw-semibold"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function lihatBukti(filePath, status, lat, lon) {
        let container = document.getElementById('buktiContent');
        let lokasiContainer = document.getElementById('lokasiContent');
        let titleText = document.getElementById('modalTitleText');
        let iconTitle = document.getElementById('modalIcon');
        let ext = filePath.split('.').pop().toLowerCase();

        if (status === 'Sakit') {
            titleText.textContent = 'Surat Keterangan Dokter';
            iconTitle.className = 'fa-solid fa-file-medical text-warning me-2';
        } else if (status === 'Izin') {
            titleText.textContent = 'Surat Bukti Izin';
            iconTitle.className = 'fa-solid fa-envelope-open-text text-info me-2';
        } else if (status === 'Hadir') {
            titleText.textContent = 'Foto Selfie Absen Mandiri';
            iconTitle.className = 'fa-solid fa-camera text-success me-2';
        }

        if (ext === 'pdf') {
            container.innerHTML = `<iframe src="${filePath}" width="100%" height="500px" style="border:none;"></iframe>`;
            lokasiContainer.innerHTML = '';
        } else {
            container.innerHTML = `<img src="${filePath}" class="img-fluid rounded shadow-sm" style="max-height: 400px;" alt="Bukti File">`;
            
            if (lat && lon && lat !== '' && lon !== '') {
                lokasiContainer.innerHTML = `<i class="fa-solid fa-location-dot text-danger me-1"></i> Lokasi GPS: Latitude ${lat}, Longitude ${lon}`;
            } else {
                lokasiContainer.innerHTML = `<span class="text-muted">Informasi lokasi tidak tersedia untuk absensi ini.</span>`;
            }
        }

        let myModal = new bootstrap.Modal(document.getElementById('modalBukti'));
        myModal.show();
    }
</script>

<?php include 'views/footer.php'; ?>