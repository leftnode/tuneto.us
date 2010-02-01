SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `track`;
CREATE TABLE IF NOT EXISTS `track` (
  `track_id` int(10) NOT NULL auto_increment,
  `date_create` int(11) NOT NULL,
  `date_modify` int(11) NOT NULL default '0',
  `user_id` int(10) NOT NULL default '0',
  `path` varchar(128) collate utf8_unicode_ci NOT NULL,
  `filename` varchar(128) collate utf8_unicode_ci NOT NULL,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `length` smallint(5) NOT NULL default '0',
  `length_formatted` varchar(24) collate utf8_unicode_ci NOT NULL,
  `view_count` mediumint(5) NOT NULL default '0',
  `permission` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`track_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `track_comment`;
CREATE TABLE IF NOT EXISTS `track_comment` (
  `track_comment_id` int(10) NOT NULL auto_increment,
  `date_create` int(11) NOT NULL,
  `date_modify` int(11) NOT NULL default '0',
  `track_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `subject` varchar(128) collate utf8_unicode_ci NOT NULL,
  `rating` tinyint(1) NOT NULL default '0',
  `comment` text collate utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`track_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `track_follow`;
CREATE TABLE IF NOT EXISTS `track_follow` (
  `track_follow_id` int(10) NOT NULL auto_increment,
  `date_create` int(11) NOT NULL,
  `date_modify` int(11) NOT NULL default '0',
  `track_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  PRIMARY KEY  (`track_follow_id`),
  KEY `track_id` (`track_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `track_queue`;
CREATE TABLE IF NOT EXISTS `track_queue` (
  `track_queue_id` int(10) NOT NULL auto_increment,
  `date_create` int(11) NOT NULL,
  `date_modify` int(11) NOT NULL default '0',
  `track_id` int(10) NOT NULL,
  `output` text collate utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY  (`track_queue_id`),
  KEY `track_id` (`track_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) NOT NULL auto_increment,
  `date_create` int(11) NOT NULL,
  `date_modify` int(11) NOT NULL default '0',
  `date_lastlogin` int(11) NOT NULL default '0',
  `content_directory` varchar(64) collate utf8_unicode_ci NOT NULL,
  `nickname` varchar(64) collate utf8_unicode_ci NOT NULL,
  `password` varchar(64) collate utf8_unicode_ci NOT NULL,
  `password_salt` varchar(64) collate utf8_unicode_ci NOT NULL,
  `email_address` varchar(255) collate utf8_unicode_ci NOT NULL,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `gender` varchar(12) collate utf8_unicode_ci NOT NULL,
  `country` varchar(32) collate utf8_unicode_ci NOT NULL,
  `birthday` int(11) NOT NULL default '0',
  `biography` text collate utf8_unicode_ci NOT NULL,
  `interests` text collate utf8_unicode_ci NOT NULL,
  `music` text collate utf8_unicode_ci NOT NULL,
  `movies` text collate utf8_unicode_ci NOT NULL,
  `books` text collate utf8_unicode_ci NOT NULL,
  `website1` varchar(128) collate utf8_unicode_ci NOT NULL,
  `website2` varchar(128) collate utf8_unicode_ci NOT NULL,
  `website3` varchar(128) collate utf8_unicode_ci NOT NULL,
  `photo` varchar(128) collate utf8_unicode_ci NOT NULL,
  `photo_thumbnail` varchar(128) collate utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  KEY `nickname` (`nickname`),
  KEY `email_address` (`email_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
