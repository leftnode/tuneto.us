<?php

require_once 'DataModeler/DataObject.php';

class Track_Favorite extends DataObject {
	public function __construct() {
		parent::__construct();
		$this->hasDate(false);
	}
}