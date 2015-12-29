-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2015 at 05:28 PM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `social`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
`comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
`friend_id` int(11) NOT NULL,
  `user_one` int(11) NOT NULL,
  `user_two` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE IF NOT EXISTS `likes` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
`message_id` int(11) NOT NULL,
  `user_one` int(11) NOT NULL,
  `user_two` int(11) NOT NULL,
  `message` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
`post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_data` text,
  `link` varchar(150) DEFAULT NULL,
  `picture_id` varchar(10) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  `access` int(11) DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `userdata`
--

CREATE TABLE IF NOT EXISTS `userdata` (
`data_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `data` text NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`user_id` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(100) NOT NULL,
  `gender` char(1) NOT NULL,
  `dob` date NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
 ADD PRIMARY KEY (`comment_id`), ADD KEY `user_id` (`user_id`), ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
 ADD PRIMARY KEY (`friend_id`), ADD KEY `user_one` (`user_one`), ADD KEY `user_two` (`user_two`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
 ADD UNIQUE KEY `user_id` (`user_id`,`post_id`,`type`), ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
 ADD PRIMARY KEY (`message_id`), ADD KEY `user_two` (`user_two`), ADD KEY `user_one` (`user_one`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
 ADD PRIMARY KEY (`post_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `userdata`
--
ALTER TABLE `userdata`
 ADD PRIMARY KEY (`data_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`user_id`), ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
MODIFY `friend_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `userdata`
--
ALTER TABLE `userdata`
MODIFY `data_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
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
-- Constraints for table `post`
--
ALTER TABLE `post`
ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `userdata`
--
ALTER TABLE `userdata`
ADD CONSTRAINT `userdata_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
`notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `msg` varchar(50) DEFAULT NULL,
  `type` tinyint(4) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
 ADD PRIMARY KEY (`notification_id`), ADD UNIQUE KEY `user_id` (`user_id`,`post_id`,`type`), ADD KEY `post_id` (`post_id`), ADD KEY `user_id_2` (`user_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`);


/*
CREATE TRIGGER  add_notification_comment AFTER INSERT ON `comments`
	FOR EACH ROW
	INSERT INTO `notifications` (`user_id`,`post_id`,`type`,`status`) VALUES(
		(SELECT `user_id` from post WHERE post.`post_id`=NEW.`post_id` LIMIT 1),
		NEW.`post_id`,
		2,
		1)  ON DUPLICATE KEY UPDATE status=VALUES(status),time=VALUES(time);

CREATE TRIGGER  add_notification_like AFTER INSERT ON `likes`
	FOR EACH ROW
	INSERT INTO `notifications` (`user_id`,`post_id`,`type`,`status`) VALUES(
		(SELECT `user_id` from post WHERE post.`post_id`=NEW.`post_id` LIMIT 1),
		NEW.`post_id`,
		1,
		1)  ON DUPLICATE KEY UPDATE status=VALUES(status),time=VALUES(time);
*/
DELIMITER //
CREATE TRIGGER  add_notification_comment AFTER INSERT ON `comments`
	FOR EACH ROW
	BEGIN
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


DELIMITER //
CREATE TRIGGER  add_notification_like AFTER INSERT ON `likes`
	FOR EACH ROW
	BEGIN
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
DROP TRIGGER IF EXISTS remove_notification_comment; 
DELIMITER //
CREATE TRIGGER  remove_notification_comment AFTER DELETE ON `comments`
	FOR EACH ROW
	BEGIN
		DECLARE commenters_count INT DEFAULT 0;
		DECLARE ui INT DEFAULT 0;
		DELETE FROM `likes` WHERE post_id=OLD.`post_id` AND type=2; -- Removing likes which was on deleted comment ..

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

DROP TRIGGER IF EXISTS remove_notification_likes; 
DELIMITER //
CREATE TRIGGER  remove_notification_likes AFTER DELETE ON `likes`
	FOR EACH ROW
	BEGIN
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

DROP TRIGGER IF EXISTS remove_notification_post; 
DELIMITER //
CREATE TRIGGER  remove_notification_post AFTER UPDATE ON `post`
	FOR EACH ROW
	BEGIN
	IF(New.`status`!=1) THEN
	DELETE FROM `notifications` WHERE `notifications`.`post_id`=OLD.`post_id`;
	END IF;
	END

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
