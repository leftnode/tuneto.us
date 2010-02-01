<?php
$start = microtime(true);

require_once 'configure.php';
require_once 'lib/Object/TuneToUs.php';

/* Run the application. */
try {
	TuneToUs::setDbConfig($config_db);
	TuneToUs::setRouterConfig($config_router);
	TuneToUs::init();
	TuneToUs::run();
} catch ( Exception $e ) {
	exit($e);
}

$end = microtime(true);
exit;