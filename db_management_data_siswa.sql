-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 25 Sep 2026 pada 02.52
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
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int NOT NULL,
  `id_siswa` int NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` enum('Hadir','Izin','Sakit','Alfa') NOT NULL,
  `foto_selfie` varchar(255) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `keterangan` text,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `id_siswa`, `tanggal`, `jam_masuk`, `jam_keluar`, `status`, `foto_selfie`, `latitude`, `longitude`, `keterangan`, `updated_at`) VALUES
(797, 18, '2026-09-23', '12:14:28', NULL, 'Hadir', 'selfie_18_1790140468.jpg', '-7.794048', '110.318779', 'selfie_18_1790140468.jpg', '2026-09-23 12:14:28'),
(798, 32, '2026-09-23', '12:16:22', NULL, 'Hadir', 'selfie_32_1790140582.jpg', '-7.793731252928924', '110.31880535798358', 'selfie_32_1790140582.jpg', '2026-09-23 12:16:22'),
(799, 39, '2026-09-23', '12:17:47', NULL, 'Hadir', 'selfie_39_1790140667.jpg', '-7.793731252928924', '110.31880535798358', 'selfie_39_1790140667.jpg', '2026-09-23 12:17:47'),
(800, 21, '2026-09-23', '12:19:00', NULL, 'Hadir', 'selfie_21_1790140740.jpg', '-7.793731252928924', '110.31880535798358', 'selfie_21_1790140740.jpg', '2026-09-23 12:19:00'),
(801, 14, '2026-09-23', '12:20:01', NULL, 'Hadir', 'selfie_14_1790140801.jpg', '-7.793731252928924', '110.31880535798358', 'selfie_14_1790140801.jpg', '2026-09-23 12:20:01'),
(802, 6, '2026-09-23', '12:20:54', NULL, 'Hadir', 'selfie_6_1790140854.jpg', '-7.793965150533138', '110.31891321722769', 'selfie_6_1790140854.jpg', '2026-09-23 12:20:54'),
(803, 10, '2026-09-23', '12:22:10', NULL, 'Hadir', 'selfie_10_1790140930.jpg', '-7.793965150533138', '110.31891321722769', 'selfie_10_1790140930.jpg', '2026-09-23 12:22:10'),
(804, 11, '2026-09-23', '12:49:55', NULL, 'Izin', NULL, NULL, NULL, 'izin_11_1790142595.png', '2026-09-23 12:49:55'),
(806, 19, '2026-09-23', '13:35:19', NULL, 'Hadir', 'selfie_19_1790145319.jpg', '-7.7940433804400975', '110.3189706435208', 'selfie_19_1790145319.jpg', '2026-09-23 13:35:19'),
(807, 17, '2026-09-23', '15:04:12', NULL, 'Hadir', 'selfie_17_1790150652.jpg', '-7.793982523661898', '110.31885698238973', 'selfie_17_1790150652.jpg', '2026-09-23 15:04:12'),
(808, 19, '2026-09-25', '09:50:37', NULL, 'Izin', NULL, NULL, NULL, 'izin_19_1790304637.png', '2026-09-25 09:50:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id` int NOT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `mapel` varchar(50) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id`, `nama_guru`, `mapel`, `updated_at`) VALUES
(1, 'Lisna Rinjani', 'Pemrograman Berorientasi Objek', '2026-09-22 05:30:07'),
(2, 'Ibu Diah S.Pd.', 'Database', '2026-09-21 04:38:12'),
(3, 'Bapak Asep S.Kom.', 'Pemrograman Web', '2026-09-21 04:35:42'),
(4, 'Ibu Dedeh S.Pd.', 'B. Inggris', '2026-09-22 05:20:51'),
(5, 'Bapak Dian S.Kom.', 'Web Security', '2026-09-21 04:35:42'),
(6, 'Ibu Tresna S.Pd.', 'PJOK', '2026-09-22 05:20:38'),
(7, 'Bapak Wahidin S.Pd.', 'Pendidikan Agama', '2026-09-22 05:22:44'),
(8, 'Bapak Endrik Arif Sahrizal S.Pd.', 'Bahasa Indonesia', '2026-09-22 05:19:03'),
(9, 'Bapak Rian Heri S.Pd.', 'Desain UI / UX', '2026-09-22 05:23:10'),
(10, 'Lista Ateu Ambarianti', 'Matematika', '2026-09-21 04:35:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `wali_kelas` varchar(100) DEFAULT NULL,
  `guru_id` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `wali_kelas`, `guru_id`, `updated_at`) VALUES
(1, 'X PPLG 1', 'Ibu Dewi S.Pd', NULL, '2026-09-25 02:37:48'),
(2, 'X PPLG 2', 'Ibu Diah S.Pd', NULL, '2026-09-22 04:10:46'),
(3, 'X PPLG 3', 'Bapak Asep S.Kom', NULL, '2026-09-22 04:10:46'),
(4, 'XI PPLG 1', 'Ibu Dedeh S.Pd.', 4, '2026-09-22 04:09:38'),
(5, 'XI PPLG 2', 'Bapak Dian S.Kom.', 5, '2026-09-22 04:09:38'),
(6, 'XI PPLG 3', 'Ibu Tresna S.Pd.', 6, '2026-09-22 04:09:38'),
(7, 'XII PPLG 1', 'Bapak Wahidin S.Pd.', 7, '2026-09-22 04:09:38'),
(8, 'XII PPLG 2', 'Bapak Endrik Arif Sahrizal S.Pd.', 8, '2026-09-22 04:09:38'),
(9, 'XII PPLG 3', 'Bapak Rian Heri S.Pd.', 9, '2026-09-22 04:09:38');

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
  `foto` varchar(255) DEFAULT 'default.png',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `nisn`, `nama`, `kelas_id`, `alamat`, `foto`, `updated_at`) VALUES
(6, '00512345634', 'Zeti Sifaun Nazwa', 8, 'Tarisi', '5c7c3cab284f784a08c4edf01942a37d.png', '2026-09-21 11:22:53'),
(7, '00512345633', 'Winda Afriani', 9, 'Langensari', 'ce01115f4f98ea093fd509046fb1b456.jpg', '2026-09-21 11:22:53'),
(8, '00512345632', 'Vina Nuraini', 9, 'Lakbok', 'b837f3404e78e111b4fd75741d7a6285.jpg', '2026-09-21 11:22:53'),
(10, '00512345630', 'Thalita Purnawati', 6, 'Wanareja', '1b450deec089582a665ae3e85e4f5d5e.jpeg', '2026-09-21 11:22:53'),
(11, '00512345629', 'Rumdani Afifaturrahmi', 6, 'Tangkeban', '250082325e068830a693971758541954.png', '2026-09-21 11:22:53'),
(12, '00512345628', 'Rizki Adit Setiawan', 6, 'Lakbok', 'cdc14326c06cfd2e1611dff0804cb576.jpg', '2026-09-21 11:22:53'),
(14, '00512345627', 'Paradisa Klara Ependi', 5, 'Langensari', 'af9cf171086b492903c0fea27c8e1845.jpg', '2026-09-21 11:22:53'),
(15, '00512345626', 'Oryza Raja Alzura', 5, 'Langensari', 'ba6197bf7467607be080d9826c8f672c.jpg', '2026-09-21 11:22:53'),
(16, '00512345625', 'Nada Alma Delia', 5, 'Sasagaran', 'e55209c72f4e9d52f6c326d5d06000e0.png', '2026-09-21 11:22:53'),
(17, '00512345624', 'Muntias Ramdani', 4, 'Cikawung', '106c3f4cc1c88aa9dd50107edd0a122b.png', '2026-09-21 11:22:53'),
(18, '00512345623', 'Lisna Rinjani', 4, 'Tarisi', '7616c863f24331391d1a8978bb718abd.png', '2026-09-21 11:22:53'),
(19, '00512345622', 'Kalinna Rizki Riah', 8, 'Lakbok', 'e519dd68c7733e268bdede08fd4abf45.jpg', '2026-09-21 11:22:53'),
(20, '00512345621', 'Janika Tobing', 3, 'Jawa Tengah', '9452ff378482bfda968768b821179867.png', '2026-09-21 11:22:53'),
(21, '00512345620', 'Indra Hermawan', 3, 'Lakbok', '6127b976c49acee7f155432a9d63210c.png', '2026-09-21 11:22:53'),
(22, '00512345619', 'Fuzi Rahmawati', 3, 'Mergo', 'e4f9d327ea16725bcb6d9c754f462f05.jpg', '2026-09-21 11:22:53'),
(26, '00512345616', 'Desi Nopitasari', 2, 'Langensari', 'f2e47d703d3978b5b7eec031737e4627.jpg', '2026-09-21 11:22:53'),
(27, '00512345615', 'Azril Nursihab Nafis Ramadhan', 1, 'Langensari', '5133eee0707cb10e39d582ea6a9f4858.png', '2026-09-21 11:22:53'),
(28, '00512345614', 'Arga Setyawan', 1, 'Lakbok', '1c2f7b79a39c0818d05ed93f9d4fb87d.jpg', '2026-09-21 11:22:53'),
(29, '00512345613', 'Afgansyah Ibrahim', 1, 'Langensari', '4b36dc4e7a76bdf28422d90f6a36e0b5.jpg', '2026-09-21 11:25:56'),
(32, '00512345612', 'Lista Ateu Ambarianti', 7, 'Langensari', '276d5660cbb3f5a11024dff04878b9e0.jpeg', '2026-09-21 11:22:53'),
(39, '005123456139', 'Diki Agustian', 8, 'puloerang', '1789817894_6aae7426498e5.png', '2026-09-21 11:22:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'siswa',
  `siswa_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `siswa_id`, `created_at`) VALUES
