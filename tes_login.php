<?php
require_once 'classes/database.php';

$database = new Database();
$db = $database->getConnection();

$username = '00512345613';
$password_input = '00512345613';

// 1. Cek Apakah Data User Ada
$query = "SELECT * FROM users WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>1. Hasil Pencarian Username:</h3>";
if (!$user) {
    echo "<p style='color:red;'><b>Error:</b> Username '$username' TIDAK DITEMUKAN di tabel users!</p>";
    exit();
} else {
    echo "<pre>";
    print_r($user);
    echo "</pre>";
}

// 2. Cek Ukuran Hash Password
$hash_db = $user['password'];
echo "<h3>2. Analisis Password Hash:</h3>";
echo "Panjang hash di DB: " . strlen($hash_db) . " karakter (Harus 60 karakter)<br>";

// 3. Update Hash Baru
$hash_baru = password_hash($password_input, PASSWORD_BCRYPT);
$update = $db->prepare("UPDATE users SET password = :p WHERE id = :id");
$update->execute([':p' => $hash_baru, ':id' => $user['id']]);

// 4. Verifikasi Ulang
if (password_verify($password_input, $hash_baru)) {
    echo "<p style='color:green;'><b>SUKSES:</b> Password '00512345613' BERHASIL diverifikasi!</p>";
} else {
    echo "<p style='color:red;'><b>GAGAL:</b> Password verify gagal.</p>";
}
?>