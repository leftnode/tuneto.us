<?php
$start = microtime(true);

require_once 'configure.php';
require_once 'lib/Object/TuneToUs.php';

/* Run the application. */
try {
	TuneToUs::setConfigDb($config_db);
	TuneToUs::setConfigRouter($config_router);
	TuneToUs::setConfigEmail($config_email);
	TuneToUs::init();
	TuneToUs::run();
} catch ( Exception $e ) {
	exit($e);
}

$end = microtime(true);
exit;