<?php

require_once 'DataModeler/DataModel.php';

class Track_Model extends DataModel {

	public function loadFirst(Track $track) {
		$track = parent::loadFirst($track);
		
		if ( true === $track->exists() ) {
			$track = $this->loadFavoriteList($track);
		}
		
		return $track;
	}
	
	private function loadFavoriteList(Track $track) {
		$track_favorite_model = new Track_Favorite_Model($this->getDataAdapter());
		$favorite_list = $track_favorite_model->where('track_id = ?', $track->id())
			->loadAll(new Track_Favorite());
		
		$track->setFavoriteList($favorite_list);
		
		return $track;
	}
}