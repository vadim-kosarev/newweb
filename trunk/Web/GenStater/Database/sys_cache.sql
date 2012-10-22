-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.1.51-community - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-10-23 00:42:44
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table gen_stat.sys_cache
CREATE TABLE IF NOT EXISTS `sys_cache` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key_hash` char(32) DEFAULT NULL,
  `key_value` text,
  `cache_content` longtext,
  PRIMARY KEY (`id`),
  KEY `key_hash` (`key_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
