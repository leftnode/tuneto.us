<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'httpdocs');

require_once 'configure.php';
require_once 'lib/Object/TuneToUs.php';

require_once 'ArtisanSystem/Db.php';
require_once 'ArtisanSystem/Db.Builder.php';

TuneToUs::setConfigDb($config_db);
TuneToUs::init(true, false);

$db = new Artisan_Db($config_db);
$db->connect();

$dbBuilder = new Artisan_Db_Builder($db, DIR_ROOT . 'sql');
$dbBuilder->run();

$db->disconnect();

exit(0);