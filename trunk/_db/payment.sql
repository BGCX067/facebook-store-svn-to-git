-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 25, 2012 at 06:21 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ym_contest`
--

-- --------------------------------------------------------

--
-- Table structure for table `ym_items`
--

CREATE TABLE IF NOT EXISTS `ym_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX_CODE` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ym_items`
--

INSERT INTO `ym_items` (`id`, `code`, `title`, `description`, `price`) VALUES
(1, '11', 'Remove footer logo', 'Remove footer logo', 4.99);

-- --------------------------------------------------------

--
-- Table structure for table `ym_orders`
--

CREATE TABLE IF NOT EXISTS `ym_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_page` varchar(20) DEFAULT NULL,
  `id_txn` varchar(17) DEFAULT NULL,
  `time_created` int(11) DEFAULT NULL,
  `time_modified` int(11) DEFAULT NULL,
  `total` float DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ID_USER` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ym_order_items`
--

CREATE TABLE IF NOT EXISTS `ym_order_items` (
  `id_order` int(11) unsigned NOT NULL,
  `id_item` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_order`,`id_item`),
  KEY `FK_ID_ORDER` (`id_order`),
  KEY `FK_ID_ITEM` (`id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ym_page_items`
--

CREATE TABLE IF NOT EXISTS `ym_page_items` (
  `id_page` varchar(20) NOT NULL,
  `id_item` int(11) unsigned NOT NULL,
  `time_paid` int(11) unsigned NOT NULL,
  `time_expired` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_page`,`id_item`),
  KEY `id_item` (`id_item`),
  KEY `id_page` (`id_page`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
