<?php

require_once 'ArtisanSystem/Exception.php';

require_once 'ArtisanSystem/Controller.php';
require_once 'ArtisanSystem/Db.php';
require_once 'ArtisanSystem/Registry.php';
require_once 'ArtisanSystem/Router.php';
require_once 'ArtisanSystem/Session.php';
require_once 'ArtisanSystem/Validator.php';
require_once 'ArtisanSystem/View.php';

require_once 'DataModeler/DataAdapterPdo.php';
require_once 'DataModeler/DataIterator.php';
require_once 'DataModeler/DataModel.php';
require_once 'DataModeler/DataObject.php';

require_once 'lib/Function/User.php';

function __autoload($class) {
	$class_path = str_replace('_', '.', $class) . '.php';
	require_once 'lib/Object' . DS . $class_path;
}

class API {
	private static $routerConfig = NULL;
	private static $dbConfig = NULL;
	private static $commandLine = false;
	
	public static function setRouterConfig(array $config) {
		self::$routerConfig = $config;
	}
	
	public static function setDbConfig(array $config) {
		self::$dbConfig = $config;
	}
	
	public static function init() {
		$db_hostname = self::$dbConfig['server'];
		$db_username = self::$dbConfig['username'];
		$db_password = self::$dbConfig['password'];
		$db_database = self::$dbConfig['database'];
		
		$dsn = "mysql:host={$db_hostname};port=3306;dbname={$db_database}";
		$pdo = new PDO($dsn, $db_username, $db_password);
	
		$sql = "SET character_set_results = 'utf8',
			character_set_client = 'utf8',
			character_set_connection = 'utf8',
			character_set_database = 'utf8',
			character_set_server = 'utf8'";
		$pdo->query($sql);
		Artisan_Registry::push('db', $pdo);

		$dataAdapter = new DataAdapterPdo($pdo);
		$dataModel = new DataModel($dataAdapter);

		Artisan_Registry::push('dataAdapter', $dataAdapter);
		Artisan_Registry::push('dataModel', $dataModel);
		
		self::$commandLine = ( 'cli' == php_sapi_name() ? true : false );

		if ( false === self::$commandLine ) {
			// Start session management
			Artisan_Session::get()->start(SESSION_NAME);
			
			// Create the token for POST methods to prevent CSRF attacks.
			self::createToken();
			
			// Build global user data. If the user is logged in, their data will be loaded,
			// otherwise, an empty user will be created.
			$user_id = ttu_user_get_userid();
			if ( $user_id > 0 && true === ttu_user_is_logged_in() ) {
				$user = $dataModel->where('user_id = ?', $user_id)->loadFirst(new User());
			} else {
				$user = new User();
			}
			
			Artisan_Registry::push('user', $user);
			
			// Validate POST requests.
			if ( POST == RM ) {
				$token = er('token', $_POST);
				if ( false === self::verifyToken($token) ) {
					exit('POST methods are not allowed without the correct token! Token given: ' . $token);
				}
			}
		}
	}
	
	public static function run() {
		$artisanRouter = new Artisan_Router(self::$routerConfig);
		echo $artisanRouter->dispatch();
	}
	
	public static function buildView() {
		$view = new Artisan_View(self::$routerConfig['root_dir']);
		$view->setIsRewrite(self::$routerConfig['rewrite'])
			->setSiteRoot(self::$routerConfig['site_root'])
			->setSiteRootSecure(self::$routerConfig['site_root_secure']);
		return $view;
	}
	
	public static function createToken() {
		if ( false === exs(SESSION_TOKEN, $_SESSION) ) {
			$token = mt_rand(1000000, mt_getrandmax());
			$salt = crypt_create_salt();
			$secret_token = crypt_compute_hash($token, $salt);
			
			$_SESSION[SESSION_TOKEN] = $token;
			$_SESSION[SESSION_TOKEN_SECRET] = $secret_token;
			$_SESSION[SESSION_TOKEN_SALT] = $salt;
		}
	}
	
	public static function verifyToken($token) {
		$salt = self::getTokenSalt();
		$secret_token = self::getSecretToken();
		$hashed_token = crypt_compute_hash($token, $salt);
		
		return ( $secret_token === $hashed_token );
	}
	
	public static function getDb() {
		return Artisan_Registry::pop('db');
	}
	
	public static function getDataModel() {
		return Artisan_Registry::pop('dataModel');
	}
	
	public static function getToken() {
		return $_SESSION[SESSION_TOKEN];
	}
	
	public static function getSecretToken() {
		return $_SESSION[SESSION_TOKEN_SECRET];
	}
	
	public static function getTokenSalt() {
		return $_SESSION[SESSION_TOKEN_SALT];
	}
	
	public static function getUser() {
		return Artisan_Registry::pop('user');
	}
}