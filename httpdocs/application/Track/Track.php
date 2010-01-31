<?php

require_once 'application/Root/Root.php';

class Track_Controller extends Root_Controller {
	
	public function playGet($track_id) {
		try {
			$track_id = intval($track_id);
			
			$track = API::getDataModel()->where('track_id = ?', $track_id)->where('status = ?', STATUS_ENABLED)->loadFirst(new Track());
			$id = $track->id();
			
			if ( $id != $track_id ) {
				throw new TuneToUs_Exception(_('Sorry, the track you were looking for can not be found.'));
			}
			
			$this->track = $track;
			
			$this->renderLayout('play');
		} catch ( TuneToUs_Exception $e ) {
			$this->pushErrorAndRedirect($e->getMessage(), 'index/index');
		} catch ( Exception $e ) {
			$this->pushErrorAndRedirect(_('An error occurred.'), 'index/index');
		}
	}
	
	public function trackGet($track_id) {
		$this->setLayout(NULL);
		
	}
	
	public function uploadGet() {
		$this->verifyUserAuthentication();
	
		$this->renderLayout('account/index');
		
		return true;
	}
	
	public function uploadPost() {
		$this->verifyUserAuthentication();
		
		try {
			$user = API::getUser();
			$content_directory = $user->getContentDirectory();
			
			$upload = (array)$this->getParam('upload');
			$track_data = (array)$this->getFilesParam('track');
			
			$validator = $this->buildValidator();
			$validator->load('upload')->setData($upload)->validate();

			$uploader = new Uploader_Track($track_data);
			$uploader->setAllowOverwrite(true)
				->setDestinationDirectory($content_directory)
				->upload();
			
			/* If the upload went well, create a new Track record and Track_Queue record. */
			$track = new Track();
			$track->setUserId($user->id())
				->setPath($content_directory)
				->setFilename($uploader->getFilename())
				->setName(er('name', $upload))
				->setDescription(er('description', $upload))
				->setLength(0)
				->setViewCount(0)
				->setListenCount(0)
				->setStatus(STATUS_DISABLED);
			$track_id = API::getDataModel()->save($track);
			
			if ( $track_id < 1 ) {
				throw new TuneToUs_Exception(_('An error occurred when uploading your track. Please try again.'));
			}
			
			$track_queue = new Track_Queue();
			$track_queue->setTrackId($track_id)
				->setOutput('')
				->setStatus(STATUS_ENABLED);
			$track_queue_id = API::getDataModel()->save($track_queue);
			
			if ( $track_queue_id < 1 ) {
				throw new TuneToUs_Exception(_('An error occurred when queueing your track. Please try again.'));
			}
			
			$this->pushSuccessAndRedirect(_('Your track was successfully uploaded. Please give us a moment while we convert it to the proper format.'), 'account/index');
		} catch ( TuneToUs_Exception $e ) {
			$this->pushErrorAndRedirect($e->getMessage(), 'account/index');
		} catch ( Exception $e ) {
			$this->pushErrorAndRedirect(ERROR_GENERAL, 'index/index');
		}
		
		return true;
	}
}