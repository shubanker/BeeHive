-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2015 at 10:19 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `social_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `friend_one` int(11) NOT NULL DEFAULT '0',
  `friend_two` int(11) NOT NULL DEFAULT '0',
  `status` enum('0','1','2') DEFAULT '0',
  PRIMARY KEY (`friend_one`,`friend_two`),
  KEY `friend_two` (`friend_two`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`friend_one`, `friend_two`, `status`) VALUES
(1, 1, '2'),
(1, 2, '1'),
(1, 3, '1'),
(2, 2, '2'),
(3, 3, '2'),
(4, 1, '1'),
(4, 4, '0');

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE IF NOT EXISTS `updates` (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `updated` varchar(45) DEFAULT NULL,
  `user_id_fk` int(45) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`update_id`),
  KEY `user_id_fk` (`user_id_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `updates`
--

INSERT INTO `updates` (`update_id`, `updated`, `user_id_fk`, `created`, `ip`) VALUES
(1, 'My first Update', 1, NULL, NULL),
(2, 'My Seconde Update', 1, NULL, NULL),
(3, 'I am user two', 2, NULL, NULL),
(4, 'I m user three', 3, NULL, NULL),
(5, 'I m User four', 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `friend_count` int(11) DEFAULT NULL,
  `profile_pic` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `friend_count`, `profile_pic`) VALUES
(1, 'vikash', 'kisku', NULL, NULL, NULL),
(2, 'rajesh', 'yadav', NULL, NULL, NULL),
(3, 'gulshan', 'kisku', NULL, NULL, NULL),
(4, 'Sakal', 'yadav', NULL, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`friend_one`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_two`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `updates`
--
ALTER TABLE `updates`
  ADD CONSTRAINT `updates_ibfk_1` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
