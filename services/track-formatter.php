<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'httpdocs');

require_once 'configure.php';
require_once 'lib/Object/TuneToUs.php';

try {
	TuneToUs::setConfigDb($config_db);
	TuneToUs::setConfigRouter($config_router);
	TuneToUs::setConfigEmail($config_email);
	TuneToUs::init();
	
	$track_iterator = TuneToUs::getDataModel()
		->where('status = ?', STATUS_ENABLED)
		->loadAll(new Track_Queue());
	
	$view = TuneToUs::buildView();
	
	foreach ( $track_iterator as $track_queue ) {
		$track_id = $track_queue->getTrackId();
		$track = TuneToUs::getDataModel()
			->where('track_id = ?', $track_id)
			->loadFirst(new Track());
		
		$user = TuneToUs::getDataModel()
			->where('user_id = ?', $track->getUserId())
			->loadFirst(new User());
		
		/* Get the length of the track */
		$track_length = 0;
		$track_directory = $track->getDirectory();
		$track_filename = $track->getFilename();
		$track_filepath = DIR_PRIVATE . $track_directory . DS . $track_filename;
		
		/* Initially disable the track so if stuff fails, it stays disabled. */
		$track->setStatus(STATUS_DISABLED);
		
		if ( true === is_file($track_filepath) ) {
			$length_match = array();
			$track_filepath_safe = escapeshellarg($track_filepath);
			
			/* If the track isn't an MP3, convert it to one. */
			if ( 0 === preg_match('/\.mp3$/i', $track_filename) ) {
				/* Get the raw track name without extension to convert to mp3. */
				$track_filename = preg_replace('/\.[a-z0-9]+$/i', NULL, $track_filename) . '.mp3';
			
				/* Now convert it to an mp3. */
				$track_filepath_new = DIR_PRIVATE . $track_directory . DS . $track_filename;
				$track_filepath_new_safe = escapeshellarg($track_filepath_new);

				shell_exec("ffmpeg -i {$track_filepath_safe} -vn -ar 44100 -ab 192 {$track_filepath_new_safe} 2>&1");
				$track_filepath_safe = $track_filepath_new_safe;
				
				$track->setFilename($track_filename);
			}
			
			$output = shell_exec("ffmpeg -i {$track_filepath_safe} 2>&1");
			preg_match('/Duration: (\d\d:\d\d:\d\d).(\d\d)/i', $output, $length_match);
			
			if ( count($length_match) > 0 ) {
				$length_bits = explode(':', $length_match[1]);
			
				if ( 3 === count($length_bits) ) {
					$hour = intval($length_bits[0]);
					$minute = intval($length_bits[1]);
					$second = intval($length_bits[2]);
			
					$track_length = ($hour * 60 * 60) + ($minute * 60) + $second;
			
					if ( $track_length > 0 ) {
						/* Determine the nice formatted text. */
						$length_formatted_bits = array();
						if ( $hour > 0 ) {
							$length_formatted_bits[] = $hour;
						}
						
						if ( $minute > 0 ) {
							$length_formatted_bits[] = $minute;
						}
						
						if ( $second > 0 ) {
							$length_formatted_bits[] = $second;
						}
						
						$length_formatted = implode(':', $length_formatted_bits);
						
						$track->setLength($track_length)
							->setLengthFormatted($length_formatted)
							->setStatus(STATUS_ENABLED);
						
						$setting_email_finished_processing = $user->getSettingEmailFinishedProcessing();
						if ( 1 == $setting_email_finished_processing ) {
							TuneToUs::getEmailer()->send('track-processed', $user->getEmailAddress(), array(
								'nickname' => $user->getNickname(),
								'track_url' => $view->url('track/play', $track->id()),
								'from_name' => $config_email['from_name']
								)
							);
						}
					}
				}
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
		
		TuneToUs::getDataModel()->save($track);
	}
} catch ( Exception $e ) {
	exit($e->getMessage());
}