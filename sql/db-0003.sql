CREATE TABLE `user_follow` (
	`user_follow_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`date_create` INT( 11 ) NOT NULL ,
	`date_modify` INT( 11 ) NOT NULL DEFAULT '0',
	`user_id` INT( 10 ) NOT NULL ,
	`follow_user_id` INT( 10 ) NOT NULL ,
	`status` TINYINT( 1 ) NOT NULL DEFAULT '0'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;