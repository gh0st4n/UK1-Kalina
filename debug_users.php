<?php
require_once 'classes/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Daftar Pengguna di Tabel 'users'</h2>";

$stmt = $db->query("SELECT u.id, u.username, u.role, u.siswa_id, s.nama FROM users u LEFT JOIN siswa s ON u.siswa_id = s.id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    echo "<p style='color:red;'>Tabel users kosong!</p>";
} else {
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr><th>ID User</th><th>Username</th><th>Role</th><th>Siswa ID</th><th>Nama Siswa (Relasi)</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>" . $u['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($u['username']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($u['role']) . "</td>";
        echo "<td>" . ($u['siswa_id'] ?? 'null') . "</td>";
        echo "<td>" . htmlspecialchars($u['nama'] ?? 'Tidak Terhubung') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>