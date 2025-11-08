-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 02:39 PM
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
-- Database: `campus_violation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(33) NOT NULL,
  `lname` varchar(33) NOT NULL,
  `email` varchar(33) NOT NULL,
  `password` varchar(33) NOT NULL,
  `role` varchar(33) NOT NULL,
  `profile` varchar(333) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `role`, `profile`, `last_activity`) VALUES
(1, 'Lance', 'Java', 'lancelevi', '123', 'admin', '../img/lance.webp', '2025-11-03 12:39:29'),
(2, 'Kyle', 'Intud', 'kyle', '192', 'teacher', '../img/kyle.jpg', '2025-11-03 12:33:03'),
(3, 'Alwyn Mark', 'Romo', 'romo', '124', 'Guard', '../img/romo.jpg', '2025-11-03 08:46:07');

-- --------------------------------------------------------

--
-- Table structure for table `violation`
--

CREATE TABLE `violation` (
  `vi_id` int(11) NOT NULL,
  `person` varchar(33) NOT NULL,
  `location` varchar(33) NOT NULL,
  `typeviolation` varchar(33) NOT NULL,
  `description` varchar(33) NOT NULL,
  `evidence` varchar(333) NOT NULL,
  `reportedBy` varchar(33) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `status_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation`
--

INSERT INTO `violation` (`vi_id`, `person`, `location`, `typeviolation`, `description`, `evidence`, `reportedBy`, `date`, `status_id`) VALUES
(1, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-01 18:37:23', 1),
(2, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-02 18:37:23', 1),
(3, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-02 18:37:23', 1),
(4, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-03 18:37:23', 1),
(5, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-04 18:37:23', 1),
(6, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-04 18:37:23', 1),
(7, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-05 18:37:23', 1),
(8, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-05 18:37:23', 1),
(9, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-05 18:37:23', 1),
(10, 'Hayabusa', '1', 'Tardiness', 'sass', '', 'Intud,  Kyle', '2025-11-05 18:37:23', 1),
(11, 'cypher', '2', 'Tardiness', '2', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-03 15:16:48', 1),
(12, 'Hayabusa', '1', 'Tardiness', 'sass', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-01 18:37:23', 1),
(13, 'Kagura', '1', 'Cutting Classes', 'Absent without notice', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-01 09:20:10', 1),
(14, 'Gusion', '2', 'Tardiness', 'Late for class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-02 07:45:33', 1),
(15, 'Lancelot', '3', 'Uniform Violation', 'No ID worn', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-02 10:15:00', 1),
(16, 'Angela', '1', 'Disrespect', 'Talking back to teacher', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-03 12:22:41', 1),
(17, 'Franco', '2', 'Tardiness', 'Arrived late', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-03 07:58:03', 1),
(18, 'Harith', '1', 'Cutting Classes', 'Absent during PE', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-04 14:17:55', 1),
(19, 'Chou', '2', 'Uniform Violation', 'Improper haircut', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-05 08:12:37', 1),
(20, 'Lesley', '1', 'Tardiness', 'Late arrival', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-06 07:40:22', 1),
(21, 'Granger', '3', 'Disrespect', 'Rude behavior', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-06 09:11:05', 1),
(22, 'Miya', '1', 'Tardiness', 'Late submission', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-07 10:30:47', 1),
(23, 'Alucard', '2', 'Cutting Classes', 'Did not attend Math', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-08 11:00:00', 1),
(24, 'Zilong', '3', 'Tardiness', 'Came after bell', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-09 08:42:33', 1),
(25, 'Layla', '1', 'Uniform Violation', 'No proper shoes', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-09 09:55:21', 1),
(26, 'Nana', '2', 'Disrespect', 'Shouting in class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-10 13:12:18', 1),
(27, 'Rafaela', '1', 'Tardiness', 'Late again', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-10 08:20:00', 1),
(28, 'Clint', '2', 'Cutting Classes', 'Skipped last subject', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-12 11:48:09', 1),
(29, 'Odette', '3', 'Uniform Violation', 'No necktie', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-12 07:33:47', 1),
(30, 'Karina', '1', 'Tardiness', 'Late morning arrival', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-13 08:07:19', 1),
(31, 'Hanzo', '3', 'Disrespect', 'Talking loudly', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-13 10:00:00', 1),
(32, 'Vale', '2', 'Tardiness', 'Missed first subject', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-15 09:20:44', 1),
(33, 'Yve', '1', 'Cutting Classes', 'Absent from English', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-16 10:18:59', 1),
(34, 'Irithel', '2', 'Uniform Violation', 'No proper ID', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-17 07:44:01', 1),
(35, 'Lunox', '3', 'Tardiness', 'Late in lab class', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-18 08:31:33', 1),
(36, 'Claude', '1', 'Disrespect', 'Ignored teacher instructions', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-19 09:50:14', 1),
(37, 'Cyclops', '3', 'Tardiness', 'Late by 10 minutes', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-20 07:56:12', 1),
(38, 'Pharsa', '2', 'Cutting Classes', 'Left early', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-22 14:05:48', 1),
(39, 'Johnson', '1', 'Disrespect', 'Used phone in class', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-22 09:42:00', 1),
(40, 'Aurora', '2', 'Uniform Violation', 'Improper uniform', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-24 08:11:33', 1),
(41, 'Estes', '3', 'Tardiness', 'Late arrival', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-24 07:59:05', 1),
(42, 'Atlas', '2', 'Disrespect', 'Argued with teacher', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-25 13:19:41', 1),
(43, 'Benedetta', '1', 'Cutting Classes', 'Absent in Science', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-26 09:10:57', 1),
(44, 'Khufra', '3', 'Tardiness', 'Came during quiz', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-28 08:07:11', 1),
(45, 'Moskov', '2', 'Uniform Violation', 'No ID worn', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-29 09:15:00', 1),
(46, 'Selena', '1', 'Disrespect', 'Disrupted class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-30 10:00:43', 1),
(47, 'Fanny', '3', 'Tardiness', 'Late to morning assembly', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-11-30 07:45:00', 1),
(48, 'Xavier', '1', 'Tardiness', 'Late for first subject', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-01 07:45:10', 1),
(49, 'Brody', '2', 'Uniform Violation', 'No proper ID', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-01 09:20:00', 1),
(50, 'Beatrix', '3', 'Disrespect', 'Talking while teacher explains', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-02 10:05:34', 1),
(51, 'Paquito', '1', 'Cutting Classes', 'Skipped afternoon class', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-02 13:50:00', 1),
(52, 'Yin', '3', 'Tardiness', 'Arrived after roll call', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-03 08:15:00', 1),
(53, 'Melissa', '2', 'Uniform Violation', 'No school shoes', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-04 07:55:00', 1),
(54, 'Fredrinn', '1', 'Disrespect', 'Arguing with classmate', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-05 09:10:22', 1),
(55, 'Novaria', '3', 'Cutting Classes', 'Left early before dismissal', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-05 14:12:18', 1),
(56, 'Mathilda', '1', 'Tardiness', 'Late to assembly', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-06 07:59:59', 1),
(57, 'Aamon', '2', 'Disrespect', 'Used phone during discussion', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-06 10:45:15', 1),
(58, 'Ruby', '3', 'Uniform Violation', 'No necktie', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-07 08:12:00', 1),
(59, 'Roger', '1', 'Cutting Classes', 'Skipped PE class', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-08 13:00:00', 1),
(60, 'Claude', '2', 'Tardiness', 'Came late due to traffic', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-09 08:30:00', 1),
(61, 'Wanwan', '3', 'Uniform Violation', 'Wearing slippers', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-09 09:00:00', 1),
(62, 'Lylia', '1', 'Disrespect', 'Did not follow instructions', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-10 11:22:11', 1),
(63, 'Natalia', '2', 'Cutting Classes', 'Absent without excuse', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-11 14:00:00', 1),
(64, 'Aldous', '3', 'Tardiness', 'Came after bell', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-12 07:50:33', 1),
(65, 'Guinevere', '1', 'Uniform Violation', 'No ID worn', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-12 08:15:15', 1),
(66, 'Valir', '2', 'Disrespect', 'Talking while teacher was speakin', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-13 09:45:00', 1),
(67, 'Hanabi', '3', 'Cutting Classes', 'Left during last period', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-13 13:05:40', 1),
(68, 'Kaja', '1', 'Tardiness', 'Late for 2 subjects', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-14 08:05:05', 1),
(69, 'Lapu-Lapu', '3', 'Uniform Violation', 'Improper haircut', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-15 07:59:00', 1),
(70, 'Phoveus', '2', 'Tardiness', 'Missed roll call', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-16 08:25:00', 1),
(71, 'Esmeralda', '1', 'Disrespect', 'Backtalking to teacher', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-16 10:00:00', 1),
(72, 'Masha', '2', 'Cutting Classes', 'Skipped morning subject', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-17 07:45:00', 1),
(73, 'Diggie', '3', 'Uniform Violation', 'No proper socks', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-18 09:33:00', 1),
(74, 'Gatotkaca', '1', 'Tardiness', 'Arrived at mid-class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-19 08:18:00', 1),
(75, 'Uranus', '2', 'Disrespect', 'Argued with group leader', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-20 09:10:00', 1),
(76, 'Angela', '3', 'Tardiness', 'Late again for class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-21 08:12:12', 1),
(77, 'Helcurt', '1', 'Cutting Classes', 'Skipped last class', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-22 14:00:00', 1),
(78, 'Harley', '3', 'Uniform Violation', 'Wearing colored shoes', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-23 08:00:00', 1),
(79, 'Natan', '2', 'Disrespect', 'Disrupting during test', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-23 10:00:00', 1),
(80, 'Chang’e', '1', 'Tardiness', 'Late after break', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-24 08:20:00', 1),
(81, 'Baxia', '2', 'Cutting Classes', 'Absent from Science', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-26 13:20:00', 1),
(82, 'Edith', '1', 'Uniform Violation', 'Wrong color of uniform', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-27 07:50:00', 1),
(83, 'Terizla', '3', 'Disrespect', 'Used foul language', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-27 09:00:00', 1),
(84, 'Cecilion', '2', 'Tardiness', 'Late attendance', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-28 08:05:00', 1),
(85, 'Alice', '1', 'Cutting Classes', 'Skipped second subject', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-29 13:10:00', 1),
(86, 'Harith', '2', 'Uniform Violation', 'No ID card', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-12-30 08:33:00', 1),
(87, 'Zilong', '3', 'Disrespect', 'Did not follow teacher’s order', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-12-30 10:20:00', 1),
(88, 'Aulus', '1', 'Tardiness', 'Late for first class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-06 07:45:00', 1),
(89, 'Popol', '2', 'Uniform Violation', 'No proper ID', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-06 09:00:00', 1),
(90, 'Barats', '3', 'Cutting Classes', 'Left before dismissal', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-15 13:15:22', 1),
(91, 'Floryn', '1', 'Tardiness', 'Late to assembly', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-15 08:00:00', 1),
(92, 'Lancelot', '2', 'Uniform Violation', 'No school shoes', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-16 07:50:00', 1),
(93, 'Odette', '1', 'Disrespect', 'Talking while teacher explains', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-16 10:00:00', 1),
(94, 'Selena', '3', 'Cutting Classes', 'Skipped afternoon class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-17 13:05:00', 1),
(95, 'Kaja', '2', 'Tardiness', 'Missed first subject', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-17 07:55:00', 1),
(96, 'Harley', '1', 'Uniform Violation', 'Wrong socks color', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-18 08:10:33', 1),
(97, 'Valentina', '2', 'Disrespect', 'Rude remarks', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-18 09:20:00', 1),
(98, 'Brody', '3', 'Tardiness', 'Arrived late after break', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-19 08:12:00', 1),
(99, 'Masha', '1', 'Uniform Violation', 'No necktie', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-19 07:50:00', 1),
(100, 'Yin', '2', 'Disrespect', 'Talking back to teacher', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-20 09:00:00', 1),
(101, 'Melissa', '3', 'Cutting Classes', 'Absent from Science', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-20 14:10:00', 1),
(102, 'Beatrix', '2', 'Tardiness', 'Came during quiz', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-21 08:07:00', 1),
(103, 'Fredrinn', '1', 'Uniform Violation', 'No ID worn', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-21 07:40:00', 1),
(104, 'Mathilda', '3', 'Cutting Classes', 'Skipped 3rd subject', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-22 13:30:00', 1),
(105, 'Xavier', '1', 'Disrespect', 'Ignored instructions', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-22 10:25:00', 1),
(106, 'Gusion', '2', 'Tardiness', 'Arrived late again', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-23 08:00:00', 1),
(107, 'Guinevere', '3', 'Uniform Violation', 'Wearing colored shoes', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-23 09:00:00', 1),
(108, 'Hanzo', '1', 'Disrespect', 'Arguing with teacher', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-24 10:00:00', 1),
(109, 'Claude', '2', 'Tardiness', 'Missed first period', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-24 08:10:00', 1),
(110, 'Angela', '3', 'Uniform Violation', 'No proper uniform', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-25 07:55:00', 1),
(111, 'Zilong', '1', 'Cutting Classes', 'Skipped last subject', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-25 13:45:00', 1),
(112, 'Alucard', '2', 'Disrespect', 'Used phone in class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-26 09:30:00', 1),
(113, 'Layla', '1', 'Tardiness', 'Came 15 minutes late', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-26 08:20:00', 1),
(114, 'Chou', '3', 'Uniform Violation', 'Improper haircut', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-27 07:58:00', 1),
(115, 'Franco', '2', 'Cutting Classes', 'Absent without notice', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-27 13:00:00', 1),
(116, 'Miya', '1', 'Tardiness', 'Late in morning assembly', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-28 08:05:00', 1),
(117, 'Cyclops', '3', 'Uniform Violation', 'No belt worn', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-28 07:55:00', 1),
(118, 'Karina', '2', 'Disrespect', 'Did not greet teacher', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-29 10:10:00', 1),
(119, 'Estes', '1', 'Cutting Classes', 'Skipped PE subject', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-29 13:00:00', 1),
(120, 'Pharsa', '2', 'Tardiness', 'Late to second class', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-30 08:12:00', 1),
(121, 'Atlas', '3', 'Uniform Violation', 'Wrong shirt color', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-30 07:48:00', 1),
(122, 'Hanabi', '1', 'Disrespect', 'Did not participate', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-10-31 10:00:00', 1),
(123, 'Nana', '2', 'Cutting Classes', 'Absent from group work', '../img/defaultIMG.png', 'Romo,  Alwyn Mark', '2025-10-31 13:20:00', 1),
(124, 'Hayabusa', 'Brgy. Mobile Legends City', 'Tardiness', 'sasda ormoc', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-03 19:54:21', 1),
(125, 'Lance Levi', 'Brgy. Mobile Legends City', 'Tardiness', 'description', '../img/defaultIMG.png', 'Intud,  Kyle', '2025-11-03 19:54:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `violationstatus`
--

CREATE TABLE `violationstatus` (
  `status_id` int(11) NOT NULL,
  `vio_stats` varchar(33) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violationstatus`
--

INSERT INTO `violationstatus` (`status_id`, `vio_stats`) VALUES
(1, 'Pending'),
(2, 'Dismissed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `violation`
--
ALTER TABLE `violation`
  ADD PRIMARY KEY (`vi_id`);

--
-- Indexes for table `violationstatus`
--
ALTER TABLE `violationstatus`
  ADD PRIMARY KEY (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `violation`
--
ALTER TABLE `violation`
  MODIFY `vi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `violationstatus`
--
ALTER TABLE `violationstatus`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
