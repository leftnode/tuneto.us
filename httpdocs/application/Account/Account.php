<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->renderLayout('index');
	}
	
	public function registerGet() {
		$this->renderLayout('register');
	}
}