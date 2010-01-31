<?php
$start = microtime(true);

require_once 'configure.php';
require_once 'lib/Object/API.php';

try {
	API::setDbConfig($config_db);
	API::setRouterConfig($config_router);
	API::init();
	API::run();
} catch ( Exception $e ) {
	exit($e);
}

$end = microtime(true);
exit;