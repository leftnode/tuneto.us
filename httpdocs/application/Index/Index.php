<?php

require_once 'application/Root/Root.php';

class Index_Controller extends Root_Controller {
	
	public function indexGet() {
		/* Get the last 5 tracks. */
		$this->track_iterator = TuneToUs::getDataModel()
			->where('status = ?', STATUS_ENABLED)
			->orderBy('date_create', 'DESC')
			->limit(5)
			->loadAll(new Track());
		
		$this->renderLayout('index');
	}
	
	public function browseGet() {
		/* Load 20 random tracks. */
		$track_list_model = new Track_Model(TuneToUs::getDataAdapter());
		$this->track_list = $track_list_model->where('status = ?', STATUS_ENABLED)
			->orderBy('RAND()', 'DESC')
			->limit(20)
			->loadAll(new Track());
		
		$this->renderLayout('browse');
	}
	
	public function pollGet() {
		$this->setLayout(NULL);
		
		
	}
}