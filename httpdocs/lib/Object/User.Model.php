<?php

require_once 'DataModeler/DataModel.php';

class User_Model extends DataModel {

	public function loadFirst(User $user) {
		$user = parent::loadFirst($user);
		
		if ( true === $user->exists() ) {
			$user = $this->loadFollowingList($user);
			$user = $this->loadFollowerList($user);
			$user = $this->loadFavoriteList($user);
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
	
	private function loadFavoriteList(User $user) {
		$sql = "SELECT t.* FROM `track_favorite` tf
			INNER JOIN `track` t
				ON tf.track_id = t.track_id
			WHERE t.status = ?";
		$statement_track_favorite = $this->getDataAdapter()
			->getConnection()
			->prepare($sql);
		$statement_track_favorite->execute(array(STATUS_ENABLED));
		
		$track = new Track();
		$track_favorite_list_data = array();

		if ( $statement_track_favorite->rowCount() > 0 ) {
			$track_favorite_data = $statement_track_favorite->fetchAll(PDO::FETCH_ASSOC);
			
			foreach ( $track_favorite_data as $track_favorite_item ) {
				$track_favorite_list_data[] = clone $track->model($track_favorite_item);
			}
		}
		
		$track_favorite_list = new DataIterator($track_favorite_list_data);
		$user->setFavoriteList($track_favorite_list);
		
		return $user;
	}
}