<?php

require_once 'application/Root/Root.php';

class Index_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->is_logged_in = User_Session::get()->isLoggedIn();
		
		$this->renderLayout('index');
	}
	
	public function indexPost() {
		$this->indexGet();
	}
}