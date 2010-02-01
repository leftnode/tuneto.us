<?php
$start = microtime(true);

require_once 'configure.php';
require_once 'lib/Object/TTU.php';

try {
	TTU::setDbConfig($config_db);
	TTU::setRouterConfig($config_router);
	TTU::init();
	TTU::run();
} catch ( Exception $e ) {
	exit($e);
}

$end = microtime(true);
exit;