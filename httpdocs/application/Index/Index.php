<?php

require_once 'application/Root/Root.php';

class Index_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->is_logged_in = ttu_user_is_logged_in();
		
		/* Get the latest and popular tracks. */
		$this->latest_track_iterator = API::getDataModel()->where('status = ?', STATUS_ENABLED)
			->orderBy('date_create', 'DESC')
			->limit(10)
			->loadAll(new Track());
		
		$this->popular_track_iterator = API::getDataModel()->where('status = ?', STATUS_ENABLED)
			->orderBy('listen_count', 'DESC')
			->limit(10)
			->loadAll(new Track());
		
		if ( true === $this->is_logged_in ) {
			$this->upload_form = $this->render('track/upload-form');
		} else {
			$this->login_form = $this->render('account/login-form');
			$this->register_form = $this->render('account/register-form-small');
		}
		
		$this->renderLayout('index');
	}
	
	public function indexPost() {
		$this->indexGet();
	}
}