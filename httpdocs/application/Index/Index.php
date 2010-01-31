<?php

require_once 'application/Root/Root.php';

class Index_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->is_logged_in = ttu_user_is_logged_in();
		
		if ( true === $this->is_logged_in ) {
			$this->upload_form = $this->render('root/upload-form');
		}
		
		$this->renderLayout('index');
	}
	
	public function indexPost() {
		$this->indexGet();
	}
}