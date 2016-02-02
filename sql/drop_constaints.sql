ALTER TABLE `comments`
DROP FOREIGN KEY `comments_ibfk_1`,
DROP FOREIGN KEY `comments_ibfk_2`;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
DROP FOREIGN KEY `friends_ibfk_1`,
DROP FOREIGN KEY `friends_ibfk_2`;

--
-- Constraints for table `keys`
--
ALTER TABLE `keys`
DROP FOREIGN KEY `keys_ibfk_1`;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
DROP FOREIGN KEY `likes_ibfk_1`;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
DROP FOREIGN KEY `messages_ibfk_1`,
DROP FOREIGN KEY `messages_ibfk_2`;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
DROP FOREIGN KEY `notifications_ibfk_1`,
DROP FOREIGN KEY `notifications_ibfk_2`;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
DROP FOREIGN KEY `post_ibfk_1`;

--
-- Constraints for table `userdata`
--
ALTER TABLE `userdata`
DROP FOREIGN KEY `userdata_ibfk_1`;