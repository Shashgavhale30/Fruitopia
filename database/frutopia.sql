-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 06:29 PM
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
-- Database: `frutopia`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_fruits`
--

CREATE TABLE `add_fruits` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `unit` enum('per_kg','per_dozen','per_item') DEFAULT NULL,
  `season` enum('summer','winter','rainy','all') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `add_fruits`
--

INSERT INTO `add_fruits` (`id`, `seller_id`, `name`, `photo`, `quantity`, `price`, `unit`, `season`, `created_at`) VALUES
(1, 5, 'apple', './uploads/684058ebe56b1_5_1749047531.png', '1', 20.00, '', 'summer', '2025-06-04 14:32:11'),
(2, 5, 'Mango', './uploads/6840598772a9d_5_1749047687.png', '60', 30.00, '', 'rainy', '2025-06-04 14:34:47'),
(3, 5, 'Banana', './uploads/68405df93b7f9_5_1749048825.png', '20', 30.00, '', 'winter', '2025-06-04 14:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `buyers`
--

CREATE TABLE `buyers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buyers`
--

INSERT INTO `buyers` (`id`, `name`, `email`, `password`, `address`, `created_at`) VALUES
(1, 'shashwati', 'gavhale@gmail.com', '$2y$10$j7Fr3qRhaH9t2.i2H/9Kiel2DrAJ3/ozzKjIG0k0Wv.7SQjIaVDLK', 'At.Jamni , Po.Akoli , Tah.Seloo , dis.Wardha.', '2025-06-04 10:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `fruit_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `season` enum('summer','rainy','winter') NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rainy_fruits`
--

CREATE TABLE `rainy_fruits` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` enum('kg','dozen','item') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `confirm_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `name`, `address`, `bank_account`, `contact_no`, `email`, `password`, `confirm_password`) VALUES
(1, 'Shashwati Gavhale', 'At.Jamni , Po.Akoli , Tah.Seloo , dis.Wardha.', '12643822857263', NULL, 'gavhaleshashwati@gmail.com', '$2y$10$8sporyxpqjl3kCTI3G7sK.08pQy3rzRIdnk0C.IjU6LFGWIjWCHRa', NULL),
(2, 'Shashwati Gavhale', 'At.Jamni , Po.Akoli , Tah.Seloo , dis.Wardha.', '12643822857263', NULL, 'gavhaleshashwati123@gmail.com', '$2y$10$HutTWotUFiiCCUMV47EEcecJ6WQEyqI1IPZKqwTDhmlWd11bUL63.', NULL),
(3, 'Shashwati Gavhale', 'At.Jamni , Po.Akoli , Tah.Seloo , dis.Wardha.', '12643822857263', NULL, 'gavhaleshashwati1234@gmail.com', '$2y$10$UxZVvOQE9PEvSR9tE5jr.O1kE.ALx92hS2DYadMvdTv8DgO.4mnt6', NULL),
(4, 'Shashwati Gavhale', 'At.Jamni , Po.Akoli , Tah.Seloo , dis.Wardha.', '12643822857263', NULL, 'gavhaleshashwati1235@gmail.com', '$2y$10$JRUOpaq.EMRT3Vik4ws3SOvinWyJA950wjLekuwhkc/pqQFDobNA2', NULL),
(5, 'vaibhav', 'At.Jamni , Po.Akoli , Tah.Seloo , dis.Wardha.', '334575432111', NULL, 'vj@gmail.com', '$2y$10$o.sMttmSJep2WO5gSaUFaeH48yj/njXj2e4JrmTSsP/tW2ISSljtO', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `summer_fruits`
--

CREATE TABLE `summer_fruits` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` enum('kg','dozen','item') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `winter_fruits`
--

CREATE TABLE `winter_fruits` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` enum('kg','dozen','item') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `winter_fruits`
--

INSERT INTO `winter_fruits` (`id`, `seller_id`, `name`, `photo`, `price`, `quantity`, `unit`, `created_at`) VALUES
(1, 5, 'Banana', './uploads/68405f8ab3005_5_1749049226.png', 30.00, 20, 'kg', '2025-06-04 15:00:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_fruits`
--
ALTER TABLE `add_fruits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buyers`
--
ALTER TABLE `buyers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_buyer` (`buyer_id`),
  ADD KEY `fk_orders_seller` (`seller_id`);

--
-- Indexes for table `rainy_fruits`
--
ALTER TABLE `rainy_fruits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `summer_fruits`
--
ALTER TABLE `summer_fruits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `winter_fruits`
--
ALTER TABLE `winter_fruits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_fruits`
--
ALTER TABLE `add_fruits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `buyers`
--
ALTER TABLE `buyers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rainy_fruits`
--
ALTER TABLE `rainy_fruits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `summer_fruits`
--
ALTER TABLE `summer_fruits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `winter_fruits`
--
ALTER TABLE `winter_fruits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orders_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
