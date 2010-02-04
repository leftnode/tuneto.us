<?php

require_once 'DataModeler/DataModel.php';

class User_Model extends DataModel {

	public function loadFirst(User $user) {
		$user = parent::loadFirst($user);
		
		if ( true === $user->exists() ) {
			$user = $this->loadFollowingList($user);
			$user = $this->loadFollowerList($user);
		}
		
		return $user;
	}
	
	private function loadFollowingList(User $user) {
		$user_following_model = new User_Follow_Model($this->getDataAdapter());
		$following_list = $user_following_model->where('follower_id = ?', $user->id())
			->loadAll(new User_Follow());
		
		$user->setFollowingList($following_list);
		return $user;
	}
	
	private function loadFollowerList(User $user) {
		$user_follower_model = new User_Follow_Model($this->getDataAdapter());
		$follower_list = $user_follower_model->where('following_id = ?', $user->id())
			->loadAll(new User_Follow());
		
		$user->setFollowerList($follower_list);
		return $user;
	}
}