(1, 'admin', '$2y$10$FBF7ta/BH1cnAVc8vN4TPuSq0JGUYpJU9WSDa6AVOu48TCcNUGSNK', 'Administrator Utama', 'admin', NULL, '2026-09-14 03:53:11'),
(2, 'user', '$2y$10$YUbQwlMuDAnDWlZCbXUC3ub1mSzS.rwUrX51GQPLPZCj1Q6VDLMDm', 'Siswa / User', 'user', NULL, '2026-09-14 03:53:11'),
(5, '00512345634', '$2y$10$9l38eI4AZxSMcav0qGDIkuc/AhJP8lyRIusETLeDufE11WnPXGQl6', 'Zeti Sifaun Nazwa', 'siswa', 6, '2026-09-22 08:20:40'),
(6, '00512345633', '$2y$10$Ge.hb945iaBqc/RJGhlREOg7YdUsVx5EW2ZTQGKg/2lciYgHxgp9e', 'Winda Afriani', 'siswa', 7, '2026-09-22 08:20:40'),
(7, '00512345632', '$2y$10$jUUelKGPvikOrpXXRON.ROnQYUEiLDlZlXZFhEdtd4kNP1uOlyKZ.', 'Vina Nuraini', 'siswa', 8, '2026-09-22 08:20:40'),
(8, '00512345630', '$2y$10$4OIqWhP4XKj2pFY6R/xBv.9T98/q/0vsqswkSoY7uuAzZp1UHHXza', 'Thalita Purnawati', 'siswa', 10, '2026-09-22 08:20:40'),
(9, '00512345629', '$2y$10$cgvukkDNLEjlUcuSdQAkkuwe0.p60pxN54dzW7S/4QUoR4W9Q9lrO', 'Rumdani Afifaturrahmi', 'siswa', 11, '2026-09-22 08:20:40'),
(10, '00512345628', '$2y$10$wkOnFFDXMexcDZk.xy3jIeAkFh4UFuSAoglXrNEVUL8ur7XJ9LAfS', 'Rizki Adit Setiawan', 'siswa', 12, '2026-09-22 08:20:40'),
(11, '00512345627', '$2y$10$GGG/0lvoB0cm9WWl9B8CO.OtOCBC03GQ.1wbItq2SMIKb4QK5Ygwq', 'Paradisa Klara Ependi', 'siswa', 14, '2026-09-22 08:20:40'),
(12, '00512345626', '$2y$10$M7ew4FY/mAcmBJMTfH2keeENmRCQvxfBXXOKhuLfxi1XpwwcS1pt6', 'Oryza Raja Alzura', 'siswa', 15, '2026-09-22 08:20:40'),
(13, '00512345625', '$2y$10$RvLZAcnTHkN2DYNv8X3R2eEsT0wxxdywHSzKbeKWze2GaOPMJV.xS', 'Nada Alma Delia', 'siswa', 16, '2026-09-22 08:20:40'),
(14, '00512345624', '$2y$10$qlm8J5WmYlwGGAZOWN2ZxOjKueOWmcDOvvLrtW9eZx/m4QVI4iv9C', 'Muntias Ramdani', 'siswa', 17, '2026-09-22 08:20:40'),
(15, '00512345623', '$2y$10$8bFZOPbXNBtJb5ItiyjUq.iYCRRGZxuaIqhVYIfs6iW3UvVIgjIqS', 'Lisna Rinjani', 'siswa', 18, '2026-09-22 08:20:40'),
(16, '00512345622', '$2y$10$c/NMj4s./nn2dXOur1FtNe1xCQMX2OBwlzLsxvu1Ax5Jz4VDAB1EW', 'Kalinna Rizki Riah', 'siswa', 19, '2026-09-22 08:20:40'),
(17, '00512345621', '$2y$10$nh6ROQCDC0Cgni1D5uZc/OCBU3.7yXvUU.3R2sHoU5A6HS85OVrYq', 'Janika Tobing', 'siswa', 20, '2026-09-22 08:20:40'),
(18, '00512345620', '$2y$10$UWlEDWt22kRqARbQoDy.wOjPaiuAIYA9YdKUk9C5gqyBaQoD7vf0a', 'Indra Hermawan', 'siswa', 21, '2026-09-22 08:20:40'),
(19, '00512345619', '$2y$10$ogCCHCUcDcmjrOfjQBsj4OKyh4fJbMA9cbMpJOadjtU2176BBt.5a', 'Fuzi Rahmawati', 'siswa', 22, '2026-09-22 08:20:40'),
(20, '00512345616', '$2y$10$zKII5ILMCb/nggcUsQkBy.07MVjtFG2La6jH91wrPpDU6FTvJLPEW', 'Desi Nopitasari', 'siswa', 26, '2026-09-22 08:20:40'),
(21, '00512345615', '$2y$10$V.VTIf5PyKT6wjaKIqQsXuWckE2F5iW0UGpnFRjz2nFlOGXmkkKM6', 'Azril Nursihab Nafis Ramadhan', 'siswa', 27, '2026-09-22 08:20:40'),
(22, '00512345614', '$2y$10$vg1k9ehPStmKyEcmnnBoeOwmX9O3QROj8fnQcsy9zsAG4Y8lETXNa', 'Arga Setyawan', 'siswa', 28, '2026-09-22 08:20:40'),
(23, '00512345613', '$2y$10$0bajHvDhaezo436ziX4sWOJS/Lwl3anrDYBd8.stLEhEtE6Fr31Qu', 'Afgansyah Ibrahim', 'siswa', 29, '2026-09-22 08:20:40'),
(24, '00512345612', '$2y$10$YUFNSRRIb8t5nnYSeSjqHeM8ba7RE5FLZv5RsBaahyii.AEFqot3K', 'Lista Ateu Ambarianti', 'siswa', 32, '2026-09-22 08:20:40'),
(25, '005123456139', '$2y$10$b2U7oZfu.8ZUmM1RZ7.CluOWa.K4m8B175sV3HDXXbCfPVyw4wHja', 'Diki Agustian', 'siswa', 39, '2026-09-22 08:20:40');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `fk_absensi_siswa` (`id_siswa`);

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
  ADD KEY `fk_kelas_guru` (`guru_id`);

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
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_siswa` (`siswa_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=809;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `fk_kelas_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
