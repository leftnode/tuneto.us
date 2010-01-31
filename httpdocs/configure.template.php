<?php

define('DEBUG_MODE', 1, false);
if ( 1 == DEBUG_MODE ) {
	error_reporting(E_ALL | E_STRICT);
} else {
	error_reporting(0);
}

require_once 'lib/Define.php';
require_once 'lib/Language.php';

if ( true === isset($_SERVER['REQUEST_METHOD']) ) {
	define('RM', strtoupper($_SERVER['REQUEST_METHOD']), false);
	define('POST', 'POST', false);
	define('GET', 'GET', false);
}

define('DS', DIRECTORY_SEPARATOR, false);

define('DIR_ROOT', '##ROOT_DIR##' . DS, false);
define('DIR_SITE_ROOT', DIR_ROOT . 'httpdocs' . DS, false);

define('DIR_LIB', 'lib' . DS, false);
define('DIR_LIB_MODULE', DIR_LIB . 'Module' . DS, false);
define('DIR_LIB_OBJECT', DIR_LIB . 'Object' . DS, false);

define('DIR_PRIVATE', DIR_ROOT . 'private' . DS, false);
define('DIR_PUBLIC', 'public' . DS, false);
define('DIR_CSS', DS . DIR_PUBLIC . 'css' . DS, false);
define('DIR_IMAGE', DS . DIR_PUBLIC . 'image' . DS, false);
define('DIR_JAVASCRIPT', DS . DIR_PUBLIC . 'javascript' . DS, false);
define('DIR_LAYOUT', DIR_PUBLIC . 'layout' . DS, false);
define('DIR_LOCALE', DIR_PUBLIC . 'locale' . DS, false);

define('COOKIE_DOMAIN', '##COOKIE_DOMAIN##', false);

$config_db = array(
	'server' => '##DBSERVER##',
	'username' => '##DBUSERNAME##',
	'password' => '##DBPASSWORD##',
	'database' => '##DBNAME##',
	'debug' => true
);

$config_router = array(
	'site_root' => '##SITE_ROOT##',
	'site_root_secure' => '##SITE_ROOT_SECURE##',
	'root_dir' => 'application',
	'layout_dir' => DIR_LAYOUT,
	'default_controller' => 'Index',
	'default_method' => 'index',
	'default_layout' => 'index',
	'rewrite' => true
);