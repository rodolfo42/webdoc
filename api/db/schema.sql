-- --------------------------------------------------------
-- Host:                         192.168.0.200
-- Server version:               5.1.37 - Source distribution
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2013-01-23 19:12:16
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for webdoc
CREATE DATABASE IF NOT EXISTS `webdoc` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `webdoc`;

-- Dumping structure for table webdoc.docs
CREATE TABLE IF NOT EXISTS `docs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `conteudo` text,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;