# --------------------------------------------------------
# Host:                         127.0.0.1
# Database:                     gen_stat
# Server version:               5.1.51-community-log
# Server OS:                    Win64
# HeidiSQL version:             5.0.0.3272
# Date/time:                    2010-10-18 19:02:39
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for procedure gen_stat.proc_showTags
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_showTags`()
    READS SQL DATA
    COMMENT 'Shows all existing tags'
BEGIN
SELECT * FROM sys_tags ORDER BY name;

END//
DELIMITER ;


# Dumping structure for table gen_stat.sys_queries
CREATE TABLE IF NOT EXISTS `sys_queries` (
  `query_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of SQL query to run',
  `sql` text NOT NULL COMMENT 'ID of SQL query to run',
  PRIMARY KEY (`query_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Queries for dashboard';

# Data exporting was unselected.


# Dumping structure for table gen_stat.sys_tags
CREATE TABLE IF NOT EXISTS `sys_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Тэги данных';

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
