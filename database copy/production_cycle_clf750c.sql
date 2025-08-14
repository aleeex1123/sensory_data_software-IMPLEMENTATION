-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 08:19 AM
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
-- Table structure for table `production_cycle_clf750c`
--

CREATE TABLE `production_cycle_clf750c` (
  `id` int(11) NOT NULL,
  `cycle_time` int(11) NOT NULL,
  `cycle_status` int(11) NOT NULL,
  `processing_time` float NOT NULL,
  `recycle_time` int(11) NOT NULL,
  `tempC_01` float NOT NULL,
  `tempF_01` float NOT NULL,
  `tempC_02` float NOT NULL,
  `tempF_02` float NOT NULL,
  `product` text NOT NULL,
  `mold_number` int(6) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_cycle_clf750c`
--

INSERT INTO `production_cycle_clf750c` (`id`, `cycle_time`, `cycle_status`, `processing_time`, `recycle_time`, `tempC_01`, `tempF_01`, `tempC_02`, `tempF_02`, `product`, `mold_number`, `timestamp`) VALUES
(1, 0, 2, 0, 0, 0, 0, 0, 0, 'Chair', 0, '2025-07-16 05:19:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `production_cycle_clf750c`
--
ALTER TABLE `production_cycle_clf750c`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `production_cycle_clf750c`
--
ALTER TABLE `production_cycle_clf750c`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
