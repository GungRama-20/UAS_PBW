-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 07:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laundrygo`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`) VALUES
(1, 'Nyoman Joko'),
(2, 'Made Ngurah'),
(3, 'Wayan Suplag'),
(4, 'Gung Suju'),
(5, 'Gede Sengker');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nama`, `username`, `email`) VALUES
(4, 'Ari', 'Ari', 'Ari@gmail.com'),
(5, 'Rama', 'Rama', 'Rama@gmail.com'),
(6, 'Swas', 'Swas', 'Swas@gmail.com'),
(7, 'Anto', 'Anto', 'Anto@gmail.com'),
(8, 'Riri', 'Riri', 'Riri@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `order_date` datetime NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `package_id` int(11) NOT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `pickup_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `order_date`, `customer_name`, `package_id`, `weight_kg`, `status`, `pickup_date`) VALUES
(1, 'CK-01', '2025-06-28 00:00:00', 'Nyoman Joko', 1, 4.00, 'pending', NULL),
(2, 'CK-02', '2025-06-28 00:00:00', 'Made Ngurah', 2, 4.00, 'pending', NULL),
(3, 'CK-03', '2025-06-28 00:00:00', 'Wayan Suplag', 1, 4.00, 'pending', NULL),
(4, 'CK-04', '2025-06-28 00:00:00', 'Gung Suju', 1, 4.00, 'pending', NULL),
(5, 'CK-05', '2025-06-28 00:00:00', 'Gede Sengker', 3, 4.00, 'pending', NULL),
(13, 'CK-478', '2025-06-30 01:43:00', 'Joko', 1, 5.00, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `duration`, `price`) VALUES
(1, 'Packet Turbo', '3 Jam', 25000.00),
(2, 'Paket Reguler', '2 Hari', 15000.00),
(3, 'Paket Reguler', '3 Hari', 13000.00);

-- --------------------------------------------------------

--
-- Table structure for table `package_list`
--

CREATE TABLE `package_list` (
  `id` int(11) NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_list`
--

INSERT INTO `package_list` (`id`, `package_name`, `description`, `image_url`) VALUES
(1, 'Packet Turbo', 'cuci dengan cepat kilat tinggal tunggu', 'tidak ada'),
(2, 'Paket Reguler', 'cuci dengan murah', 'tidak ada');

-- --------------------------------------------------------

--
-- Table structure for table `packet_turbo`
--

CREATE TABLE `packet_turbo` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `berat` varchar(50) NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `tarif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paket`
--

CREATE TABLE `paket` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `berat` varchar(50) NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `tarif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket`
--

INSERT INTO `paket` (`id`, `nama`, `berat`, `durasi`, `tarif`) VALUES
(1, 'Selimut dan Sprai', '2kg', '2hari', 20000);

-- --------------------------------------------------------

--
-- Table structure for table `paket_reguler`
--

CREATE TABLE `paket_reguler` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `berat` varchar(50) NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `tarif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paket_turbo`
--

CREATE TABLE `paket_turbo` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `berat` varchar(50) NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `tarif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_turbo`
--

INSERT INTO `paket_turbo` (`id`, `nama`, `berat`, `durasi`, `tarif`) VALUES
(1, 'Baju dan Celana', '5kg', '3jam', 10000),
(2, 'Seprai', '1kg', '3jam', 10000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'userdemo', '$2y$10$a./Py5HzVJwz/V6nMD4GZOS23kGABrvR.t3/E7PHCzuU4iAE6VOXS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_list`
--
ALTER TABLE `package_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packet_turbo`
--
ALTER TABLE `packet_turbo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket_reguler`
--
ALTER TABLE `paket_reguler`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket_turbo`
--
ALTER TABLE `paket_turbo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `package_list`
--
ALTER TABLE `package_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `packet_turbo`
--
ALTER TABLE `packet_turbo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paket`
--
ALTER TABLE `paket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paket_reguler`
--
ALTER TABLE `paket_reguler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paket_turbo`
--
ALTER TABLE `paket_turbo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
