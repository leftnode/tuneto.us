<?php

require_once 'DataModeler/DataObject.php';

class User_Follow extends DataObject {
	public function __construct() {
		parent::__construct();
		$this->hasDate(false);
	}
}