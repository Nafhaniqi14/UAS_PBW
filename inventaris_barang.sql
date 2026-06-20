-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql312.infinityfree.com
-- Waktu pembuatan: 20 Jun 2026 pada 09.29
-- Versi server: 11.4.7-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41698549_inventaris_barang`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(50) DEFAULT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `id_kategori`, `stok`, `satuan`, `harga`) VALUES
(1, 'Granluxor abu', 1, 70, 'Dus', 50000),
(2, 'Gladiator Biru', 1, 80, 'Dus', 120000),
(3, 'Tiga Roda', 2, 10, 'Sak', 70000),
(4, 'Gresik', 2, 90, 'Sak', 80000),
(5, 'Kaisar', 1, 70, 'Dus', 150000),
(6, 'Semen Padang 50 kg', 2, 300, 'kg', 65000),
(7, 'Bata Merah Jumbo', 5, 4000, 'pcs', 1200),
(8, 'Multipleks 9 mm', 6, 120, 'pcs', 135000),
(9, 'Kaca Bening 5 mm', 8, 30, 'pcs', 160000),
(10, 'Pipa PVC AW 1/2 Inch', 11, 115, 'pcs', 28000),
(11, 'Palu Konde Gagang Kayu', 9, 30, 'pcs', 48000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id_keluar` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tujuan` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `barang_keluar`
--

INSERT INTO `barang_keluar` (`id_keluar`, `id_barang`, `tanggal`, `jumlah`, `tujuan`, `id_user`) VALUES
(1, 1, '2026-06-16', 4, 'Gudang C', 1),
(2, 7, '2026-06-17', 5000, 'Gudang A', 1),
(3, 6, '2026-06-17', 300, 'Divisi T', 1),
(4, 11, '2026-06-17', 25, 'Divisi Alat', 1),
(5, 7, '2026-06-17', 2000, 'Gudang B', 1),
(6, 9, '2026-06-17', 10, 'Gudang T7', 1),
(7, 11, '2026-06-17', 15, 'Divisi Alat', 1),
(8, 1, '2026-06-17', 19, 'Gudang C', 1),
(9, 8, '2026-06-17', 30, 'Gudang B', 1),
(10, 10, '2026-06-17', 35, 'Gudang T7', 1),
(11, 6, '2026-06-17', 100, 'Gudang A', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id_masuk` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `barang_masuk`
--

