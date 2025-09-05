-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 09:16 AM
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
-- Database: `raisdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_cards`
--

CREATE TABLE `about_cards` (
  `id` int(11) NOT NULL,
  `tab_title` varchar(255) NOT NULL,
  `card_title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_cards`
--

INSERT INTO `about_cards` (`id`, `tab_title`, `card_title`, `content`, `sort_order`) VALUES
(1, 'one', 'mission', 'this is mission', 0),
(2, 'two', 'vision', 'this is vision ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `about_content_blocks`
--

CREATE TABLE `about_content_blocks` (
  `id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `content` text DEFAULT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `media_type` varchar(10) DEFAULT 'image',
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content_blocks`
--

INSERT INTO `about_content_blocks` (`id`, `type`, `content`, `media_path`, `media_type`, `sort_order`) VALUES
(1, 'text', 'to learn more about usafad;lj', NULL, 'image', 0);

-- --------------------------------------------------------

--
-- Table structure for table `about_main`
--

CREATE TABLE `about_main` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `media_type` varchar(10) DEFAULT 'image'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_main`
--

INSERT INTO `about_main` (`id`, `title`, `description`, `media_path`, `media_type`) VALUES
(1, 'About Roman & Associates Immigration Services ', 'We are a licensed Canadian immigration firm based in Vancouver Island BC, providing expert advice on visas, permits, and sponsorships to help people achieve a brighter future in Canada.', 'uploads/about/1756978673_niagara22.mp4', 'video');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `is_archived_by_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`, `is_read`, `is_archived_by_admin`) VALUES
(2, 0, 4, 'hello', '2025-08-29 00:27:04', 0, 0),
(3, 4, 0, 'how are you', '2025-08-29 00:27:15', 0, 0),
(4, 2, 0, 'Hellow madlang people mabuhaay', '2025-08-29 00:28:11', 0, 0),
(5, 2, 0, 'Hello po ate', '2025-08-29 01:38:28', 0, 0),
(6, 0, 2, 'Hi po', '2025-08-29 01:38:37', 0, 0),
(0, 0, 4, 'fine', '2025-09-04 04:17:46', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `consultation_date` date NOT NULL,
  `consultation_time` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `departure_city` varchar(255) NOT NULL,
  `destination_city` varchar(255) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hero_media`
--

CREATE TABLE `hero_media` (
  `id` int(11) NOT NULL,
  `media_name` varchar(255) NOT NULL,
  `uploader` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_media`
--

INSERT INTO `hero_media` (`id`, `media_name`, `uploader`, `upload_date`, `file_path`, `is_active`) VALUES
(1, 'Trial', 'me', '2025-09-04 12:30:50', 'uploads/hero/hero_68b915fa451cf3.70130649.mp4', 1);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `hero_media_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `hero_media_path`, `file_path`, `created_at`) VALUES
(7, 'Visa Permit', '', 'uploads/service/68b980189ef9d-Fvisit.jpg', 'services/visa-permit.php', '2025-09-04 12:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `service_sections`
--

CREATE TABLE `service_sections` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_sections`
--

INSERT INTO `service_sections` (`id`, `service_id`, `title`, `content`, `media_path`, `display_order`) VALUES
(10, 7, 'About', 'asdfhvkclxcx', 'uploads/service/68b980189f98e-visa1.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `statement_of_account`
--

CREATE TABLE `statement_of_account` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `charges` decimal(10,2) DEFAULT NULL,
  `payments` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profileImage` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `gmail` varchar(255) DEFAULT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `documents_uploaded` tinyint(1) NOT NULL DEFAULT 0,
  `profile_picture_uploaded` tinyint(1) NOT NULL DEFAULT 0,
  `birthday_added` tinyint(1) NOT NULL DEFAULT 0,
  `social_links_added` tinyint(1) NOT NULL DEFAULT 0,
  `has_seen_tour` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(50) NOT NULL DEFAULT 'client',
  `status` varchar(50) NOT NULL DEFAULT 'Inactive',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `address`, `phone`, `email`, `password`, `profileImage`, `location`, `birthday`, `facebook`, `instagram`, `gmail`, `email_notifications`, `dark_mode`, `documents_uploaded`, `profile_picture_uploaded`, `birthday_added`, `social_links_added`, `has_seen_tour`, `role`, `status`, `last_login`, `last_activity`) VALUES
(1, 'John Paul', 'Godoy', 'Darasa, Tanauan City Batangas', '09359306521', 'godoyjp443@gmail.com', '$2y$10$LxDGp8XROe201KZCttcLSOUlAqajOp5/TqhlZk89ReZwLbMjpzFf.', 'uploads/chae1.jpg', NULL, '2005-02-04', 'https://www.facebook.com/chaepi04', '', '', 1, 1, 0, 0, 0, 0, 1, 'client', 'Inactive', NULL, NULL),
(2, 'Kim', 'Chaewon', 'Darasa, Tanauan City Batangas', '09359306521', 'kimchae1chi@gmail.com', '$2y$10$YzarQtmz8o0nxRxl5vASierqYIj5.pGSSZ1yNhkgHQ/2gnW4N9vqC', 'uploads/68aea7cdd0f67-cha.jpg', NULL, '2005-08-18', 'https://www.facebook.com/chaepi04', '', 'godoyjp443@gmail.com', 1, 0, 0, 1, 1, 1, 1, 'client', 'Inactive', NULL, '2025-08-29 17:52:53'),
(3, 'Kim', 'Yooyeon', 'Darasa', '09359306521', 'jp04@gmail.com', '$2y$10$hCC3xNl8HBw99lN/6gi5Z.etwr0OC79hXywGdIC5nrq2BfR6m3NQm', 'uploads/68ae93c2840bb-chaewon.jpg', NULL, '2005-02-04', 'https://www.facebook.com/chaepi04', '', '', 1, 1, 0, 1, 1, 1, 1, 'client', 'Inactive', NULL, NULL),
(4, 'Jisoo', 'Hong', 'San Pedro, Santo Tomas, Batangas', '09618225084', 'hongjisoo@gmail.com', '$2y$10$JdXfhvws62So9kLTaB5Q7uyznoRFIbsVKdawmKKGea44eZZlTMUGu', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1, 'client', 'Inactive', NULL, '2025-09-04 12:30:59'),
(5, 'Matthew', 'Hernandez', 'San Pedro, Santo Tomas, Batangas', '09067664653', 'matthewehernandez0712@gmail.com', '$2y$10$nhydeg12pimZzlCLtyeOkeV.fUTJc2DjYZE4DOQ6PAIccodCxmDnq', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1, 'Super Admin', 'Inactive', NULL, '2025-09-04 17:03:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_documents`
--

CREATE TABLE `user_documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_documents`
--

INSERT INTO `user_documents` (`id`, `user_id`, `file_name`, `file_path`, `upload_date`, `status`) VALUES
(1, 2, 'cha.jpg', '../uploads/68ae9150f3ca9-cha.jpg', '2025-08-26 21:02:09', 'pending'),
(2, 3, 'afs.webp', '../uploads/68ae96a5e3c80-afs.webp', '2025-08-26 21:24:53', 'pending'),
(3, 3, 'chae.webp', '../uploads/68ae96a5e57d6-chae.webp', '2025-08-26 21:24:53', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `v_chat_with_names`
--

CREATE TABLE `v_chat_with_names` (
  `message_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sender_id` int(11) DEFAULT NULL,
  `sender_firstName` varchar(50) DEFAULT NULL,
  `sender_lastName` varchar(50) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_cards`
--
ALTER TABLE `about_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `about_content_blocks`
--
ALTER TABLE `about_content_blocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `about_main`
--
ALTER TABLE `about_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_media`
--
ALTER TABLE `hero_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_sections`
--
ALTER TABLE `service_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_cards`
--
ALTER TABLE `about_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `about_content_blocks`
--
ALTER TABLE `about_content_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `about_main`
--
ALTER TABLE `about_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hero_media`
--
ALTER TABLE `hero_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `service_sections`
--
ALTER TABLE `service_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `service_sections`
--
ALTER TABLE `service_sections`
  ADD CONSTRAINT `service_sections_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
