-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 08:21 AM
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
-- Database: `sensory_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `mold_thickness`
--

CREATE TABLE `mold_thickness` (
  `id` int(11) NOT NULL,
  `mold_name` text NOT NULL,
  `mold_number` int(12) NOT NULL,
  `thickness` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mold_thickness`
--

INSERT INTO `mold_thickness` (`id`, `mold_name`, `mold_number`, `thickness`) VALUES
(1, '12 OZ PEPSI CRATE', 3761, 480),
(2, 'CRATE COVER #1 (CL, PL, CM, PM)', 2820, 525),
(3, 'KIDDIE DESKTOP', 1344, 600),
(4, 'SQUARE TOP TABLE 30”', 1000, 652),
(5, '9-B2 CRATE', 3277, 700),
(6, '1-L COKE CRATE', 3698, 746),
(7, 'CHICKEN FLOORING 2X4', 4050, 795),
(8, 'PNS-2 9”', 3274, 840),
(9, 'LEO SIDECHAIR', 1110, 920),
(10, '55F CRATE BODY MERCURY', 3786, 1050),
(11, 'B-2 BREAD CRATE', 3185, 790),
(12, '1L PEPSI CRATE #6', 4135, 770),
(13, '37 TABLE LEG', 3728, 699);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mold_thickness`
--
ALTER TABLE `mold_thickness`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mold_thickness`
--
ALTER TABLE `mold_thickness`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
