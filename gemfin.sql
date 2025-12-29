-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2025 at 08:31 PM
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
-- Database: `gemfin`
--

-- --------------------------------------------------------

--
-- Table structure for table `balance`
--

CREATE TABLE `balance` (
  `balance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `current_balance` decimal(15,2) DEFAULT 0.00,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `balance`
--

INSERT INTO `balance` (`balance_id`, `user_id`, `current_balance`, `time_stamp`) VALUES
(1, 1, 26700.00, '2025-12-06 06:31:09'),
(2, 2, 20.00, '2025-12-01 09:43:26'),
(3, 3, 0.00, '2025-12-06 07:03:32'),
(4, 4, 300.00, '2025-12-07 08:21:11'),
(5, 5, 8800.00, '2025-12-07 08:27:05');

-- --------------------------------------------------------

--
-- Table structure for table `budget`
--

CREATE TABLE `budget` (
  `budget_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `month` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget`
--

INSERT INTO `budget` (`budget_id`, `user_id`, `category_id`, `amount`, `month`) VALUES
(1, 1, 2, 100000.00, 'December 2025'),
(2, 1, 3, 10000.00, 'December 2025'),
(3, 1, 4, 10000.00, 'December 2025'),
(4, 1, 5, 100000.00, 'December 2025'),
(5, 2, 7, 800.00, 'December 2025'),
(6, 2, 8, 100.00, 'December 2025'),
(7, 1, 9, 2000.00, 'December 2025'),
(8, 4, 12, 10000.00, 'December 2025'),
(9, 5, 14, 1000.00, 'December 2025'),
(10, 5, 15, 5000.00, 'December 2025');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `user_id`, `type`, `description`) VALUES
(1, 1, 'expense', 'General'),
(2, 1, 'expense', 'Food'),
(3, 1, 'expense', 'transport'),
(4, 1, 'expense', 'General_expense'),
(5, 1, 'expense', 'Laptop'),
(6, 2, 'expense', 'General'),
(7, 2, 'expense', 'ghjk'),
(8, 2, 'expense', 'abcd'),
(9, 1, 'expense', 'rent'),
(10, 3, 'expense', 'General'),
(11, 4, 'expense', 'General'),
(12, 4, 'expense', 'raw material'),
(13, 5, 'expense', 'General'),
(14, 5, 'expense', 'transport'),
(15, 5, 'expense', 'Food');

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `expense_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `merchant` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`expense_id`, `transaction_id`, `merchant`) VALUES
(1, 1, 'burger'),
(2, 3, 'toffee'),
(3, 4, 'mouse'),
(4, 5, 'bus'),
(5, 6, 'Flight'),
(6, 9, 'Paneer lawabdar'),
(7, 12, 'tyui'),
(8, 13, 'yrg'),
(9, 14, 'rgun'),
(10, 15, 'rthgdf'),
(11, 16, 'chicken kofta'),
(12, 17, 'chocholate'),
(13, 19, 'dhaaga'),
(14, 20, 'service'),
(15, 22, 'saree'),
(16, 23, 'grocery store');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `income_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `source` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`income_id`, `transaction_id`, `source`) VALUES
(1, 2, 'salary'),
(2, 7, 'salary'),
(3, 8, 'bonus'),
(4, 10, 'freelancing'),
(5, 11, 'salary'),
(6, 18, 'salary'),
(7, 21, 'salary');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `total_income` decimal(15,2) DEFAULT NULL,
  `savings` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saving_goals`
--

