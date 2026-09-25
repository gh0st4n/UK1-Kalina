<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

require_once 'classes/database.php';
require_once 'classes/auth.php';

$database = new Database();
$db =$database->getConnection();
$auth = new Auth($db);

$is_logged_in =$auth->isLoggedIn();
$user_role =$_SESSION['user_role'] ?? '';

if (!$is_logged_in or$user_role !== 'siswa') {
    header("Location: login.php");
    exit();
}

$message = '';
$alert_type = '';$success_script = '';

// Ambil ID siswa secara spesifik dan akurat berdasarkan session login
$siswa_id = null;
if (isset($_SESSION['siswa_data']['id'])) {
    $siswa_id =$_SESSION['siswa_data']['id'];
} elseif (isset($_SESSION['siswa_id'])) {
    $siswa_id =$_SESSION['siswa_id'];
} else {
    $logged_user =$_SESSION['username'] ?? $_SESSION['user_name'] ?? $_SESSION['nama'] ?? '';
    $stmt_cari =$db->prepare("SELECT id FROM siswa WHERE nama = ? OR username = ? LIMIT 1");
    $stmt_cari->execute([$logged_user,$logged_user]);
    $row_siswa =$stmt_cari->fetch(PDO::FETCH_ASSOC);
    if ($row_siswa) {
        $siswa_id =$row_siswa['id'];
    } else {
        $siswa_id =$_SESSION['user_id'] ?? null;
    }
}

$tanggal = date('Y-m-d');

// PROSES POST (SIMPAN ABSEN / IZIN / SAKIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {$jenis_absen     = $_POST['jenis_absen'] ?? 'Hadir';$jam_masuk       = date('H:i:s');
    $keterangan_teks = trim($_POST['keterangan_teks'] ?? '');

    if (empty($siswa_id)) {$message = "ID Siswa tidak ditemukan. Silakan logout lalu login kembali!";
        $alert_type = "danger";
    } else {
        $stmt_cek =$db->prepare("SELECT id_absensi FROM absensi WHERE id_siswa = ? AND tanggal = ?");
        $stmt_cek->execute([$siswa_id,$tanggal]);
        if ($stmt_cek->rowCount() > 0) {$message = "Anda sudah melakukan absensi hari ini! Tidak dapat mengirim absensi dua kali.";
            $alert_type = "warning";
        } else {
            if ($jenis_absen === 'Hadir') {
                $img       =$_POST['image'] ?? '';
                $latitude  =$_POST['latitude'] ?? '';
                $longitude =$_POST['longitude'] ?? '';

                if (empty($img)) {$message = "Gagal mengambil foto dari kamera. Silakan coba lagi!";
                    $alert_type = "danger";
                } else {
                    $img = str_replace('data:image/jpeg;base64,', '',$img);
                    $img = str_replace(' ', '+',$img);
                    $data = base64_decode($img);

                    $folder_relative = 'uploads/selfie/';
                    $folder = __DIR__ . '/' .$folder_relative;
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }

                    $fileName = 'selfie_' .$siswa_id . '_' . time() . '.jpg';
                    $filePath = $folder .$fileName;
                    file_put_contents($filePath,$data);

                    $query = "INSERT INTO absensi (id_siswa, tanggal, jam_masuk, status, keterangan, foto_selfie, latitude, longitude) 
                              VALUES (:siswa_id, :tanggal, :jam_masuk, 'Hadir', :keterangan, :foto_selfie, :latitude, :longitude)";
                    $stmt = $db->prepare($query);
                    $exec =$stmt->execute([
                        'siswa_id'     => $siswa_id,
                        ':tanggal'     => $tanggal,
                        ':jam_masuk'   => $jam_masuk,
                        ':keterangan'  => $fileName,
                        ':foto_selfie' => $fileName,
                        ':latitude'    => $latitude,
                        ':longitude'   => $longitude
                    ]);

                    if ($exec) {$success_script = "Swal.fire({ title: 'Berhasil!', text: 'Absen Hadir berhasil dikirim!', icon: 'success', confirmButtonText: 'OK' }).then((r) => { if(r.isConfirmed) window.location.href='absensi_siswa.php'; });";
                    } else {
                        $message = "Gagal menyimpan data absensi.";
                        $alert_type = "danger";
                    }
                }
            } elseif ($jenis_absen === 'Izin' || $jenis_absen === 'Sakit') {$file_name_surat = '';
                $file_input_key = ($jenis_absen === 'Sakit') ? 'file_surat_sakit' : 'file_surat_izin';

                if (isset($_FILES[$file_input_key]) &&$_FILES[$file_input_key]['error'] === UPLOAD_ERR_OK) {$file_tmp = $_FILES[$file_input_key]['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES[$file_input_key]['name'], PATHINFO_EXTENSION));$allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
                    
                    if (in_array($file_ext, $allowed_ext)) {$sub_folder = 'uploads/surat dokter/';
                        $folder = __DIR__ . '/' .$sub_folder;
                        if (!file_exists($folder)) {
                            mkdir($folder, 0777, true);
                        }
                        $file_name_surat = strtolower($jenis_absen) . '_' . $siswa_id . '_' . time() . '.' . $file_ext;
                        move_uploaded_file($file_tmp, $folder .$file_name_surat);
                    }
                }

                if (empty($file_name_surat)) {$message = "Harap unggah bukti surat/foto yang valid (JPG, PNG, atau PDF).";
                    $alert_type = "danger";
                } else {
                    $query = "INSERT INTO absensi (id_siswa, tanggal, jam_masuk, status, keterangan) 
                              VALUES (:siswa_id, :tanggal, :jam_masuk, :status, :keterangan)";
                    $stmt = $db->prepare($query);
                    $exec =$stmt->execute([
                        'siswa_id'    => $siswa_id,
                        ':tanggal'    => $tanggal,
                        ':jam_masuk'  => $jam_masuk,
                        ':status'     => $jenis_absen,
                        ':keterangan' => $file_name_surat
                    ]);

                    if ($exec) {
                        $success_script = "Swal.fire({ title: 'Berhasil!', text: 'Pengajuan " . $jenis_absen . " berhasil dikirim!', icon: 'success', confirmButtonText: 'OK' }).then((r) => { if(r.isConfirmed) window.location.href='absensi_siswa.php'; });";
                    } else {
                        $message = "Gagal menyimpan pengajuan.";
                        $alert_type = "danger";
                    }
                }
            }
        }
    }
}

