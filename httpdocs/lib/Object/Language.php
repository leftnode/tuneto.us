<?php

class Language {
	public static $lang = array(
		'en_us.UTF8' => array(
			'error_general' => "A general error occurred. Sorry we can't give you more info.",
			'error_account_not_found' => "Your account can not be found.",
			'error_nickname_taken' => "The nickname you attempted to register with is already in use. Please choose another one.",
			'error_email_address_taken' => "The email address you attempted to register with is already in use.",
			'error_account_creation_failed' => "Failed to create your account. Please try again.",
			'error_form_validation_error' => "Your form failed to validate, please check the errors and try again.",
			'error_not_logged_in' => "Please register or log in first to view this section of the website.",
			'error_track_not_found' => "Track Not Found",
			'error_track_not_found_header' => "We Hope We Didn't Make The Error",
			'error_profile_not_found' => "Profile Not Found",
			'error_select_image' => "Please select an image to upload.",
			'error_select_track' => "Please select an MP3 to upload.",
			'error_track_already_favorited' => "You have already added this track to your favorites.",
			'error_already_following' => "You are already following this person.",
			'error_page_not_found' => "Page Not Found",
			'error_page_not_found_text' => "Sorry, the page you request can not be found.",
			'error_account_follow_disabled' => "The user you're attempting to follow is not enabled.",
			
			'success_account_created' => "Your account was successfully created.",
			'success_account_track_uploaded' => "Your track was successfully uploaded. Please give us a moment while we convert it to the proper format.",
			'success_profile_updated' => "Your profile was successfully updated.",
			'success_photo_updated' => "Your photo was successfully updated.",
			'success_settings_updated' => "Your settings were successfully updated.",
			'success_track_favorited' => "You have added this track to your favorite list.",
			'success_profile_followed' => "You are now following this user's updates.",
			'success_contact_sent' => "Your contact form was successfully sent, you'll be hearing back shortly.",
			
			'index_welcome_text' => "Easily share your favorite music and MP3's, let your friends know what you're listening to, and keep track of your friends daily.",
			'index_latest_tracks' => "Latest Tracks From The Community In Real Time",
			
			'account_your_dashboard' => "Your Dashboard",
			'account_update_your_profile' => "Update Your Profile",
			'account_upload_track' => "Upload New Track",
			'account_favorites' => "Your Favorite Tracks",
			'account_settings' => "Account Settings",
			'account_track_list' => "Your Tracks",
			'account_view_all_tracks' => "View All Tracks",
			'account_update_settings' => "Update Settings",
			'account_following' => "You Are Following These Users",
			'account_followers' => "These Users Are Following You",
			'account_settings_help' => "By changing your settings, you can adjust how you use the website, and how other members can see your profile.",
			'account_setting_allow_followers' => "Allow other members on the site to follow your profile.",
			'account_setting_email_new_follower' => "Send me an email when a new user starts following my profile.",
			'account_setting_email_finished_processing' => "Send me an email when a track I've recently uploaded is finished processing.",
			'account_no_favorites' => "You do not have any favorite tracks yet. Visit a track's page and click Add To Favorites.",
			'account_my_tracks_help' => "This is a list of tracks you have uploaded. If you remove a track from the system, it will be removed from all people who have favorited it as well.",
			'account_favorite_help' => "This is a list of your favorite tracks. You can add to this list by visiting a new track and clicking Add To Favorites.",
			'account_upload_tracks_first' => "You haven't uploaded any tracks yet. Upload your first track and you'll see them here.",
			'account_profile' => "Profile For <em>%s</em>",
			'account_no_following' => "You are not following anyone yet.",
			'account_no_followers' => "No one is following you yet.",
			'account_track_uploading' => "Please give us a moment while your track uploads.",
			
			'page_contact_us' => "Contact Us",
			
			'profile_upload_tracks_first' => "This user hasn't uploaded any tracks yet.",
			'profile_no_followers' => "This user is not following anyone yet.",
			'profile_public' => "Public Profile",
			'profile_track_list' => "Tracks For %s",
			'profile_follow' => "Follow This User",
			'profile_return' => "Return To Profile",
			'profile_followers' => "%s Is Following",
			'profile_following' => "%s Has These Followers",
			
			'track_add_to_favorites' => "Add To Favorites",
			'track_view_count' => "%d Views",
			
			
			'processing_track' => "Processing Track",
			
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