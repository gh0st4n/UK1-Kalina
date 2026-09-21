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

$pesan_sukses = '';
$pesan_error = '';

$today = date('Y-m-d');
$tanggal_dipilih = $_GET['tanggal'] ?? $_POST['tanggal'] ?? $today;

// Validasi format tanggal
if (!strtotime($tanggal_dipilih)) {
    $tanggal_dipilih = $today;
}

// OTOMATIS REDIRECT: Jika user mengetik tanggal masa depan di URL, lempar kembali ke tanggal hari ini
if ($tanggal_dipilih > $today) {
    header("Location: absensi.php?tanggal=" . $today);
    exit();
}

// Cek akhir pekan (6 = Sabtu, 7 = Minggu)
$day_of_week = date('N', strtotime($tanggal_dipilih));
$is_weekend = ($day_of_week == 6 || $day_of_week == 7);

if ($is_weekend) {
    $hari_nama = ($day_of_week == 6) ? 'Sabtu' : 'Minggu';
    $pesan_error = "Tanggal " . date('d-m-Y', strtotime($tanggal_dipilih)) . " adalah hari " . $hari_nama . " (Libur). Absensi tidak dapat diisi.";
}

// Block input jika hari libur
$is_disabled = $is_weekend;

// Proses Simpan / Update Absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan']) && !$is_disabled) {
    $tanggal = $_POST['tanggal'] ?? $today;
    $status_list = $_POST['status'] ?? [];
    $keterangan_text_list = $_POST['keterangan_text'] ?? [];

    $upload_dir = 'uploads/surat_dokter/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $valid = true;
    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

    // Validasi input terlebih dahulu
    foreach ($status_list as $id_siswa => $status_val) {
        if ($status_val === 'Izin') {
            $ket_text = trim($keterangan_text_list[$id_siswa] ?? '');
            if (empty($ket_text)) {
                $valid = false;
                $pesan_error = "Keterangan wajib diisi untuk siswa yang berstatus Izin!";
                break;
            }
        } elseif ($status_val === 'Sakit') {
            $has_new_file = isset($_FILES['surat_dokter']['name'][$id_siswa]) && 
                            $_FILES['surat_dokter']['error'][$id_siswa] === UPLOAD_ERR_OK && 
                            !empty($_FILES['surat_dokter']['name'][$id_siswa]);

            // Cek file lama di database
            $queryCekSakit = "SELECT keterangan FROM absensi WHERE id_siswa = :id_siswa AND tanggal = :tanggal AND status = 'Sakit'";
            $stmtCekSakit = $db->prepare($queryCekSakit);
            $stmtCekSakit->execute([':id_siswa' => $id_siswa, ':tanggal' => $tanggal]);
            $existing = $stmtCekSakit->fetch(PDO::FETCH_ASSOC);

            if (!$has_new_file && empty($existing['keterangan'])) {
                $valid = false;
                $pesan_error = "Surat Dokter wajib diunggah untuk siswa yang berstatus Sakit!";
                break;
            }

            // Validasi ekstensi file jika ada unggahan baru
            if ($has_new_file) {
                $file_name = $_FILES['surat_dokter']['name'][$id_siswa];
                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_ext)) {
                    $valid = false;
                    $pesan_error = "Format file Surat Dokter harus berupa JPG, PNG, atau PDF!";
                    break;
                }
            }
        }
    }

    // Jika validasi lolos, lakukan simpan / update
    if ($valid && !empty($status_list)) {
        foreach ($status_list as $id_siswa => $status_val) {
            $ket_val = '';

            if ($status_val === 'Izin') {
                $ket_val = trim($keterangan_text_list[$id_siswa] ?? '');
            } elseif ($status_val === 'Sakit') {
                if (isset($_FILES['surat_dokter']['name'][$id_siswa]) && $_FILES['surat_dokter']['error'][$id_siswa] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['surat_dokter']['tmp_name'][$id_siswa];
                    $file_name = $_FILES['surat_dokter']['name'][$id_siswa];
                    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $new_file_name = 'surat_' . $id_siswa . '_' . date('Ymd_His') . '.' . $ext;
                    $target_file = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $ket_val = $new_file_name;
                    }
                } else {
                    // Ambil file yang sudah tersimpan sebelumnya
                    $queryCek = "SELECT keterangan FROM absensi WHERE id_siswa = :id_siswa AND tanggal = :tanggal";
                    $stmtCek = $db->prepare($queryCek);
                    $stmtCek->execute([':id_siswa' => $id_siswa, ':tanggal' => $tanggal]);
                    $old_data = $stmtCek->fetch(PDO::FETCH_ASSOC);
                    $ket_val = $old_data['keterangan'] ?? '';
                }
            }

            $queryCek = "SELECT id_absensi FROM absensi WHERE id_siswa = :id_siswa AND tanggal = :tanggal";
            $stmtCek = $db->prepare($queryCek);
            $stmtCek->execute([':id_siswa' => $id_siswa, ':tanggal' => $tanggal]);

            if ($stmtCek->rowCount() > 0) {
                $query = "UPDATE absensi SET status = :status, keterangan = :keterangan, updated_at = NOW() WHERE id_siswa = :id_siswa AND tanggal = :tanggal";
            } else {
                $query = "INSERT INTO absensi (id_siswa, tanggal, status, keterangan, updated_at) VALUES (:id_siswa, :tanggal, :status, :keterangan, NOW())";
            }

            $stmt = $db->prepare($query);
            $stmt->execute([
                ':id_siswa'   => $id_siswa,
                ':tanggal'    => $tanggal,
                ':status'     => $status_val,
                ':keterangan' => $ket_val
            ]);
        }
        $pesan_sukses = "Data absensi tanggal " . date('d-m-Y', strtotime($tanggal)) . " berhasil disimpan!";
    }
}

