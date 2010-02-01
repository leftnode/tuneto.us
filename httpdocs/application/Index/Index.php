<?php

require_once 'application/Root/Root.php';

class Index_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->renderLayout('index');
	}
	
	public function listGet() {
		$this->setLayout(NULL);
		
		
	}
}