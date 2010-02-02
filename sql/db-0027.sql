CREATE TABLE `profile_comment` (
	`profile_comment_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`date_create` INT( 11 ) NOT NULL ,
	`date_modify` INT( 11 ) NOT NULL DEFAULT '0',
	`profile_id` INT( 10 ) NOT NULL ,
	`owner_id` INT( 10 ) NOT NULL ,
	`comment` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
	`status` TINYINT( 1 ) NOT NULL DEFAULT '0'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;