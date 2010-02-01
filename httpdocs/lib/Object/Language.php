<?php

class Language {
	public static $lang = array(
		'en_us.UTF8' => array(
			'error_account_not_found' => "Your account can not be found.",
			'error_nickname_taken' => "The nickname you attempted to register with is already in use. Please choose another one.",
			'error_email_address_taken' => "The email address you attempted to register with is already in use.",
			'error_account_creation_failed' => "Failed to create your account. Please try again.",
			'error_form_validation_error' => "Your form failed to validate, please check the errors and try again.",
			'error_not_logged_in' => "Please register or log in first to view this section of the website.",
			'error_track_not_found' => "Track Not Found",
			'error_track_not_found_header' => "We Hope We Didn't Make The Error",
			
			'success_account_created' => "Your account was successfully created. Start uploading track immediately!",
			'success_account_track_uploaded' => "Your track was successfully uploaded. Please give us a moment while we convert it to the proper format.",
			
			
			'index_welcome_text' => "Easily share your favorite music and MP3's, let your friends know what you're listening to, and keep track of your friends daily.",
			'index_latest_tracks' => "Latest Tracks From The Community In Real Time",
			
			'account_your_dashboard' => "Your Dashboard",
			'account_upload_track' => "Upload New Track",
			
			
			
			
			'play' => "Play",
			'plays' => "Plays",
			'views' => "Views",
			'track_name' => "Track Name",
		)
	);
	
	public static $locale = 'en_us.UTF8';

	public static function __($s) {
		if ( true === isset(self::$lang[self::$locale][$s]) ) {
			return self::$lang[self::$locale][$s];
		}
		
		return NULL;
	}
}