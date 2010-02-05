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
	
	
	
	
	
	
	
	
	// people this user is following
	private function loadFollowingList(User $user) {
		$user_following_model = new User_Follow_Model($this->getDataAdapter());
		
		$following_list = $user_following_model->fieldsFrom()
			->innerJoin(new User_Follow(), 'following_id')
			->where('follower_id = ?', $user->id())
			->loadAll(new User());
		$user->setFollowingList($following_list);
		
		return $user;
	}
	
	// People who are following this user
	private function loadFollowerList(User $user) {
		$user_following_model = new User_Follow_Model($this->getDataAdapter());
		
		$follower_list = $user_following_model->fieldsFrom()
			->innerJoin(new User_Follow(), 'follower_id')
			->where('following_id = ?', $user->id())
			->loadAll(new User());
		$user->setFollowerList($follower_list);
		
		return $user;
	}
	
	
	
	
	
	
	
	
	
	private function loadFavoriteList(User $user) {
		$track_favorite_model = new Track_Favorite_Model($this->getDataAdapter());
		$track_favorite_list = $track_favorite_model->fieldsFrom()
			->innerJoin(new Track_Favorite(), 'track_id')
			->where('status = ?', STATUS_ENABLED)
			->where('track_favorite.user_id = ?', $user->id())
			->loadAll(new Track());
		
		$user->setFavoriteList($track_favorite_list);
		
		return $user;
	}
}