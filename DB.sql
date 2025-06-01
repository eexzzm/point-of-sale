-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_pos
CREATE DATABASE IF NOT EXISTS `db_pos` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_pos`;

-- Dumping structure for table db_pos.accounts
CREATE TABLE IF NOT EXISTS `accounts` (
  `username` varchar(15) NOT NULL,
  `id_role` int NOT NULL,
  `password` varchar(15) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  PRIMARY KEY (`username`),
  KEY `accounts_id_role_roles_id_role` (`id_role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Dumping data for table db_pos.accounts: 2 rows
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` (`username`, `id_role`, `password`, `fullname`) VALUES
	('owner', 1, 'owner123', 'Sandhika Galih'),
	('staff', 2, 'staff123', 'Rizqa Aulia');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;

-- Dumping structure for table db_pos.items
CREATE TABLE IF NOT EXISTS `items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item_id_txt` varchar(15) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `item_price` varchar(30) NOT NULL,
  `create_at` varchar(50) NOT NULL,
  `last_modified` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table db_pos.items: 3 rows
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` (`item_id`, `item_id_txt`, `item_name`, `item_price`, `create_at`, `last_modified`) VALUES
	(1, 'ITM001', 'Sunsilk Hijab 300ml', '25.000', '01/09/2022 11:03:14 pm', ''),
	(2, 'ITM002', 'Pepsodent 450ml', '27.000', '01/10/2022 11:39:06 pm', ''),
	(4, 'ITM003', 'Ultramilk 100ml', '15.000', '05/26/2025 09:02:42 pm', NULL);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;

-- Dumping structure for table db_pos.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `role` varchar(15) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table db_pos.roles: 2 rows
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id_role`, `role`) VALUES
	(1, 'Owner'),
	(2, 'Staff');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table db_pos.sales
CREATE TABLE IF NOT EXISTS `sales` (
  `sale_id` int NOT NULL AUTO_INCREMENT,
  `transaction_id_txt` varchar(255) NOT NULL,
  `sale_date` datetime NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `cash_paid` decimal(15,2) NOT NULL,
  `money_changes` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_pos.sales: ~4 rows (approximately)
INSERT INTO `sales` (`sale_id`, `transaction_id_txt`, `sale_date`, `total_amount`, `cash_paid`, `money_changes`, `discount_amount`, `created_at`) VALUES
	(8, 'TRX002', '2025-05-31 18:29:35', 49500.00, 60000.00, 10500.00, 5500.00, '2025-05-31 10:29:35'),
	(9, 'TRX003', '2025-05-31 18:34:19', 27000.00, 40000.00, 13000.00, 0.00, '2025-05-31 10:34:19'),
	(10, 'TRX004', '2025-05-31 18:34:58', 21000.00, 100000.00, 79000.00, 21000.00, '2025-05-31 10:34:58'),
	(11, 'TRX004', '2025-05-31 18:47:38', 56000.00, 100000.00, 44000.00, 14000.00, '2025-05-31 10:47:38');

-- Dumping structure for table db_pos.transaction_details
CREATE TABLE IF NOT EXISTS `transaction_details` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `sale_id` int NOT NULL,
  `quantity` int NOT NULL,
  `item_price` decimal(15,2) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `FK_transaction_details_items` (`item_id`),
  KEY `fk_transaction_details_sale_id` (`sale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table db_pos.transaction_details: 23 rows
/*!40000 ALTER TABLE `transaction_details` DISABLE KEYS */;
INSERT INTO `transaction_details` (`transaction_id`, `item_id`, `sale_id`, `quantity`, `item_price`) VALUES
	(2, 1, 2, 2, 0.00),
	(4, 2, 4, 1, 0.00),
	(5, 1, 5, 1, 0.00),
	(6, 4, 6, 1, 0.00),
	(7, 2, 7, 10, 0.00),
	(8, 1, 1, 1, 25000.00),
	(9, 4, 1, 2, 15000.00),
	(10, 1, 2, 3, 25000.00),
	(11, 4, 2, 2, 15000.00),
	(12, 2, 2, 1, 27000.00),
	(13, 1, 3, 1, 25000.00),
	(14, 1, 4, 1, 25000.00),
	(15, 1, 5, 1, 25000.00),
	(16, 1, 6, 1, 25000.00),
	(17, 1, 7, 1, 25000.00),
	(18, 4, 7, 1, 15000.00),
	(19, 4, 8, 2, 15000.00),
	(20, 1, 8, 1, 25000.00),
	(21, 2, 9, 1, 27000.00),
	(22, 4, 10, 1, 15000.00),
	(23, 2, 10, 1, 27000.00),
	(24, 1, 11, 1, 25000.00),
	(25, 4, 11, 3, 15000.00);
/*!40000 ALTER TABLE `transaction_details` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
