-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 07:18 AM
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
-- Database: `food_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_details` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`, `phone`, `address`, `payment_method`, `notes`, `payment_details`) VALUES
(1, 7, 200.00, 'completed', '2025-12-09 14:51:51', NULL, NULL, NULL, NULL, NULL),
(2, 8, 125.00, 'pending', '2025-12-09 14:58:44', NULL, NULL, NULL, NULL, NULL),
(3, 8, 125.00, 'pending', '2025-12-09 23:15:13', NULL, NULL, NULL, NULL, NULL),
(4, 8, 200.00, 'pending', '2025-12-09 23:15:40', NULL, NULL, NULL, NULL, NULL),
(5, 7, 125.00, 'cancelled', '2025-12-10 00:56:16', NULL, NULL, NULL, NULL, NULL),
(6, 7, 200.00, 'pending', '2025-12-10 01:12:15', NULL, NULL, NULL, NULL, NULL),
(7, 9, 2485.00, 'pending', '2025-12-10 14:59:50', NULL, NULL, NULL, NULL, NULL),
(8, 7, 1220.00, 'pending', '2025-12-10 17:16:49', NULL, NULL, NULL, NULL, NULL),
(9, 7, 1220.00, 'pending', '2025-12-10 18:27:29', NULL, NULL, NULL, NULL, NULL),
(10, 7, 1220.00, 'pending', '2025-12-10 18:37:22', NULL, NULL, NULL, NULL, NULL),
(11, 7, 10035.00, 'cancelled', '2025-12-10 18:39:26', NULL, NULL, NULL, NULL, NULL),
(12, 7, 125.00, 'pending', '2025-12-11 12:24:09', NULL, 'manila', 'card', 'deliver in frontdoor', '1234567890123456'),
(13, 9, 190.00, 'completed', '2025-12-11 17:22:57', NULL, 'Paranaque', 'gcash', 'bring infront of door', '09123456789'),
(14, 9, 210.00, 'cancelled', '2025-12-11 17:23:57', NULL, 'Paranaque', 'card', 'none', '1234567890123456'),
(15, 4, 195.00, 'pending', '2025-12-12 00:34:09', NULL, 'sdfjksfks', 'cod', 'dyan lang', ''),
(16, 9, 195.00, 'pending', '2025-12-12 16:59:54', NULL, 'manila', 'gcash', 'anything', '09123456789'),
(17, 10, 330.00, 'cancelled', '2026-03-30 05:43:20', NULL, 'taga jn lang bok', 'gcash', 'sa tabi ng bahay ni goku', '09123456789'),
(18, 11, 200.00, 'pending', '2026-03-30 05:51:50', NULL, 'kanila jai', 'cod', 'dun sa bisaya na taga davao', ''),
(19, 11, 500.00, 'pending', '2026-03-30 05:53:38', NULL, 'asdf', 'cod', 'asdfsd', ''),
(20, 12, 80.00, 'completed', '2026-05-11 03:39:46', NULL, 'South Philippine Adventist College', 'cod', 'none', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_time_of_order` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_time_of_order`) VALUES
(1, 1, 2, 1, 75.00),
(2, 1, 3, 1, 75.00),
(3, 2, 2, 1, 75.00),
(4, 3, 2, 1, 75.00),
(5, 4, 3, 2, 75.00),
(6, 5, 2, 1, 75.00),
(7, 6, 2, 1, 75.00),
(8, 6, 3, 1, 75.00),
(9, 7, 3, 3, 75.00),
(10, 7, 5, 3, 20.00),
(11, 7, 4, 2, 1000.00),
(12, 7, 2, 2, 75.00),
(13, 8, 3, 1, 75.00),
(14, 8, 2, 1, 75.00),
(15, 8, 4, 1, 1000.00),
(16, 8, 5, 1, 20.00),
(17, 9, 2, 1, 75.00),
(18, 9, 3, 1, 75.00),
(19, 9, 4, 1, 1000.00),
(20, 9, 5, 1, 20.00),
(21, 10, 2, 1, 75.00),
(22, 10, 3, 1, 75.00),
(23, 10, 4, 1, 1000.00),
(24, 10, 5, 1, 20.00),
(25, 11, 5, 8, 20.00),
(26, 11, 2, 1, 75.00),
(27, 11, 3, 10, 75.00),
(28, 11, 4, 9, 1000.00),
(29, 12, 3, 1, 75.00),
(30, 13, 7, 1, 60.00),
(31, 13, 14, 1, 55.00),
(32, 13, 21, 1, 25.00),
(33, 14, 10, 1, 75.00),
(34, 14, 16, 1, 65.00),
(35, 14, 24, 1, 20.00),  -- FIX: product_id 24 added below
(36, 15, 8, 1, 65.00),
(37, 15, 14, 1, 55.00),
(38, 15, 21, 1, 25.00),
(39, 16, 8, 1, 65.00),
(40, 16, 14, 1, 55.00),
(41, 16, 21, 1, 25.00),
(42, 17, 8, 3, 65.00),
(43, 17, 7, 1, 60.00),
(44, 17, 21, 1, 25.00),
(45, 18, 30, 1, 150.00),  -- FIX: product_id 30 added below
(46, 19, 30, 3, 150.00),  -- FIX: product_id 30 added below
(47, 20, 22, 1, 30.00);   -- FIX: product_id 22 added below

-- --------------------------------------------------------

--
-- Table structure for table `products`
--
-- FIX: Removed invalid DEFAULT 'musubi' — 'musubi' is not a valid enum value.
--      Changed DEFAULT to 'Chicken Meals' (first valid enum member).
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Chicken Meals','Burger Meals','Spaghetti & Pasta','Rice Meals','Breakfast Meals','Desserts & Drinks','Family Bundles') DEFAULT 'Chicken Meals',
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `image_url`, `created_at`, `is_active`) VALUES

-- Chicken Meals
(1, 'Chickenjoy Solo', 'Crispy and juicy fried chicken served with rice.', 'Chicken Meals', 95.00, 'assets/img/products/chickenjoy_solo.png', NOW(), 1),
(2, '2pc Chickenjoy', 'Two pieces of crispy Chickenjoy with rice and gravy.', 'Chicken Meals', 185.00, 'assets/img/products/2pc_chickenjoy.png', NOW(), 1),
(3, 'Chicken Sandwich', 'Crunchy chicken fillet sandwich with mayo dressing.', 'Chicken Meals', 89.00, 'assets/img/products/chicken_sandwich.png', NOW(), 1),

-- Burger Meals
(4, 'Yumburger', 'Classic beef burger with signature dressing.', 'Burger Meals', 45.00, 'assets/img/products/yumburger.png', NOW(), 1),
(5, 'Cheesy Yumburger', 'Yumburger topped with creamy cheese.', 'Burger Meals', 65.00, 'assets/img/products/cheesy_yumburger.png', NOW(), 1),
(6, 'Champ Burger', 'Large premium beef burger with cheese and fresh toppings.', 'Burger Meals', 165.00, 'assets/img/products/champ_burger.png', NOW(), 1),

-- Spaghetti & Pasta
(7, 'Jolly Spaghetti Solo', 'Sweet-style Filipino spaghetti with cheese and hotdog slices.', 'Spaghetti & Pasta', 60.00, 'assets/img/products/jolly_spaghetti.png', NOW(), 1),
(8, 'Jolly Spaghetti Family Pan', 'Large tray of sweet-style spaghetti perfect for sharing.', 'Spaghetti & Pasta', 250.00, 'assets/img/products/spaghetti_family_pan.png', NOW(), 1),
(9, 'Palabok Fiesta', 'Traditional palabok noodles with savory shrimp sauce.', 'Spaghetti & Pasta', 120.00, 'assets/img/products/palabok_fiesta.png', NOW(), 1),

-- Rice Meals
(10, 'Burger Steak Solo', 'Savory burger patty with mushroom gravy and rice.', 'Rice Meals', 75.00, 'assets/img/products/burger_steak_solo.png', NOW(), 1),
(11, '1pc Burger Steak', 'One-piece burger steak meal with rice.', 'Rice Meals', 85.00, 'assets/img/products/1pc_burger_steak.png', NOW(), 1),
(12, 'Shanghai Rice', 'Crispy lumpia shanghai served with rice.', 'Rice Meals', 99.00, 'assets/img/products/shanghai_rice.png', NOW(), 1),

-- Breakfast Meals
(13, 'Longganisa Breakfast', 'Sweet Filipino sausage served with garlic rice and egg.', 'Breakfast Meals', 120.00, 'assets/img/products/longganisa_breakfast.png', NOW(), 1),
(14, 'Corned Beef Breakfast', 'Savory corned beef with garlic rice and egg.', 'Breakfast Meals', 130.00, 'assets/img/products/corned_beef_breakfast.png', NOW(), 1),
(15, 'Pancake Sandwich', 'Fluffy pancake sandwich with sausage filling.', 'Breakfast Meals', 80.00, 'assets/img/products/pancake_sandwich.png', NOW(), 1),

-- Desserts & Drinks
(16, 'Peach Mango Pie', 'Hot crispy pie filled with peach and mango.', 'Desserts & Drinks', 45.00, 'assets/img/products/peach_mango_pie.png', NOW(), 1),
(17, 'Chocolate Sundae', 'Creamy vanilla sundae topped with chocolate syrup.', 'Desserts & Drinks', 50.00, 'assets/img/products/chocolate_sundae.png', NOW(), 1),
(18, 'Coke Float', 'Refreshing Coca-Cola topped with vanilla ice cream.', 'Desserts & Drinks', 59.00, 'assets/img/products/coke_float.png', NOW(), 1),

-- Family Bundles
(19, 'Family Chicken Bundle', '6pc Chickenjoy with rice, gravy, and drinks for the family.', 'Family Bundles', 549.00, 'assets/img/products/family_chicken_bundle.png', NOW(), 1),
(20, 'Burger Family Bundle', 'Assorted burgers, fries, and drinks perfect for sharing.', 'Family Bundles', 499.00, 'assets/img/products/burger_family_bundle.png', NOW(), 1),
(21, 'Spaghetti Party Bundle', 'Large spaghetti tray with Chickenjoy bucket.', 'Family Bundles', 799.00, 'assets/img/products/spaghetti_party_bundle.png', NOW(), 1),

-- Additional Products
(22, 'Mango Float Cup', 'Creamy mango float in a cup.', 'Desserts & Drinks', 30.00, 'assets/img/products/mango_float_cup.png', NOW(), 1),

(23, 'Iced Coffee', 'Chilled brewed coffee with cream.', 'Desserts & Drinks', 55.00, 'assets/img/products/iced_coffee.png', NOW(), 1),

(24, 'Bottled Water', 'Cold bottled mineral water.', 'Desserts & Drinks', 20.00, 'assets/img/products/bottled_water.png', NOW(), 1),

(25, 'Lipton Iced Tea', 'Refreshing bottled iced tea.', 'Desserts & Drinks', 25.00, 'assets/img/products/lipton_iced_tea.png', NOW(), 1),

(26, 'Fries Regular', 'Crispy golden regular-sized fries.', 'Burger Meals', 49.00, 'assets/img/products/fries_regular.png', NOW(), 1),

(27, 'Fries Large', 'Crispy golden large-sized fries.', 'Burger Meals', 69.00, 'assets/img/products/fries_large.png', NOW(), 1),

(28, 'Corn Soup', 'Warm and creamy corn soup.', 'Rice Meals', 35.00, 'assets/img/products/corn_soup.png', NOW(), 1),

(29, 'Mashed Potato', 'Creamy mashed potato with gravy.', 'Chicken Meals', 39.00, 'assets/img/products/mashed_potato.png', NOW(), 1),

(30, 'Party Bucket (8pc)', '8-piece Chickenjoy party bucket.', 'Family Bundles', 150.00, 'assets/img/products/party_bucket_8pc.png', NOW(), 1);
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(4, 'Admin Extra', 'extraadmin@example.com', '$2y$10$Rgz5df.pTfzQ6zhXJ9O7TOZMMMWr0v4kE.GsI.OtGkHDgXY9MlUeq', 'admin', '2026-05-11 03:54:50');

--
-- Indexes for dumped tables
--

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
