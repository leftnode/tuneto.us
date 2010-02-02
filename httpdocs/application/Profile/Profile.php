<?php

require_once 'application/Root/Root.php';

class Profile_Controller extends Root_Controller {
	
	
	public function viewGet($profile_id) {
		
		$profile = TuneToUs::getDataModel()
			->where('user_id = ?', $profile_id)
			->where('status = ?', STATUS_ENABLED)
			->loadFirst(new User());
		
		if ( true === $profile->exists() ) {
			$user = TuneToUs::getUser();
			$this->track_iterator = TuneToUs::getDataModel()
				->where('user_id = ?', $profile->id())
				->where('status <> ?', STATUS_DISABLED)
				->limit(10)
				->loadAll(new Track());
			
			$user_is_logged_in = ttu_user_is_logged_in();
			
			$this->user_is_logged_in = $user_is_logged_in;
			$this->user = $user;
			$this->profile = $profile;
			
			$this->can_follow = false;
			if ( true === $user_is_logged_in && $user->id() != $profile->id() ) {
				$user_follow = TuneToUs::getDataModel()
					->where('follower_id = ?', $user->id())
					->where('following_id = ?', $profile->id())
					->loadFirst(new User_Follow());
					
				if ( false === $user_follow->exists() ) {
					$this->can_follow = true;
				}
			}
			
			$view = 'profile';
		} else {
			$view = 'profile-disabled';
		}
		
		parent::renderLayout($view);
	}
	
	
}