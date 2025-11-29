-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 29, 2025 at 01:46 PM
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
-- Database: `rental_broker`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `property_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `property_id` (`property_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `property_id`) VALUES
(1, 1, 1),
(3, 5, 4),
(4, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `property_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','approved','declined') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `sender_id` (`sender_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `property_id`, `sender_id`, `owner_id`, `message`, `status`, `created_at`) VALUES
(1, 1, 1, 2, 'i want to book it', 'pending', '2025-11-24 15:38:50'),
(2, 4, 5, 4, 'i am interesred', '', '2025-11-29 16:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_replies`
--

DROP TABLE IF EXISTS `inquiry_replies`;
CREATE TABLE IF NOT EXISTS `inquiry_replies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inquiry_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `reply` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inquiry_id` (`inquiry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inquiry_replies`
--

INSERT INTO `inquiry_replies` (`id`, `inquiry_id`, `owner_id`, `reply`, `created_at`) VALUES
(1, 2, 4, 'really?', '2025-11-29 16:33:01');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event` varchar(255) NOT NULL,
  `user_id` int DEFAULT NULL,
  `details` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `title` varchar(120) NOT NULL,
  `location` varchar(120) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `type` enum('apartment','house','condo') NOT NULL,
  `description` text NOT NULL,
  `images` text,
  `availability` enum('available','not available') NOT NULL DEFAULT 'available',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `owner_id`, `title`, `location`, `price`, `type`, `description`, `images`, `availability`, `created_at`) VALUES
(1, 2, '4 by 4 room', 'Hossana', 150.00, 'apartment', 'jdjsdlkfjsdlfjsldjflsdjflsdjfklsdf sdkl fskldfsdjnf sdf sd f sdf sd', 'prop_6924517a9f6359.06213953.jpg,prop_6924524a8736d7.77897349.png,prop_6924529490e7a6.40603008.png', 'available', '2025-11-24 15:37:14'),
(2, 2, 'hossana tsede bet', 'hossana,lucy sefer', 120.00, 'apartment', 'wow yemr tsede bet new come and check it', 'prop_69245998c15fb8.15629058.jpg', 'available', '2025-11-24 16:11:52'),
(3, 2, 'anbeso', '143 room 116', 12.00, 'apartment', 'anbeso tsadik kumsa', 'prop_69245a5b695790.63085700.jpg', 'available', '2025-11-24 16:15:07'),
(4, 4, '4x4', 'Hossana', 100.00, 'condo', 'Description   bla bla', 'prop_692aef9a105639.56425814.jpg', 'available', '2025-11-29 16:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('guest','renter','owner','admin') NOT NULL DEFAULT 'renter',
  `created_at` datetime NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `status`) VALUES
(1, 'geda', 'tesemagedamo@gmail.com', '$2y$10$QqnPCy7PCykxWPdmJjg3PuutiGuIL6UhMI1cpYIgwB5lzX5PELX4y', 'renter', '2025-11-24 15:32:02', 'active'),
(2, 'gedaaa', 'gedaforschoolstuff@gmail.com', '$2y$10$vvf37XQCVKDCWKf5wvEidOMiOTFoO3QYntCu5ZAZrP5Ntf471nTBm', 'owner', '2025-11-24 15:33:49', 'active'),
(3, 'naod', 'naodhailu@gmail.com', '$2y$10$fMctK/.7SHlBAqkdHw9G6.ZtH2pzc7qC5TpFYjpffjddQN6uw2hSG', 'renter', '2025-11-24 16:09:46', 'active'),
(4, 'gedamo', 'geda@geda.com', '$2y$10$gzQPgZ/jayZmV0IB/tEzgukDZlOMi2xZCnWrjdsd8U0EDxOpe2DUC', 'owner', '2025-11-29 15:57:50', 'active'),
(5, 'naod', 'naod@naod.com', '$2y$10$q8d8oMjLiAYeVZIyqk7XR.Q6yvvlgfmDg6yIaCodQZ5vB8bZM52WW', 'renter', '2025-11-29 16:10:24', 'active');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
