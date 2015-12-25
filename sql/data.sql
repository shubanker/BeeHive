-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2015 at 05:32 PM
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

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `gender`, `dob`, `status`) VALUES
(1, 'Subhanker', 'Chourasia', 'subhankerchourasia@gmail.com', '$2a$09$NGFiNzlmZTVkNTllOTQyMe.A52Rj9u.tv5O54ftIlLZ0q2sKDhG/C', 'M', '1994-04-25', 1),
(3, 'Vikash', 'Kisku', 'vikashkisku@gmail.com', '$2a$09$NGM1NDhlNzVmMzkwYzZlN.Jz6URvNX454s5Y1ALe6E6o7ti1QUD7u', 'M', '1993-10-03', 1),
(8, 'Aamir', 'Sohail', 'aamirsohail@gmail.com', '$2a$09$Yzc3NDI4NzU0NDMzYWE5O.Z8YOUjfbnUJhCC5j18k4A4amQuHh1tK', 'M', '1994-08-03', 1);

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `user_id`, `post_data`, `link`, `picture_id`, `time`, `status`, `access`) VALUES
(1, 1, 'A very good Morning..:)', '', '', '0000-00-00 00:00:00', 0, 0),
(2, 1, 'A very good Morning..:)', '', '', '0000-00-00 00:00:00', 0, 0),
(3, 1, 'Another testing post isn''t it?', NULL, NULL, '2015-12-25 06:56:59', 1, 1),
(4, 1, 'Another testing post isn''t it?', NULL, NULL, '2015-12-25 06:57:10', 1, 1),
(5, 1, 'Another testing post', NULL, NULL, '2015-12-25 06:59:07', 1, 1),
(6, 3, 'Hello Friends..', NULL, NULL, '2015-12-25 07:02:30', 1, 1),
(7, 8, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus voluptatum pariatur voluptatibus impedit?', NULL, NULL, '2015-12-25 07:03:49', 1, 1);

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `user_id`, `post_id`, `comment`, `time`, `status`) VALUES
(1, 1, 1, 'hello''', '2015-12-25 15:43:20', 1),
(2, 1, 1, 'Good one', '2015-12-25 16:02:45', 1),
(3, 1, 1, 'Great!!', '2015-12-25 16:03:50', 1);

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`friend_id`, `user_one`, `user_two`, `status`) VALUES
(1, 1, 3, 2),
(2, 8, 1, 2),
(3, 3, 8, 2);

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`user_id`, `post_id`, `type`) VALUES
(1, 1, 1),
(3, 1, 1);

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `user_one`, `user_two`, `message`, `time`, `status`) VALUES
(1, 1, 3, 'Hey there What''s up ?', '2015-12-24 07:29:21', 1),
(2, 3, 1, 'I am Fine What about you buddy ?', '2015-12-24 07:30:00', 3),
(3, 1, 8, 'Or bhai kaisa hai?', '2015-12-24 07:31:51', 1),
(4, 8, 1, 'Maze me hu Apna suna', '2015-12-24 07:31:51', 1),
(5, 1, 3, 'How was your exams ?', '2015-12-24 07:31:51', 1),
(6, 3, 1, 'Theek thak', '2015-12-24 07:31:52', 1),
(7, 8, 3, 'Kya ho raha hai ?', '2015-12-24 11:09:17', 1),
(8, 3, 1, 'Aa Kb Raha hai?', '2015-12-24 16:41:52', 1);




/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
