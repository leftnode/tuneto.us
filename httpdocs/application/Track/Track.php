<?php

require_once 'application/Root/Root.php';

class Track_Controller extends Root_Controller {
	
	public function addToFavoritesGet($track_id) {
		$this->verifyUserSession();
		
		try {
			$user = TuneToUs::getUser();
			$track_id = intval($track_id);
			
			$track_favorite_model = new Track_Favorite_Model(TuneToUs::getDataAdapter());
			$track_favorite = $track_favorite_model->where('track_id = ?', $track_id)
				->where('user_id = ?', $user->id())
				->loadFirst(new Track_Favorite());
			
			if ( true === $track_favorite->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_track_already_favorited'));
			}
			
			$track_favorite = new Track_Favorite();
			$track_favorite->setTrackId($track_id)
				->setUserId($user->id());
			
			$track_favorite_model->save($track_favorite);
			
			TuneToUs::getMessenger()->pushSuccess(Language::__('success_track_favorited'));
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		}
		
		$this->redirect($this->url('track/play', $track_id));
		
		return true;
	}
	
	/**
	 * View the page to play a track.
	 * 
	 * @param integer $track_id The ID of the track to view.
	 * @retval bool Returns true.
	 */
	public function playGet($track_id) {
		try {
			$track_id = intval($track_id);
			
			$track_model = new Track_Model(TuneToUs::getDataAdapter());
			$track = $track_model->where('track_id = ?', $track_id)
				->where('status = ?', STATUS_ENABLED)
				->loadFirst(new Track());	

			if ( true === $track->exists() ) {
				/* Register the view for this track. */
				TuneToUs::getDataModel()->save($track->updateViewCount());
				
				/* Get the track owner to display some more information. */
				$this->owner = TuneToUs::getUser($track->getUserId());
				
				/* Determine if the user viewing this track can favorite it. */
				$can_favorite = false;
				
				$user = TuneToUs::getUser();
				if ( true === ttu_user_is_logged_in() ) {
					/**
					 * Doing a direct query here rather than filtering on their
					 * favorite list because its cheaper, surprisingly.
					 */
					$track_favorite_model = new Track_Favorite_Model(TuneToUs::getDataAdapter());
					$track_favorite = $track_favorite_model->where('track_id = ?', $track_id)
						->where('user_id = ?', $user->id())
						->loadFirst(new Track_Favorite());
					$can_favorite = !$track_favorite->exists();
				}
				
				$this->can_favorite = $can_favorite;
				$this->track = $track;
				
				$view = 'play';
			} else {
				$view = 'play-not-exist';
			}
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) { }
		
		$this->renderLayout($view);
		
		return true;
	}
	
	/**
	 * Stream a track through HTTP.
	 * 
	 * @param integer $track_id The ID of the track to stream.
	 * @retval bool Returns true.
	 */
	public function streamGet($track_id) {
		$this->setLayout(NULL);
		
		try {
			ob_start();

			header('Content-Type: audio/mpeg');
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

			$track_id = intval($track_id);
			$track = TuneToUs::getDataModel()
				->where('track_id = ?', $track_id)
				->where('status = ?', STATUS_ENABLED)
				->loadFirst(new Track());

			$file_path = DIR_PRIVATE . $track->getDirectory() . DS . $track->getFilename();

			if ( true === is_file($file_path) ) {
				$fh = fopen($file_path, 'rb');
				fseek($fh, 0);
				while ( false === feof($fh) ) {
					echo fread($fh, 1024);
					ob_flush();
				}
				
				fclose($fh);
			}
		} catch ( Exception $e ) { }
		
		return true;
	}
}