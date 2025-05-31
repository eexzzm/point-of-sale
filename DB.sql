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

-- Dumping structure for table db_pos.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `transaction_id_txt` varchar(15) NOT NULL,
  `transaction_create_at` varchar(50) NOT NULL,
  `transaction_amount` varchar(30) NOT NULL,
  `transaction_total` varchar(50) NOT NULL,
  `transaction_cash` varchar(50) NOT NULL,
  `transaction_money_changes` varchar(50) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `transactions_item_id_items_item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table db_pos.transactions: 5 rows
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` (`transaction_id`, `item_id`, `transaction_id_txt`, `transaction_create_at`, `transaction_amount`, `transaction_total`, `transaction_cash`, `transaction_money_changes`) VALUES
	(2, 1, 'TRX001', '01/10/2022 11:30:59 pm', '2', '50.000', '100.000', '50.000'),
	(4, 2, 'TRX003', '01/10/2022 11:39:41 pm', '1', '27.000', '30.000', '3.000'),
	(5, 1, 'TRX003', '05/26/2025 09:00:38 pm', '1', '25.000', '50000', '25.000'),
	(6, 4, 'TRX010', '05/26/2025 09:01:44 pm', '1', '50.000', '50000', '0'),
	(7, 2, 'TRX010', '05/30/2025 09:37:41 pm', '10', '270.000', '300.000', '30.000');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SETtemp_db SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
temp_db