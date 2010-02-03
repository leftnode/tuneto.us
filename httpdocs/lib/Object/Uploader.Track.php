<?php

require_once 'lib/Object/Uploader.php';

class Uploader_Track extends Uploader {
	
	public function setData($data) {
		$type = strtolower(er('type', $data));
		
		if ( 0 === preg_match('/audio|video/i', $type) ) {
			throw new TuneToUs_Exception(Language::__(''));
		}
		
		return parent::setData($data);
	}
	
	public function upload() {
		$this->createTrackName();
		return parent::upload();
	}
	
	private function createTrackName() {
		$filename = $this->getFilename();
		$filename = strtolower($filename);
		$filename = preg_replace('/[^a-z0-9_\-\.]/i', '-', $filename);
		$filename = preg_replace('/[\-]{1,}/i', '-', $filename);
		$this->setFilename($filename);
		return true;
	}
}