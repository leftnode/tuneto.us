CREATE TABLE `track_favorite` (
	`track_favorite_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`track_id` INT( 10 ) NOT NULL ,
	`user_id` INT( 10 ) NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;