<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'httpdocs');

require_once 'configure.php';
require_once 'lib/Object/TuneToUs.php';

try {
	TuneToUs::setDbConfig($config_db);
	TuneToUs::init();
	
	$track_iterator = TuneToUs::getDataModel()
		->where('status = ?', STATUS_ENABLED)
		->loadAll(new Track_Queue());
	
	foreach ( $track_iterator as $track_queue ) {
		$track_id = $track_queue->getTrackId();
		$track = TuneToUs::getDataModel()
			->where('track_id = ?', $track_id)
			->loadFirst(new Track());
		
		/* Get the length of the track */
		$track_length = 0;
		$track_file_path = DIR_PRIVATE . $track->getDirectory() . DS . $track->getFilename();
		if ( true === is_file($track_file_path) ) {
			$track_file_path_safe = escapeshellarg($track_file_path);
			
			$output = system("ffmpeg -i {$track_file_path_safe}");
			$length = system("ffmpeg -i {$track_file_path_safe} 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/,//");
			
			$length_bits = explode(':', $length);
			
			$hour = intval($length_bits[0]);
			$minute = intval($length_bits[1]);
			$second = intval($length_bits[2]);
			
			$track_length = ($hour * 60 * 60) + ($minute * 60) + $second;
			
			if ( $track_length > 0 ) {
				$track->setLength($track_length)
					->setStatus(STATUS_ENABLED);
				TuneToUs::getDataModel()->save($track);
			}
			
			$track_queue->setOutput($output)
				->setStatus(STATUS_DISABLED);
			TuneToUs::getDataModel()->save($track_queue);
		} else {
			$output = $track_file_path . ' is not a valid file.';
			$track_queue->setOutput($output)
				->setStatus(STATUS_DISABLED);
			TuneToUs::getDataModel()->save($track_queue);
		}
	}
} catch ( Exception $e ) {
	exit($e->getMessage());
}
