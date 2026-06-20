-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 20, 2026 at 03:13 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pariwisata_karawang`
--

-- --------------------------------------------------------

--
-- Table structure for table `tiket`
--

CREATE TABLE `tiket` (
  `id` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `kode_tiket` varchar(20) NOT NULL,
  `tanggal_kunjungan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tiket`
--

INSERT INTO `tiket` (`id`, `id_transaksi`, `kode_tiket`, `tanggal_kunjungan`) VALUES
(1, 1, 'TICK-492719', '2026-06-25'),
(2, 3, 'TICK-957554', '2026-06-25');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_wisata` int NOT NULL,
  `jumlah_tiket` int NOT NULL,
  `total_harga` int NOT NULL,
  `tanggal_transaksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `status_pembayaran` enum('belum_bayar','lunas','batal') NOT NULL DEFAULT 'belum_bayar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_user`, `id_wisata`, `jumlah_tiket`, `total_harga`, `tanggal_transaksi`, `status_pembayaran`) VALUES
(1, 2, 1, 2, 30000, '2026-06-19 23:48:21', 'lunas'),
(2, 2, 2, 3, 60000, '2026-06-19 23:48:21', 'lunas'),
(3, 4, 1, 1, 15000, '2026-06-19 23:53:08', 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pembeli','penjual') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Dinas Pariwisata Karawang', 'penjual@karawang.go.id', '$2y$10$p8tSI0SR39xqdmOuyixMvOYhOzvJgoItad46GIwdhP.moAFx1HEpu', 'penjual'),
(2, 'Ahmad Wisatawan', 'user@gmail.com', '$2y$10$U6U.Jg2mDqZ54N9hYd7m9uK6CqH.VfS52vGisPOfhNo98N7B6xM4m', 'pembeli'),
(3, 'Budi Santoso', 'wisatawan@desa.com', '$2y$10$U6U.Jg2mDqZ54N9hYd7m9uK6CqH.VfS52vGisPOfhNo98N7B6xM4m', 'pembeli'),
(4, 'Hasan', 'hasan@mail.com', '$2y$10$p8tSI0SR39xqdmOuyixMvOYhOzvJgoItad46GIwdhP.moAFx1HEpu', 'pembeli');

-- --------------------------------------------------------

--
-- Table structure for table `wisata`
--

CREATE TABLE `wisata` (
  `id` int NOT NULL,
  `id_penjual` int NOT NULL,
  `nama_wisata` varchar(150) NOT NULL,
  `gambar` text NOT NULL,
  `deskripsi` text NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `harga_tiket` int NOT NULL,
  `stok_tiket` int NOT NULL,
  `jam_operasional` varchar(100) NOT NULL,
  `fasilitas` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wisata`
--

INSERT INTO `wisata` (`id`, `id_penjual`, `nama_wisata`, `gambar`, `deskripsi`, `lokasi`, `harga_tiket`, `stok_tiket`, `jam_operasional`, `fasilitas`) VALUES
(1, 1, 'Pantai Tanjung Pakis', 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?q=80&w=800', 'Pantai Tanjung Pakis menawarkan hamparan pasir halus di pesisir utara Karawang dengan kuliner hidangan laut segar, perahu sewaan tradisional, gazebo pantai nyaman, serta keelokan matahari terbenam yang memukau.', 'Kecamatan Pakisjaya, Kabupaten Karawang', 15000, 249, '07:00 - 18:00 WIB', 'Area Parkir, Tempat Makan Seafood, Toilet Umum, Mushola, Sewa Perahu, Gazebo'),
(2, 1, 'Curug Cigentis', 'assets/uploads/wisata_1781959620_414.webp', 'Curug Cigentis merupakan air terjun megah setinggi 25 meter di kawasan Gunung Sanggabuana. Alam yang hijau, udara yang sejuk, serta kesegaran air jernihnya sangat digemari penjelajah alam.', 'Desa Mekarbuana, Kecamatan Tegalwaru, Karawang', 20000, 150, '08:00 - 17:00 WIB', 'Mushola, Kamar Mandi Bilas, Warung Makan, Spot Foto, Area Parkir Motor, Camping Ground'),
(3, 1, 'Dermaga Situ Cipule', 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800', 'Situ Cipule adalah danau luas dengan pemandangan pegunungan indah. Terkenal sebagai arena lomba dayung berstandar internasional, tempat ini menawarkan kuliner sunda khas rakit dan perahu penyeberangan menuju Pulau Cinta.', 'Desa Mulyasari, Kecamatan Ciampel, Karawang', 10000, 200, '06:00 - 18:00 WIB', 'Spot Foto Dermaga, Warung Bakar Ikan, Sewa Perahu, Pulau Cinta, Kamar Mandi, Toilet'),
(4, 1, 'Candi Jiwa Batujaya', 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?q=80&w=800', 'Kompleks percandian Buddha tertua abad ke-5 peninggalan Kerajaan Tarumanegara yang berdiri unik di tengah areal sawah hijau yang memukau. Destinasi edukasi sejarah yang kaya nilai religi dan arkeologis.', 'Kecamatan Batujaya, Kabupaten Karawang', 8000, 300, '07:30 - 16:30 WIB', 'Papan Informasi Sejarah, Pemandu Arkeologi, Tempat Parkir, Gazebo, Penyewaan Sepeda'),
(5, 1, 'Kampung Budaya Karawang', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=800', 'Kampung Budaya Karawang didirikan untuk memelihara warisan adat seni dan budaya Sunda. Terdiri dari rumah panggung tradisional, sanggar tari jaipong, galeri kerajinan khas, serta restoran kuliner lokal.', 'Desa Wadas, Kecamatan Telukjambe Barat, Karawang', 12000, 180, '08:00 - 17:00 WIB', 'Pendopo Pertunjukan, Galeri Batik, Rumah Adat Sunda, Restoran, Toilet Bersih, Area Pertunjukan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tiket`
--
ALTER TABLE `tiket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_tiket` (`kode_tiket`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_wisata` (`id_wisata`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wisata`
--
ALTER TABLE `wisata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penjual` (`id_penjual`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tiket`
--
ALTER TABLE `tiket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wisata`
--
ALTER TABLE `wisata`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tiket`
--
ALTER TABLE `tiket`
  ADD CONSTRAINT `tiket_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wisata`
--
ALTER TABLE `wisata`
  ADD CONSTRAINT `wisata_ibfk_1` FOREIGN KEY (`id_penjual`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
