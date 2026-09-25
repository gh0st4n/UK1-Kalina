<?php

class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $username = trim($username);

        // Join tabel users dengan siswa dan kelas berdasarkan siswa_id
        $query = "SELECT u.*, 
                         s.nama AS nama_siswa, 
                         s.nisn, 
                         s.foto, 
                         k.nama_kelas 
                  FROM users u
                  LEFT JOIN siswa s ON u.siswa_id = s.id
                  LEFT JOIN kelas k ON s.kelas_id = k.id
                  WHERE u.username = :username 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifikasi password hash
            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'] ?? $user['username'];
                $_SESSION['user_role'] = $user['role'] ?? 'user';
                $_SESSION['siswa_id'] = $user['siswa_id'] ?? null;

                // Simpan detail data siswa jika akun memiliki relasi siswa_id
                if (!empty($user['siswa_id'])) {
                    $_SESSION['siswa_data'] = [
                        'nama'       => $user['nama_siswa'],
                        'nisn'       => $user['nisn'],
                        'foto'       => $user['foto'],
                        'nama_kelas' => $user['nama_kelas']
                    ];
                } else {
                    unset($_SESSION['siswa_data']);
                }

                return true;
            }
        }

        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getRole() {
        return $_SESSION['user_role'] ?? 'user';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = array();
        session_destroy();

        return true;
    }
}