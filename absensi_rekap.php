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

// PROTEKSI HAK AKSES: Hanya admin yang boleh mengakses rekap absensi
$user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? '');
if ($user_role !== 'admin') {
    header("Location: index.php");
    exit();
}

$today = date('Y-m-d');
$filter_tanggal =$_GET['tanggal'] ?? '';

// OTOMATIS REDIRECT: Jika user mengetik tanggal masa depan di URL, lempar kembali ke tanggal hari ini
if (!empty($filter_tanggal) && $filter_tanggal >$today) {
    header("Location: absensi_rekap.php?tanggal=" . $today);
    exit();
}

$pesan_libur = '';$is_weekend = false;
$last_update = null;

// Pengecekan Hari Libur jika ada Filter Tanggal
if (!empty($filter_tanggal)) {
    $day_of_week = date('N', strtotime($filter_tanggal)); // 6 = Sabtu, 7 = Minggu
    if ($day_of_week == 6 || $day_of_week == 7) {$is_weekend = true;
        $hari_nama = ($day_of_week == 6) ? 'Sabtu' : 'Minggu';
        $pesan_libur = "Tanggal " . date('d-m-Y', strtotime($filter_tanggal)) . " adalah hari " . $hari_nama . " (Hari Libur).";
    }

    // Rekap Per Tanggal
    $query = "SELECT s.nisn, s.nama, k.nama_kelas, a.status, a.keterangan
              FROM siswa s
              LEFT JOIN kelas k ON s.kelas_id = k.id
              LEFT JOIN absensi a ON s.id = a.id_siswa AND a.tanggal = :tanggal
              ORDER BY s.nama ASC";
    $stmt = $db->prepare($query);
    $stmt->execute([':tanggal' =>$filter_tanggal]);
    $rekap =$stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil waktu update terakhir
    try {
        $queryUpdated = "SELECT MAX(updated_at) AS last_update FROM absensi WHERE tanggal = :tanggal";
        $stmtUpdated = $db->prepare($queryUpdated);
        $stmtUpdated->execute([':tanggal' =>$filter_tanggal]);
        $last_update =$stmtUpdated->fetch(PDO::FETCH_ASSOC)['last_update'] ?? null;
    } catch (PDOException $e) {$last_update = null;
    }

} else {
    // Rekap Total Akumulasi
    $query = "SELECT s.nisn, s.nama, k.nama_kelas,
                SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END) AS hadir,
                SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) AS izin,
                SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) AS sakit,
                SUM(CASE WHEN a.status = 'Alfa' THEN 1 ELSE 0 END) AS alfa
              FROM siswa s
              LEFT JOIN kelas k ON s.kelas_id = k.id
              LEFT JOIN absensi a ON s.id = a.id_siswa
              GROUP BY s.id
              ORDER BY s.nama ASC";
    $stmt =$db->prepare($query);$stmt->execute();
    $rekap =$stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil waktu update terakhir secara keseluruhan
    try {
        $queryUpdated = "SELECT MAX(updated_at) AS last_update FROM absensi";
        $stmtUpdated =$db->prepare($queryUpdated);$stmtUpdated->execute();
        $last_update =$stmtUpdated->fetch(PDO::FETCH_ASSOC)['last_update'] ?? null;
    } catch (PDOException $e) {$last_update = null;
    }
}

include 'views/header.php';
?>

