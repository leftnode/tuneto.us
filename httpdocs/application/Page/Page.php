<?php

require_once 'application/Root/Root.php';

class Page_Controller extends Root_Controller {
	
	public function viewGet($page) {
		$this->renderLayout('index');
	}
}
