CREATE TABLE `email` (
  `email_id` tinyint(2) NOT NULL auto_increment,
  `date_create` int(11) NOT NULL,
  `date_modify` int(11) NOT NULL default '0',
  `locale` varchar(16) collate utf8_unicode_ci NOT NULL,
  `name` varchar(32) collate utf8_unicode_ci NOT NULL,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  `alt_body` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`email_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;