INSERT INTO `barang_masuk` (`id_masuk`, `id_barang`, `id_supplier`, `tanggal`, `jumlah`, `id_user`) VALUES
(1, 1, 1, '2026-06-16', 3, 1),
(2, 6, 2, '2026-06-17', 500, 1),
(3, 7, 4, '2026-06-17', 1000, 1),
(4, 10, 9, '2026-06-17', 50, 1),
(5, 11, 2, '2026-06-17', 30, 1),
(6, 8, 7, '2026-06-17', 50, 1),
(7, 5, 4, '2026-06-17', 40, 1),
(8, 9, 11, '2026-06-17', 10, 1),
(9, 1, 5, '2026-06-17', 50, 1),
(10, 2, 10, '2026-06-17', 50, 1),
(11, 8, 7, '2026-06-17', 50, 1),
(12, 4, 9, '2026-06-17', 50, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Keramik'),
(2, 'Semen'),
(4, 'Pasir & Kerikil'),
(5, 'Batu Bata & Batako'),
(6, 'Kayu & Papan'),
(7, 'Besi & Baja'),
(8, 'Kaca'),
(9, 'Alat Pertukangan'),
(10, 'Alat Cat'),
(11, 'Pipa'),
(12, 'Perlengkapan Dapur');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `alamat`, `no_hp`) VALUES
(1, 'PT Asa Putra Cahaya Keramik', 'Jl. Raya Peruri, Kutapohaci, Kec. Ciampel, Karawang, Jawa Barat 41363', '0812-8144-1924'),
(2, 'PT Sumber Jaya Abadi', 'Jl. Raya Cakung Cilincing No. 89, Jakarta Utara, DKI Jakarta 14140', '081234567801'),
(3, 'CV Maju Bersama Konstruksi', 'Komplek Pergudangan Wisma Indah Blok B-12, Tangerang, Banten 15143', '085678902345'),
(4, 'UD Tiga Putra Mandiri', 'Jl. Soekarno-Hatta Km 10 No. 45, Bandung, Jawa Barat 40294', '081356789123'),
(5, 'Toko Besi & Baja Sejahtera', 'Jl. Pahlawan Revolusi No. 100, Pondok Bambu, Jakarta Timur 13430', '087887654321'),
(6, 'PT Indobeton Cipta Karya', 'Jl. Raya Narogong KM 12, Cileungsi, Bogor, Jawa Barat 16820', '081122334455'),
(7, 'CV Kayu Nusantara Lestari', 'Jl. Raya Semarang - Demak KM 15, Demak, Jawa Tengah 59511', '081544789900'),
(8, 'Supplier Pasir & Batu Galunggung', 'Jl. Cibaduyut Raya No. 234, Bandung, Jawa Barat 40237', '082156781234'),
(9, 'PT Sanitasi Makmur Abadi', 'Ruko Grand Galaxy City Blok B-28, Bekasi Selatan, Jawa Barat 17147', '081999887766'),
(10, 'UD Cat & Kimia Pratama', 'Jl. Raya Kalisari No. 55, Surabaya, Jawa Timur 60292', '083123456789'),
(11, 'CV Multi Elektrindo Perkasa', 'Jl. Gatot Subroto No. 212, Medan, Sumatera Utara 20119', '081298765432');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `status`) VALUES
(1, 'admin', '$2y$10$bpOr6SlddL0ix7ezK/J5leN4X6oRfhjBJs3ff7ha6CULl4kmaKEKq', 'admin', 'aktif'),
(2, 'nafhan', '$2y$10$QWI0ygcWrMbYSK4zytijPOhzRGnQl.Ax5tQxjCTcBNoAwXxbDgdMC', 'petugas', 'aktif'),
(4, 'Cakra', '$2y$10$nquLd/Td3tPZXWRkzKGwxe.6NQoqnCyAzNL2pWA8DkSv.i851L9Gi', 'petugas', 'aktif'),
(5, 'Abid', '$2y$10$RmgqIFN44woNr.vhgTpfGebL.t194en1aVhMqucODQJBviHfpB8.6', 'petugas', 'aktif'),
(6, 'Michael', '$2y$10$2OVJ3L4N.CBjspaXvvmgK.wtEPFNBJ699pMpc9.iYN0yI.DA/SkBS', 'petugas', 'aktif'),
(7, 'Daud', '$2y$10$s1vIGtSxQr1C8QXshokXde5VWhupyPnMQVQs8KD1YNd2wdib8D58m', 'petugas', 'aktif'),
(8, 'Joko', '$2y$10$Lpw1vrb5uNYHRfvbyuzL.uhj0.qnZhC5piZDwJKlbte7F0cEbvDT6', 'petugas', 'nonaktif'),
(9, 'Ahmad', '$2y$10$F91mgD.YmOoRd50mHQOZeex67lrtxQo/OiP2mPatqWd3SWt/quBay', 'petugas', 'nonaktif'),
(10, 'Muhammad', '$2y$10$083hRdegAyL6q6swjSiqcOsk9J0rnJH0BjydSiaVZS6ZnJfqavZje', 'admin', 'nonaktif'),
(11, 'Abdul', '$2y$10$OgQeU2bgZ8t.AZp.LWovMOYUqBnFEcSyNTq9ov67RommkT4ksjcfG', 'admin', 'nonaktif'),
(12, 'Hakim', '$2y$10$W1eWRSt/2u3k960bmBXVXefcNVHNS6e2iiv0xrZepRo9obTmY3dUO', 'petugas', 'nonaktif');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id_keluar`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `fk_barangkeluar_user` (`id_user`);

--
-- Indeks untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id_masuk`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `fk_barangmasuk_user` (`id_user`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id_masuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barangkeluar_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `barang_masuk_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barangmasuk_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
