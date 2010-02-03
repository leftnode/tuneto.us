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
		$data_model = new DataModel($this->getDataAdapter());
		$following_list = $data_model->where('follower_id = ?', $user->id())
			->loadAll(new User_Follow());
		
		$user->setFollowingList($following_list);
		return $user;
	}
	
	private function loadFollowerList(User $user) {
		$data_model = new DataModel($this->getDataAdapter());
		$follower_list = $data_model->where('following_id = ?', $user->id())
			->loadAll(new User_Follow());
		
		$user->setFollowerList($follower_list);
		return $user;
	}
}