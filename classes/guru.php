<?php
require_once __DIR__ . '/database.php';

class Guru {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Ambil semua data guru
    public function getAllGuru() {
        $query = "SELECT * FROM guru ORDER BY id DESC";
        return $this->db->query($query);
    }

    // Ambil detail satu guru berdasarkan ID
    public function getGuruById($id) {
        $query = "SELECT * FROM guru WHERE id = :id";
        return $this->db->single($query, ['id' => $id]);
    }

    // Tambah Data Guru
    public function tambahGuru($nama_guru, $mapel) {
        $query = "INSERT INTO guru (nama_guru, mapel) VALUES (:nama_guru, :mapel)";
        return $this->db->execute($query, [
            'nama_guru' => $nama_guru,
            'mapel'     => $mapel
        ]);
    }

    // Edit Data Guru
    public function updateGuru($id, $nama_guru, $mapel) {
        $query = "UPDATE guru SET nama_guru = :nama_guru, mapel = :mapel WHERE id = :id";
        return $this->db->execute($query, [
            'id'        => $id,
            'nama_guru' => $nama_guru,
            'mapel'     => $mapel
        ]);
    }

    // Hapus Data Guru
    public function hapusGuru($id) {
        $query = "DELETE FROM guru WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }
}