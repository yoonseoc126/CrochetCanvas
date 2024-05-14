-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 05, 2024 at 08:12 PM
-- Server version: 8.0.37
-- PHP Version: 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yoonseoc_crochet_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` int NOT NULL,
  `path` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`image_id`, `path`) VALUES
(1, 'capybara.png'),
(2, 'crochet_cat.png'),
(3, 'crochet_duck.png'),
(4, 'mushroom_crochet.png'),
(26, 'crochet_cat.png'),
(27, 'crochet_cat.png'),
(28, '461164b6-3cbe-4ad4-8caa-dddfe4d1f0be.jpeg'),
(29, 'How-To-Get-Each-Of-Eevees-Evolutions-In-Pokemon-Go.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int NOT NULL,
  `name` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `duration` varchar(64) DEFAULT NULL,
  `yarn_id` int DEFAULT NULL,
  `hook_size` varchar(64) DEFAULT NULL,
  `image_id` int DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` int NOT NULL,
  `notes` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `name`, `date`, `duration`, `yarn_id`, `hook_size`, `image_id`, `url`, `status`, `notes`) VALUES
(2, 'lazy cat', '2024-03-11', 'n/a', 1, '4mm', 27, 'https://www.youtube.com/watch?v=M2eFwiWv7iE', 2, NULL),
(3, 'capybara', '2023-05-10', '2 days', 2, '5.5mm', 1, '', 3, NULL),
(4, 'froggy mushroom', '2023-03-27', '2 days', 3, '5.5mm', 4, 'https://youtu.be/WM8jHNE91eM?si=N4bBsH-RdcX7IZJK', 3, NULL),
(5, 'froggy duck', '2023-03-22', '3 days', 3, '5mm', 3, 'https://www.youtube.com/watch?v=2a9kFedQhhc&ab_channel=Sara%F0%9F%92%8C', 3, NULL),
(46, 'project 3', '2024-05-05', '', NULL, '', 28, '', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `name`) VALUES
(1, 'not started'),
(2, 'in progress'),
(3, 'finished');

-- --------------------------------------------------------

--
-- Table structure for table `yarns`
--

CREATE TABLE `yarns` (
  `yarn_id` int NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `yarns`
--

INSERT INTO `yarns` (`yarn_id`, `name`) VALUES
(1, 'Impeccable Solid Yarn by Loops & Threads (White)'),
(2, 'Estako Velvet Yarn (Camel)'),
(3, 'NICEEC Chenille Yarn (Army Green)'),
(4, 'Lion Brand Touch of Linen Yarn (Clay)');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `yarn_id` (`yarn_id`),
  ADD KEY `imgfile_id` (`image_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `yarns`
--
ALTER TABLE `yarns`
  ADD PRIMARY KEY (`yarn_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `yarns`
--
ALTER TABLE `yarns`
  MODIFY `yarn_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`image_id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`yarn_id`) REFERENCES `yarns` (`yarn_id`),
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`status`) REFERENCES `status` (`status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
