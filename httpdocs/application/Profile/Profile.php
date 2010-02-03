<?php

require_once 'application/Root/Root.php';

class Profile_Controller extends Root_Controller {
	
	
	public function viewGet($profile_id) {
		
		$profile = TuneToUs::getUser($profile_id);
		
		if ( true === $profile->exists() ) {
			$this->track_iterator = TuneToUs::getDataModel()
				->where('user_id = ?', $profile->id())
				->where('status = ?', STATUS_ENABLED)
				->orderBy('date_create', 'DESC')
				->limit(10)
				->loadAll(new Track());
				
			/*$user = TuneToUs::getUser();
			
			
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
			}*/
			
			$this->profile = $profile;
			
			$view = 'profile';
		} else {
			$view = 'profile-disabled';
		}
		
		parent::renderLayout($view);
	}
	
	
}