CREATE TABLE `saving_goals` (
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `target_amount` decimal(15,2) NOT NULL,
  `current_amount` decimal(15,2) DEFAULT 0.00,
  `target_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saving_goals`
--

INSERT INTO `saving_goals` (`goal_id`, `user_id`, `description`, `target_amount`, `current_amount`, `target_date`) VALUES
(1, 1, 'bike', 100000.00, 100965.00, '2025-12-31'),
(2, 2, 'bikw', 10000.00, 10650.00, '2025-12-31'),
(3, 1, 'Laptop', 100000.00, 1000000.00, '2025-12-31'),
(4, 1, 'car', 2000000.00, 2002000.00, '2026-01-29'),
(5, 4, 'sales', 2000000.00, 236665.00, '2025-12-31'),
(6, 5, 'sales', 100000.00, 0.00, '2025-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `user_id`, `category_id`, `amount`, `description`, `date`, `time_stamp`) VALUES
(1, 1, 2, 500.00, 'burger', '2025-12-01', '2025-12-01 05:45:23'),
(2, 1, 1, 10000.00, 'salary', '2025-12-01', '2025-12-01 05:49:20'),
(3, 1, 2, 300.00, 'toffee', '2025-12-01', '2025-12-01 05:49:55'),
(4, 1, 2, 300.00, 'mouse', '2025-11-07', '2025-12-01 06:13:20'),
(5, 1, 3, 500.00, 'bus', '2025-12-01', '2025-12-01 06:50:44'),
(6, 1, 3, 9000.00, 'Flight', '2025-12-01', '2025-12-01 07:09:25'),
(7, 1, 1, 10000.00, 'salary', '2025-11-30', '2025-12-01 07:11:26'),
(8, 1, 1, 10000.00, 'bonus', '2025-12-01', '2025-12-01 07:40:59'),
(9, 1, 2, 500.00, 'Paneer lawabdar', '2025-12-01', '2025-12-01 07:42:16'),
(10, 1, 1, 9000.00, 'freelancing', '2025-12-01', '2025-12-01 08:30:26'),
(11, 2, 6, 2000.00, 'salary', '2025-12-01', '2025-12-01 09:34:10'),
(12, 2, 7, 200.00, 'tyui', '2025-12-01', '2025-12-01 09:35:32'),
(13, 2, 7, 1000.00, 'yrg', '2025-11-11', '2025-12-01 09:36:20'),
(14, 2, 7, 700.00, 'rgun', '2025-12-01', '2025-12-01 09:42:33'),
(15, 2, 8, 80.00, 'rthgdf', '2025-12-01', '2025-12-01 09:43:26'),
(16, 1, 2, 1000.00, 'chicken kofta', '2025-12-06', '2025-12-06 06:19:01'),
(17, 1, 2, 200.00, 'chocholate', '2025-12-06', '2025-12-06 06:31:09'),
(18, 4, 11, 1000.00, 'salary', '2025-12-06', '2025-12-06 07:05:00'),
(19, 4, 12, 500.00, 'dhaaga', '2025-12-06', '2025-12-06 07:07:10'),
(20, 4, 12, 200.00, 'service', '2025-12-07', '2025-12-07 08:21:11'),
(21, 5, 13, 10000.00, 'salary', '2025-12-07', '2025-12-07 08:25:15'),
(22, 5, 14, 200.00, 'saree', '2025-12-07', '2025-12-07 08:26:11'),
(23, 5, 15, 1000.00, 'grocery store', '2025-12-07', '2025-12-07 08:27:05');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `email`, `password`, `time_stamp`) VALUES
(1, 'Utkarsh', 'Gupta', 'ut@gmail.com', '$2y$10$whFm3pe2R1P72ubuhCuZf.i/ib/QCrMrcdv7b.1tXMTh6U3d055C.', '2025-12-01 05:40:30'),
(2, '4567890-', '7890-', '5678@gmail.cop', '$2y$10$H.iMkSQqT4glTaEWmsfe/e6E.pf.v7pVJR5riDh9/OTdprdvAzBO6', '2025-12-01 09:32:36'),
(3, 'ut', 'gupta', 'utk@gmail.cpm', '$2y$10$eU1lSf7xSTOccmsQDDgJYOGA20330nJXoyE0L.ngq8HS3ih62PpPK', '2025-12-06 07:03:32'),
(4, 'ut', 'Gupta', 'utk@gmail.com', '$2y$10$DOjiIOvdzLxwlI7gswUmw.hkClJTxACYZloTs8IXpkZkO67NdF8nm', '2025-12-06 07:04:26'),
(5, 'k k', 'Gupta', 'kk@gmail.com', '$2y$10$Z3GrGi9HQ9bCKwLv0iZueOaGXIDD5Vp9M9OGVd36LeB58yzRkR0Me', '2025-12-07 08:24:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`balance_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budget`
--
ALTER TABLE `budget`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`income_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `saving_goals`
--
ALTER TABLE `saving_goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `balance`
--
ALTER TABLE `balance`
  MODIFY `balance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `budget`
--
ALTER TABLE `budget`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `saving_goals`
--
ALTER TABLE `saving_goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance`
--
ALTER TABLE `balance`
  ADD CONSTRAINT `balance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `budget`
--
ALTER TABLE `budget`
  ADD CONSTRAINT `budget_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `budget_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`transaction_id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `saving_goals`
--
ALTER TABLE `saving_goals`
  ADD CONSTRAINT `saving_goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
