<?php

function ttu_user_is_logged_in() {
	$user = er(SESSION_USER, $_SESSION, array());
	
	if ( count($user) > 0 ) {
		$user_id = er('user_id', $user, 0);
		$status = er('status', $user, 0);

		if ( 1 === $status && $user_id > 0 ) {
			return true;
		}
	}
	
	return false;
}

function ttu_user_login($user_id) {
	$user_id = intval($user_id);
	if ( $user_id > 0 ) {
		$_SESSION[SESSION_USER] = array(
			'user_id' => $user_id,
			'user_agent' => sha1(input_get_user_agent()),
			'status' => 1
		);
	}
	return true;
}

function ttu_user_logout() {
	unset($_SESSION[SESSION_USER]);
}

function ttu_user_get_userid() {
	$user = er(SESSION_USER, $_SESSION, array());
	$user_id = er('user_id', $user, 0);
	return $user_id;
}

function ttu_user_get_gravatar($email_address) {
	$gravatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5(strtolower($email_address));
	return $gravatar_url;
}