<?php
require_once 'classes/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // 1. Ambil semua akun role 'siswa'
    $stmt = $db->query("SELECT id, username FROM users WHERE role = 'siswa'");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach ($users as $u) {
        $username = trim($u['username']);
        
        // Buat hash dengan PASSWORD_DEFAULT
        $hash = password_hash($username, PASSWORD_DEFAULT);

        $update = $db->prepare("UPDATE users SET password = :pass WHERE id = :id");
        $update->execute([
            ':pass' => $hash,
            ':id'   => $u['id']
        ]);
        $count++;
    }

    echo "<h3>✅ Berhasil Update Password untuk $count Siswa!</h3>";
    echo "<hr>";

    // 2. TES UJI COBA LANGSUNG UNTUK SATU SISWA (Arga Setyawan)
    $tes_nisn = '00512345614';
    $cek = $db->prepare("SELECT * FROM users WHERE username = :u");
    $cek->execute([':u' => $tes_nisn]);
    $user_tes = $cek->fetch(PDO::FETCH_ASSOC);

    if ($user_tes) {
        echo "<b>Hasil Pengujian Akun Test ($tes_nisn):</b><br>";
        echo "Username: " . $user_tes['username'] . "<br>";
        echo "Hash di DB: " . $user_tes['password'] . "<br>";
        echo "Panjang Hash: " . strlen($user_tes['password']) . " karakter<br>";

        // Uji fungsi password_verify
        if (password_verify($tes_nisn, $user_tes['password'])) {
            echo "<h2 style='color:green;'>🎉 KONEKSI HASH COCOK (MATCH)!</h2>";
            echo "<p>Sekarang silakan buka <a href='login.php'>Halaman Login</a> dan masuk dengan:</p>";
            echo "<ul><li><b>Username:</b> 00512345614</li><li><b>Password:</b> 00512345614</li></ul>";
        } else {
            echo "<h2 style='color:red;'>❌ HASH TETAP TIDAK COCOK!</h2>";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>