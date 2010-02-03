<?php

require_once 'DataModeler/DataObject.php';

class Track extends DataObject {
	public function canPlay() {
		$status = $this->getStatus();
		
		if ( STATUS_ENABLED == $status ) {
			return true;
		}
		
		return false;
	}
	
	public function updateViewCount() {
		$view_count = $this->getViewCount();
		$this->setViewCount(++$view_count);
		return $this;
	}
	
	public function getTrackImage() {
		return DIR_IMAGE . 'trackImage.png';
	}
}