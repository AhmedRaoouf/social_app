-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2024 at 01:03 AM
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
-- Database: `landing`
--

-- --------------------------------------------------------

--
-- Table structure for table `support_ready_responses`
--

CREATE TABLE `support_ready_responses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `detail` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `business_id` bigint(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_ready_responses`
--

INSERT INTO `support_ready_responses` (`id`, `name`, `detail`, `status`, `business_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'جار الفحص', 'لقد استلمنا طلبك، وجار الفحص الآن\r\n\r\nسيتم الرد فور الإنتهاء\r\n\r\n..................................', 1, 1, NULL, '2024-04-16 13:18:14', '2024-04-16 13:18:14'),
(2, 'جار  التفيذ', 'جار تنفيذ طلبك الآن\r\n\r\nسيتم الرد فور الإنتهاء\r\n\r\n..................................', 1, 1, NULL, '2024-04-16 13:18:42', '2024-04-16 13:18:42'),
(3, 'تم الإنتهاء', 'تم الإنتهاء من تنفيذ طلبك، برجاء الفحص والتأكيد\r\n\r\n..................................', 1, 1, NULL, '2024-04-16 13:19:08', '2024-04-16 13:19:08'),
(4, 'في إنتظار', 'في إنتظار ردك\r\n\r\n..................................', 1, 1, NULL, '2024-04-16 13:19:34', '2024-04-16 13:19:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `support_ready_responses`
--
ALTER TABLE `support_ready_responses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `support_ready_responses`
--
ALTER TABLE `support_ready_responses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
