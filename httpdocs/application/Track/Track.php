<?php

require_once 'application/Root/Root.php';

class Track_Controller extends Root_Controller {
	
	/**
	 * View the page to play a track.
	 * 
	 * @param integer $track_id The ID of the track to view.
	 * @retval bool Returns true.
	 */
	public function playGet($track_id) {
		try {
			$track_id = intval($track_id);
			
			$track = TuneToUs::getDataModel()
				->where('track_id = ?', $track_id)
				->where('status = ?', STATUS_ENABLED)
				->loadFirst(new Track());

			if ( true === $track->exists() ) {
				$track->updateViewCount();
				TuneToUs::getDataModel()->save($track);
				
				$profile = TuneToUs::getDataModel()
					->where('user_id = ?', $track->getUserId())
					->where('status = ?', STATUS_ENABLED)
					->loadFirst(new User());
				
				$this->profile = $profile;
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