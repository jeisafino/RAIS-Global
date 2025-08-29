-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 11:53 AM
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
(2, 0, 4, 'hello', '2025-08-29 08:27:04', 0, 0),
(3, 4, 0, 'how are you', '2025-08-29 08:27:15', 0, 0),
(4, 2, 0, 'Hellow madlang people mabuhaay', '2025-08-29 08:28:11', 0, 0),
(5, 2, 0, 'Hello po ate', '2025-08-29 09:38:28', 0, 0),
(6, 0, 2, 'Hi po', '2025-08-29 09:38:37', 0, 0);

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
(4, 'Jisoo', 'Hong', 'San Pedro, Santo Tomas, Batangas', '09618225084', 'hongjisoo@gmail.com', '$2y$10$JdXfhvws62So9kLTaB5Q7uyznoRFIbsVKdawmKKGea44eZZlTMUGu', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1, 'client', 'Inactive', NULL, '2025-08-29 16:27:43'),
(5, 'Matthew', 'Hernandez', 'San Pedro, Santo Tomas, Batangas', '09067664653', 'matthewehernandez0712@gmail.com', '$2y$10$nhydeg12pimZzlCLtyeOkeV.fUTJc2DjYZE4DOQ6PAIccodCxmDnq', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1, 'Super Admin', 'Inactive', NULL, '2025-08-29 14:17:45');

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
(1, 2, 'cha.jpg', '../uploads/68ae9150f3ca9-cha.jpg', '2025-08-27 05:02:09', 'pending'),
(2, 3, 'afs.webp', '../uploads/68ae96a5e3c80-afs.webp', '2025-08-27 05:24:53', 'pending'),
(3, 3, 'chae.webp', '../uploads/68ae96a5e57d6-chae.webp', '2025-08-27 05:24:53', 'pending');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_chat_with_names`
-- (See below for the actual view)
--
CREATE TABLE `v_chat_with_names` (
`message_id` int(11)
,`message` text
,`timestamp` timestamp
,`sender_id` int(11)
,`sender_firstName` varchar(50)
,`sender_lastName` varchar(50)
,`receiver_id` int(11)
);

-- --------------------------------------------------------

--
-- Structure for view `v_chat_with_names`
--
DROP TABLE IF EXISTS `v_chat_with_names`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_chat_with_names`  AS SELECT `cm`.`id` AS `message_id`, `cm`.`message` AS `message`, `cm`.`timestamp` AS `timestamp`, `cm`.`sender_id` AS `sender_id`, `u`.`firstName` AS `sender_firstName`, `u`.`lastName` AS `sender_lastName`, `cm`.`receiver_id` AS `receiver_id` FROM (`chat_messages` `cm` left join `users` `u` on(`cm`.`sender_id` = `u`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `statement_of_account`
--
ALTER TABLE `statement_of_account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statement_of_account`
--
ALTER TABLE `statement_of_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `flights_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `statement_of_account`
--
ALTER TABLE `statement_of_account`
  ADD CONSTRAINT `statement_of_account_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD CONSTRAINT `user_documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
