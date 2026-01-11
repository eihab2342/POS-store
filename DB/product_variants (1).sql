-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 09, 2025 at 06:24 PM
-- Server version: 8.0.30
-- PHP Version: 8.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos-clothes`
--

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_qty` int NOT NULL DEFAULT '0',
  `reorder_level` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_low_stock` tinyint(1) GENERATED ALWAYS AS ((`stock_qty` < `reorder_level`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `supplier_id`, `sku`, `barcode`, `color`, `size`, `cost`, `price`, `stock_qty`, `reorder_level`, `created_at`, `updated_at`, `name`) VALUES
(1, NULL, NULL, '102', '102', '--', '40', 214.00, 270.00, 4, 1, '2025-11-09 15:10:44', '2025-11-09 15:30:08', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(2, NULL, NULL, '103', '103', '--', '38', 208.00, 260.00, 6, 1, '2025-11-09 15:34:29', '2025-11-09 15:34:29', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(3, NULL, NULL, '104', '104', NULL, '36', 99.00, 250.00, 6, 1, '2025-11-09 15:43:22', '2025-11-09 15:43:22', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(4, NULL, NULL, '105', '105', NULL, '34', 193.00, 210.00, 6, 1, '2025-11-09 15:50:38', '2025-11-09 15:50:38', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(5, NULL, NULL, '106', '106', NULL, '14', 187.00, 210.00, 6, 1, '2025-11-09 15:53:40', '2025-11-09 15:53:40', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(7, NULL, NULL, '107', '107', NULL, '12', 176.00, 210.00, 6, 1, '2025-11-09 15:58:36', '2025-11-09 15:58:36', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(8, NULL, NULL, '108', '108', NULL, '10', 167.00, 200.00, 6, 1, '2025-11-09 16:00:05', '2025-11-09 16:00:05', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(9, NULL, NULL, '109', '109', NULL, '8', 160.00, 200.00, 6, 1, '2025-11-09 16:03:21', '2025-11-09 16:03:21', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(10, NULL, NULL, '110', '110', NULL, '6', 150.00, 200.00, 6, 1, '2025-11-09 16:05:55', '2025-11-09 16:05:55', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(11, NULL, NULL, '111', '111', NULL, '4', 144.00, 200.00, 6, 1, '2025-11-09 16:10:07', '2025-11-09 16:10:07', 'طقم بوكسر اولاد 2 قطعة اوليف'),
(12, NULL, NULL, '315', '315', NULL, '4', 195.00, 270.00, 6, 1, '2025-11-09 16:19:01', '2025-11-09 16:19:01', 'طقم بوكسر بناتى 3 قطعة اوليف'),
(13, NULL, NULL, '316', '316', NULL, '6', 209.00, 280.00, 6, 1, '2025-11-09 16:20:57', '2025-11-09 16:20:57', 'طقم بوكسر بناتى3 قطعة اوليف'),
(14, NULL, NULL, '317', '317', NULL, NULL, 219.00, 290.00, 6, 1, '2025-11-09 16:22:39', '2025-11-09 16:22:39', 'طقم بوكسر بناتى3 قطعة اوليف');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_variants_sku_unique` (`sku`),
  ADD UNIQUE KEY `product_variants_barcode_unique` (`barcode`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`),
  ADD KEY `product_variants_supplier_id_index` (`supplier_id`),
  ADD KEY `product_variants_price_index` (`price`),
  ADD KEY `product_variants_stock_qty_index` (`stock_qty`),
  ADD KEY `product_variants_reorder_level_index` (`reorder_level`),
  ADD KEY `product_variants_is_low_stock_index` (`is_low_stock`),
  ADD KEY `idx_supplier_id` (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variants_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
