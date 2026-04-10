-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 02:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parkir_rfid`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_parkir`
--

CREATE TABLE `tbl_parkir` (
  `id` int(11) NOT NULL,
  `card_id` varchar(50) NOT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `fee` int(11) DEFAULT NULL,
  `status` enum('IN','OUT','DONE') DEFAULT 'IN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_parkir`
--

INSERT INTO `tbl_parkir` (`id`, `card_id`, `checkin_time`, `checkout_time`, `duration`, `fee`, `status`) VALUES
(1, 'RFID001', '2026-02-26 14:25:33', '2026-02-26 15:15:56', 1, 2000, ''),
(2, 'RFID001', '2026-02-26 15:26:16', '2026-02-26 15:26:27', 1, 2000, ''),
(3, 'RFID001', '2026-02-26 15:30:33', '2026-02-26 15:31:01', 1, 2000, 'DONE'),
(4, 'RFID002', '2026-02-26 15:33:21', '2026-02-26 15:33:51', 1, 2000, 'DONE'),
(5, 'RFID003', '2026-02-26 15:33:35', '2026-02-26 15:34:42', 1, 2000, 'DONE'),
(6, 'RFID003', '2026-02-27 12:48:19', '2026-03-03 12:26:45', 90, 180000, 'DONE'),
(7, 'RFID004', '2026-02-27 16:37:19', '2026-03-03 15:49:46', 86, 172000, 'DONE'),
(8, 'RFID005', '2026-02-27 16:38:13', '2026-03-03 15:48:31', 88, 176000, 'DONE'),
(9, 'RFID006', '2026-03-03 12:25:05', '2026-03-03 15:47:16', 1, 2000, 'DONE'),
(10, 'RFID007', '2026-03-03 14:32:03', '2026-03-03 14:34:14', 1, 2000, 'DONE'),
(11, 'RFID008', '2026-03-03 15:59:19', '2026-03-03 15:59:41', 1, 2000, 'DONE'),
(12, 'RFID009', '2026-03-03 16:02:16', '2026-03-03 16:02:32', 1, 2000, 'DONE'),
(13, 'RFID009', '2026-03-03 16:07:03', '2026-03-03 16:07:32', 1, 2000, 'DONE'),
(14, 'RFID003', '2026-03-03 16:11:05', '2026-03-03 16:11:21', 1, 2000, 'DONE'),
(15, 'RFID003', '2026-03-03 16:33:40', '2026-03-03 16:33:51', 1, 2000, 'DONE'),
(16, 'RFID010', '2026-03-03 16:34:17', '2026-03-03 16:34:30', 1, 2000, 'DONE');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role` enum('admin','petugas','owner') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2, 'petugas', 'petugas123', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_parkir`
--
ALTER TABLE `tbl_parkir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_parkir`
--
ALTER TABLE `tbl_parkir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
