<?php

class User_Session {
	private static $instance = NULL;
	private $user = array();

	const SESSION_USER = "_user";
	
	private function __construct() { }
	
	private function __clone() { }
	
	public static function get() {
		if ( true === is_null(self::$instance) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function setLogin($user_id) {
		$user_id = intval($user_id);
		if ( $user_id > 0 ) {
			Artisan_Session::get()->add(self::SESSION_USER, array(
				'user_id' => $user_id,
				'user_agent' => sha1(input_get_user_agent()),
				'status' => 1
				)
			);
		}
		return true;
	}

	public function destroyLogin() {
		Artisan_Session::get()->remove(self::SESSION_USER);
		return true;
	}
	
	public function load() {
		$this->user = (array)Artisan_Session::get()->key(self::SESSION_USER);
		
		if ( count($this->user) > 0 ) {
			$user_agent_hashed_session = $this->user['user_agent'];
			$user_agent_hashed = sha1(input_get_user_agent());
			
			if ( $user_agent_hashed_session != $user_agent_hashed ) {
				$this->destroyLogin();
			}
		}
		return true;
	}
	
	public function isLoggedIn() {
		if ( count($this->user) > 0 ) {
			$user_id = er('user_id', $this->user, 0);
			$status = er('status', $this->user, 0);
			
			if ( 1 === $status && $user_id > 0 ) {
				return true;
			}
		}
		return false;
	}
	
	public function getUserId() {
		$user_id = er('user_id', $this->user, 0);
		return $user_id;
	}
}