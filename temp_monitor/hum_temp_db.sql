-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 17, 2026 at 11:52 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hum_temp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500'),
(2, 'admin1', '4c3d30db18c7e79e27be78c175b7c0a6'),
(3, 'admin1@gmail.com', '0e7517141fb53f21ee439b355b5a1d0a');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

DROP TABLE IF EXISTS `records`;
CREATE TABLE IF NOT EXISTS `records` (
  `id` int NOT NULL AUTO_INCREMENT,
  `veg_id` int DEFAULT NULL,
  `temperature` float DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `veg_id`, `temperature`, `status`, `created_at`) VALUES
(1, 0, 21, 'High', '2026-03-17 09:25:13'),
(2, 0, 25, 'High', '2026-03-17 09:25:23'),
(3, 1, 20, 'Normal', '2026-03-17 09:32:07'),
(4, 1, -10, 'Low', '2026-03-17 09:32:12'),
(5, 1, 20, 'Normal', '2026-03-17 09:32:18'),
(6, 1, 25, 'Normal', '2026-03-17 09:37:45'),
(7, 2, 21, 'High', '2026-03-17 09:38:09'),
(8, 1, 20, 'Normal', '2026-03-17 09:40:49'),
(9, 1, 18, 'Low', '2026-03-17 10:46:38'),
(10, 1, 30, 'High', '2026-03-17 10:46:53'),
(11, 1, 22, 'Normal', '2026-03-17 10:52:00'),
(12, 1, 23, 'Normal', '2026-03-17 10:52:52'),
(13, 3, 20, 'Normal', '2026-03-17 10:57:41'),
(14, 3, 20, 'Normal', '2026-03-17 11:00:17'),
(15, 3, -10, 'Low', '2026-03-17 11:00:38');

-- --------------------------------------------------------

--
-- Table structure for table `temperature_humidity`
--

DROP TABLE IF EXISTS `temperature_humidity`;
CREATE TABLE IF NOT EXISTS `temperature_humidity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `temperature` float DEFAULT NULL,
  `humidity` float DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `temperature_humidity`
--

INSERT INTO `temperature_humidity` (`id`, `temperature`, `humidity`, `date`) VALUES
(1, 20, 30, '2026-03-14 09:56:16'),
(2, 30, 20, '2026-03-14 09:56:30'),
(3, 25, 20, '2026-03-14 10:06:17'),
(4, -18, 20, '2026-03-16 04:08:46'),
(5, 35, 20, '2026-03-17 07:41:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'user1', 'user1@gmail.com', '$2y$10$DxEBwRz2bI8mX1k6Egw/5umJ9UXl6OBHl9/X3gJdsHL1k/Uhw1Gfi', NULL, '2026-03-17 08:37:09', '2026-03-17 11:04:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vegetables`
--

DROP TABLE IF EXISTS `vegetables`;
CREATE TABLE IF NOT EXISTS `vegetables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `min_temp` float DEFAULT NULL,
  `max_temp` float DEFAULT NULL,
  `humidity` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vegetables`
--

INSERT INTO `vegetables` (`id`, `name`, `min_temp`, `max_temp`, `humidity`) VALUES
(1, 'brinjal', 20, 25, 0),
(2, 'tomto', 10, 30, 20),
(3, 'Piece', 10, 30, 5);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
