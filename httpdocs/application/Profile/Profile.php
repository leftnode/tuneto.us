<?php

require_once 'application/Root/Root.php';

class Profile_Controller extends Root_Controller {
	public function indexGet($profile_id) {
		$this->viewGet($profile_id);
	}
	
	public function followGet($profile_id) {
		$this->verifyUserSession();
		
		try {
			$user = TuneToUs::getUser();
			$profile_id = intval($profile_id);
			
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
			
			TuneToUs::getMessenger()->pushSuccess(Language::__('success_profile_followed'));
			
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		}
		
		$this->redirect($this->url('profile/view', $profile_id));
		
		return true;
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
				if ( true === ttu_user_is_logged_in() && $profile_id != ttu_user_get_userid() ) {
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