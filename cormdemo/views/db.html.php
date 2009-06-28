<pre>
-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 29, 2009 at 01:34 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.9-2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogposts`
--

CREATE TABLE IF NOT EXISTS `blogposts` (
  `id` int(12) NOT NULL auto_increment,
  `person_id` int(12) NOT NULL,
  `title` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `blogposts`
--

INSERT INTO `blogposts` (`id`, `person_id`, `title`, `text`, `time`) VALUES
(1, 1, 'test', 'text', 1246231669),
(2, 1, 'sdfsd', 'sdfsdfs', 1246230980),
(3, 1, 'title18532', 'text31437', 1246231563),
(4, 1, 'title28546', 'text18067', 1246231656),
(5, 1, 'title1744', 'text30409', 1246231664),
(6, 1, 'title462', 'text14575', 1246231703),
(7, 1, 'title16380', 'text13957', 1246231875);

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE IF NOT EXISTS `cars` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `person_id` int(10) unsigned NOT NULL,
  `brand` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`person_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `person_id`, `brand`) VALUES
(1, 1, 'lada'),
(2, 2, 'porsche');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`) VALUES
(1, 'groupy'),
(2, 'Strange people group');

-- --------------------------------------------------------

--
-- Table structure for table `groups_persons`
--

CREATE TABLE IF NOT EXISTS `groups_persons` (
  `group_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  KEY `group_id` (`group_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups_persons`
--

INSERT INTO `groups_persons` (`group_id`, `person_id`) VALUES
(1, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE IF NOT EXISTS `persons` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`id`, `email`, `name`) VALUES
(1, 'bla@bla.nl', 'Mister Name'),
(2, 'blo@blo.nl', 'Miss Name');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups_persons`
--
ALTER TABLE `groups_persons`
  ADD CONSTRAINT `groups_persons_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groups_persons_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;
</pre>