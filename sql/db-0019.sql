CREATE TABLE `user_follow` (
	`user_follow_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`follower_id` INT NOT NULL ,
	`following_id` INT NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;