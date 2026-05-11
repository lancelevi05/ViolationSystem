-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 01:09 PM
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
-- Table structure for table `advisory_tbl`
--

CREATE TABLE `advisory_tbl` (
  `id` int(11) NOT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisory_tbl`
--

INSERT INTO `advisory_tbl` (`id`, `adviser_id`, `idstrandcourse`, `glevel`, `section`) VALUES
(217, 5, 1, '11', 'A'),
(218, 5, 1, '12', 'A'),
(222, 4, 7, '1', 'A'),
(223, 4, 7, '1', 'B'),
(224, 4, 7, '2', 'A'),
(225, 4, 7, '2', 'B'),
(226, 10, 1, '11', 'B'),
(227, 10, 1, '12', 'B'),
(233, 2, 10, '1', 'A'),
(234, 2, 10, '1', 'B'),
(235, 2, 10, '1', 'C'),
(236, 2, 10, '1', 'D'),
(237, 2, 10, '1', 'E');

-- --------------------------------------------------------

--
-- Table structure for table `collegecourse_tbl`
--

CREATE TABLE `collegecourse_tbl` (
  `idcourse` int(11) NOT NULL,
  `course` varchar(33) NOT NULL,
  `max_section` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collegecourse_tbl`
--

INSERT INTO `collegecourse_tbl` (`idcourse`, `course`, `max_section`) VALUES
(1, 'BSIT', 4),
(2, 'BSCS', 1),
(3, 'BSBA', 5),
(4, 'BSHM', 5),
(5, 'ACT', 4),
(6, 'WAD', 4),
(7, 'OAT', 4),
(8, 'HRT', 4);

-- --------------------------------------------------------

--
-- Table structure for table `collegedep_tbl`
--

CREATE TABLE `collegedep_tbl` (
  `iddepartment` int(11) NOT NULL,
  `department` varchar(33) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collegedep_tbl`
--

INSERT INTO `collegedep_tbl` (`iddepartment`, `department`) VALUES
(1, 'CS DEPARTMENT'),
(2, 'HM DEPARTMENT'),
(3, 'BUSINESS DEPARTMENT');

-- --------------------------------------------------------

--
-- Table structure for table `college_tbl`
--

CREATE TABLE `college_tbl` (
  `id` int(11) NOT NULL,
  `usn` bigint(33) NOT NULL,
  `lname` varchar(33) NOT NULL,
  `fname` varchar(33) NOT NULL,
  `mname` varchar(32) NOT NULL,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL,
  `genid` int(11) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(333) NOT NULL,
  `iddepartment` int(11) DEFAULT NULL,
  `vio_record` int(11) NOT NULL,
  `Archive` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_tbl`
--

INSERT INTO `college_tbl` (`id`, `usn`, `lname`, `fname`, `mname`, `idstrandcourse`, `glevel`, `section`, `genid`, `birthdate`, `address`, `iddepartment`, `vio_record`, `Archive`) VALUES
(23, 14928591801, 'Garcia', 'Johna', 'Alexander', 7, '1', 'A', 1, '2005-01-15', 'Biliran, Culaba', 1, 1, 0),
(24, 15849271652, 'Reyes', 'Maria', 'Beatrice', 7, '1', 'A', 2, '2005-02-10', 'Biliran, Culaba', 1, 1, 0),
(25, 16259837144, 'Lopez', 'Miguels', 'Christian', 10, '1', 'A', 1, '2005-03-20', 'Biliran, Culaba', 2, 0, 0),
(26, 17598346210, 'Torres', 'Ana', 'Louise', 7, '1', 'B', 2, '2005-04-11', 'Biliran, Culaba', 1, 0, 0),
(27, 18374659221, 'Cruz', 'Paulo', 'Ricardo', 7, '2', 'C', 1, '2005-05-05', 'Biliran, Culaba', 1, 0, 1),
(28, 19485732019, 'Santos', 'Ellaaa', 'Katherine', 7, '1', 'D', 2, '2005-06-12', 'Biliran, Culaba', 1, 0, 1),
(29, 15382947562, 'Navarro', 'Luis', 'Michael', 8, '2', 'A', 1, '2005-01-20', 'Biliran, Culaba', 1, 0, 0),
(30, 16749283501, 'Delos Santos', 'Leah', 'Nicole', 8, '1', 'A', 2, '2005-02-15', 'Biliran, Culaba', 1, 0, 1),
(31, 17938465277, 'Perez', 'Ethan', 'Bryan', 9, '1', 'A', 1, '2005-03-01', 'Biliran, Culaba', 2, 0, 0),
(32, 19837462588, 'Dalisay', 'Cardo', 'a', 7, '1', 'B', 1, '2005-03-16', 'Bulacan, Paombong, Santa Cruz', 1, 1, 1),
(33, 22000621300, 'JAVA', 'LANCE LEVI', 'MOLATO', 12, '1', 'D', 1, '2025-10-29', 'Agusan Del Norte, Nasipit, Cubi-cubi', 1, 0, 0),
(34, 46276236736, 'Dalisay', 'Ador', 'a', 8, '1', 'A', 1, '2025-11-04', 'Bohol, Catigbian, Mahayag Norte', 1, 1, 0),
(35, 9876543231, 'Abayata', 'RJ', '', 9, '1', 'B', 1, '2003-01-01', 'Batangas, Cuenca, Barangay 5 (Pob.)', 2, 1, 0),
(36, 17283940501, 'Delgado', 'Kevin', 'Manalo', 7, '1', 'A', 1, '2005-02-14', 'Manila, Sampaloc', 1, 0, 0),
(37, 17283940502, 'Ortega', 'Melissa', 'Reyes', 7, '2', 'B', 2, '2004-11-23', 'Cebu, Mandaue', 1, 0, 0),
(38, 17283940503, 'Fernandez', 'Jomar', 'Torres', 7, '1', 'B', 1, '2005-07-08', 'Davao, Matina', 1, 0, 0),
(39, 17283940504, 'Santos', 'Clarissa', 'Ramos', 7, '2', 'A', 2, '2004-05-15', 'Quezon City, Cubao', 1, 0, 0),
(40, 17283940505, 'Villanueva', 'Mark', 'Perez', 7, '1', 'A', 1, '2005-03-19', 'Batangas, Lipa', 1, 0, 0),
(41, 17283940506, 'Torres', 'Nicole', 'Cruz', 7, '2', 'B', 2, '2004-09-12', 'Iloilo, Mandurriao', 1, 0, 0),
(42, 17283940507, 'Bautista', 'Christian', 'Santos', 7, '1', 'B', 1, '2005-12-01', 'Laguna, Sta. Rosa', 1, 0, 0),
(43, 17283940508, 'Reyes', 'Angela', 'Jimenez', 7, '2', 'A', 2, '2004-08-05', 'Cagayan de Oro, Carmen', 1, 0, 0),
(44, 17283940509, 'Gomez', 'Jerome', 'Navarro', 7, '1', 'A', 1, '2005-06-22', 'Bacolod, Pahanocoy', 1, 0, 0),
(45, 17283940510, 'Castro', 'Hannah', 'Lopez', 7, '2', 'B', 2, '2004-10-30', 'Pampanga, Angeles', 1, 0, 0),
(46, 17283940501, 'Delgado', 'Kevin', 'Manalo', 7, '1', 'A', 1, '2005-02-14', 'Manila, Sampaloc', 1, 0, 0),
(47, 17283940502, 'Ortega', 'Melissa', 'Reyes', 7, '2', 'B', 2, '2004-11-23', 'Cebu, Mandaue', 1, 0, 0),
(48, 17283940503, 'Fernandez', 'Jomar', 'Torres', 7, '1', 'B', 1, '2005-07-08', 'Davao, Matina', 1, 0, 0),
(49, 17283940504, 'Santos', 'Clarissa', 'Ramos', 7, '2', 'A', 2, '2004-05-15', 'Quezon City, Cubao', 1, 0, 0),
(50, 17283940505, 'Villanueva', 'Mark', 'Perez', 7, '1', 'A', 1, '2005-03-19', 'Batangas, Lipa', 1, 0, 0),
(51, 17283940506, 'Torres', 'Nicole', 'Cruz', 7, '2', 'B', 2, '2004-09-12', 'Iloilo, Mandurriao', 1, 0, 0),
(52, 17283940507, 'Bautista', 'Christian', 'Santos', 7, '1', 'B', 1, '2005-12-01', 'Laguna, Sta. Rosa', 1, 0, 0),
(53, 17283940508, 'Reyes', 'Angela', 'Jimenez', 7, '2', 'A', 2, '2004-08-05', 'Cagayan de Oro, Carmen', 1, 0, 0),
(54, 17283940509, 'Gomez', 'Jerome', 'Navarro', 7, '1', 'A', 1, '2005-06-22', 'Bacolod, Pahanocoy', 1, 0, 0),
(55, 17283940510, 'Castro', 'Hannah', 'Lopez', 7, '2', 'B', 2, '2004-10-30', 'Pampanga, Angeles', 1, 0, 0),
(56, 23463463463, 'Magallanes', 'Avelino', 'V', 7, '2', 'B', 1, '2003-01-01', 'Benguet, Mankayan, Cabiten', 1, 1, 0),
(57, 23652462346, 'GONZALES', 'Josh Neithan', '', 7, '2', 'A', 1, '2003-01-01', 'Biliran, Culaba, Looc', 1, 0, 0),
(58, 12541351353, 'Alcuetas', 'Yuan Axl', '', 7, '2', 'B', 1, '2003-01-01', 'Benguet, Sablan, Banengbeng', 1, 1, 0),
(59, 36234623623, 'Labana', 'John Rex', '', 7, '2', 'B', 1, '2003-01-01', 'Biliran, Kawayan, Masagaosao', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gender_tbl`
--

CREATE TABLE `gender_tbl` (
  `genid` int(11) NOT NULL,
  `gender` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gender_tbl`
--

INSERT INTO `gender_tbl` (`genid`, `gender`) VALUES
(1, 'Male'),
(2, 'Female');

-- --------------------------------------------------------

--
-- Table structure for table `notification_tbl`
--

CREATE TABLE `notification_tbl` (
  `id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `violation_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `message` varchar(500) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shsdep_tbl`
--

CREATE TABLE `shsdep_tbl` (
  `iddepartment` int(11) NOT NULL,
  `department` varchar(33) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shsdep_tbl`
--

INSERT INTO `shsdep_tbl` (`iddepartment`, `department`) VALUES
(1, 'IT Department'),
(2, 'STEM Department'),
(3, 'HE Department'),
(4, 'Academic Department');

-- --------------------------------------------------------

--
-- Table structure for table `shsstrand_tbl`
--

CREATE TABLE `shsstrand_tbl` (
  `idstrand` int(11) NOT NULL,
  `strand` varchar(33) NOT NULL,
  `max_section` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shsstrand_tbl`
--

INSERT INTO `shsstrand_tbl` (`idstrand`, `strand`, `max_section`) VALUES
(1, 'Programming', 2),
(2, 'Animation', 1),
(3, 'Gas', 7),
(4, 'HE', 5),
(5, 'STEM', 8),
(6, 'HUMMS', 6);

-- --------------------------------------------------------

--
-- Table structure for table `shs_tbl`
--

CREATE TABLE `shs_tbl` (
  `id` int(11) NOT NULL,
  `usn` bigint(113) NOT NULL,
  `lname` varchar(33) NOT NULL,
  `fname` varchar(33) NOT NULL,
  `mname` varchar(32) NOT NULL,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL,
  `genid` int(11) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(333) NOT NULL,
  `iddepartment` int(11) DEFAULT NULL,
  `vio_record` int(11) NOT NULL,
  `Archive` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shs_tbl`
--

INSERT INTO `shs_tbl` (`id`, `usn`, `lname`, `fname`, `mname`, `idstrandcourse`, `glevel`, `section`, `genid`, `birthdate`, `address`, `iddepartment`, `vio_record`, `Archive`) VALUES
(124, 15928471652, 'Smith', 'John', 'Alexander', 1, '12', 'A', 1, '2008-01-15', 'Biliran, Culaba', 1, 1, 0),
(125, 17392058411, 'Doe', 'Jane', 'Beatrice', 1, '12', 'A', 2, '2008-02-20', 'Biliran, Culaba', 1, 1, 1),
(126, 14837592016, 'Ramos', 'Carlos', 'Christian', 1, '12', 'A', 1, '2008-03-05', 'Biliran, Culaba', 1, 1, 0),
(127, 16294837155, 'Reyes', 'Ana', 'Louise', 1, '12', 'A', 2, '2008-04-18', 'Biliran, Culaba', 1, 0, 0),
(128, 19485732019, 'Lopez', 'Miguel', 'Patrick', 2, '12', 'A', 1, '2008-05-25', 'Biliran, Culaba', 1, 0, 0),
(129, 15293746180, 'Garcia', 'Maria', 'Josephine', 1, '12', 'A', 2, '2008-06-15', 'Biliran, Culaba', 1, 1, 0),
(130, 16749283501, 'Tan', 'Luis', 'Michael', 1, '12', 'A', 1, '2008-07-12', 'Biliran, Culaba', 1, 0, 0),
(131, 18374659221, 'Cruz', 'Paulo', 'Ricardo', 1, '12', 'B', 1, '2008-08-03', 'Biliran, Culaba', 1, 1, 0),
(132, 17649258344, 'Santos', 'Ella', 'Katherine', 1, '12', 'A', 2, '2008-09-17', 'Biliran, Culaba', 1, 0, 0),
(133, 19837462588, 'Villanueva', 'Mark', 'Thomas', 1, '12', 'A', 1, '2008-10-21', 'Biliran, Culaba', 1, 0, 0),
(134, 15589372041, 'Morales', 'Leah', 'Nicole', 1, '12', 'A', 2, '2008-11-02', 'Biliran, Culaba', 1, 0, 0),
(135, 16482937510, 'Perez', 'Ian', 'Gabriel', 1, '12', 'A', 1, '2008-12-15', 'Biliran, Culaba', 1, 0, 1),
(136, 17938465277, 'Alvarez', 'May', 'Faith1', 4, '12', 'B', 2, '2008-01-20', 'Biliran, Culaba', 3, 1, 1),
(137, 19385746290, 'Navarro', 'Jake', 'Henry', 1, '12', 'A', 1, '2008-02-10', 'Biliran, Culaba', 1, 0, 0),
(138, 18849273566, 'Delos Santoss', 'Rina1', 'Clairse', 1, '11', 'B', 2, '2008-03-29', 'Biliran, Culaba', 1, 0, 0),
(139, 14682937522, 'Torres', 'Ethan', 'Bryan', 1, '12', 'A', 1, '2008-04-07', 'Biliran, Culaba', 1, 0, 0),
(140, 17294836511, 'Gutierrez', 'Lana', 'Danielle', 1, '12', 'A', 2, '2008-05-19', 'Biliran, Culaba', 1, 1, 0),
(141, 18593746280, 'Castillo', 'Noah', 'Edward', 1, '12', 'A', 1, '2008-06-23', 'Biliran, Culaba', 1, 1, 0),
(142, 14928375640, 'Diaz', 'Sofia', 'Madeline', 1, '12', 'A', 2, '2008-07-30', 'Biliran, Culaba', 1, 0, 0),
(143, 16849273519, 'Vega', 'Liam', 'Robert', 1, '12', 'A', 1, '2008-08-11', 'Biliran, Culaba', 1, 0, 0),
(144, 75843563246, 'CARANAWA', 'BEA', 's', 1, '12', 'A', 2, '2003-03-15', 'Camarines Norte, San Vicente, Man-Ogob', 1, 0, 0),
(145, 13512542512, 'MESTOLA', 'JONNEL', '', 1, '12', 'A', 1, '2003-01-01', 'Benguet, Sablan, Kamog', 1, 1, 0),
(146, 24142634737, 'CAPORAS', 'ROLANDO', '', 1, '12', 'A', 1, '2003-01-01', 'Biliran, Maripipi, Ermita', 1, 0, 0),
(147, 22000621300, 'JAVA', 'Mark', '', 2, '11', 'A', 1, '2003-01-01', 'Bukidnon, Malitbog, Santa Ines', 1, 0, 0),
(148, 1111111111, 'Hitler', 'Adolf', '', 1, '11', 'B', 1, '2003-01-01', 'Bohol, Clarin, Caluwasan', 1, 0, 0),
(149, 15928471001, 'Garcia', 'Miguel', 'Santos', 1, '11', 'B', 1, '2008-03-12', 'Manila, Tondo', 1, 0, 0),
(150, 15928471002, 'Reyes', 'Angela', 'Lopez', 1, '12', 'B', 2, '2007-11-05', 'Cebu, Lapu-Lapu', 1, 0, 0),
(151, 15928471003, 'Cruz', 'Nathaniel', 'Torres', 1, '11', 'B', 1, '2008-07-21', 'Leyte, Ormoc', 1, 0, 0),
(152, 15928471004, 'Dela Cruz', 'Sofia', 'Marquez', 1, '12', 'B', 2, '2007-09-14', 'Quezon City, Commonwealth', 1, 0, 0),
(153, 15928471005, 'Villanueva', 'Carlos', 'Ramos', 1, '11', 'B', 1, '2008-02-10', 'Cavite, Bacoor', 1, 0, 0),
(154, 15928471006, 'Santos', 'Eunice', 'Fernandez', 1, '12', 'B', 2, '2007-05-19', 'Davao City, Buhangin', 1, 0, 0),
(155, 15928471007, 'Bautista', 'Jerome', 'Pascual', 1, '11', 'B', 1, '2008-10-08', 'Iloilo, Jaro', 1, 0, 0),
(156, 15928471008, 'Torres', 'Hannah', 'Jimenez', 1, '12', 'B', 2, '2007-12-25', 'Bohol, Tagbilaran', 1, 0, 0),
(157, 15928471009, 'Ramirez', 'Christian', 'Gomez', 1, '11', 'B', 1, '2008-04-17', 'Tacloban City, Sagkahan', 1, 0, 0),
(158, 15928471010, 'Navarro', 'Julia', 'Castro', 1, '12', 'B', 2, '2007-08-29', 'Pampanga, San Fernando', 1, 0, 0),
(159, 12345678234, 'SubingSubing', 'Julius', '', 1, '12', 'B', 1, '2003-01-01', 'Bataan, Mariveles, Ipag', 1, 0, 0),
(160, 23626747345, 'Flores', 'Andrie', '', 1, '12', 'B', 1, '2005-01-01', 'Bataan, Orion, General Lim', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `strandcourse_tbl`
--

CREATE TABLE `strandcourse_tbl` (
  `idstrandcourse` int(11) NOT NULL,
  `strandcourse` varchar(33) NOT NULL,
  `max_section` int(11) NOT NULL,
  `shs_college` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strandcourse_tbl`
--

INSERT INTO `strandcourse_tbl` (`idstrandcourse`, `strandcourse`, `max_section`, `shs_college`) VALUES
(1, 'Programming', 2, 1),
(2, 'Animation', 1, 1),
(3, 'CSS', 3, 1),
(4, 'HE', 5, 1),
(5, 'STEM', 8, 1),
(6, 'HUMMS', 6, 1),
(7, 'BSIT', 4, 0),
(8, 'BSCS', 1, 0),
(9, 'BSBA', 5, 0),
(10, 'BSHM', 5, 0),
(11, 'ACT', 4, 2),
(12, 'WAD', 4, 2),
(13, 'OAT', 4, 2),
(14, 'HRT', 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(33) NOT NULL,
  `lname` varchar(33) NOT NULL,
  `email` varchar(33) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(33) NOT NULL,
  `genid` int(11) DEFAULT NULL,
  `profile` varchar(333) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `role`, `genid`, `profile`, `last_activity`) VALUES
(1, 'Regi', 'Porcari', 'regi', '$2y$10$clZTX3iKmBZ.O0Z/AHRGbO4eUmGagzlu8KvvZiIbWLE8Ehs1Qmz32', 'admin', 2, '../img/porcari.png', '2025-11-30 07:31:40'),
(2, 'Rafael', 'Sabino', 'sabino', '$2y$10$lkgs52NiYs4WO581zSXQGuGDdQASVkiwweHeTpnv.p0iEPIdhCIt6', 'teacher', 1, '../img/sabino.png', '2025-11-30 11:56:50'),
(3, 'Alwyn Mark', 'Romo', 'romo', '$2y$10$364n1f.dmdrJe6kZPVcX9.BFHQ92ZE4Gq189oZ.OCt4z6qzFs4ui.', 'Guard', 1, '../img/romo.jpg', '2025-11-30 12:09:18'),
(4, 'Rina', 'Lucanas', 'rina', '$2y$10$Ud0J8WZrgXTVjm2wbkWF6uIIjJ3JZFxt4Gjx80HQQRZ9b1fI.lmia', 'teacher', 2, '../img/lucanas.png', '2025-11-30 12:08:23'),
(5, 'Russel', 'Guinares', 'russel', '$2y$10$WWlUs2yNXIY94B9oScsO6eZCeTN7gueK1Q7P66Ay1sJ3Y3MZSFROi', 'admin', 1, '../img/guinares.jpg', '2025-11-30 12:06:19'),
(6, 'Marizthela', 'De La Cruz', 'delacruz', '$2y$10$197nkptPr2uu7eFrToJi7ese8RZnBWoJy5eB4SiGtqYnpWYJKcU0G', 'teacher', 2, '../img/delacruz.jpg', '2025-11-30 11:19:37'),
(9, 'Dunhill Mar Louise', 'Leal', 'leal', '$2y$10$ntq5SeYnxiuX29yefk3Cme1MIwYgVvZvJYtf/1KpkrQdSXKMauG4G', 'teacher', 1, '../img/leal.jpg', '2025-11-25 09:59:14'),
(10, 'Jonas', 'Molato', 'jonas', '$2y$10$Tedk0p2BDmAPm9VJz6X9beCDXBGVKK.Ya7XPDaGz.rPN7aMrS3eL.', 'Guidance', 1, '../img/molato.jpg', '2025-11-30 12:01:03'),
(11, 'Lance Levi', 'Java', 'lancelevi', '$2y$10$chkJVsLiURQNt81pKdBbW.TgjosbzfSpdVP.A.GiOhifskDDNdP6i', 'Technical', 1, '../img/java.jpg', '2025-11-30 06:45:08'),
(12, 'Kyle', 'Intud', 'kyle', '$2y$10$zk03B7UlYwKVeg54vbJNYuuBVuKGLWCT6s.vtKylCBxcMV6Aw/JDu', 'Janitor', 1, '../img/defaultIMG.png', '2025-11-29 12:46:24');

-- --------------------------------------------------------

--
-- Table structure for table `violation`
--

CREATE TABLE `violation` (
  `vi_id` int(11) NOT NULL,
  `usn` bigint(11) NOT NULL,
  `lname` varchar(321) NOT NULL,
  `fname` varchar(321) NOT NULL,
  `mname` varchar(321) NOT NULL,
  `location` varchar(33) NOT NULL,
  `typeviolation` varchar(33) NOT NULL,
  `description` varchar(33) NOT NULL,
  `evidence` varchar(333) NOT NULL,
  `reportedBy` varchar(33) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `status_id` int(11) DEFAULT NULL,
  `idstrandcourse` int(11) DEFAULT NULL,
  `glevel` varchar(33) DEFAULT NULL,
  `section` varchar(12) NOT NULL,
  `date_resolved` varchar(3232) NOT NULL,
  `seen_by_adviser` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation`
--

INSERT INTO `violation` (`vi_id`, `usn`, `lname`, `fname`, `mname`, `location`, `typeviolation`, `description`, `evidence`, `reportedBy`, `date`, `status_id`, `idstrandcourse`, `glevel`, `section`, `date_resolved`, `seen_by_adviser`) VALUES
(93, 23463463463, 'Magallanes', 'Avelino', 'V', 'ama', 'Bullying', 'BULLYING HIS CLASSMATE', '../uploads/evidences/_20251130_120124.jpg', 'Guinares, Russel', '2025-11-30 19:01:24', 1, 7, '2', 'B', '', 1),
(94, 17938465277, 'Alvarez', 'May', 'Faith1', 'ACLC', 'Misconduct', 'not behaving well', '../uploads/evidences/_20251130_123742.jpg', 'Lucanas, Rina', '2025-11-30 19:37:42', 1, 4, '12', 'B', '', 0),
(95, 14837592016, 'Ramos', 'Carlos', 'Christian', 'aclc campus', 'Dress Code', 'NOT WEARING PROPER UNIFORM', '../uploads/evidences/_20251130_124713.jfif', 'Romo, Alwyn Mark', '2025-11-30 19:47:13', 1, 1, '12', 'A', '', 0),
(96, 17392058411, 'Doe', 'Jane', 'Beatrice', 'campus aclc', 'Dress Code', 'not wearing proper uniform', '../uploads/evidences/_20251130_125202.jpg', 'Romo, Alwyn Mark', '2025-11-30 19:52:02', 1, 1, '12', 'A', '', 0),
(97, 23626747345, 'Flores', 'Andrie', '', 'Cutting classes', 'bully', 'bullying his classmate', '../uploads/evidences/_20251130_125650.jpg', 'Sabino, Rafael', '2025-11-30 19:56:50', 1, 1, '12', 'B', '', 0),
(98, 12541351353, 'Alcuetas', 'Yuan Axl', '', 'aclc campus', 'Stealing', 'Stealing someone\'s wallet ', '../uploads/evidences/_20251130_130340.webp', 'Guinares, Russel', '2025-11-30 20:03:40', 1, 7, '2', 'B', '', 1),
(99, 36234623623, 'Labana', 'John Rex', '', 'ACLC CAMPUS', 'WEARING INFORMAL OUTFIT', 'NOT WEARING PROPER OUTFIT', '../uploads/evidences/_20251130_130814.webp', 'Romo, Alwyn Mark', '2025-11-30 20:08:14', 1, 7, '2', 'B', '', 1);

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
(2, 'Dismissed'),
(3, 'under investigation');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advisory_tbl`
--
ALTER TABLE `advisory_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_idstrandourse` (`idstrandcourse`),
  ADD KEY `fk_adviser_id` (`adviser_id`);

--
-- Indexes for table `collegecourse_tbl`
--
ALTER TABLE `collegecourse_tbl`
  ADD PRIMARY KEY (`idcourse`);

--
-- Indexes for table `collegedep_tbl`
--
ALTER TABLE `collegedep_tbl`
  ADD PRIMARY KEY (`iddepartment`);

--
-- Indexes for table `college_tbl`
--
ALTER TABLE `college_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_idcoursecollege` (`idstrandcourse`),
  ADD KEY `fk_genidcollege` (`genid`),
  ADD KEY `fk_iddepartmentcollege` (`iddepartment`);

--
-- Indexes for table `gender_tbl`
--
ALTER TABLE `gender_tbl`
  ADD PRIMARY KEY (`genid`);

--
-- Indexes for table `notification_tbl`
--
ALTER TABLE `notification_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shsdep_tbl`
--
ALTER TABLE `shsdep_tbl`
  ADD PRIMARY KEY (`iddepartment`);

--
-- Indexes for table `shsstrand_tbl`
--
ALTER TABLE `shsstrand_tbl`
  ADD PRIMARY KEY (`idstrand`);

--
-- Indexes for table `shs_tbl`
--
ALTER TABLE `shs_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_idcourse` (`idstrandcourse`),
  ADD KEY `fk_genid` (`genid`),
  ADD KEY `fk_iddepartment` (`iddepartment`);

--
-- Indexes for table `strandcourse_tbl`
--
ALTER TABLE `strandcourse_tbl`
  ADD PRIMARY KEY (`idstrandcourse`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gender` (`genid`);

--
-- Indexes for table `violation`
--
ALTER TABLE `violation`
  ADD PRIMARY KEY (`vi_id`),
  ADD KEY `fk_idcoursevio` (`idstrandcourse`);

--
-- Indexes for table `violationstatus`
--
ALTER TABLE `violationstatus`
  ADD PRIMARY KEY (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advisory_tbl`
--
ALTER TABLE `advisory_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;

--
-- AUTO_INCREMENT for table `collegecourse_tbl`
--
ALTER TABLE `collegecourse_tbl`
  MODIFY `idcourse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `collegedep_tbl`
--
ALTER TABLE `collegedep_tbl`
  MODIFY `iddepartment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `college_tbl`
--
ALTER TABLE `college_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `gender_tbl`
--
ALTER TABLE `gender_tbl`
  MODIFY `genid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notification_tbl`
--
ALTER TABLE `notification_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shsdep_tbl`
--
ALTER TABLE `shsdep_tbl`
  MODIFY `iddepartment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shsstrand_tbl`
--
ALTER TABLE `shsstrand_tbl`
  MODIFY `idstrand` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shs_tbl`
--
ALTER TABLE `shs_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `strandcourse_tbl`
--
ALTER TABLE `strandcourse_tbl`
  MODIFY `idstrandcourse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `violation`
--
ALTER TABLE `violation`
  MODIFY `vi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `violationstatus`
--
ALTER TABLE `violationstatus`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advisory_tbl`
--
ALTER TABLE `advisory_tbl`
  ADD CONSTRAINT `fk_adviser_id` FOREIGN KEY (`adviser_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_idstrandourse` FOREIGN KEY (`idstrandcourse`) REFERENCES `strandcourse_tbl` (`idstrandcourse`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `college_tbl`
--
ALTER TABLE `college_tbl`
  ADD CONSTRAINT `fk_genidcollege` FOREIGN KEY (`genid`) REFERENCES `gender_tbl` (`genid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_idcoursecollege` FOREIGN KEY (`idstrandcourse`) REFERENCES `strandcourse_tbl` (`idstrandcourse`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_iddepartmentcollege` FOREIGN KEY (`iddepartment`) REFERENCES `collegedep_tbl` (`iddepartment`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shs_tbl`
--
ALTER TABLE `shs_tbl`
  ADD CONSTRAINT `fk_genid` FOREIGN KEY (`genid`) REFERENCES `gender_tbl` (`genid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_idcourse` FOREIGN KEY (`idstrandcourse`) REFERENCES `strandcourse_tbl` (`idstrandcourse`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_iddepartment` FOREIGN KEY (`iddepartment`) REFERENCES `shsdep_tbl` (`iddepartment`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_gender` FOREIGN KEY (`genid`) REFERENCES `gender_tbl` (`genid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `violation`
--
ALTER TABLE `violation`
  ADD CONSTRAINT `fk_idcoursevio` FOREIGN KEY (`idstrandcourse`) REFERENCES `strandcourse_tbl` (`idstrandcourse`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