<div class="container my-4">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-chart-column text-primary me-2"></i>
                Rekap Absensi Siswa <?= !empty($filter_tanggal) ? ' - Tanggal ' . date('d-m-Y', strtotime($filter_tanggal)) : '(Keseluruhan)'; ?>
            </h5>
            <a href="absensi.php<?= !empty($filter_tanggal) ? '?tanggal='.$filter_tanggal : ''; ?>" class="btn btn-primary btn-sm fw-semibold">
                <i class="fa-solid fa-plus me-1"></i> Input Absensi
            </a>
        </div>
        <div class="card-body p-4">

            <!-- Alert Hari Libur -->
            <?php if (!empty($pesan_libur)): ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation fa-lg me-3 text-warning"></i>
                    <div>
                        <strong>Peringatan:</strong> <?= htmlspecialchars($pesan_libur); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filter Tanggal & Terakhir Diperbarui -->
            <div class="row align-items-center mb-4 g-2">
                <div class="col-md-7">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label class="col-form-label fw-semibold text-muted">Filter Tanggal:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($filter_tanggal); ?>" max="<?= $today; ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-secondary fw-semibold">Cari</button>
                            <?php if (!empty($filter_tanggal)): ?>
                                <a href="absensi_rekap.php" class="btn btn-outline-secondary">Reset / Tampil Semua</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <?php if ($last_update): ?>
                    <div class="col-md-5 text-md-end">
                        <span class="badge bg-light text-secondary border px-3 py-2">
                            <i class="fa-regular fa-clock me-1 text-primary"></i> 
                            Terakhir diperbarui: <strong><?= date('d-m-Y H:i', strtotime($last_update)); ?> WIB</strong>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <?php if (!empty($filter_tanggal)): ?>
                            <tr>
                                <th class="py-3 px-3" width="50">No</th>
                                <th class="py-3" width="160">NISN</th>
                                <th class="py-3">Nama Siswa</th>
                                <th class="py-3" width="140">Kelas</th>
                                <th class="py-3 text-center" width="130">Status</th>
                                <th class="py-3">Keterangan</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th class="py-3 px-3" width="50">No</th>
                                <th class="py-3" width="160">NISN</th>
                                <th class="py-3">Nama Siswa</th>
                                <th class="py-3" width="140">Kelas</th>
                                <th class="py-3 text-center text-success" width="90">Hadir</th>
                                <th class="py-3 text-center text-info" width="90">Izin</th>
                                <th class="py-3 text-center text-warning" width="90">Sakit</th>
                                <th class="py-3 text-center text-danger" width="90">Alfa</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php if (count($rekap) > 0): ?>
                            <?php $no = 1; foreach ($rekap as$row): ?>
                            <tr>
                                <td class="px-3 fw-semibold text-muted"><?= $no++; ?></td>
                                <td class="fw-semibold text-muted"><?= htmlspecialchars($row['nisn'] ?? '-'); ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                        <?= htmlspecialchars($row['nama_kelas'] ?? 'Tanpa Kelas'); ?>
                                    </span>
                                </td>

                                <?php if (!empty($filter_tanggal)): ?>
                                    <td class="text-center fw-bold">
                                        <?php 
                                            $st = $row['status'] ?? ($is_weekend ? 'Libur' : 'Belum Absen');
                                            $badge_class = 'bg-secondary';
                                            if ($st == 'Hadir')$badge_class = 'bg-success';
                                            elseif ($st == 'Izin')$badge_class = 'bg-info text-dark';
                                            elseif ($st == 'Sakit')$badge_class = 'bg-warning text-dark';
                                            elseif ($st == 'Alfa')$badge_class = 'bg-danger';
                                            elseif ($st == 'Libur')$badge_class = 'bg-dark';
                                        ?>
                                        <span class="badge <?= $badge_class; ?>"><?= $st; ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $ket =$row['keterangan'] ?? '';
                                            if ($st == 'Sakit' && !empty($ket)) {
                                                $file_path = 'uploads/surat_dokter/' .$ket;
                                                if (file_exists($file_path)) {
                                                    echo '<button type="button" class="btn btn-outline-warning btn-sm fw-semibold" onclick="lihatSurat(\'' . $file_path . '\')">
                                                            <i class="fa-solid fa-file-medical me-1"></i> Lihat Surat
                                                          </button>';
                                                } else {
                                                    echo '<span class="text-muted small">' . htmlspecialchars($ket) . '</span>';
                                                }
                                            } elseif ($is_weekend) {
                                                echo '<span class="text-muted">Hari Libur Sekolah</span>';
                                            } else {
                                                echo htmlspecialchars(!empty($ket) ?$ket : '-');
                                            }
                                        ?>
                                    </td>
                                <?php else: ?>
                                    <td class="text-center fw-bold text-success"><?= $row['hadir']; ?></td>
                                    <td class="text-center fw-bold text-info"><?= $row['izin']; ?></td>
                                    <td class="text-center fw-bold text-warning"><?= $row['sakit']; ?></td>
                                    <td class="text-center fw-bold text-danger"><?= $row['alfa']; ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= !empty($filter_tanggal) ? '6' : '8'; ?>" class="text-center py-4 text-muted">Data absensi tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pop-up Lihat Surat Dokter -->
<div class="modal fade" id="modalSurat" tabindex="-1" aria-labelledby="modalSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalSuratLabel">
                    <i class="fa-solid fa-file-medical text-warning me-2"></i> Surat Dokter
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3" id="suratContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function lihatSurat(filePath) {
    let container = document.getElementById('suratContent');
    let ext = filePath.split('.').pop().toLowerCase();

    if (ext === 'pdf') {
        container.innerHTML = `<iframe src="${filePath}" width="100%" height="500px" style="border:none;"></iframe>`;
    } else {
        container.innerHTML = `<img src="${filePath}" class="img-fluid rounded shadow-sm" style="max-height: 500px;" alt="Surat Dokter">`;
    }

    let myModal = new bootstrap.Modal(document.getElementById('modalSurat'));
    myModal.show();
}
</script>

<?php include 'views/footer.php'; ?>