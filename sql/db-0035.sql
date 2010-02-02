CREATE TABLE `image` (
	`image_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`date_create` INT( 11 ) NOT NULL ,
	`date_modify` INT( 11 ) NOT NULL DEFAULT '0',
	`image_large` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
	`image_thumbnail` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
	`image_micro` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;