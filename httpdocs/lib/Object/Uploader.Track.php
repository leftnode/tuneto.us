<?php

class Uploader_Track extends Uploader {
	
	public function setUploadData($uploadData) {
		$type = strtolower(er('type', $uploadData));
		if ( 'audio/mpeg' !== $type ) {
			$error = self::ERROR_UPLOAD_INCORRECT_TYPE;
		}
		
		parent::setUploadData($uploadData);
	}
	
	public function upload() {
		$this->createTrackName();
		return parent::upload();
	}
	
	private function createTrackName() {
		// Make everything all nice and pretty like
		$track_name = $this->fileName;
		$track_name = strtolower($track_name);
		$track_name = preg_replace('/[^a-z0-9_\-\.]/i', '-', $track_name);
		$this->fileName = $track_name;
		
		return true;
	}
}