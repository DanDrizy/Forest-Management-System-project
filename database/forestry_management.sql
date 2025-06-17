-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 04:07 PM
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
-- Database: `forestry_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `an_id` int(11) NOT NULL,
  `title` varchar(500) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`an_id`, `title`, `type`, `date`, `content`) VALUES
(1, 'FORESTRY DAY', 'Siliculture', '2025-05-04', 'HAPPY INTERNATIONAL DAY OF FORESTS TO ALL OUR COMMUNITY'),
(2, 'ikwigisha umuntu', 'All', '2025-05-04', 'kuruno wambere harabaho amahugurwa yo kwigisha'),
(3, 'ureba neza umuntu witwa Kevin', 'admin', '2025-05-04', 'amaze imitsi ari kugerageza ku applinga ariko byanga'),
(4, 'We have includers in the systems', 'admin', '2025-06-16', 'check for your cridetuals if they are correct');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_log_id` int(11) NOT NULL,
  `action` int(11) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `germination`
--

CREATE TABLE `germination` (
  `g_id` int(11) NOT NULL,
  `plant_name` varchar(255) DEFAULT NULL,
  `g_sdate` date DEFAULT NULL,
  `g_edate` date DEFAULT NULL,
  `seeds` int(11) DEFAULT NULL,
  `soil` varchar(255) DEFAULT NULL,
  `g_note` text DEFAULT NULL,
  `g_status` varchar(50) DEFAULT 'unsend',
  `curdate` date DEFAULT NULL,
  `del` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `germination`
--

INSERT INTO `germination` (`g_id`, `plant_name`, `g_sdate`, `g_edate`, `seeds`, `soil`, `g_note`, `g_status`, `curdate`, `del`) VALUES
(1, 'bubasian', '2025-05-23', '2025-05-28', 35, 'Clay', 'akoou', 'unsend', '2025-05-22', 0),
(2, 'foromina', '2025-05-29', '2025-06-25', 32, 'Silt', 'u', 'unsend', '2025-05-22', 0),
(3, 'coconat', '2025-06-30', '2025-07-05', 100, 'Silt', 'u', 'unsend', '2025-06-05', 0),
(4, 'beans', '2025-06-12', '2025-06-20', 50, 'Loam', 'u', 'unsend', '2025-06-05', 0),
(5, 'coconats', '2025-06-30', '2025-07-05', 100, 'Silt', 'u', 'unsend', '2025-06-05', 0),
(6, 'Ibishuka', '2025-07-05', '2025-07-31', 100, 'Clay', '', 'unsend', '2025-06-16', 0),
(7, 'ibiti bitukura', '2025-06-16', '2025-06-30', 200, 'Silt', '', 'unsend', '2025-06-16', 0);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `l_id` int(11) NOT NULL,
  `p_id` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `v1` decimal(10,2) DEFAULT NULL,
  `v2` decimal(10,2) DEFAULT NULL,
  `d1` decimal(10,2) DEFAULT NULL,
  `d2` decimal(10,2) DEFAULT NULL,
  `l_indate` date DEFAULT NULL,
  `l_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`l_id`, `p_id`, `amount`, `height`, `v1`, `v2`, `d1`, `d2`, `l_indate`, `l_status`) VALUES
(1, 1, 90, 20.00, 15.00, 20.00, 30.00, 20.00, '2025-06-16', 'sent'),
(2, 2, 100, 20.00, 30.00, 10.00, 20.00, 12.00, '2025-06-16', 'sent'),
(3, 3, 20, 10.00, 19.00, 22.00, 20.00, 30.00, '2025-06-16', 'unsend');

-- --------------------------------------------------------

--
-- Table structure for table `plant`
--

CREATE TABLE `plant` (
  `p_id` int(11) NOT NULL,
  `g_id` int(11) DEFAULT NULL,
  `DBH` decimal(10,2) DEFAULT NULL,
  `health` varchar(255) DEFAULT NULL,
  `p_height` decimal(10,2) DEFAULT NULL,
  `info` text DEFAULT NULL,
  `p_status` varchar(50) DEFAULT NULL,
  `indate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plant`
--

INSERT INTO `plant` (`p_id`, `g_id`, `DBH`, `health`, `p_height`, `info`, `p_status`, `indate`) VALUES
(1, 7, 12.00, 'A', 22.00, 'this is the orginal trees', 'send', '2025-06-16'),
(2, 6, 20.00, 'B', 30.00, '', 'send', '2025-06-16'),
(3, 7, 12.00, 'B', 20.00, '', 'send', '2025-06-16');

-- --------------------------------------------------------

--
-- Table structure for table `pole`
--

CREATE TABLE `pole` (
  `po_id` int(11) NOT NULL,
  `po_amount` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `po_indate` date DEFAULT NULL,
  `t_id` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `tree_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `received`
--

CREATE TABLE `received` (
  `re_id` int(11) NOT NULL,
  `req_id` int(11) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `subject` varchar(500) DEFAULT 'empty',
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `read_status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `received`
--

INSERT INTO `received` (`re_id`, `req_id`, `title`, `subject`, `content`, `created_at`, `read_status`) VALUES
(1, 1, 'Re: turi gushaka ibiti', 'Re: turi gushaka ibiti', 'urashaka iki', '2025-06-16 18:42:17', 0);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `r_id` int(11) NOT NULL,
  `title` varchar(500) DEFAULT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `read_status` int(11) DEFAULT 0,
  `user` varchar(100) DEFAULT 'pole'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`r_id`, `title`, `subject`, `content`, `created_at`, `read_status`, `user`) VALUES
(1, 'turi gushaka ibiti', 'turi gushaka ibiti', 'aaaaaaaaaaaaaaa', '2025-06-06 18:43:52', 1, 'pole'),
(2, 'Gusaba', 'ask for', 'bbbbbbbbbbbbbbbbbbbbb', '2025-06-06 18:47:22', 0, 'sawmill'),
(3, 'Ibiti Byashize', 'request for another trees', 'ndi gushaka ibindi biti', '2025-06-16 18:57:16', 0, 'pole');

-- --------------------------------------------------------

--
-- Table structure for table `stockin`
--

CREATE TABLE `stockin` (
  `in_id` int(11) NOT NULL,
  `t_id` int(11) DEFAULT NULL,
  `s_amount` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `s_indate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockin`
--

INSERT INTO `stockin` (`in_id`, `t_id`, `s_amount`, `price`, `s_indate`) VALUES
(1, 1, 30, 60000.00, '2025-06-17');

-- --------------------------------------------------------

--
-- Table structure for table `stockout`
--

CREATE TABLE `stockout` (
  `out_id` int(11) NOT NULL,
  `in_id` int(11) DEFAULT NULL,
  `out_price` decimal(10,2) DEFAULT NULL,
  `out_date` date DEFAULT NULL,
  `out_note` text DEFAULT NULL,
  `out_amount` int(11) DEFAULT NULL,
  `up_notes` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockout`
--

INSERT INTO `stockout` (`out_id`, `in_id`, `out_price`, `out_date`, `out_note`, `out_amount`, `up_notes`, `updated_at`) VALUES
(1, 1, 90000.00, '2025-06-17', '', 15, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_audit_log`
--

CREATE TABLE `stock_audit_log` (
  `id` int(11) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `old_value` int(11) DEFAULT NULL,
  `new_value` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timber`
--

CREATE TABLE `timber` (
  `t_id` int(11) NOT NULL,
  `l_id` int(11) DEFAULT NULL,
  `type` varchar(11) DEFAULT NULL,
  `t_height` int(11) DEFAULT NULL,
  `t_width` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT 0,
  `t_volume` int(11) DEFAULT NULL,
  `t_indate` date DEFAULT NULL,
  `t_location` varchar(110) DEFAULT NULL,
  `t_note` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'unsend',
  `t_amount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timber`
--

INSERT INTO `timber` (`t_id`, `l_id`, `type`, `t_height`, `t_width`, `size`, `t_volume`, `t_indate`, `t_location`, `t_note`, `status`, `t_amount`) VALUES
(1, 1, 'Wood', 20, 15, 15, 100, '2025-06-16', 'Musanze', '', 'send', 55);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `image` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `role`, `email`, `status`, `image`, `phone`, `date`) VALUES
(1, 'admin', 'admin', 'admin', 'admin@gmail.com', 'active', '1750167738_WhatsApp Image 2025-04-22 at 20.17.48_88ec5a92.jpg', '0790365853', '2025-06-17 15:42:00'),
(2, 'TUGIRIMANA Danny', '12345', 'sales', 'dannytugirimana12@gmail.com', 'active', '1750167693__DSC6267.jpg', '0790365857', '2025-06-17 15:41:00'),
(3, 'Pole Plant', 'pole', 'pole plant', 'poleplant@gmail.com', 'active', NULL, '7903657562025', '2025-05-27 00:00:00'),
(4, 'sawmill', 'sawmill', 'sawmill', 'sawmill@gmail.com', 'active', NULL, '7903658572025', '2025-05-27 00:00:00'),
(5, 'Makuza Betty', '321', 'pole plant', 'makuzabetty@gmail.com', 'active', NULL, '07364304362025', '2025-05-04 00:00:00'),
(6, 'Tuyishimire Guillaine', 'danny', 'Siliculture', 'guillain@gmail.com', 'active', '1750087691_gii (8).jpg', '0790365857', '2025-06-16 18:03:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`an_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_log_id`),
  ADD KEY `idx_audit_log_table_record` (`table_name`,`record_id`);

--
-- Indexes for table `germination`
--
ALTER TABLE `germination`
  ADD PRIMARY KEY (`g_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`l_id`),
  ADD KEY `idx_logs_p_id` (`p_id`);

--
-- Indexes for table `plant`
--
ALTER TABLE `plant`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `idx_plant_g_id` (`g_id`);

--
-- Indexes for table `pole`
--
ALTER TABLE `pole`
  ADD PRIMARY KEY (`po_id`),
  ADD KEY `idx_pole_t_id` (`t_id`);

--
-- Indexes for table `received`
--
ALTER TABLE `received`
  ADD PRIMARY KEY (`re_id`),
  ADD KEY `idx_received_req_id` (`req_id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `stockin`
--
ALTER TABLE `stockin`
  ADD PRIMARY KEY (`in_id`),
  ADD KEY `idx_stockin_t_id` (`t_id`);

--
-- Indexes for table `stockout`
--
ALTER TABLE `stockout`
  ADD PRIMARY KEY (`out_id`),
  ADD KEY `idx_stockout_in_id` (`in_id`);

--
-- Indexes for table `stock_audit_log`
--
ALTER TABLE `stock_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_stock_audit_table_record` (`table_name`,`record_id`);

--
-- Indexes for table `timber`
--
ALTER TABLE `timber`
  ADD PRIMARY KEY (`t_id`),
  ADD KEY `idx_timber_l_id` (`l_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `an_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `germination`
--
ALTER TABLE `germination`
  MODIFY `g_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `plant`
--
ALTER TABLE `plant`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pole`
--
ALTER TABLE `pole`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `received`
--
ALTER TABLE `received`
  MODIFY `re_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stockin`
--
ALTER TABLE `stockin`
  MODIFY `in_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stockout`
--
ALTER TABLE `stockout`
  MODIFY `out_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_audit_log`
--
ALTER TABLE `stock_audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timber`
--
ALTER TABLE `timber`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `plant` (`p_id`) ON DELETE CASCADE;

--
-- Constraints for table `plant`
--
ALTER TABLE `plant`
  ADD CONSTRAINT `plant_ibfk_1` FOREIGN KEY (`g_id`) REFERENCES `germination` (`g_id`) ON DELETE SET NULL;

--
-- Constraints for table `pole`
--
ALTER TABLE `pole`
  ADD CONSTRAINT `pole_ibfk_1` FOREIGN KEY (`t_id`) REFERENCES `timber` (`t_id`) ON DELETE SET NULL;

--
-- Constraints for table `received`
--
ALTER TABLE `received`
  ADD CONSTRAINT `received_ibfk_1` FOREIGN KEY (`req_id`) REFERENCES `request` (`r_id`) ON DELETE CASCADE;

--
-- Constraints for table `stockin`
--
ALTER TABLE `stockin`
  ADD CONSTRAINT `stockin_ibfk_1` FOREIGN KEY (`t_id`) REFERENCES `timber` (`t_id`) ON DELETE CASCADE;

--
-- Constraints for table `stockout`
--
ALTER TABLE `stockout`
  ADD CONSTRAINT `stockout_ibfk_1` FOREIGN KEY (`in_id`) REFERENCES `stockin` (`in_id`) ON DELETE CASCADE;

--
-- Constraints for table `timber`
--
ALTER TABLE `timber`
  ADD CONSTRAINT `timber_ibfk_1` FOREIGN KEY (`l_id`) REFERENCES `logs` (`l_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
