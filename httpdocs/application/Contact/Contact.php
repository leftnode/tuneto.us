<?php

require_once 'application/Root/Root.php';

class Contact_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->renderLayout('index');
	}
}
