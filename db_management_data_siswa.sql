-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 16 Sep 2026 pada 02.31
-- Versi server: 8.0.30
-- Versi PHP: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_management_data_siswa`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id` int NOT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `mapel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id`, `nama_guru`, `mapel`) VALUES
(1, 'Valen, S.Kom', 'Web Security'),
(2, 'Endrik Arif Sahrizal, S.Pd', 'Pemrograman Web'),
(3, 'Bambang Sugianto, S.Kom.', 'Pemrograman Web'),
(4, 'Siti Rahmawati, M.T.', 'Basdat (Database)'),
(5, 'Eko Prasetyo, S.Pd.', 'Pemrograman Berorientasi Objek');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `wali_kelas` varchar(100) DEFAULT NULL,
  `guru_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `wali_kelas`, `guru_id`) VALUES
(1, 'X PPLG 1', 'Ibu Dewi S.Pd', NULL),
(2, 'X PPLG 2', 'Ibu Diah S.Pd', NULL),
(3, 'X PPLG 3', NULL, NULL),
(4, 'XI PPLG 1', 'Ibu Dedeh S.Pd.', NULL),
(5, 'XI PPLG 2', 'Bapak Dian S.Kom.', NULL),
(6, 'XI PPLG 3', 'Ibu Tresna S.Pd.', NULL),
(7, 'XII PPLG 1', 'Bapak Wahidin S.Pd.', NULL),
(8, 'XII PPLG 2', 'Bapak Endrik Arif Sahrizal S.Pd.', NULL),
(9, 'XII PPLG 3', 'Bapak Rian Herni S.Pd.', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id` int NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas_id` int DEFAULT NULL,
  `alamat` text,
  `foto` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `nisn`, `nama`, `kelas_id`, `alamat`, `foto`) VALUES
(6, '00512345634', 'Zeti Sifaun Nazwa', 8, 'Tarisi', '5c7c3cab284f784a08c4edf01942a37d.png'),
(7, '00512345633', 'Winda Afriani', 7, 'Langensari', 'ce01115f4f98ea093fd509046fb1b456.jpg'),
(8, '00512345632', 'Vina Nuraini', 7, 'Lakbok', 'b837f3404e78e111b4fd75741d7a6285.jpg'),
(9, '00512345631', 'Via Nur Oktaviani', 7, 'Langensari', 'b8f19234bb033b1a6bb0645cbc181d44.png'),
(10, '00512345630', 'Thalita Purnawati', 6, 'Wanareja', '1b450deec089582a665ae3e85e4f5d5e.jpeg'),
(11, '00512345629', 'Rumdani Afifaturrahmi', 6, 'Tangkeban', '250082325e068830a693971758541954.png'),
(12, '00512345628', 'Rizki Adit Setiawan', 6, 'Lakbok', 'cdc14326c06cfd2e1611dff0804cb576.jpg'),
(14, '00512345627', 'Paradisa Klara Ependi', 5, 'Langensari', 'af9cf171086b492903c0fea27c8e1845.jpg'),
(15, '00512345626', 'Oryza Raja Alzura', 5, 'Langensari', 'ba6197bf7467607be080d9826c8f672c.jpg'),
(16, '00512345625', 'Nada Alma Delia', 5, 'Sasagaran', 'e55209c72f4e9d52f6c326d5d06000e0.png'),
(17, '00512345624', 'Muntias Ramdani', 4, 'Cikawung', '106c3f4cc1c88aa9dd50107edd0a122b.png'),
(18, '00512345623', 'Lisna Rinjani', 4, 'Tarisi', '7616c863f24331391d1a8978bb718abd.png'),
(19, '00512345622', 'Kalinna Rizki Riah', 8, 'Lakbok', 'e519dd68c7733e268bdede08fd4abf45.jpg'),
(20, '00512345621', 'Janika Tobing', 3, '', '9452ff378482bfda968768b821179867.png'),
(21, '00512345620', 'Indra Hermawan', 3, 'Lakbok', '6127b976c49acee7f155432a9d63210c.png'),
(22, '00512345619', 'Fuzi Rahmawati', 3, 'Mergo', 'e4f9d327ea16725bcb6d9c754f462f05.jpg'),
(26, '00512345616', 'Desi Nopitasari', 2, 'Langensari', 'f2e47d703d3978b5b7eec031737e4627.jpg'),
(27, '00512345615', 'Azril Nursihab Nafis Ramadhan', 1, '', '5133eee0707cb10e39d582ea6a9f4858.png'),
(28, '00512345614', 'Arga Setyawan', 1, 'Lakbok', '1c2f7b79a39c0818d05ed93f9d4fb87d.jpg'),
(29, '00512345613', 'Afgansyah Ibrahim', 1, '', '4b36dc4e7a76bdf28422d90f6a36e0b5.jpg'),
(32, '00512345612', 'Lista Ateu Ambarianti', 7, 'Langensari', '276d5660cbb3f5a11024dff04878b9e0.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', 'd7caed25e5bf33da4e752d774afed033', 'Administrator Utama', 'admin', '2026-09-14 03:53:11'),
(2, 'user', 'f0bdd8a9ebdae53c6a61907a4333c9e9', 'Siswa / User', 'user', '2026-09-14 03:53:11');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
