<?php

require_once 'application/Root/Root.php';

class Track_Controller extends Root_Controller {
	
	public function playGet($track_id) {
		
		
	}
	
	public function uploadGet() {
		$this->verifyUserAuthentication();
	
		$this->renderLayout('account/index');
		
		return true;
	}
	
	public function uploadPost() {
		$this->verifyUserAuthentication();
		try {	
			$upload = (array)$this->getParam('upload');
			$track_data = (array)$this->getFilesParam('track');
			
			$validator = $this->buildValidator();
			$validator->load('upload')->setData($upload)->validate();

			$uploader = new Uploader_Track($track_data);
			$uploader->setAllowOverwrite(true)
				->setDestinationDirectory('')
				->upload();
			
			// If the upload went well, create a new Track record
			$track = new Track();
			$track->setUserId(ttu_user_get_userid())
				->setPath($uploader->getFilename())
				->setFilename($uploader->getFilename())
				->setName(er('name', $upload))
				->setDescription(er('description', $upload))
				->setViewCount(0)
				->setListenCount(0)
				->setStatus(STATUS_ENABLED);
			
			API::getDataModel()->save($track);
			
			$this->pushSuccessAndRedirect(SUCCESS_TRACK_UPLOADED, 'account/index');
		} catch ( TuneToUs_Exception $e ) {
			$this->pushErrorAndRedirect($e->getMessage(), 'index/index');
		} catch ( Exception $e ) {
			$this->pushErrorAndRedirect(ERROR_GENERAL, 'index/index');
		}
		
		return true;
	}
}