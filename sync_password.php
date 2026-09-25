<?php
require_once 'classes/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Ambil semua user dengan role 'siswa'
    $query = "SELECT id, username FROM users WHERE role = 'siswa'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach ($users as $user) {
        $username = trim($user['username']);
        
        // Buat BCRYPT Hash resmi yang dikenali oleh password_verify()
        $hash_password = password_hash($username, PASSWORD_BCRYPT);

        // Update password ke database
        $update = "UPDATE users SET password = :password WHERE id = :id";
        $stmt_update = $db->prepare($update);
        $stmt_update->bindParam(':password', $hash_password);
        $stmt_update->bindParam(':id', $user['id']);
        
        if ($stmt_update->execute()) {
            $count++;
        }
    }

    echo "<h3>✅ Berhasil memperbarui $count password siswa!</h3>";
    echo "<p>Sekarang semua siswa bisa login dengan Username & Password berupa NISN masing-masing.</p>";
    echo "<a href='login.php'>Kembali ke Login</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>