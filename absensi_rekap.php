<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

require_once 'classes/database.php';
require_once 'classes/auth.php';

class AbsensiRekapManager {
    private $db;
    private $auth;
    private $today;
    
    private $filterTanggal = '';
    private $filterBulan = '';
    private $filterKelas = '';
    
    private $isWeekend = false;
    private $pesanLibur = '';
    private $lastUpdate = null;
    
    private $listKelas = array();
    private $rekapData = array();
    
    private $alertMessage = '';
    private $alertType = '';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->auth = new Auth($this->db);
        $this->today = date('Y-m-d');
        
        $this->checkAccessPermission();
        $this->handleActions();
        $this->parseFilters();
        $this->loadData();
    }

    private function checkAccessPermission() {
        if (!$this->auth->isLoggedIn()) {
            header("Location: login.php");
            exit();
        }

        $userRole = '';
        if (isset($_SESSION['user_role'])) {
            $userRole = strtolower($_SESSION['user_role']);
        } elseif (isset($_SESSION['role'])) {
            $userRole = strtolower($_SESSION['role']);
        }

        if ($userRole !== 'admin') {
            header("Location: index.php");
            exit();
        }
    }

    private function handleActions() {
        // Handle Delete Single
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $idAbsensi = $_GET['id'];
            try {
                $query = "DELETE FROM absensi WHERE id_absensi = :id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(':id' => $idAbsensi));
                
                $redirectTanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
                $redirectKelas = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
                header("Location: absensi_rekap.php?tanggal=$redirectTanggal&kelas_id=$redirectKelas&status=deleted");
                exit();
            } catch (PDOException $e) {
                $this->alertMessage = "Gagal menghapus data: " . $e->getMessage();
                $this->alertType = "danger";
            }
        }

        // Handle Delete All By Tanggal
        if (isset($_GET['action']) && $_GET['action'] == 'delete_all' && isset($_GET['tanggal'])) {
            $tanggalTarget = $_GET['tanggal'];
            $kelasTarget = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
            try {
                if (!empty($kelasTarget)) {
                    $query = "DELETE a FROM absensi a JOIN siswa s ON a.id_siswa = s.id WHERE a.tanggal = :tanggal AND s.kelas_id = :kelas_id";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute(array(':tanggal' => $tanggalTarget, ':kelas_id' => $kelasTarget));
                } else {
                    $query = "DELETE FROM absensi WHERE tanggal = :tanggal";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute(array(':tanggal' => $tanggalTarget));
                }

                header("Location: absensi_rekap.php?tanggal=$tanggalTarget&kelas_id=$kelasTarget&status=deleted_all");
                exit();
            } catch (PDOException $e) {
                $this->alertMessage = "Gagal menghapus semua data: " . $e->getMessage();
                $this->alertType = "danger";
            }
        }

        // Handle Edit / Update Absen
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
            $idAbsensi = $_POST['absensi_id'];
            $status = $_POST['status'];
            $keterangan = $_POST['keterangan'];
            $tanggalRedirect = $_POST['current_tanggal'];
            $kelasRedirect = $_POST['current_kelas'];

            try {
                $query = "UPDATE absensi SET status = :status, keterangan = :keterangan, updated_at = NOW() WHERE id_absensi = :id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(
                    ':status' => $status,
                    ':keterangan' => $keterangan,
                    ':id' => $idAbsensi
                ));

                header("Location: absensi_rekap.php?tanggal=$tanggalRedirect&kelas_id=$kelasRedirect&status=updated");
                exit();
            } catch (PDOException $e) {
                $this->alertMessage = "Gagal memperbarui data: " . $e->getMessage();
                $this->alertType = "danger";
            }
        }

        // Notifikasi dari URL parameter
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'updated') {
                $this->alertMessage = "Data absensi berhasil diperbarui!";
                $this->alertType = "success";
            } elseif ($_GET['status'] == 'deleted') {
                $this->alertMessage = "Data absensi berhasil dihapus!";
                $this->alertType = "success";
            } elseif ($_GET['status'] == 'deleted_all') {
                $this->alertMessage = "Semua data absensi pada tanggal tersebut berhasil dihapus!";
                $this->alertType = "success";
            }
        }
    }

    private function parseFilters() {
        if (isset($_GET['tanggal'])) {
            $this->filterTanggal = $_GET['tanggal'];
        }
        if (isset($_GET['bulan'])) {
            $this->filterBulan = $_GET['bulan'];
        }
        if (isset($_GET['kelas_id'])) {
            $this->filterKelas = $_GET['kelas_id'];
        }

        if (empty($this->filterTanggal) && empty($this->filterBulan)) {
            $this->filterTanggal = $this->today;
        }

        if (!empty($this->filterTanggal)) {
            if ($this->filterTanggal > $this->today) {
                $this->filterTanggal = $this->today;
            }

            $dayOfWeek = date('N', strtotime($this->filterTanggal));
            if ($dayOfWeek == 6) {
                $this->isWeekend = true;
                $this->pesanLibur = "Tanggal " . date('d-m-Y', strtotime($this->filterTanggal)) . " adalah hari Sabtu (Hari Libur).";
            } elseif ($dayOfWeek == 7) {
                $this->isWeekend = true;
                $this->pesanLibur = "Tanggal " . date('d-m-Y', strtotime($this->filterTanggal)) . " adalah hari Minggu (Hari Libur).";
            }
        }
    }

    private function loadData() {
        $queryKelas = "SELECT * FROM kelas ORDER BY nama_kelas ASC";
        $stmtKelas = $this->db->prepare($queryKelas);
        $stmtKelas->execute();
        $this->listKelas = $stmtKelas->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($this->filterTanggal)) {
            $this->loadDataHarian();
        } else {
            $this->loadDataAkumulasi();
        }
    }

    private function loadDataHarian() {
        $query = "SELECT s.nisn, s.nama, k.nama_kelas, a.id_absensi AS absensi_id, a.status, a.keterangan
                  FROM siswa s
                  LEFT JOIN kelas k ON s.kelas_id = k.id
                  LEFT JOIN absensi a ON s.id = a.id_siswa AND a.tanggal = :tanggal
                  WHERE 1=1";
        
        $params = array(':tanggal' => $this->filterTanggal);

        if (!empty($this->filterKelas)) {
            $query .= " AND s.kelas_id = :kelas_id";
            $params[':kelas_id'] = $this->filterKelas;
        }

        $query .= " ORDER BY s.nama ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $this->rekapData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        try {
            $queryUpdated = "SELECT MAX(updated_at) AS last_update FROM absensi WHERE tanggal = :tanggal";
            $stmtUpdated = $this->db->prepare($queryUpdated);
            $stmtUpdated->execute(array(':tanggal' => $this->filterTanggal));
            $rowUpdate = $stmtUpdated->fetch(PDO::FETCH_ASSOC);
            if (isset($rowUpdate['last_update'])) {
                $this->lastUpdate = $rowUpdate['last_update'];
            }
        } catch (PDOException $e) {
            $this->lastUpdate = null;
        }
    }

    private function loadDataAkumulasi() {
        $query = "SELECT s.nisn, s.nama, k.nama_kelas,
                    SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END) AS hadir,
                    SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) AS izin,
                    SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) AS sakit,
                    SUM(CASE WHEN a.status = 'Alfa' THEN 1 ELSE 0 END) AS alfa
                  FROM siswa s
                  LEFT JOIN kelas k ON s.kelas_id = k.id
                  LEFT JOIN absensi a ON s.id = a.id_siswa";

        $conditions = array();
        $params = array();

        if (!empty($this->filterBulan)) {
            $conditions[] = "DATE_FORMAT(a.tanggal, '%Y-%m') = :bulan";
            $params[':bulan'] = $this->filterBulan;
        }

        if (!empty($this->filterKelas)) {
            $conditions[] = "s.kelas_id = :kelas_id";
            $params[':kelas_id'] = $this->filterKelas;
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " GROUP BY s.id ORDER BY s.nama ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $this->rekapData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        try {
            $queryUpdated = "SELECT MAX(updated_at) AS last_update FROM absensi";
            $stmtUpdated = $this->db->prepare($queryUpdated);
            $stmtUpdated->execute();
            $rowUpdate = $stmtUpdated->fetch(PDO::FETCH_ASSOC);
            if (isset($rowUpdate['last_update'])) {
                $this->lastUpdate = $rowUpdate['last_update'];
            }
        } catch (PDOException $e) {
            $this->lastUpdate = null;
        }
    }

    public function render() {
        include 'views/header.php';
        ?>
        <div class="container my-4">
            
            <?php if (!empty($this->alertMessage)): ?>
                <div class="alert alert-<?php echo $this->alertType; ?> alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fa-solid fa-circle-info me-2"></i> <?php echo htmlspecialchars($this->alertMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-chart-column text-primary me-2"></i>
                        Rekap Absensi Siswa 
                        <?php 
                            if (!empty($this->filterTanggal)) {
                                echo '- Tanggal ' . date('d-m-Y', strtotime($this->filterTanggal));
                            } elseif (!empty($this->filterBulan)) {
                                echo '- Bulan ' . date('F Y', strtotime($this->filterBulan . '-01'));
                            } else {
                                echo '(Keseluruhan)';
                            }
                        ?>
                    </h5>
                    
                 
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($this->pesanLibur)): ?>
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                            <i class="fa-solid fa-triangle-exclamation fa-lg me-3 text-warning"></i>
                            <div>
                                <strong>Peringatan:</strong> <?php echo htmlspecialchars($this->pesanLibur); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="GET" class="row g-3 align-items-end mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted small mb-1">Filter Harian (Tanggal):</label>
                            <input type="date" id="inputTanggal" name="tanggal" class="form-control form-control-sm" value="<?php echo htmlspecialchars($this->filterTanggal); ?>" max="<?php echo $this->today; ?>" oninput="toggleFilters()">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted small mb-1">Filter Bulanan:</label>
                            <input type="month" id="inputBulan" name="bulan" class="form-control form-control-sm" value="<?php echo htmlspecialchars($this->filterBulan); ?>" oninput="toggleFilters()">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted small mb-1">Filter Kelas:</label>
                            <select name="kelas_id" class="form-select form-select-sm">
                                <option value="">-- Semua Kelas --</option>
                                <?php foreach ($this->listKelas as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php if ($this->filterKelas == $k['id']) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($k['nama_kelas']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold flex-fill">
                                <i class="fa-solid fa-filter me-1"></i> Cari
                            </button>
                            <?php 
                                $hasFilter = false;
                                if (!empty($this->filterTanggal)) { $hasFilter = true; }
                                if (!empty($this->filterBulan)) { $hasFilter = true; }
                                if (!empty($this->filterKelas)) { $hasFilter = true; }
                                if ($hasFilter): 
                            ?>
                                <a href="absensi_rekap.php" class="btn btn-outline-secondary btn-sm flex-fill">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if ($this->lastUpdate): ?>
                        <div class="text-end mb-3">
                            <span class="badge bg-light text-secondary border px-3 py-2">
                                <i class="fa-regular fa-clock me-1 text-primary"></i> 
                                Terakhir diperbarui: <strong><?php echo date('d-m-Y H:i', strtotime($this->lastUpdate)); ?> WIB</strong>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary">
                                <?php if (!empty($this->filterTanggal)): ?>
                                    <tr>
                                        <th class="py-3 px-3" width="50">No</th>
                                        <th class="py-3" width="140">NISN</th>
                                        <th class="py-3">Nama Siswa</th>
                                        <th class="py-3" width="130">Kelas</th>
                                        <th class="py-3 text-center" width="120">Status</th>
                                        <th class="py-3">Keterangan / Bukti</th>
                                        <th class="py-3 text-center" width="110">Aksi</th>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th class="py-3 px-3" width="50">No</th>
                                        <th class="py-3" width="150">NISN</th>
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
                                <?php if (count($this->rekapData) > 0): ?>
                                    <?php $no = 1; foreach ($this->rekapData as $row): ?>
                                    <tr>
                                        <td class="px-3 fw-semibold text-muted"><?php echo $no++; ?></td>
                                        <td class="fw-semibold text-muted"><?php echo htmlspecialchars(isset($row['nisn']) ? $row['nisn'] : '-'); ?></td>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars(isset($row['nama']) ? $row['nama'] : '-'); ?></td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                                <?php echo htmlspecialchars(isset($row['nama_kelas']) ? $row['nama_kelas'] : 'Tanpa Kelas'); ?>
                                            </span>
                                        </td>

                                        <?php if (!empty($this->filterTanggal)): ?>
                                            <td class="text-center fw-bold">
                                                <?php 
                                                    $hasAbsen = !empty($row['absensi_id']);
                                                    if ($hasAbsen) {
                                                        $st = $row['status'];
                                                    } else {
                                                        $st = $this->isWeekend ? 'Libur' : 'Belum Absen';
                                                    }

                                                    $badgeClass = 'bg-secondary';
                                                    if ($st == 'Hadir') { $badgeClass = 'bg-success'; }
                                                    elseif ($st == 'Izin') { $badgeClass = 'bg-info text-dark'; }
                                                    elseif ($st == 'Sakit') { $badgeClass = 'bg-warning text-dark'; }
                                                    elseif ($st == 'Alfa') { $badgeClass = 'bg-danger'; }
                                                    elseif ($st == 'Libur') { $badgeClass = 'bg-dark'; }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $st; ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                    $ket = isset($row['keterangan']) ? $row['keterangan'] : '';
                                                    $target_folder = '';
                                                    $btn_class = 'btn-outline-primary';
                                                    $icon_class = 'fa-file-image';
                                                    $label_btn = 'Lihat Bukti';

                                                    if ($st == 'Hadir') {
                                                        $target_folder = 'uploads/selfie/';
                                                        $btn_class = 'btn-outline-success';
                                                        $icon_class = 'fa-camera';
                                                        $label_btn = 'Lihat Foto Selfie';
                                                    } elseif ($st == 'Sakit') {
                                                        $target_folder = 'uploads/surat dokter/';
                                                        $btn_class = 'btn-outline-warning text-dark';
                                                        $icon_class = 'fa-file-medical';
                                                        $label_btn = 'Lihat Surat Dokter';
                                                    } elseif ($st == 'Izin') {
                                                        $target_folder = 'uploads/surat dokter/';
                                                        $btn_class = 'btn-outline-info text-dark';
                                                        $icon_class = 'fa-envelope-open-text';
                                                        $label_btn = 'Lihat Surat Izin';
                                                    }

                                                    $full_path = $target_folder . $ket;

                                                    if (!empty($target_folder) && !empty($ket)):
                                                ?>
                                                    <button type="button" class="btn <?= $btn_class; ?> btn-sm fw-semibold" 
                                                            onclick="bukaModalFoto('<?php echo $full_path; ?>', '<?php echo $st; ?>')">
                                                        <i class="fa-solid <?= $icon_class; ?> me-1"></i> <?= $label_btn; ?>
                                                    </button>
                                                <?php else: ?>
                                                    <?= !empty($ket) ? htmlspecialchars($ket) : '-'; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($hasAbsen): ?>
                                                    <button type="button" class="btn btn-outline-primary btn-sm px-2 py-1 me-1" 
                                                        onclick="editAbsensi(<?php echo $row['absensi_id']; ?>, '<?php echo $row['status']; ?>', '<?php echo htmlspecialchars($row['keterangan'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>')"
                                                        title="Edit Absensi">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <a href="absensi_rekap.php?tanggal=<?php echo $this->filterTanggal; ?>&kelas_id=<?php echo $this->filterKelas; ?>&action=delete&id=<?php echo $row['absensi_id']; ?>" 
                                                        class="btn btn-outline-danger btn-sm px-2 py-1" 
                                                        onclick="return confirm('Yakin ingin menghapus data absensi siswa <?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>?')"
                                                        title="Hapus Absensi">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php else: ?>
                                            <td class="text-center fw-bold text-success"><?php echo $row['hadir']; ?></td>
                                            <td class="text-center fw-bold text-info"><?php echo $row['izin']; ?></td>
                                            <td class="text-center fw-bold text-warning"><?php echo $row['sakit']; ?></td>
                                            <td class="text-center fw-bold text-danger"><?php echo $row['alfa']; ?></td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <?php 
                                            if (!empty($this->filterTanggal)) {
                                                echo '<td colspan="7" class="text-center py-4 text-muted">Data absensi tidak ditemukan.</td>';
                                            } else {
                                                echo '<td colspan="8" class="text-center py-4 text-muted">Data absensi tidak ditemukan.</td>';
                                            }
                                        ?>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pop-up Pratinjau Foto/Surat -->
        <div class="modal fade" id="modalLihatBukti" tabindex="-1" aria-labelledby="modalLihatBuktiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark" id="modalLihatBuktiLabel">
                            <i id="ikonModalHeader" class="fa-solid fa-camera text-success me-2"></i> 
                            <span id="teksJudulModal">Foto Selfie Absen Mandiri</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-4 bg-white" id="isiKontenModal">
                        <!-- Gambar dimuat secara dinamis -->
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Absensi -->
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="absensi_rekap.php?tanggal=<?php echo $this->filterTanggal; ?>&kelas_id=<?php echo $this->filterKelas; ?>">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="modalEditLabel">
                                <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Absensi - <span id="editNamaSiswa"></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="absensi_id" id="editAbsensiId">
                            <input type="hidden" name="current_tanggal" value="<?php echo $this->filterTanggal; ?>">
                            <input type="hidden" name="current_kelas" value="<?php echo $this->filterKelas; ?>">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status Kehadiran:</label>
                                <select name="status" id="editStatus" class="form-select" required>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Alfa">Alfa</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Keterangan / Nama File:</label>
                                <textarea name="keterangan" id="editKeterangan" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary fw-semibold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Skrip JavaScript Pop-up Modal & Footer -->
        <script>
        function toggleFilters() {
            let inputTanggal = document.getElementById('inputTanggal');
            let inputBulan = document.getElementById('inputBulan');

            if (inputTanggal.value) {
                inputBulan.value = '';
            } else if (inputBulan.value) {
                inputTanggal.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', toggleFilters);

        function bukaModalFoto(filePath, status) {
            let container = document.getElementById('isiKontenModal');
            let titleText = document.getElementById('teksJudulModal');
            let iconHeader = document.getElementById('ikonModalHeader');
            let ext = filePath.split('.').pop().toLowerCase();

            if (status === 'Sakit') {
                titleText.textContent = 'Surat Keterangan Dokter';
                iconHeader.className = 'fa-solid fa-file-medical text-warning me-2';
            } else if (status === 'Izin') {
                titleText.textContent = 'Surat Bukti Izin';
                iconHeader.className = 'fa-solid fa-envelope-open-text text-info me-2';
            } else if (status === 'Hadir') {
                titleText.textContent = 'Foto Selfie Absen Mandiri';
                iconHeader.className = 'fa-solid fa-camera text-success me-2';
            } else {
                titleText.textContent = 'Pratinjau Bukti Absensi';
                iconHeader.className = 'fa-solid fa-file-image text-primary me-2';
            }

            if (ext === 'pdf') {
                container.innerHTML = `<iframe src="${filePath}" width="100%" height="450px" style="border:none;"></iframe>`;
            } else {
                container.innerHTML = `<img src="${filePath}" class="img-fluid rounded shadow-sm" style="max-height: 450px;" alt="Bukti Absensi">`;
            }

            let myModal = new bootstrap.Modal(document.getElementById('modalLihatBukti'));
            myModal.show();
        }

        function editAbsensi(id, status, keterangan, nama) {
            document.getElementById('editAbsensiId').value = id;
            document.getElementById('editStatus').value = status;
            document.getElementById('editKeterangan').value = keterangan;
            document.getElementById('editNamaSiswa').textContent = nama;

            let editModal = new bootstrap.Modal(document.getElementById('modalEdit'));
            editModal.show();
        }
        </script>
        <?php 
        // Sertakan footer agar skrip JavaScript Bootstrap (bootstrap.bundle.min.js) ikut termuat
        include 'views/footer.php';
    }
}

// Inisialisasi dan jalankan class
$rekapManager = new AbsensiRekapManager();$rekapManager->render();