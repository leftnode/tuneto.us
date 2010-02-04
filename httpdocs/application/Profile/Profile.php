<?php

require_once 'application/Root/Root.php';

class Profile_Controller extends Root_Controller {
	public function indexGet($profile_id) {
		$this->viewGet($profile_id);
	}
	
	public function followGet($profile_id) {
		$this->verifyUserSession();
		
		try {
			$profile = TuneToUs::getUser($profile_id);
			
			if ( false === $profile->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_account_follow_disabled'));
			}
			
			/* This user is following $profile from above. */
			$user = TuneToUs::getUser();
			
			$user_follow_model = new User_Follow_Model(TuneToUs::getDataAdapter());
			$user_following = $user_follow_model->where('follower_id = ?', $user->id())
				->where('following_id = ?', $profile_id)
				->loadFirst(new User_Follow());
			
			if ( true === $user_following->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_already_following'));
			}
			
			$user_follow = new User_Follow();
			$user_follow->setFollowerId($user->id())
				->setFollowingId($profile_id);
			$user_follow_model->save($user_follow);

			/* Send out the email if they want it. */
			$setting_email_new_follower = $profile->getSettingEmailNewFollower();
			if ( 1 == $setting_email_new_follower ) {
				$emailer = TuneToUs::getEmailer();
				$emailer->send('new-follower', $profile->getEmailAddress(), array(
					'nickname' => $profile->getNickname(),
					'site_url' => $user->getNickname(),
					'follower_nickname' => $user->getNickname(),
					'profile_url' => $this->url('profile/view', $user->id()),
					'from_name' => er('from_name', $emailer->getConfig())
					)
				);
			}
			
			TuneToUs::getMessenger()->pushSuccess(Language::__('success_profile_followed'));
			
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		}
		
		$this->redirect($this->url('profile/view', $profile_id));
		
		return true;
	}
	
	public function trackListGet($profile_id) {
		try {
			$profile = TuneToUs::getUser($profile_id);
			
			$this->track_list = $profile->getTrackList()
				->filter('status = ?', STATUS_ENABLED)
				->fetch();
			
			$this->profile = $profile;
			$this->renderLayout('track-list');
		} catch ( Exception $e ) { }
	}
	
	public function viewGet($profile_id) {
		try {
			$profile = TuneToUs::getUser($profile_id);
			
			if ( true === $profile->exists() ) {
				$this->track_list = $profile->getTrackList()
					->filter('status = ?', STATUS_ENABLED)
					->limit(10)
					->fetch();
				
				$can_follow = false;
				$setting_allow_followers = $profile->getSettingAllowFollowers();
				if ( 1 == $setting_allow_followers && true === ttu_user_is_logged_in() && $profile_id != ttu_user_get_userid() ) {
					/**
					 * Doing a direct query here rather than filtering on their following
					 * list because its cheaper, surprisingly.
					 */
					$user = TuneToUs::getUser();
					$user_follow_model = new User_Follow_Model(TuneToUs::getDataAdapter());
					$user_following = $user_follow_model->where('follower_id = ?', $user->id())
						->where('following_id = ?', $profile_id)
						->loadFirst(new User_Follow());
					$can_follow = !$user_following->exists();
				}
				
				$this->can_follow = $can_follow;
				$this->profile = $profile;
				
				$view = 'profile';
			} else {
				$view = 'profile-disabled';
			}
		} catch ( Exception $e ) { }
		
		$this->renderLayout($view);
	}
}