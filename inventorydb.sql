-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2026 at 03:43 PM
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
-- Database: `inventorydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(256) NOT NULL,
  `product_category` varchar(256) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_cost` int(11) NOT NULL,
  `user_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`product_id`, `product_name`, `product_category`, `product_quantity`, `product_price`, `product_cost`, `user_id_fk`) VALUES
(1, 'Fita', 'food', 25, 10, 7, 1),
(3, 'tide', 'soap', 7, 10, 7, 1),
(30, 'cheese cake', 'food', 47, 15, 12, 1),
(37, 're', 'food', 12, 12, 12, 1),
(38, 'sarap', 'sama ng loob', 45, 45, 45, 1),
(39, 'magic', 'seasonings', 5, 5, 5, 1),
(43, 'mawi mawi', 'soap', 12, 11, 11, 1),
(44, 'test', 'food', 1, 1, 1, 1),
(45, 'test', 'sama ng loob', 1, 1, 1, 1),
(46, 'milo', 'soap', 12, 15, 10, 1),
(47, 'dil', 'sama ng loob', 12, 311, 67, 1),
(48, 'Eggs', 'Food', 30, 220, 180, 26),
(49, 'Rice (1 kilo)', 'Food', 19, 60, 50, 26),
(50, 'RC Cola (bottle)', 'Drinks', 49, 25, 18, 26),
(51, 'Mineral Water (500ml)', 'Drinks', 50, 15, 10, 26),
(52, '555 Sardines', 'Canned', 40, 50, 40, 26),
(53, 'Argentina Corned Beef', 'Canned', 29, 85, 70, 26),
(54, 'Lucky Me Pancit Canton', 'Noodles', 50, 28, 20, 26),
(55, 'Nissin Cup Noodles', 'Noodles', 50, 35, 25, 26),
(56, 'Piattos', 'Snacks', 50, 22, 15, 26),
(57, 'Nova Cheddar', 'Snacks', 50, 18, 12, 26),
(58, 'Surf Laundry Powder', 'Cleaning', 20, 60, 45, 26),
(59, 'Clorox Bleach', 'Cleaning', 15, 70, 55, 26),
(60, 'Candles', 'Others', 25, 10, 5, 26),
(61, 'Lighter', 'Others', 30, 15, 10, 26);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `receipt_id` int(11) NOT NULL,
  `item_id_fk` int(11) NOT NULL,
  `item_name_fk` varchar(256) NOT NULL,
  `item_price_fk` decimal(10,2) NOT NULL,
  `item_quantity_fk` int(11) NOT NULL DEFAULT 1,
  `user_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `sale_date` date NOT NULL,
  `total_sales` decimal(10,2) NOT NULL,
  `total_items` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  `item_name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `sale_date`, `total_sales`, `total_items`, `total_cost`, `user_id_fk`, `item_name`) VALUES
(1, '2025-12-09', 10.00, 1, 10.00, 1, 'Fita'),
(2, '2025-12-09', 15.00, 1, 12.00, 1, 'cheese cake'),
(3, '2025-12-10', 60.00, 1, 50.00, 26, 'Rice (1 kilo)'),
(4, '2025-12-10', 25.00, 1, 18.00, 26, 'RC Cola (bottle)'),
(5, '2025-12-10', 85.00, 1, 70.00, 26, 'Argentina Corned Beef');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_email` varchar(256) NOT NULL,
  `user_name` varchar(256) NOT NULL,
  `user_password` varchar(256) NOT NULL,
  `user_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_email`, `user_name`, `user_password`, `user_date`) VALUES
(1, 'example@gmail.com', 'examplename', 'example', '2025-12-02'),
(2, 'renier@gmail.com', 'renier', '$2y$10$xFYTaN9QVBx3Rfw4S5YKXeSmE8lfmcgRPpJakUl6BT8rFCfR0loX.', '2025-12-01'),
(3, 'admin@gmail.com', 'admin', '$2y$10$7vUrncsSWAst9lhvARPszuYtQnwhx2uvT5vC9jqy92rEVYJ4WoTyS', '2025-11-27'),
(5, 'user1@gmail.com', 'user1', '$2y$10$5k8m4PW9SQeoaUczOZF3Ve97nYz.cG0lcEQrR0dWrw0oUHPkEBH/O', '2025-11-30'),
(7, 'test12', 'test12', '$2y$10$HsNNTisi8IKxzYN/lZHfAuhgkwESKk981e8ICz8Gy9aiYrtjgGzbq', '0000-00-00'),
(8, 'user1', 'user1', '$2y$10$B1R5alDd1MImVGuTNpf56uFRASw1jjcqeDMbu4ZC52vxVTW.Uw4T6', '0000-00-00'),
(13, 'user@gmail.com', 'user', '$2y$10$8SM49KgE.f7x7A6DRKFhye.rRoCCfXkLl3UV9Ct3RV.lSiVJ6rsbG', '0000-00-00'),
(17, 'user2@gmail.com', 'user@gmail.com', '$2y$10$YW9n.4SZcBBYDuSkzld9WOEfgo5pOguGtstfIi2qPVx4WU4n1kb.W', '0000-00-00'),
(18, 'user5@gmail.com', 'user5@gmail.com', '$2y$10$Lx/JwQhoakShGNVPJlaPxeQsZIFAUqZ50NqKqYDUHkOpBUr7j8Oie', '0000-00-00'),
(19, 'user6@gmail.com', 'user6@gmail.com', '$2y$10$0hTwAh/lee6Yq/jaJJdif.1q2O80Mv7s/KM2S.puHdVljpoLEa7NW', '0000-00-00'),
(20, 'user7@gmail.com', 'user7@gmail.com', '$2y$10$NK/bbZSFbJ9Af7SuPRVJHuprjtKzbJaB4GFLCbLdr14K5UMnrgFP.', '0000-00-00'),
(24, 'user8@gmail.com', 'user8', '$2y$10$uaMPyoPTb4pCsNQEUmDm9.5vT/nsXg.y8pAUj1V3Id4Y7I0qKpAM6', '0000-00-00'),
(25, 'user100@gmail.com', 'ultimate', '$2y$10$HzyG1esDKgW1aNk5MSSdTuGseM3eEqRBK8RqFglYWB08gCAlE2ZXO', '0000-00-00'),
(26, 'tester@gmail.com', 'tester', '$2y$10$0.o8ZBz4LlKrw24W7CKHWumAKE8euKm0BhfA39u/c1iyMZk9BV1LK', '0000-00-00'),
(27, 'tester2@gmail.com', 'tester2', '$2y$10$2FHCKyaEVj6/jom1ftNTTu00H08CiNKrP252z0awJC2dYsStAqYJG', '0000-00-00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `user_id_FK` (`user_id_fk`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`receipt_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `fk_sales_user` (`user_id_fk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_unique` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_user` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