// CEK APAKAH SISWA SUDAH ABSEN HARI INI
$stmt_sudah =$db->prepare("SELECT * FROM absensi WHERE id_siswa = ? AND tanggal = ?");
$stmt_sudah->execute([$siswa_id,$tanggal]);
$sudah_absen_hari_ini =$stmt_sudah->fetch(PDO::FETCH_ASSOC);

// AMBIL STATISTIK REKAP
$stmt_rekap =$db->prepare("
    SELECT 
        SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
        SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
        SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
        SUM(CASE WHEN status = 'Alfa' THEN 1 ELSE 0 END) as total_alpha
    FROM absensi WHERE id_siswa = ?
");
$stmt_rekap->execute([$siswa_id]);
$rekap =$stmt_rekap->fetch(PDO::FETCH_ASSOC);

// AMBIL RIWAYAT ABSENSI SISWA
$stmt_riwayat =$db->prepare("SELECT * FROM absensi WHERE id_siswa = ? ORDER BY tanggal DESC LIMIT 10");
$stmt_riwayat->execute([$siswa_id]);
$riwayat_list =$stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);

include 'views/header.php';
?>

<div class="container my-5" style="max-width: 800px;">

    <?php if (!empty($message) && empty($success_script)): ?>
        <div class="alert alert-<?= $alert_type; ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <?= $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 1. KOTAK INFORMASI REKAP & RIWAYAT -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-primary text-white text-center py-3">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-clipboard-user me-2"></i>Rekapitulasi & Riwayat Absensi Saya</h5>
        </div>
        <div class="card-body p-4 text-center">
            
            <?php if ($sudah_absen_hari_ini): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i> <strong>Anda Sudah Melakukan Absensi Hari Ini</strong> (Status: <span class="badge bg-success"><?= $sudah_absen_hari_ini['status']; ?></span> pada Pukul <?=$sudah_absen_hari_ini['jam_masuk']; ?>)
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="p-3 border rounded bg-light">
                        <span class="text-muted small d-block">Hadir</span>
                        <h3 class="fw-bold text-primary mb-0"><?= $rekap['total_hadir'] ?? 0; ?></h3>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 border rounded bg-light">
                        <span class="text-muted small d-block">Izin</span>
                        <h3 class="fw-bold text-primary mb-0"><?= $rekap['total_izin'] ?? 0; ?></h3>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 border rounded bg-light">
                        <span class="text-muted small d-block">Sakit</span>
                        <h3 class="fw-bold text-primary mb-0"><?= $rekap['total_sakit'] ?? 0; ?></h3>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold text-start mb-3 text-primary"><i class="fa-solid fa-clock-rotate-left me-2"></i>Riwayat Absensi Terakhir</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-start">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="bg-primary text-white">Tanggal</th>
                            <th class="bg-primary text-white">Jam</th>
                            <th class="bg-primary text-white">Status</th>
                            <th class="bg-primary text-white">Keterangan / Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($riwayat_list) > 0): ?>
                            <?php foreach ($riwayat_list as$row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                    <td><?= htmlspecialchars($row['jam_masuk'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $st =$row['status'];
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

                                        if (!empty($target_folder) && !empty($ket) && file_exists(__DIR__ . '/' . $target_folder .$ket)):
                                        ?>
                                            <button type="button" class="btn <?= $btn_class; ?> btn-sm fw-semibold" onclick="lihatBukti('<?= $target_folder . $ket; ?>', '<?=$st; ?>', '<?= $row['latitude'] ?? ''; ?>', '<?=$row['longitude'] ?? ''; ?>')">
                                                <i class="fa-solid <?= $icon_class; ?> me-1"></i> <?= $label_btn; ?>
                                            </button>
                                        <?php else: ?>
                                            <?= htmlspecialchars($ket); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada riwayat absensi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. FORM ABSENSI -->
    <div class="card border-0 shadow-sm rounded-3" style="max-width: 600px; margin: auto;">
        <div class="card-header bg-primary text-white text-center py-3">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-camera me-2"></i>Form Kirim Absensi</h5>
        </div>
        <div class="card-body p-4">

            <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="tab-hadir" data-bs-toggle="pill" data-bs-target="#content-hadir" type="button" role="tab" onclick="setJenisAbsen('Hadir')">
                        <i class="fa-solid fa-camera me-1"></i> Hadir (Selfie)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="tab-izin" data-bs-toggle="pill" data-bs-target="#content-izin" type="button" role="tab" onclick="setJenisAbsen('Izin')">
                        <i class="fa-solid fa-envelope-open-text me-1"></i> Izin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="tab-sakit" data-bs-toggle="pill" data-bs-target="#content-sakit" type="button" role="tab" onclick="setJenisAbsen('Sakit')">
                        <i class="fa-solid fa-file-medical me-1"></i> Sakit
                    </button>
                </li>
            </ul>

            <form id="formAbsen" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="jenis_absen" id="inputJenisAbsen" value="Hadir">
                <input type="hidden" name="image" id="image-tag">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active text-center" id="content-hadir" role="tabpanel">
                        <div id="my_camera" class="mx-auto border rounded-3 mb-3" style="width:100%; max-width:320px; height:240px; background:#f8f9fa;"></div>
                        <div id="status_gps" class="mb-3 small text-muted">
                            <i class="fa-solid fa-location-dot me-1"></i> Mendeteksi lokasi GPS...
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-izin" role="tabpanel">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold">Unggah Bukti Surat Izin (Foto / PDF):</label>
                            <input type="file" name="file_surat_izin" class="form-control" <?= $sudah_absen_hari_ini ? 'disabled' : ''; ?> accept=".jpg, .jpeg, .png, .pdf">
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-sakit" role="tabpanel">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold">Unggah Surat Keterangan Dokter (Foto / PDF):</label>
                            <input type="file" name="file_surat_sakit" class="form-control" <?= $sudah_absen_hari_ini ? 'disabled' : ''; ?> accept=".jpg, .jpeg, .png, .pdf">
                        </div>
                    </div>
                </div>

                <button type="button" id="btnSubmit" class="btn btn-primary w-100 py-2 fw-bold" <?= $sudah_absen_hari_ini ? 'disabled' : ''; ?> onclick="prosesKirim()">
                    <i class="fa-solid fa-check-circle me-1"></i> Kirim Absensi
                </button>
            </form>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let sudahAbsen = <?= $sudah_absen_hari_ini ? 'true' : 'false'; ?>;
    let currentJenis = 'Hadir';

    function setJenisAbsen(jenis) {
        currentJenis = jenis;
        document.getElementById('inputJenisAbsen').value = jenis;

        if (!sudahAbsen) {
            if (jenis === 'Hadir') {
                try { Webcam.attach('#my_camera'); } catch(e){}
            } else {
                try { Webcam.reset(); } catch(e){}
            }
        }
    }

    if (!sudahAbsen && document.getElementById('my_camera')) {
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90,
            facing_mode: "user"
        });
        Webcam.attach('#my_camera');

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                document.getElementById('status_gps').innerHTML = "<span class='text-success'><i class='fa-solid fa-circle-check me-1'></i> Lokasi GPS Berhasil Didapatkan</span>";
            }, function(error) {
                document.getElementById('status_gps').innerHTML = "<span class='text-warning'><i class='fa-solid fa-triangle-exclamation me-1'></i> GPS tidak diizinkan</span>";
            });
        }
    } else if (sudahAbsen && document.getElementById('my_camera')) {
        document.getElementById('my_camera').innerHTML = "<div class='d-flex align-items-center justify-content-center h-100 text-muted'>Kamera Dinonaktifkan (Sudah Absen)</div>";
        document.getElementById('status_gps').innerHTML = "<span class='text-muted'>Absensi hari ini sudah selesai</span>";
    }

    function prosesKirim() {
        if (sudahAbsen) return;
        if (currentJenis === 'Hadir') {
            Webcam.snap(function(data_uri) {
                document.getElementById('image-tag').value = data_uri;
                document.getElementById('formAbsen').submit();
            });
        } else {
            document.getElementById('formAbsen').submit();
        }
    }

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

    <?php if (!empty($success_script)) echo$success_script; ?>
</script>

<?php include 'views/footer.php'; ?>