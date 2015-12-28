-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 19, 2012 at 03:55 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `market-store`
--

-- --------------------------------------------------------

--
-- Table structure for table `buyers`
--

CREATE TABLE IF NOT EXISTS `buyers` (
  `id_user` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE IF NOT EXISTS `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cash` decimal(11,2) NOT NULL,
  `id_buyer` int(11) NOT NULL,
  `id_shipper` int(11) DEFAULT NULL,
  `id_payment` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `payment_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_buyer` (`id_buyer`,`id_shipper`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cart_products`
--

CREATE TABLE IF NOT EXISTS `cart_products` (
  `id_cart` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `sale_off` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_cart`,`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `id_store` int(11) NOT NULL,
  `id_page` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_store` (`id_store`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id_store` int(11) NOT NULL,
  `id_payment` tinyint(4) NOT NULL,
  `paypal_account` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id_store`,`id_payment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `range` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(11,2) NOT NULL,
  `sale_off` decimal(11,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(4) NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_category` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_category` (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_photos`
--

CREATE TABLE IF NOT EXISTS `product_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `id_product` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_product` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shippers`
--

CREATE TABLE IF NOT EXISTS `shippers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE IF NOT EXISTS `stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `banner` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'USD',
  `number_products` tinyint(4) NOT NULL DEFAULT '12',
  `about_us` text COLLATE utf8_unicode_ci,
  `terms_conditions` text COLLATE utf8_unicode_ci,
  `contact_info` text COLLATE utf8_unicode_ci,
  `status` int(11) NOT NULL,
  `classification` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'general',
  `id_page` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `theme` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default_theme.css',
  PRIMARY KEY (`id`),
  KEY `type` (`classification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_social` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `birthday` date DEFAULT NULL,
  `gender` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` tinyint(2) DEFAULT NULL,
  `locale` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `time_created` int(11) NOT NULL,
  `time_modified` int(11) NOT NULL,
  `time_social_modified` int(11) NOT NULL,
  `role` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_social` (`id_social`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
