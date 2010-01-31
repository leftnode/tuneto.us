<?php

class User_Session {
	private static $user = array();

	const SESSION_USER = "_user";
	
	public static function logIn($user_id) {
		self::load();
		
		$user_id = intval($user_id);
		if ( $user_id > 0 ) {
			$_SESSION[self::SESSION_USER] = array(
				'user_id' => $user_id,
				'user_agent' => sha1(input_get_user_agent()),
				'status' => 1
			);
		}
		return true;
	}

	public static function logOut() {
		unset($_SESSION[self::SESSION_USER]);
		return true;
	}
	
	public static function load() {
		self::$user = er(self::SESSION_USER, $_SESSION, array());
		
		if ( count(self::$user) > 0 ) {
			$user_agent_hashed_session = self::$user['user_agent'];
			$user_agent_hashed = sha1(input_get_user_agent());
			
			if ( $user_agent_hashed_session != $user_agent_hashed ) {
				self::destroyLogin();
			}
		}
		return true;
	}
	
	public static function isLoggedIn() {
		self::load();
		
		if ( count(self::$user) > 0 ) {
			$user_id = er('user_id', self::$user, 0);
			$status = er('status', self::$user, 0);
			
			if ( 1 === $status && $user_id > 0 ) {
				return true;
			}
		}
		return false;
	}
	
	public static function getUserId() {
		self::load();
		
		$user_id = er('user_id', self::$user, 0);
		return $user_id;
	}
}