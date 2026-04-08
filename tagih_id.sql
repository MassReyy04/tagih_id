-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Apr 2026 pada 06.42
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tagih_id`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `meta`, `created_at`, `updated_at`) VALUES
(1, 1, 'download_journal', '{\"journal_id\":2,\"journal_title\":\"Analisis Pengaruh Metode Pembelajaran Online terhadap Hasil Belajar\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\"}', '2026-01-22 11:41:49', '2026-01-22 11:41:49'),
(2, 1, 'view_journal', '{\"journal_id\":4,\"journal_title\":\"Aplikasi IoT dalam Monitoring Kesehatan Pasien\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\"}', '2026-01-22 13:52:05', '2026-01-22 13:52:05'),
(3, NULL, 'view_journal', '{\"journal_id\":1,\"journal_title\":\"Implementasi Machine Learning dalam Sistem Rekomendasi\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\"}', '2026-01-22 14:09:45', '2026-01-22 14:09:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Teknologi Informasi', 'teknologi-informasi', 'Jurnal tentang teknologi informasi dan sistem komputer', '2025-12-12 03:04:43', '2025-12-12 03:04:43'),
(2, 'Pendidikan', 'pendidikan', 'Jurnal tentang pendidikan dan pembelajaran', '2025-12-12 03:04:43', '2025-12-12 03:04:43'),
(3, 'Ekonomi', 'ekonomi', 'Jurnal tentang ekonomi dan bisnis', '2025-12-12 03:04:43', '2025-12-12 03:04:43'),
(4, 'Kesehatan', 'kesehatan', 'Jurnal tentang kesehatan dan kedokteran', '2025-12-12 03:04:43', '2025-12-12 03:04:43'),
(5, 'Teknik', 'teknik', 'Jurnal tentang teknik dan rekayasa', '2025-12-12 03:04:43', '2025-12-12 03:04:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `journals`
--

CREATE TABLE `journals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `abstract` text NOT NULL,
  `authors` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `status` enum('draft','published','rejected') NOT NULL DEFAULT 'draft',
  `visibility` enum('public','private') NOT NULL DEFAULT 'public',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `journals`
--

