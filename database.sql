-- DATABASE PARIWISATA KABUPATEN KARAWANG
-- CREATE DATABASE pariwisata_karawang;
-- USE pariwisata_karawang;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `tiket`;
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `wisata`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. TABEL USERS
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('pembeli', 'penjual') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABEL WISATA
CREATE TABLE `wisata` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_penjual` INT NOT NULL,
  `nama_wisata` VARCHAR(150) NOT NULL,
  `gambar` TEXT NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `lokasi` VARCHAR(255) NOT NULL,
  `harga_tiket` INT NOT NULL,
  `stok_tiket` INT NOT NULL,
  `jam_operasional` VARCHAR(100) NOT NULL,
  `fasilitas` TEXT,
  FOREIGN KEY (`id_penjual`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABEL TRANSAKSI
CREATE TABLE `transaksi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_user` INT NOT NULL,
  `id_wisata` INT NOT NULL,
  `jumlah_tiket` INT NOT NULL,
  `total_harga` INT NOT NULL,
  `tanggal_transaksi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status_pembayaran` ENUM('belum_bayar', 'lunas', 'batal') NOT NULL DEFAULT 'belum_bayar',
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_wisata`) REFERENCES `wisata`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. TABEL TIKET
CREATE TABLE `tiket` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_transaksi` INT NOT NULL,
  `kode_tiket` VARCHAR(20) UNIQUE NOT NULL,
  `tanggal_kunjungan` DATE NOT NULL,
  FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================
-- POPULASI SEED DATA (BAWAAN SISTEM)
-- =====================================

-- PENGGUNA (Password default: password123)
-- Menggunakan hashing password standard PHP password_hash('password123', PASSWORD_DEFAULT)
-- Nilai hash: '$2y$10$U6U.Jg2mDqZ54N9hYd7m9uK6CqH.VfS52vGisPOfhNo98N7B6xM4m'
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Dinas Pariwisata Karawang', 'penjual@karawang.go.id', '$2y$10$U6U.Jg2mDqZ54N9hYd7m9uK6CqH.VfS52vGisPOfhNo98N7B6xM4m', 'penjual'),
(2, 'Ahmad Wisatawan', 'user@gmail.com', '$2y$10$U6U.Jg2mDqZ54N9hYd7m9uK6CqH.VfS52vGisPOfhNo98N7B6xM4m', 'pembeli'),
(3, 'Budi Santoso', 'wisatawan@desa.com', '$2y$10$U6U.Jg2mDqZ54N9hYd7m9uK6CqH.VfS52vGisPOfhNo98N7B6xM4m', 'pembeli');

-- PILIHAN DESTINASI WISATA KARAWANG
INSERT INTO `wisata` (`id`, `id_penjual`, `nama_wisata`, `gambar`, `deskripsi`, `lokasi`, `harga_tiket`, `stok_tiket`, `jam_operasional`, `fasilitas`) VALUES
(1, 1, 'Pantai Tanjung Pakis', 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?q=80&w=800', 'Pantai Tanjung Pakis menawarkan hamparan pasir halus di pesisir utara Karawang dengan kuliner hidangan laut segar, perahu sewaan tradisional, gazebo pantai nyaman, serta keelokan matahari terbenam yang memukau.', 'Kecamatan Pakisjaya, Kabupaten Karawang', 15000, 250, '07:00 - 18:00 WIB', 'Area Parkir, Tempat Makan Seafood, Toilet Umum, Mushola, Sewa Perahu, Gazebo'),
(2, 1, 'Curug Cigentis', 'https://images.unsplash.com/photo-1432406775156-6f3de584587c?q=80&w=800', 'Curug Cigentis merupakan air terjun megah setinggi 25 meter di kawasan Gunung Sanggabuana. Alam yang hijau, udara yang sejuk, serta kesegaran air jernihnya sangat digemari penjelajah alam.', 'Desa Mekarbuana, Kecamatan Tegalwaru, Karawang', 20000, 150, '08:00 - 17:00 WIB', 'Mushola, Kamar Mandi Bilas, Warung Makan, Spot Foto, Area Parkir Motor, Camping Ground'),
(3, 1, 'Dermaga Situ Cipule', 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800', 'Situ Cipule adalah danau luas dengan pemandangan pegunungan indah. Terkenal sebagai arena lomba dayung berstandar internasional, tempat ini menawarkan kuliner sunda khas rakit dan perahu penyeberangan menuju Pulau Cinta.', 'Desa Mulyasari, Kecamatan Ciampel, Karawang', 10000, 200, '06:00 - 18:00 WIB', 'Spot Foto Dermaga, Warung Bakar Ikan, Sewa Perahu, Pulau Cinta, Kamar Mandi, Toilet'),
(4, 1, 'Candi Jiwa Batujaya', 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?q=80&w=800', 'Kompleks percandian Buddha tertua abad ke-5 peninggalan Kerajaan Tarumanegara yang berdiri unik di tengah areal sawah hijau yang memukau. Destinasi edukasi sejarah yang kaya nilai religi dan arkeologis.', 'Kecamatan Batujaya, Kabupaten Karawang', 8000, 300, '07:30 - 16:30 WIB', 'Papan Informasi Sejarah, Pemandu Arkeologi, Tempat Parkir, Gazebo, Penyewaan Sepeda'),
(5, 1, 'Kampung Budaya Karawang', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=800', 'Kampung Budaya Karawang didirikan untuk memelihara warisan adat seni dan budaya Sunda. Terdiri dari rumah panggung tradisional, sanggar tari jaipong, galeri kerajinan khas, serta restoran kuliner lokal.', 'Desa Wadas, Kecamatan Telukjambe Barat, Karawang', 12000, 180, '08:00 - 17:00 WIB', 'Pendopo Pertunjukan, Galeri Batik, Rumah Adat Sunda, Restoran, Toilet Bersih, Area Pertunjukan');

-- TRANSAKSI PERTAMA
INSERT INTO `transaksi` (`id`, `id_user`, `id_wisata`, `jumlah_tiket`, `total_harga`, `tanggal_transaksi`, `status_pembayaran`) VALUES
(1, 2, 1, 2, 30000, NOW(), 'lunas'),
(2, 2, 2, 3, 60000, NOW(), 'belum_bayar');

-- TIKET DIGITAL PERTAMA (Untuk transaksi yang sudah lunas)
INSERT INTO `tiket` (`id`, `id_transaksi`, `kode_tiket`, `tanggal_kunjungan`) VALUES
(1, 1, 'TICK-492719', '2026-06-25');
