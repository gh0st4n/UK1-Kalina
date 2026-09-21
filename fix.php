<?php
require_once 'classes/database.php';

$database = new Database();
$db = $database->getConnection();

// Hash Bcrypt yang diproduksi LANGSUNG oleh server PHP lokal kamu
$passAdmin = password_hash('kalinadmin08', PASSWORD_BCRYPT);
$passUser  = password_hash('user99887711', PASSWORD_BCRYPT);

try {
    // 1. Pastikan ukuran kolom password cukup (255 karakter)
    $db->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL");

    // 2. Update password admin
    $stmt1 = $db->prepare("UPDATE users SET password = :pass WHERE username = 'admin'");
    $stmt1->execute([':pass' => $passAdmin]);

    // 3. Update password user
    $stmt2 = $db->prepare("UPDATE users SET password = :pass WHERE username = 'user'");
    $stmt2->execute([':pass' => $passUser]);

    echo "<h2 style='color:green;'>SUCCESS! Database berhasil diperbarui dengan Bcrypt resmi dari server kamu.</h2>";
    echo "<p>Password Admin: <b>kalinadmin08</b></p>";
    echo "<p>Password User: <b>user99887711</b></p>";
    echo "<a href='login.php'>Kembali ke Halaman Login</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>