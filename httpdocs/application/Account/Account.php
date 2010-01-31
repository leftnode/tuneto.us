<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		if ( false === User_Session::get()->isLoggedIn() ) {
			// Redirect to index, give error.
		}
		
		$this->renderLayout('index');
	}
	
	public function logoutGet() {
		try {
			User_Session::get()->destroyLogin();
		} catch ( Exception $e ) { }
		
		$this->getMessage()->pushSuccess(SUCCESS_LOGGED_OUT);
		$this->redirect($this->url('index/index'));
	}
	
	public function registerGet() {
		if ( true === User_Session::get()->isLoggedIn() ) {
			// Redirect to index, give error
		}
		
		$this->renderLayout('register');
	}
	
	public function loginPost() {
		try {
			$login = (array)$this->getParam('login');
			
			$email_address = er('email_address', $login);
			$password = er('password', $login);
			
			$user = API::getDataModel()->where('email_address = ?', $email_address)
				->where('status = ?', STATUS_ENABLED)
				->limit(1)
				->loadFirst(new User());
			
			$password_salt = $user->getPasswordSalt();
			$password_user = $user->getPassword();
			$password_hashed = crypt_compute_hash($password, $password_salt);
			
			if ( $password_user !== $password_hashed ) {
				throw new TuneToUs_Exception(ERROR_ACCOUNT_NOT_FOUND);
			}
			
			$user_id = $user->id();
			User_Session::get()->setLogin($user_id);
			
			$this->pushSuccessAndRedirect(SUCCESS_LOGGED_IN, 'account/index');
		} catch ( TuneToUs_Exception $e ) {
			$this->pushErrorAndRedirect($e->getMessage(), 'index/index');
		} catch ( Exception $e ) {
			$this->pushErrorAndRedirect(ERROR_GENERAL, 'index/index');
		}
		
		return true;
	}
	
	public function registerPost() {
		try {
			$register = (array)$this->getParam('register');
			
			if ( false === is_array($register) || 0 === count($register) ) {
				throw new TuneToUs_Exception('No registration information was available. Please try again.');
			}
			
			/* Attempt to validate the data. */
			$email_address = er('email_address', $register);
			$password = er('password', $register);
			
			$user = API::getDataModel()->where('email_address = ?', $email_address)->loadFirst(new User());
			$user_id = $user->id();
			if ( $user_id > 0 ) {
				throw new TuneToUs_Exception(ERROR_ACCOUNT_EXISTS);
			}
			
			$password_salt = crypt_create_salt();
			$password_hashed = crypt_compute_hash($password, $password_salt);
			
			$user = new User();
			$user->setEmailAddress($email_address)
				->setPassword($password_hashed)
				->setPasswordSalt($password_salt)
				->setFirstname(er('firstname', $register))
				->setLastname(er('lastname', $register))
				->setUrl(er('url', $register))
				->setStatus(STATUS_ENABLED)
				->setComplete(1);
			$user_id = API::getDataModel()->save($user);
			
			if ( $user_id < 1 ) {
				throw new TuneToUs_Exception(ERROR_ACCOUNT_FAILED_TO_CREATE);
			}
			
			User_Session::get()->setLogin($user_id);
			
			$this->pushSuccessAndRedirect(SUCCESS_ACCOUNT_CREATED, 'account/index');
		} catch ( TuneToUs_Exception $e ) {
			$this->pushErrorAndRedirect($e->getMessage(), 'account/register');
		} catch ( Exception $e ) {
			$this->pushErrorAndRedirect(ERROR_GENERAL, 'index/index');
		}
		
		return true;
	}
}