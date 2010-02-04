<?php

require_once 'DataModeler/DataModel.php';

class User_Model extends DataModel {

	public function loadFirst(User $user) {
		$user = parent::loadFirst($user);
		
		if ( true === $user->exists() ) {
			$user = $this->loadTrackList($user);
			$user = $this->loadFollowingList($user);
			$user = $this->loadFollowerList($user);
			$user = $this->loadFavoriteList($user);
		}
		
		return $user;
	}
	
	private function loadTrackList(User $user) {
		$track_model = new Track_Model($this->getDataAdapter());
		$track_list = $track_model->where('user_id = ?', $user->id())
			->orderBy('date_create', 'DESC')
			->loadAll(new Track());
		$user->setTrackList($track_list);
		
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
	
	private function loadFavoriteList(User $user) {
		$track_favorite_model = new Track_Favorite_Model($this->getDataAdapter());
		$track_favorite_list = $track_favorite_model->fieldsFrom()
			->innerJoin(new Track_Favorite())
			->where('status = ?', STATUS_ENABLED)
			->where('track_favorite.user_id = ?', $user->id())
			->loadAll(new Track());
		
		$user->setFavoriteList($track_favorite_list);
		
		return $user;
	}
}