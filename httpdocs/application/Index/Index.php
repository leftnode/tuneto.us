<?php

require_once 'application/Root/Root.php';

class Index_Controller extends Root_Controller {
	
	public function indexGet() {
		/* Get the last 5 tracks. */
		$this->track_iterator = TuneToUs::getDataModel()->where('status = ?', STATUS_ENABLED)
			->orderBy('date_create', 'DESC')
			->limit(5)
			->loadAll(new Track());
		
		$this->renderLayout('index');
	}
	
	public function listGet() {
		$this->setLayout(NULL);
		
		
	}
}