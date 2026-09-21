<?php
class Siswa {
    /**
     * @var PDO
     */
    private $conn;
    private $table_name = "siswa";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read Data Siswa (Mendukung Pencarian dan Alias Wali Kelas/Guru)
    public function read($search = '') {
        $query = "SELECT s.*, k.nama_kelas, k.wali_kelas 
                  FROM " . $this->table_name . " s
                  LEFT JOIN kelas k ON s.kelas_id = k.id";

        if (!empty($search)) {
            $query .= " WHERE s.nisn LIKE :search 
                         OR s.nama LIKE :search 
                         OR k.nama_kelas LIKE :search";
        }

        $query .= " ORDER BY s.id DESC";

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindValue(':search', $searchTerm);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read One Data Siswa
    public function readOne($id) {
        $query = "SELECT s.*, k.nama_kelas, k.wali_kelas 
                  FROM " . $this->table_name . " s
                  LEFT JOIN kelas k ON s.kelas_id = k.id 
                  WHERE s.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil Data Kelas untuk Dropdown Form
    public function getKelas() {
        $query = "SELECT * FROM kelas ORDER BY nama_kelas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tambah Data Siswa (Create)
    public function create($nisn, $nama, $kelas_id, $alamat, $foto = 'default.png') {
        // Cek duplikasi NISN
        $checkQuery = "SELECT id FROM " . $this->table_name . " WHERE nisn = :nisn";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindValue(':nisn', $nisn);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            return false; // NISN sudah ada
        }

        $query = "INSERT INTO " . $this->table_name . " (nisn, nama, kelas_id, alamat, foto) 
                  VALUES (:nisn, :nama, :kelas_id, :alamat, :foto)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':nisn', $nisn);
            $stmt->bindValue(':nama', $nama);
            $stmt->bindValue(':kelas_id', $kelas_id);
            $stmt->bindValue(':alamat', $alamat);
            $stmt->bindValue(':foto', $foto);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Update Data Siswa
    public function update($id, $nisn, $nama, $kelas_id, $alamat, $foto = null) {
        // Cek duplikasi NISN pada ID lain
        $checkQuery = "SELECT id FROM " . $this->table_name . " WHERE nisn = :nisn AND id != :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindValue(':nisn', $nisn);
        $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            return "duplicate_nisn";
        }

        if ($foto) {
            $query = "UPDATE " . $this->table_name . " 
                      SET nisn = :nisn, nama = :nama, kelas_id = :kelas_id, alamat = :alamat, foto = :foto 
                      WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table_name . " 
                      SET nisn = :nisn, nama = :nama, kelas_id = :kelas_id, alamat = :alamat 
                      WHERE id = :id";
        }

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':nisn', $nisn);
            $stmt->bindValue(':nama', $nama);
            $stmt->bindValue(':kelas_id', $kelas_id);
            $stmt->bindValue(':alamat', $alamat);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            if ($foto) {
                $stmt->bindValue(':foto', $foto);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Hapus Data Siswa (Delete)
    public function delete($id) {
        // Hapus file foto fisik jika bukan default.png
        $siswa = $this->readOne($id);
        if ($siswa && !empty($siswa['foto']) && $siswa['foto'] !== 'default.png') {
            $file_path = './uploads/' . $siswa['foto'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}