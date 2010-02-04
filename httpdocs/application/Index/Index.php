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
		$db = TuneToUs::getDb();
		
		$sql = "SELECT * FROM `track`
			WHERE (`status` = ?)
			ORDER BY RAND() DESC
			LIMIT 20";
		$statement_track_list = $db->prepare($sql);
		$statement_track_list->execute(array(STATUS_ENABLED));
		
		$track = new Track();
		$track_list_data = array();

		if ( $statement_track_list->rowCount() > 0 ) {
			$track_data = $statement_track_list->fetchAll(PDO::FETCH_ASSOC);
			
			foreach ( $track_data as $track_item ) {
				$track_list_data[] = clone $track->model($track_item);
			}
		}
		
		$track_list = new DataIterator($track_list_data);
		
		$this->track_list = $track_list;
		
		$this->renderLayout('browse');
	}
	
	public function listGet() {
		$this->setLayout(NULL);
		
		
	}
}