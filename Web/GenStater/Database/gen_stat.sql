# --------------------------------------------------------
# Host:                         127.0.0.1
# Database:                     gen_stat
# Server version:               5.1.51-community
# Server OS:                    Win64
# HeidiSQL version:             5.0.0.3272
# Date/time:                    2010-10-16 11:51:13
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table gen_stat.data_events_processstarted
CREATE TABLE IF NOT EXISTS `data_events_processstarted` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `d_date_startDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `d_ext_host` int(10) unsigned NOT NULL DEFAULT '0',
  `d_int_pid` int(10) unsigned NOT NULL DEFAULT '0',
  `d_str_path` varchar(255) NOT NULL DEFAULT '0',
  `tag0` int(10) unsigned DEFAULT NULL,
  `tag1` int(10) unsigned DEFAULT NULL,
  `tag2` int(10) unsigned DEFAULT NULL,
  `tag3` int(10) unsigned DEFAULT NULL,
  `tag4` int(10) unsigned DEFAULT NULL,
  `tag5` int(10) unsigned DEFAULT NULL,
  `tag6` int(10) unsigned DEFAULT NULL,
  `tag7` int(10) unsigned DEFAULT NULL,
  `tag8` int(10) unsigned DEFAULT NULL,
  `tag9` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table gen_stat.data_ext_host
CREATE TABLE IF NOT EXISTS `data_ext_host` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Data exporting was unselected.


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
# --------------------------------------------------------
# Host:                         127.0.0.1
# Database:                     mysql
# Server version:               5.1.51-community
# Server OS:                    Win64
# HeidiSQL version:             5.0.0.3272
# Date/time:                    2010-10-16 11:51:13
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table mysql.proc
CREATE TABLE IF NOT EXISTS `proc` (
  `db` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `name` char(64) NOT NULL DEFAULT '',
  `type` enum('FUNCTION','PROCEDURE') NOT NULL,
  `specific_name` char(64) NOT NULL DEFAULT '',
  `language` enum('SQL') NOT NULL DEFAULT 'SQL',
  `sql_data_access` enum('CONTAINS_SQL','NO_SQL','READS_SQL_DATA','MODIFIES_SQL_DATA') NOT NULL DEFAULT 'CONTAINS_SQL',
  `is_deterministic` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `security_type` enum('INVOKER','DEFINER') NOT NULL DEFAULT 'DEFINER',
  `param_list` blob NOT NULL,
  `returns` longblob NOT NULL,
  `body` longblob NOT NULL,
  `definer` char(77) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sql_mode` set('REAL_AS_FLOAT','PIPES_AS_CONCAT','ANSI_QUOTES','IGNORE_SPACE','NOT_USED','ONLY_FULL_GROUP_BY','NO_UNSIGNED_SUBTRACTION','NO_DIR_IN_CREATE','POSTGRESQL','ORACLE','MSSQL','DB2','MAXDB','NO_KEY_OPTIONS','NO_TABLE_OPTIONS','NO_FIELD_OPTIONS','MYSQL323','MYSQL40','ANSI','NO_AUTO_VALUE_ON_ZERO','NO_BACKSLASH_ESCAPES','STRICT_TRANS_TABLES','STRICT_ALL_TABLES','NO_ZERO_IN_DATE','NO_ZERO_DATE','INVALID_DATES','ERROR_FOR_DIVISION_BY_ZERO','TRADITIONAL','NO_AUTO_CREATE_USER','HIGH_NOT_PRECEDENCE','NO_ENGINE_SUBSTITUTION','PAD_CHAR_TO_FULL_LENGTH') NOT NULL DEFAULT '',
  `comment` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `character_set_client` char(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `collation_connection` char(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `db_collation` char(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `body_utf8` longblob,
  PRIMARY KEY (`db`,`name`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stored Procedures';

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
