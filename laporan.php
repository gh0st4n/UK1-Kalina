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

// Ambil daftar semua kelas untuk dropdown filter
$query_kelas_all = "SELECT * FROM kelas ORDER BY nama_kelas ASC";
$stmt_kelas_all = $db->prepare($query_kelas_all);
$stmt_kelas_all->execute();
$list_kelas = $stmt_kelas_all->fetchAll(PDO::FETCH_ASSOC);

// Filter Berdasarkan Kelas
$kelas_id = $_GET['kelas_id'] ?? '';

$query = "SELECT siswa.*, kelas.nama_kelas, kelas.wali_kelas 
          FROM siswa 
          LEFT JOIN kelas ON siswa.kelas_id = kelas.id";

if (!empty($kelas_id)) {
    $query .= " WHERE siswa.kelas_id = :kelas_id";
}

$query .= " ORDER BY siswa.nama ASC";

$stmt = $db->prepare($query);
if (!empty($kelas_id)) {
    $stmt->bindParam(':kelas_id', $kelas_id);
}
$stmt->execute();
$data_siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'views/header.php';
?>

<!-- CSS Khusus Mode Cetak (Print) -->
<style>
@page {
    /* Menghilangkan header & footer otomatis browser (seperti title & URL) */
    margin-top: 1.5cm;
    margin-bottom: 1.5cm;
}

@media print {
    /* Sembunyikan elemen navigasi, header, footer, & tombol cetak saat diprint */
    .no-print, header, footer, .navbar, .btn, form {
        display: none !important;
    }
    body {
        background-color: #fff !important;
        font-size: 12pt;
    }
    .container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table-bordered th, .table-bordered td {
        border: 1px solid #000 !important;
    }
}
</style>

<div class="container my-4">
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center p-3 mb-4 bg-white rounded-3 shadow-sm border-start border-4 border-primary no-print">
        <div>
            <h4 class="fw-bold mb-1 text-primary">
                <i class="fa-solid fa-file-invoice text-primary me-2"></i>Laporan Data Siswa
            </h4>
            <p class="text-muted mb-0 small">Cetak dan filter rekapitulasi data siswa per kelas</p>
        </div>
        <div>
            <button onclick="window.print();" class="btn btn-success px-3 shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter Form (no-print) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 no-print">
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Filter Kelas</label>
                    <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($list_kelas as $k): ?>
                            <option value="<?= $k['id']; ?>" <?= ($kelas_id == $k['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end mt-4">
                    <?php if (!empty($kelas_id)): ?>
                        <a href="laporan.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-rotate-left me-1"></i> Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- KOP Laporan Saat Dicetak (Hanya Tampil di Cetak) -->
    <div class="d-none d-print-block text-center mb-4 border-bottom pb-3">
        <h3 class="fw-bold text-uppercase mb-1">LAPORAN DATA SISWA</h3>
        <h5 class="fw-bold mb-0">SMK NEGERI 3 BANJAR</h5>
    </div>

    <!-- Area Tabel Laporan -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th width="50" class="text-center py-2">No</th>
                            <th width="120" class="py-2">NISN</th>
                            <th class="py-2">Nama Lengkap</th>
                            <th width="150" class="py-2">Kelas</th>
                            <th class="py-2">Wali Kelas</th>
                            <th class="py-2">Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_siswa) > 0): ?>
                            <?php $no = 1; foreach ($data_siswa as $row): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nisn'] ?? '-'); ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['nama_kelas'] ?? 'Belum Ada Kelas'); ?></td>
                                    <td><?= htmlspecialchars($row['wali_kelas'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Tidak ada data siswa yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Lembar Tanda Tangan (Hanya Tampil Saat Cetak / Print) -->
    <div class="d-none d-print-block mt-5 pt-4">
        <div class="row">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p class="mb-1">Banjar, <?= date('d F Y'); ?></p>
                <p class="mb-5">Kepala Sekolah / Admin,</p>
                <br><br>
                <p class="fw-bold text-decoration-underline mb-0">( ____________________ )</p>
                <p class="text-muted small">NIP. ....................................</p>
            </div>
        </div>
    </div>

    <!-- Navigasi Kembali (no-print) -->
    <div class="mt-4 no-print">
        <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-2"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
    </div>
</div>

<?php include 'views/footer.php'; ?>