// Ambil Data Siswa Beserta Data Absensi
$querySiswa = "SELECT s.id, s.nisn, s.nama, k.nama_kelas, a.status, a.keterangan 
               FROM siswa s
               LEFT JOIN kelas k ON s.kelas_id = k.id 
               LEFT JOIN absensi a ON s.id = a.id_siswa AND a.tanggal = :tanggal
               ORDER BY s.nama ASC";
$stmtSiswa = $db->prepare($querySiswa);
$stmtSiswa->execute([':tanggal' => $tanggal_dipilih]);
$dataSiswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

// Waktu Terakhir Diperbarui
try {
    $queryUpdated = "SELECT MAX(updated_at) AS last_update FROM absensi WHERE tanggal = :tanggal";
    $stmtUpdated = $db->prepare($queryUpdated);
    $stmtUpdated->execute([':tanggal' => $tanggal_dipilih]);
    $last_update = $stmtUpdated->fetch(PDO::FETCH_ASSOC)['last_update'] ?? null;
} catch (PDOException $e) {
    $last_update = null;
}

include 'views/header.php';
?>

<div class="container my-4">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Input Absensi Siswa
            </h5>
            <a href="absensi_rekap.php?tanggal=<?= htmlspecialchars($tanggal_dipilih); ?>" class="btn btn-outline-primary btn-sm fw-semibold">
                <i class="fa-solid fa-chart-column me-1"></i> Lihat Rekap Absensi
            </a>
        </div>
        <div class="card-body p-4">

            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($pesan_sukses); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($pesan_error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="formAbsensi">
                <div class="row mb-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Tanggal Absensi</label>
                        <input type="date" name="tanggal" id="tanggalInput" class="form-control" value="<?= htmlspecialchars($tanggal_dipilih); ?>" max="<?= $today; ?>" required onchange="gantiTanggal(this.value)">
                    </div>
                    <?php if ($last_update): ?>
                        <div class="col-md-8 text-md-end mt-2 mt-md-0">
                            <span class="badge bg-light text-secondary border px-3 py-2">
                                <i class="fa-regular fa-clock me-1 text-primary"></i> 
                                Terakhir diperbarui: <strong><?= date('d-m-Y H:i', strtotime($last_update)); ?> WIB</strong>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="py-3 px-3" width="50">No</th>
                                <th class="py-3" width="160">NISN</th>
                                <th class="py-3">Nama Siswa</th>
                                <th class="py-3" width="140">Kelas</th>
                                <th class="py-3 text-center" width="160">Status</th>
                                <th class="py-3" width="280">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($dataSiswa) > 0): ?>
                                <?php $no = 1; foreach ($dataSiswa as $siswa): 
                                    $status_db = $siswa['status'] ?? 'Hadir';
                                    $ket_db = $siswa['keterangan'] ?? '';
                                ?>
                                <tr>
                                    <td class="px-3 fw-semibold text-muted"><?= $no++; ?></td>
                                    <td class="fw-semibold text-muted"><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($siswa['nama'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                            <?= htmlspecialchars($siswa['nama_kelas'] ?? 'Tanpa Kelas'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <select name="status[<?= $siswa['id']; ?>]" class="form-select form-select-sm fw-semibold text-center status-select" data-id="<?= $siswa['id']; ?>" <?= $is_disabled ? 'disabled' : ''; ?>>
                                            <option value="Hadir" <?= $status_db == 'Hadir' ? 'selected' : ''; ?>>Hadir</option>
                                            <option value="Izin" <?= $status_db == 'Izin' ? 'selected' : ''; ?>>Izin</option>
                                            <option value="Sakit" <?= $status_db == 'Sakit' ? 'selected' : ''; ?>>Sakit</option>
                                            <option value="Alfa" <?= $status_db == 'Alfa' ? 'selected' : ''; ?>>Alfa</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="keterangan_text[<?= $siswa['id']; ?>]" id="ket_text_<?= $siswa['id']; ?>" class="form-control form-control-sm" value="<?= htmlspecialchars($ket_db); ?>" style="display:none;" <?= $is_disabled ? 'disabled' : ''; ?>>
                                        
                                        <div id="ket_file_<?= $siswa['id']; ?>" style="display:none;">
                                            <input type="file" name="surat_dokter[<?= $siswa['id']; ?>]" id="file_<?= $siswa['id']; ?>" class="form-control form-control-sm" accept="image/*,.pdf" <?= $is_disabled ? 'disabled' : ''; ?>>
                                            <?php if ($status_db == 'Sakit' && !empty($ket_db)): ?>
                                                <small class="text-success d-block mt-1" style="font-size: 11px;">
                                                    <i class="fa-solid fa-circle-check me-1"></i> File tersimpan: <?= htmlspecialchars($ket_db); ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted d-block mt-1" style="font-size: 11px;">*Wajib unggah Surat Dokter (PDF/Foto)</small>
                                            <?php endif; ?>
                                        </div>

                                        <span id="ket_none_<?= $siswa['id']; ?>" class="text-muted small">-</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data siswa.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" name="simpan" class="btn btn-primary px-4 fw-semibold" id="btnSimpan" <?= $is_disabled ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function gantiTanggal(tanggalVal) {
    if (tanggalVal) {
        window.location.href = "absensi.php?tanggal=" + tanggalVal;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    function handleStatusChange(selectElem) {
        let id = selectElem.getAttribute('data-id');
        let textInput = document.getElementById('ket_text_' + id);
        let fileDiv = document.getElementById('ket_file_' + id);
        let fileInput = document.getElementById('file_' + id);
        let noneSpan = document.getElementById('ket_none_' + id);
        let val = selectElem.value;

        if (val === 'Izin') {
            textInput.style.display = 'block';
            textInput.setAttribute('required', 'required');
            fileDiv.style.display = 'none';
            fileInput.removeAttribute('required');
            noneSpan.style.display = 'none';
        } else if (val === 'Sakit') {
            textInput.style.display = 'none';
            textInput.removeAttribute('required');
            fileDiv.style.display = 'block';
            
            // Cek apakah ada file lama yang tersimpan
            if (!fileDiv.querySelector('.text-success')) {
                fileInput.setAttribute('required', 'required');
            } else {
                fileInput.removeAttribute('required');
            }
            noneSpan.style.display = 'none';
        } else {
            textInput.style.display = 'none';
            textInput.removeAttribute('required');
            fileDiv.style.display = 'none';
            fileInput.removeAttribute('required');
            noneSpan.style.display = 'inline';
        }
    }

    document.querySelectorAll('.status-select').forEach(function(select) {
        handleStatusChange(select);
        select.addEventListener('change', function() {
            handleStatusChange(this);
        });
    });
});
</script>

<?php include 'views/footer.php'; ?>