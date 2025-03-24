-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2024 at 10:21 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bakery_oop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`) VALUES
(1, 'Jane Smith', 'jane@example.com', 'I love your cakes!'),
(2, 'brian', 'brian@gmail.com', 'yawa'),
(3, 'brian', 'brian@gmail.com', 'yawa'),
(4, 'brian', 'brian@gmail.com', 'yawa'),
(5, 'brian', 'brian@gmail.com', 'yawa'),
(6, 'brian', 'brian@gmail.com', 'yawa'),
(7, 'brian', 'brian@gmail.com', 'yawa'),
(8, 'brian', 'brian@gmail.com', 'yawa'),
(9, 'brian', 'brian@gmail.com', 'yawa');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_type` enum('delivery','pickup') NOT NULL DEFAULT 'pickup',
  `delivery_address` text DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `pickup_time` datetime DEFAULT NULL,
  `final_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_email`, `product_id`, `quantity`, `total_price`, `payment_method`, `address`, `status`, `order_date`, `order_type`, `delivery_address`, `customer_name`, `pickup_time`, `final_total`) VALUES
(137, 6, 'admin@gmail.com', 0, 0, 300.00, 'cod', 'yippeboy', 'delivered', '2024-10-15 00:00:42', 'delivery', 'yippeboy', NULL, '0000-00-00 00:00:00', NULL),
(140, 6, 'admin@gmail.com', 0, 0, 160.00, 'credit_card', 'yippe', 'delivered', '2024-10-15 00:08:55', 'delivery', 'yippe', NULL, '0000-00-00 00:00:00', NULL),
(148, 6, 'admin@gmail.com', 0, 0, 160.00, 'credit_card', 'yippe', 'pending', '2024-10-15 05:26:45', 'delivery', 'yippe', NULL, '0000-00-00 00:00:00', NULL),
(149, 6, 'admin@gmail.com', 0, 0, 150.00, 'credit_card', 'Basta', 'pending', '2024-10-15 05:29:33', 'delivery', 'Basta', NULL, '0000-00-00 00:00:00', NULL),
(167, 39, '', 0, 0, 0.00, 'credit_card', 'test123', 'pending', '2024-10-17 07:52:48', 'delivery', 'test123', NULL, '0000-00-00 00:00:00', 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(110, 137, 150, 2),
(113, 140, 151, 1),
(121, 148, 151, 1),
(122, 149, 150, 1),
(140, 167, 150, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `featured`, `quantity`) VALUES
(150, 'cake1', 'The Best Cakey 1 ', 150.00, 'featured1.jpg_1.jpg', NULL, 1, 3),
(151, 'cakey2', 'The Best Cakey 2 ', 160.00, 'featured2.jpg_1.jpg', NULL, 1, 2),
(152, 'Cakey3', 'The Best Cakey 3', 200.00, 'featured3.jpg', NULL, 1, 5),
(153, 'Cakey4', 'The Best Cake 4', 180.00, 'cakey4.jpg', NULL, 0, 13),
(154, 'Cakey5', 'The Best Cakey 5', 190.00, 'cakey5.jpg', NULL, 0, 14),
(155, 'cakey6', 'The Best Cake 6', 190.00, 'cakey6.jpg', NULL, 0, 15);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `isAdmin` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `reset_token`, `isAdmin`) VALUES
(6, 'admin', 'admin@gmail.com', '$2y$10$yf3pCYzsjeIOaUVHYG3g4ey.ujdz0DZGZ4ycFwesMkB6bJHOqx46W', NULL, 1),
(32, 'brian1', 'brian1@gmail.com', '$2y$10$YlAvENDj4Y3zFce2gbni/OUD/QskEPSom3ezLBP1uArUZJqhT/aGi', NULL, 0),
(33, 'brendo', 'brendo@gmail.com', '$2y$10$Sp9ZFNJiQmJFjb5pdttXT.3D5q8tAwSFlxt28SrZp27xrtmG.Ym3i', NULL, 0),
(34, 'brendobrendo', 'brendobrendo@gmail.com', '$2y$10$jvW71Z7LxAFWyUJ0DOeku.Kw9pPzdDL5YDmcn81IAMQ3ks3NpuOMS', NULL, 0),
(35, 'brian', 'brendosundalo@gmail.com', '$2y$10$D/Wr9NbVw0M4UKVn5vMRa./JX8lQWJJT7hQOPpOTP7g4/v7HIHLdS', NULL, 0),
(36, 'yespo', 'yespo@gmail.com', '$2y$10$2zgfRvbprZQ6rekqhQku6.yqN5s/XYLfGqt0ZdcGFBziNDUHviNam', NULL, 0),
(37, 'brendosundalo', 'brendosundalosundalo@gmail.com', '$2y$10$9n9YGARW3n4mlFxfZK7iZu5v9GJek4H.GLKoSg5EdBCkTuoLLJnCC', NULL, 0),
(38, 'yawa', 'yawa@gmail.com', '$2y$10$uYo37UXKGtBkM9bppY7Se.N92uMH1g5d0LXiA1FaSt26KHlnEedf2', NULL, 0),
(39, 'test', 'test@gmail.com', '$2y$10$x2ktJVyp8pWGZ//SBUWHGO1d0X3OMGc8kpbOp7MsYuVd63h2KG1h.', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
