<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->verifyUserAuthentication();
		
		$this->upload_form = $this->render('track/upload-form');
		
		// Get a list of the latest 10 tracks for this user.
		$user = API::getUser();
		$track_list = API::getDataModel()
			->where('user_id = ?', $user->id())
			->where('status = ?', STATUS_ENABLED)
			->orderBy('date_create', 'DESC')
			->limit(10)
			->loadAll(new Track());
		$this->track_list = $track_list;
		$this->user = $user;
		
		$this->renderLayout('index');
		
		return true;
	}
	
	public function logoutGet() {
		try {
			ttu_user_logout();
		} catch ( Exception $e ) { }

		$this->pushSuccessAndRedirect(SUCCESS_LOGGED_OUT, 'index/index');
	}
	
	public function registerGet() {
		if ( true === ttu_user_is_logged_in() ) {
			// Redirect to index, give error
		}
		
		$this->register = array();
		$this->renderLayout('register');
	}
	
	public function loginPost() {
		try {
			if ( true === ttu_user_is_logged_in() ) {
				$this->redirect($this->url('account/index'));
			}
			
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
			
			// Update the last time they were last logged in.
			$user->setDateLastlogin(time());
			API::getDataModel()->save($user);
			
			$user_id = $user->id();
			if ( $user_id < 1 ) {
				throw new TuneToUs_Exception(ERROR_ACCOUNT_NOT_FOUND);
			}
			
			ttu_user_login($user_id);
			
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

			$validator = $this->buildValidator();
			$validator->load('register')->setData($register)->validate();
			
			/* Attempt to validate the data. */
			$email_address = er('email_address', $register);
			$password = er('password', $register);
			
			$user = API::getDataModel()->where('email_address = ?', $email_address)->loadFirst(new User());
			$user_email_address = $user->getEmailAddress();
			
			if ( $email_address == $user_email_address ) {
				throw new TuneToUs_Exception(ERROR_ACCOUNT_EXISTS);
			}
			
			$password_salt = crypt_create_salt();
			$password_hashed = crypt_compute_hash($password, $password_salt);
			
			$user = new User();
			$user->setEmailAddress($email_address)
				->setPassword($password_hashed)
				->setPasswordSalt($password_salt)
				->setNickname(er('nickname', $register))
				->setStatus(STATUS_ENABLED)
				->setComplete(1);
			$user_id = API::getDataModel()->save($user);
			
			if ( $user_id < 1 ) {
				throw new TuneToUs_Exception(ERROR_ACCOUNT_FAILED_TO_CREATE);
			}
			
			ttu_user_login($user_id);
			
			$this->pushSuccessAndRedirect(SUCCESS_ACCOUNT_CREATED, 'account/index');
		} catch ( TuneToUs_Exception $e ) {
			$this->pushErrorAndRedirect($e->getMessage(), 'account/register');
		} catch ( Exception $e ) {
			$this->getMessage()->pushError(ERROR_FAILED_FORM);
		}
		
		$this->view->setValidator($validator);
		$this->register = $register;
		
		$this->renderLayout('register');
		
		return true;
	}
}