<?php

require_once 'configure.php';
require_once 'lib/Object/API.php';

try {
	API::setDbConfig($config_db);
	API::init();
	
	ob_start();

	header('Content-Type: audio/mpeg');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

	$track_id = intval($_GET['track_id']);

	$track = API::getDataModel()->where('track_id = ?', $track_id)
		->where('status = ?', STATUS_ENABLED)
		->loadFirst(new Track());

	$file_path = DIR_PRIVATE . $track->getPath() . DS . $track->getFilename();

	if ( true === is_file($file_path) ) {
		$fh = fopen($file_path, 'rb');
		fseek($fh, 0);
		while ( false === feof($fh) ) {
			echo fread($fh, 1024);
			ob_flush();
		}
		
		fclose($fh);
	}
} catch ( Exception $e ) {
	exit('Can not stream content.');
}

exit;