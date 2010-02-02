CREATE TABLE `user_following` (
	`user_following_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`user_id` INT( 10 ) NOT NULL ,
	`following_id` INT( 10 ) NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;