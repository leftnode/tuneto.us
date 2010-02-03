<?php

require_once 'ArtisanSystem/Exception.php';

require_once 'ArtisanSystem/Controller.php';
require_once 'ArtisanSystem/Db.php';
require_once 'ArtisanSystem/Registry.php';
require_once 'ArtisanSystem/Router.php';
require_once 'ArtisanSystem/Session.php';
require_once 'ArtisanSystem/Template.php';
require_once 'ArtisanSystem/Validator.php';
require_once 'ArtisanSystem/View.php';

require_once 'DataModeler/DataAdapterPdo.php';
require_once 'DataModeler/DataIterator.php';
require_once 'DataModeler/DataModel.php';
require_once 'DataModeler/DataObject.php';

require_once 'lib/Function/General.php';
require_once 'lib/Function/User.php';
require_once 'lib/Function/Image.php';

function __autoload($class) {
	$class_path = str_replace('_', '.', $class) . '.php';
	require_once 'lib/Object' . DS . $class_path;
}

class TuneToUs {
	private static $config_router = array();
	private static $config_db = array();
	private static $config_email = array();
	private static $is_cli = false;
	
	public static function setConfigRouter(array $config) {
		self::$config_router = $config;
	}
	
	public static function setConfigDb(array $config) {
		self::$config_db = $config;
	}
	
	public static function setConfigEmail(array $config) {
		self::$config_email = $config;
	}
	
	public static function init() {
		$db_hostname = self::$config_db['server'];
		$db_username = self::$config_db['username'];
		$db_password = self::$config_db['password'];
		$db_database = self::$config_db['database'];
		
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
		
		self::$is_cli = ( 'cli' === php_sapi_name() ? true : false );

		if ( false === self::$is_cli ) {
			/* Start session management */
			Artisan_Session::get()->start(SESSION_NAME);
			
			/* Create the token for POST methods to prevent CSRF attacks. */
			self::createToken();
			
			/**
			 * Build global user data. If the user is logged in, their data will be loaded,
			 * otherwise, an empty user will be created.
			 */
			$user_id = ttu_user_get_userid();
			if ( $user_id > 0 && true === ttu_user_is_logged_in() ) {
				$user_model = new User_Model($dataAdapter);
				$user = $user_model->where('user_id = ?', $user_id)->loadFirst(new User());
			} else {
				$user = new User();
			}
			
			Artisan_Registry::push('user', $user);
			
			/* Validate POST requests. */
			if ( POST == RM ) {
				$token = er('token', $_POST);
				if ( false === self::verifyToken($token) ) {
					exit('POST methods are not allowed without the correct token! Token given: ' . $token);
				}
			}
		}
		
		setlocale(LC_ALL, 'en_US.utf8');
		//bindtextdomain('lang', DIR_LOCALE);
		//textdomain('lang');
	}
	
	public static function run() {
		$artisanRouter = new Artisan_Router(self::$config_router);
		echo $artisanRouter->dispatch();
	}
	
	public static function buildView() {
		$view = new Artisan_View(self::$config_router['root_dir']);
		$view->setIsRewrite(self::$config_router['rewrite'])
			->setSiteRoot(self::$config_router['site_root'])
			->setSiteRootSecure(self::$config_router['site_root_secure']);
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
	
	public static function getDataAdapter() {
		return Artisan_Registry::pop('dataAdapter');
	}
	
	public static function getDataModel() {
		return Artisan_Registry::pop('dataModel');
	}
	
	public static function getDb() {
		return Artisan_Registry::pop('db');
	}
	
	public static function getEmailer() {
		return new Emailer(self::$config_email);
	}
	
	public static function getMessenger() {
		return Messenger::get();
	}
	
	public static function getPaginator() {
		return new Paginator();
	}
	
	public static function getSecretToken() {
		return $_SESSION[SESSION_TOKEN_SECRET];
	}
	
	public static function getToken() {
		return $_SESSION[SESSION_TOKEN];
	}
	
	public static function getTokenSalt() {
		return $_SESSION[SESSION_TOKEN_SALT];
	}
	
	public static function getUser() {
		return Artisan_Registry::pop('user');
	}
}
