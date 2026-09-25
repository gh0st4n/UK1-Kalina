<?php
require_once 'classes/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // 1. Ambil semua data siswa dari tabel 'siswa'
    $query_siswa = "SELECT id, nisn, nama FROM siswa";
    $stmt_siswa = $db->prepare($query_siswa);
    $stmt_siswa->execute();
    $daftar_siswa = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

    $berhasil = 0;
    $dilewati = 0;

    foreach ($daftar_siswa as $siswa) {
        $nisn = trim($siswa['nisn']);
        $nama = $siswa['nama'];
        $siswa_id = $siswa['id'];

        // Cek apakah NISN sudah ada di tabel users
        $cek_user = $db->prepare("SELECT id FROM users WHERE username = :username");
        $cek_user->execute([':username' => $nisn]);

        if ($cek_user->rowCount() == 0) {
            // Jika belum ada, buatkan akun baru (Password = NISN)
            $password_hash = password_hash($nisn, PASSWORD_BCRYPT);
            
            $insert = "INSERT INTO users (username, password, role, nama_lengkap, siswa_id) 
                       VALUES (:username, :password, 'siswa', :nama_lengkap, :siswa_id)";
            $stmt_insert = $db->prepare($insert);
            $stmt_insert->execute([
                ':username'     => $nisn,
                ':password'     => $password_hash,
                ':nama_lengkap' => $nama,
                ':siswa_id'     => $siswa_id
            ]);

            $berhasil++;
        } else {
            $dilewati++;
        }
    }

    echo "<h2>🎉 Proses Selesai!</h2>";
    echo "<p>✅ Berhasil membuat <strong>$berhasil</strong> akun siswa baru.</p>";
    echo "<p>ℹ️ Ditemukan <strong>$dilewati</strong> akun yang sudah ada sebelumnya (seperti akun Afgan).</p>";
    echo "<hr>";
    echo "<p>Sekarang semua siswa bisa login dengan:</p>";
    echo "<ul><li><strong>Username:</strong> NISN Masing-masing</li><li><strong>Password:</strong> NISN Masing-masing</li></ul>";
    echo "<br><a href='login.php' style='padding: 10px 15px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px;'>Coba Login Sekarang</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>