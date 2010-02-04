<?php

require_once 'DataModeler/DataObject.php';

class Track extends DataObject {
	private $favorite_list = NULL;
	
	public function setFavoriteList(DataIterator $list) {
		$this->favorite_list = $list;
		return $this;
	}
	
	public function getFavoriteList() {
		return $this->favorite_list;
	}


	
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
}