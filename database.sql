-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 27, 2025 at 09:17 AM
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
-- Database: `rental_broker1`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `property_id` int NOT NULL,
  `user1_id` int NOT NULL,
  `user2_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `user1_id` (`user1_id`),
  KEY `user2_id` (`user2_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `property_id`, `user1_id`, `user2_id`, `created_at`) VALUES
(1, 1, 1, 2, '2025-12-14 17:45:35'),
(2, 4, 5, 2, '2025-12-15 08:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `property_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `property_id` (`property_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `property_id`, `created_at`) VALUES
(1, 1, 1, '2025-12-14 17:45:33'),
(4, 2, 4, '2025-12-14 17:56:16'),
(5, 3, 4, '2025-12-15 08:13:39'),
(6, 5, 4, '2025-12-15 08:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversation_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 1, 'hi', 0, '2025-12-14 17:45:41'),
(2, 1, 2, 'hi', 0, '2025-12-14 19:07:19'),
(3, 1, 1, 'endet neh', 0, '2025-12-14 19:07:32'),
(4, 1, 2, 'alehu', 0, '2025-12-14 19:07:44'),
(5, 1, 1, 'eshi dena der', 0, '2025-12-14 19:07:54'),
(6, 1, 2, 'pis', 0, '2025-12-14 19:08:06'),
(7, 2, 5, 'heyy', 0, '2025-12-15 08:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` int NOT NULL AUTO_INCREMENT,
  `landlord_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` enum('apartment','house','studio','villa') NOT NULL,
  `status` enum('available','rented','pending') DEFAULT 'available',
  `images` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `landlord_id` (`landlord_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'REGISTER', 'New tenant registered', '::1', '2025-12-14 17:42:58'),
(2, 2, 'REGISTER', 'New landlord registered', '::1', '2025-12-14 17:43:43'),
(3, 2, 'CREATE_PROPERTY', 'Created property ID 1', '::1', '2025-12-14 17:44:13'),
(4, 1, 'LOGIN', 'User logged in', '::1', '2025-12-14 17:45:19'),
(5, 1, 'MESSAGE', 'Sent message in conv 1', '::1', '2025-12-14 17:45:41'),
(6, 2, 'LOGIN', 'User logged in', '::1', '2025-12-14 17:45:59'),
(7, 2, 'CREATE_PROPERTY', 'Created property ID 2', '::1', '2025-12-14 17:54:04'),
(8, 2, 'CREATE_PROPERTY', 'Created property ID 3', '::1', '2025-12-14 17:54:15'),
(9, 2, 'CREATE_PROPERTY', 'Created property ID 4', '::1', '2025-12-14 17:55:27'),
(10, 2, 'LOGIN', 'User logged in', '::1', '2025-12-14 17:59:33'),
(11, 2, 'CREATE_PROPERTY', 'Created property ID 5', '::1', '2025-12-14 18:00:09'),
(12, 1, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:00:48'),
(13, 3, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:03:04'),
(14, 2, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:07:56'),
(15, 2, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:09:23'),
(16, 2, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:14:18'),
(17, 2, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:20:56'),
(18, 1, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:27:40'),
(19, 4, 'REGISTER', 'New tenant registered', '::1', '2025-12-14 18:37:38'),
(20, 4, 'RESET_PASSWORD', 'Password reset via secret code', '::1', '2025-12-14 18:38:32'),
(21, 4, 'LOGIN', 'User logged in', '::1', '2025-12-14 18:38:42'),
(22, 2, 'LOGIN', 'User logged in', '::1', '2025-12-14 19:03:55'),
(23, 3, 'LOGIN', 'User logged in', '::1', '2025-12-14 19:08:51'),
(24, 1, 'LOGIN', 'User logged in', '::1', '2025-12-15 07:25:38'),
(25, 3, 'LOGIN', 'User logged in', '::1', '2025-12-15 08:12:50'),
(26, 1, 'LOGIN', 'User logged in', '::1', '2025-12-15 08:14:01'),
(27, 5, 'REGISTER', 'New tenant registered', '::1', '2025-12-15 08:14:43'),
(28, 1, 'LOGIN', 'User logged in', '::1', '2025-12-16 09:23:03'),
(29, 3, 'LOGIN', 'User logged in', '::1', '2025-12-17 11:38:32'),
(30, 5, 'LOGIN', 'User logged in', '::1', '2025-12-27 08:27:40'),
(31, 6, 'REGISTER', 'New tenant registered', '::1', '2025-12-27 08:36:07'),
(32, 3, 'LOGIN', 'User logged in', '::1', '2025-12-27 08:40:24'),
(33, 2, 'LOGIN', 'User logged in', '::1', '2025-12-27 08:52:35'),
(34, 7, 'REGISTER', 'New tenant registered', '::1', '2025-12-27 08:53:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `secret_code` varchar(255) DEFAULT NULL,
  `role` enum('guest','tenant','landlord','broker','admin') DEFAULT 'tenant',
  `bio` text,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `secret_code`, `role`, `bio`, `profile_picture`, `created_at`) VALUES
(1, 'gedamo tesema', 'tesemagedamo@gmail.com', '$2y$10$az7Ywh0a/AnC2.sGPwlycep9MV1ZGNMfARXDxuZko5sNiGyWhCqS2', NULL, 'tenant', NULL, NULL, '2025-12-14 17:42:58'),
(2, 'naod', 'naod@naod.com', '$2y$10$VKDn/nW8E9dOuvYWYLbYjuvt9HU4j1k1x2MGmL3FQwbJfzn23Dao2', NULL, 'landlord', '', 'uploads/profiles/user_693f004b18e771.47462979.jpg', '2025-12-14 17:43:43'),
(3, 'admin', 'admin@admin.com', '$2y$10$az7Ywh0a/AnC2.sGPwlycep9MV1ZGNMfARXDxuZko5sNiGyWhCqS2', NULL, 'admin', 'test admin', NULL, '2025-12-14 18:02:43'),
(5, 'test', 'test@test.com', '$2y$10$hB7cO/yc07u0Y5Wj3HN9JuLtQ66xGK3GM86FaREDhg4cFAgC8wH72', '$2y$10$XmAmrkbvPWh1txBhp8.iDuWACyM0ycTFl8DDmEz9nCP2GOarHrCQu', 'tenant', NULL, NULL, '2025-12-15 08:14:43'),
(6, 'anuwar', 'anuwar@gmail.com', '$2y$10$87QmvWY2FTza.DdxoEkYKeYmDVMsm8ThP1YlqQkcfoK7ZpcFPvTQe', '$2y$10$OaRwEYGkE/fvok71/PkNdO8XomjkgaadZ1hnlIP24O5p59yQjfYy6', 'tenant', NULL, NULL, '2025-12-27 08:36:07'),
(7, 'yosep elias', 'yosepelias@gmail.com', '$2y$10$tp028NzDxXOCVEBsqs1.Tevvaglt7dloFFk9e6g8Fuqk/UbDmkCp2', '$2y$10$c1BUhP/rWMfXKXYE2Z5QiuDp6XsPyxG2SIncCAdKyMfaSFiykmE5u', 'tenant', NULL, NULL, '2025-12-27 08:53:25');

-- --------------------------------------------------------

--
-- Table structure for table `viewing_requests`
--

DROP TABLE IF EXISTS `viewing_requests`;
CREATE TABLE IF NOT EXISTS `viewing_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `property_id` int NOT NULL,
  `tenant_id` int NOT NULL,
  `preferred_date` datetime NOT NULL,
  `message` text,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `tenant_id` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `viewing_requests`
--

INSERT INTO `viewing_requests` (`id`, `property_id`, `tenant_id`, `preferred_date`, `message`, `status`, `created_at`) VALUES
(1, 1, 1, '2025-12-15 23:30:00', 'hi am interested to view the site', 'accepted', '2025-12-14 18:28:29');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