INSERT INTO `journals` (`id`, `title`, `slug`, `abstract`, `authors`, `year`, `category_id`, `keywords`, `file_path`, `file_size`, `uploaded_by`, `status`, `visibility`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Implementasi Machine Learning dalam Sistem Rekomendasi', 'implementasi-machine-learning-dalam-sistem-rekomendasi', 'Penelitian ini membahas implementasi algoritma machine learning untuk sistem rekomendasi yang dapat meningkatkan akurasi prediksi preferensi pengguna. Metode yang digunakan meliputi collaborative filtering, content-based filtering, dan hybrid approach.', 'Dr. Ahmad Wijaya;Siti Nurhaliza', '2024', 1, 'machine learning, sistem rekomendasi, collaborative filtering, artificial intelligence', 'sample-journal-1.pdf', 2048576, 2, 'published', 'private', '2025-12-12 03:04:45', '2025-12-12 03:04:45', '2026-01-22 14:06:29'),
(2, 'Analisis Pengaruh Metode Pembelajaran Online terhadap Hasil Belajar', 'analisis-pengaruh-metode-pembelajaran-online-terhadap-hasil-belajar', 'Penelitian ini menganalisis efektivitas pembelajaran online dibandingkan dengan pembelajaran konvensional dalam meningkatkan hasil belajar siswa. Data dikumpulkan dari 200 responden siswa SMA.', 'Budi Santoso;Dr. Ahmad Wijaya', '2024', 2, 'pembelajaran online, hasil belajar, pendidikan, teknologi pembelajaran', 'sample-journal-2.pdf', 1536000, 3, 'published', 'public', '2025-12-07 03:04:45', '2025-12-12 03:04:45', '2025-12-12 03:04:45'),
(3, 'Strategi Digital Marketing untuk UMKM di Era Digital', 'strategi-digital-marketing-untuk-umkm-di-era-digital', 'Penelitian ini mengkaji strategi digital marketing yang efektif untuk Usaha Mikro, Kecil dan Menengah (UMKM) dalam menghadapi tantangan era digital. Fokus pada penggunaan media sosial dan e-commerce.', 'Siti Nurhaliza', '2023', 3, 'digital marketing, UMKM, media sosial, e-commerce, strategi bisnis', 'sample-journal-3.pdf', 3072000, 4, 'draft', 'public', NULL, '2025-12-12 03:04:45', '2025-12-12 03:04:45'),
(4, 'Aplikasi IoT dalam Monitoring Kesehatan Pasien', 'aplikasi-iot-dalam-monitoring-kesehatan-pasien', 'Penelitian ini mengembangkan sistem monitoring kesehatan berbasis Internet of Things (IoT) untuk memantau kondisi pasien secara real-time. Sistem menggunakan sensor wearable dan aplikasi mobile.', 'Dr. Ahmad Wijaya;Budi Santoso', '2024', 4, 'IoT, monitoring kesehatan, sensor wearable, telemedicine, kesehatan digital', 'sample-journal-4.pdf', 4096000, 2, 'published', 'public', '2025-12-02 03:04:45', '2025-12-12 03:04:45', '2025-12-12 03:04:45'),
(5, 'Optimasi Struktur Jembatan dengan Finite Element Analysis', 'optimasi-struktur-jembatan-dengan-finite-element-analysis', 'Penelitian ini menggunakan metode Finite Element Analysis (FEA) untuk mengoptimalkan struktur jembatan agar lebih efisien dan aman. Analisis dilakukan pada berbagai kondisi beban dan lingkungan.', 'Siti Nurhaliza;Dr. Ahmad Wijaya', '2023', 5, 'finite element analysis, struktur jembatan, optimasi, teknik sipil, simulasi', 'sample-journal-5.pdf', 5120000, 3, 'published', 'public', '2025-11-27 03:04:45', '2025-12-12 03:04:45', '2025-12-12 03:04:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_10_25_143245_add_role_to_users_table', 1),
(6, '2025_10_25_143253_create_categories_table', 1),
(7, '2025_10_25_143317_create_journals_table', 1),
(8, '2025_10_25_143325_create_activity_logs_table', 1),
(9, '2026_01_22_210150_add_visibility_to_journals_table', 2),
(10, '2014_10_12_100000_create_password_resets_table', 3),
(11, '2026_04_02_000001_add_role_to_users_table', 4),
(12, '2026_04_02_000002_create_monitoring_penagihan_table', 4),
(13, '2026_04_02_000003_ensure_users_role_string', 5),
(14, '2026_04_07_120000_add_unique_index_to_monitoring_penagihan_nomor_surat', 6),
(15, '2026_04_07_130000_add_geotag_address_fields_to_monitoring_penagihan', 7),
(16, '2026_04_07_180000_update_default_user_emails_to_ptpn_domain', 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitoring_penagihan`
--

CREATE TABLE `monitoring_penagihan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_surat` varchar(128) NOT NULL,
  `nama_mitra` varchar(255) NOT NULL,
  `nama_usaha` varchar(255) NOT NULL,
  `nomor_induk` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(32) NOT NULL,
  `nilai_pinjaman` decimal(18,2) NOT NULL DEFAULT 0.00,
  `sisa_pinjaman` decimal(18,2) NOT NULL DEFAULT 0.00,
  `alasan` text DEFAULT NULL,
  `janji` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `kebutuhan` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `signature_mitra` varchar(255) DEFAULT NULL,
  `signature_petugas` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `geo_jalan` varchar(255) DEFAULT NULL,
  `geo_kelurahan` varchar(255) DEFAULT NULL,
  `geo_kecamatan` varchar(255) DEFAULT NULL,
  `geo_kota` varchar(255) DEFAULT NULL,
  `geo_provinsi` varchar(255) DEFAULT NULL,
  `geo_kode_pos` varchar(16) DEFAULT NULL,
  `geo_negara` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `monitoring_penagihan`
--

INSERT INTO `monitoring_penagihan` (`id`, `nomor_surat`, `nama_mitra`, `nama_usaha`, `nomor_induk`, `alamat`, `no_hp`, `nilai_pinjaman`, `sisa_pinjaman`, `alasan`, `janji`, `catatan`, `kebutuhan`, `tanggal`, `signature_mitra`, `signature_petugas`, `foto`, `latitude`, `longitude`, `geo_jalan`, `geo_kelurahan`, `geo_kecamatan`, `geo_kota`, `geo_provinsi`, `geo_kode_pos`, `geo_negara`, `user_id`, `created_at`, `updated_at`) VALUES
(18, 'BAM 4/08/IV/2026', 'saiful', 'RM.CAHYA PADANG', 'NIM-2019', 'JL.PATTIMURA', '081234876509', 20000000.00, 10000000.00, '-', '-', 'BAYAR HARI INI Rp.1.000.000', '-', '2026-04-08', 'signatures/mitra_1775619672_o5thj8qo.png', 'signatures/petugas_1775619672_7d4rc9bh.png', 'fotos/N8S4UiTNAGnhDmlR4GFSdAxPG1auHpfDbd0Jm1jI.jpg', -1.6679818, 103.5844661, 'Jalan Lingkar Barat', 'Kenali Asam Bawah', 'Kota Baru', 'Jambi', 'Jambi', '36129', NULL, 10, '2026-04-07 20:41:12', '2026-04-07 20:41:12'),
(19, 'BAM 5/08/IV/2026', 'khomaruddin', 'toko sembako 2', 'NIM-2558', 'TALANGBAKUNG,', '-', 15000000.00, 10000000.00, '-', '-', 'BAYAR HARI INI RP.1.000.000', '-', '2026-04-08', 'signatures/mitra_1775619857_wm6cu6ex.png', 'signatures/petugas_1775619857_vbezkzyg.png', 'fotos/0oBkqYnyQ5QDDgFerEsMK3Ra91wDQgHtycS4a1Mr.jpg', -1.6679767, 103.5844476, 'Jalan Lingkar Barat', 'Kenali Asam Bawah', 'Kota Baru', 'Jambi', 'Jambi', '36129', NULL, 11, '2026-04-07 20:44:17', '2026-04-07 20:58:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(32) NOT NULL DEFAULT 'petugas',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `is_active`) VALUES
(1, 'Admin', 'adminrepo@gmail.com', NULL, '$2y$12$G8Xj3lEJ06ee/85nZtWX3u32KbfYgvP43xief1Tk0wSDJSzbHmT32', 't6Q5zi8TXvq2HLQYpbSxY8NntmLbZLgd8oWh1sJmWIFHgDxqkspjkUqsKqDZ', '2025-12-12 03:04:44', '2025-12-12 03:04:44', 'admin', 1),
(2, 'Dr. Ahmad Wijaya', 'ahmad@example.com', NULL, '$2y$12$w3K.1cKj64FPinUJp0vRlum3gF/CW1r05PwzxVyHHOZeIAA8XKULa', NULL, '2025-12-12 03:04:44', '2025-12-12 03:04:44', 'dosen_mahasiswa', 1),
(3, 'Siti Nurhaliza', 'siti@example.com', NULL, '$2y$12$Sv.gIjBC.nZ7WHgOcHUeVuugGRhzpt//aNX9qE4b77xF2oQrjSdyy', NULL, '2025-12-12 03:04:44', '2025-12-12 03:04:44', 'dosen_mahasiswa', 1),
(4, 'Budi Santoso', 'budi@example.com', NULL, '$2y$12$T/kVdLuwPxYoENT5UpSBKuTlrtI2zdxDHMQBDb8BbYSir.tN12bu2', NULL, '2025-12-12 03:04:44', '2025-12-12 03:04:44', 'dosen_mahasiswa', 1),
(5, 'Guest User', 'guest@example.com', NULL, '$2y$12$mFj7aOFfF2/ajxzN4ZAKxOFgGjnEEllrM6nxBTMwxEKDEuBdsQyli', NULL, '2025-12-12 03:04:45', '2025-12-12 03:04:45', 'guest', 1),
(8, 'Admin', 'admin@ptpn.ac.id', NULL, '$2y$12$IbuJ1O/Jm2SYp2TQ6NEcP..uOeoMNwu7ChL1Ik3u1Kfv0x9iVdiW2', NULL, '2026-04-01 22:07:38', '2026-04-06 21:36:07', 'admin', 1),
(10, 'andre', 'andre@ptpn.ac.id', NULL, '$2y$12$Wot0h3qkQipAzvruaJjlB.XqYVqonY8VRKBa16uD4D80Yv6S5Ik2u', NULL, '2026-04-07 19:28:40', '2026-04-07 19:28:40', 'petugas', 1),
(11, 'Hasan Samik', 'Hasan@ptpn.ac.id', NULL, '$2y$12$7FJ9y4kVzag2KKLvyybZe.wSrAmzv81I4Tmtyl8JaEsKOhSNfWQHi', NULL, '2026-04-07 19:38:34', '2026-04-07 19:38:34', 'petugas', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journals_slug_unique` (`slug`),
  ADD KEY `journals_category_id_foreign` (`category_id`),
  ADD KEY `journals_uploaded_by_foreign` (`uploaded_by`);
ALTER TABLE `journals` ADD FULLTEXT KEY `journals_title_abstract_authors_keywords_fulltext` (`title`,`abstract`,`authors`,`keywords`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `monitoring_penagihan`
--
ALTER TABLE `monitoring_penagihan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `monitoring_penagihan_nomor_surat_unique` (`nomor_surat`),
  ADD KEY `monitoring_penagihan_user_id_foreign` (`user_id`),
  ADD KEY `monitoring_penagihan_tanggal_nomor_surat_index` (`tanggal`,`nomor_surat`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `journals`
--
ALTER TABLE `journals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `monitoring_penagihan`
--
ALTER TABLE `monitoring_penagihan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `journals`
--
ALTER TABLE `journals`
  ADD CONSTRAINT `journals_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journals_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `monitoring_penagihan`
--
ALTER TABLE `monitoring_penagihan`
  ADD CONSTRAINT `monitoring_penagihan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
