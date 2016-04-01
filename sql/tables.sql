-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2016 at 01:58 PM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `social`
--
CREATE DATABASE IF NOT EXISTS `social` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `social`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`comment_id`),
  KEY `comments_ibfk_1` (`user_id`),
  KEY `comments_ibfk_2` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `comments`
--
DELIMITER $$
CREATE TRIGGER `add_notification_comment` AFTER INSERT ON `comments` FOR EACH ROW BEGIN
		DECLARE ui INT DEFAULT 0;
		SET ui = (SELECT `user_id` from post WHERE post.`post_id`=NEW.`post_id` LIMIT 1);
		IF(ui != NEW.`user_id`) THEN
		INSERT INTO `notifications` (`user_id`,`post_id`,`type`,`status`) VALUES(
			ui,
			NEW.`post_id`,
			2,
			1)  ON DUPLICATE KEY UPDATE status=VALUES(status),time=VALUES(time);
		END IF;
	END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_notification_comment` AFTER DELETE ON `comments` FOR EACH ROW BEGIN
		DECLARE commenters_count INT DEFAULT 0;
		DECLARE ui INT DEFAULT 0;
		DELETE FROM `likes` WHERE `likes`.post_id=OLD.`comment_id` AND type=2; -- Removing likes which was on comments ..
		SET ui=(SELECT `user_id` from post WHERE post.`post_id`=OLD.`post_id` LIMIT 1);

		SET commenters_count = (SELECT count(`comments`.`user_id`) from `comments` WHERE 
			`comments`.`post_id` = OLD.`post_id` AND 
			`comments`.`status`=1 AND 
			`comments`.`user_id`!=ui
			);
		IF(commenters_count = 0) THEN
		DELETE FROM `notifications` WHERE post_id=OLD.`post_id` AND
		user_id = ui AND 
		type=2;
		END IF;
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_one` int(11) NOT NULL,
  `user_two` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`friend_id`),
  KEY `friends_ibfk_1` (`user_one`),
  KEY `friends_ibfk_2` (`user_two`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `friends`
--
DELIMITER $$
CREATE TRIGGER `add_notification_acceptfriendrequest` AFTER UPDATE ON `friends` FOR EACH ROW BEGIN
 		IF(NEW.`status` = 2) THEN
 			INSERT INTO `notifications` (`user_id`,`from_user_id`,`type`,`status`) VALUES(
				NEW.`user_one`,NEW.`user_two`,4,1
			);
			DELETE FROM `notifications` WHERE `type`=3 AND `user_id` = NEW.`user_two` AND `from_user_id` = NEW.`user_one`; -- Deleting friend request notification.
		END IF;
	END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `add_notification_sendfriendrequest` AFTER INSERT ON `friends` FOR EACH ROW BEGIN
 		IF(NEW.`status` = 1) THEN
 			INSERT INTO `notifications` (`user_id`,`from_user_id`,`type`,`status`) VALUES(
				NEW.`user_two`,NEW.`user_one`,3,1
			);
		END IF;
	END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_notification_unfriend` AFTER DELETE ON `friends` FOR EACH ROW BEGIN
 		DELETE FROM `notifications` WHERE `type`=4 AND `user_id` = OLD.`user_one` AND `from_user_id` = OLD.`user_two`; -- Deleting friend request accepted notification.
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keys`
--

CREATE TABLE IF NOT EXISTS `keys` (
  `user_id` int(11) NOT NULL,
  `skey` varchar(100) COLLATE utf8_bin NOT NULL,
  `exp` varchar(12) COLLATE utf8_bin NOT NULL,
  `lastused` varchar(12) COLLATE utf8_bin DEFAULT NULL,
  `hits` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE IF NOT EXISTS `likes` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`post_id`,`type`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `likes`
--
DELIMITER $$
CREATE TRIGGER `add_notification_like` AFTER INSERT ON `likes` FOR EACH ROW BEGIN
		DECLARE ui INT DEFAULT 0;
		IF(NEW.`type` = 1) THEN
			SET ui = (SELECT `user_id` from post WHERE post.`post_id`=NEW.`post_id` LIMIT 1);
			IF(ui != NEW.`user_id`) THEN
			INSERT INTO `notifications` (`user_id`,`post_id`,`type`,`status`) VALUES(
				ui,
				NEW.`post_id`,
				1,
				1)  ON DUPLICATE KEY UPDATE status=VALUES(status),time=VALUES(time);
			END IF;
		END IF;
	END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_notification_likes` AFTER DELETE ON `likes` FOR EACH ROW BEGIN
		DECLARE likes_count INT DEFAULT 0;
		DECLARE ui INT DEFAULT 0;
		SET ui=(SELECT `user_id` from post WHERE post.`post_id`=OLD.`post_id` LIMIT 1);

		SET likes_count = (SELECT count(`likes`.`user_id`) from `likes` WHERE 
			`likes`.`post_id` = OLD.`post_id` AND 
			`likes`.`type`=1 AND 
			`likes`.`user_id`!=ui
			);
		IF(likes_count = 0) THEN
		DELETE FROM `notifications` WHERE post_id=OLD.`post_id` AND
		user_id = ui AND 
		type=1;
		END IF;
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_one` int(11) NOT NULL,
  `user_two` int(11) NOT NULL,
  `message` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`message_id`),
  KEY `messages_ibfk_1` (`user_two`),
  KEY `messages_ibfk_2` (`user_one`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `messages`
--
DELIMITER $$
CREATE TRIGGER `prevent_msg_toblocked` BEFORE INSERT ON `messages` FOR EACH ROW BEGIN
		DECLARE blocked_count INT DEFAULT 0;
		SET blocked_count = (
			SELECT count(*)
				FROM `friends` WHERE (
				(`user_one`=NEW.`user_one` AND `user_two`=NEW.`user_two`) OR
				(`user_two`=NEW.`user_one` AND `user_one`=NEW.`user_two`)
				) AND status=3
		); -- Finding blocked list.
		IF(blocked_count != 0) THEN
			SET NEW.user_one=null; -- this will throw an error and prevent sending message
		END IF;
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`notification_id`),
  UNIQUE KEY `user_id` (`user_id`,`post_id`,`type`),
  KEY `user_id_2` (`user_id`),
  KEY `notifications_ibfk_2` (`post_id`),
  KEY `type` (`type`),
  KEY `notifications_ibfk_3` (`from_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification_msg`
--

CREATE TABLE IF NOT EXISTS `notification_msg` (
  `type` int(11) NOT NULL AUTO_INCREMENT,
  `msg` text NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_data` text,
  `picture_id` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  `access` int(11) DEFAULT '1',
  PRIMARY KEY (`post_id`),
  KEY `post_ibfk_1` (`user_id`),
  KEY `picture_id` (`picture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `post`
--
DELIMITER $$
CREATE TRIGGER `remove_notification_post` AFTER UPDATE ON `post` FOR EACH ROW BEGIN
	IF(New.`status`!=1) THEN
	DELETE FROM `notifications` WHERE `notifications`.`post_id`=OLD.`post_id`;
	END IF;
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `userdata`
--

CREATE TABLE IF NOT EXISTS `userdata` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `data` text,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`data_id`),
  UNIQUE KEY `user_id_2` (`user_id`,`type`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(100) NOT NULL,
  `gender` char(1) NOT NULL,
  `dob` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`);

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_one`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`user_two`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `keys`
--
ALTER TABLE `keys`
  ADD CONSTRAINT `keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_two`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_one`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`),
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`type`) REFERENCES `notification_msg` (`type`);

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`picture_id`) REFERENCES `images` (`image_id`);

--
-- Constraints for table `userdata`
--
ALTER TABLE `userdata`
  ADD CONSTRAINT `userdata_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
