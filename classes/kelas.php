<?php
class Kelas {
    private $conn;
    private $table_name = "kelas";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ambil semua data kelas
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil 1 data kelas berdasarkan ID
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tambah kelas baru
    public function create($nama_kelas, $wali_kelas) {
        $query = "INSERT INTO " . $this->table_name . " (nama_kelas, wali_kelas) VALUES (:nama_kelas, :wali_kelas)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_kelas', $nama_kelas);
        $stmt->bindParam(':wali_kelas', $wali_kelas);
        return $stmt->execute();
    }

    // Update data kelas
    public function update($id, $nama_kelas, $wali_kelas) {
        $query = "UPDATE " . $this->table_name . " SET nama_kelas = :nama_kelas, wali_kelas = :wali_kelas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_kelas', $nama_kelas);
        $stmt->bindParam(':wali_kelas', $wali_kelas);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Hapus data kelas
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>