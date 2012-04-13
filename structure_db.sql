-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 29, 2012 at 12:03 AM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `orangead`
--

-- --------------------------------------------------------

--
-- Table structure for table `gracq_points_noirs`
--

CREATE TABLE `gracq_points_noirs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lon` decimal(40,20) NOT NULL,
  `lat` decimal(40,20) NOT NULL,
  `description` text NOT NULL,
  `nom_prenom` varchar(200) NOT NULL,
  `lieu` varchar(500) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type` enum('points_noirs','parkings','points_noirs_supp') NOT NULL DEFAULT 'points_noirs',
  `couleur` enum('white','gray','red','yellow','green') NOT NULL DEFAULT 'white',
  `date_resol` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;
