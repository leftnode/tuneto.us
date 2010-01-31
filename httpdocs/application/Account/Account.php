<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->verifyUserAuthentication();
		
		$this->upload_form = $this->render('track/upload-form');
		
		/* Get a list of the latest 10 tracks for this user. */
		$user = API::getUser();
		$track_iterator = API::getDataModel()
			->where('user_id = ?', $user->id())
			->where('status = ?', STATUS_ENABLED)
			->orderBy('date_create', 'DESC')
			->limit(10)
			->loadAll(new Track());
		$this->track_iterator = $track_iterator;
		
		
		
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
			$nickname = er('nickname', $register);
			
			/* Must have a unique email address. */
			$user = API::getDataModel()->where('email_address = ?', $email_address)->loadFirst(new User());
			$user_email_address = $user->getEmailAddress();

			if ( $email_address == $user_email_address ) {
				throw new TuneToUs_Exception(_('The email address you attempted to register with is already in use. Please choose another one'));
			}
			
			/* And must have a unique nickname. */
			$user = API::getDataModel()->where('nickname = ?', $nickname)->loadFirst(new User());
			$user_nickname = $user->getNickname();
			
			if ( $nickname == $user_nickname ) {
				throw new TuneToUs_Exception(_('The nickname you attempted to register with is already in use. Please choose another one'));
			}
			
			/* Fairly simple salts and password creation. */
			$password_salt = crypt_create_salt();
			$password_hashed = crypt_compute_hash($password, $password_salt);
		
			$user = new User();
			$content_directory = $user->createContentDirectory($email_address);
			
			/* Save the user. */
			$user->setEmailAddress($email_address)
				->setPassword($password_hashed)
				->setPasswordSalt($password_salt)
				->setNickname(er('nickname', $register))
				->setStatus(STATUS_ENABLED)
				->setContentDirectory($content_directory);
			$user_id = API::getDataModel()->save($user);
			
			if ( $user_id < 1 ) {
				throw new TuneToUs_Exception(_('Failed to create your account. Please try again.'));
			}
			
			ttu_user_login($user_id);
			
			$this->pushSuccessAndRedirect(_('Your account was successfully created!'), 'account/index');
		} catch ( TuneToUs_Exception $e ) {
			$this->getMessage()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			$this->getMessage()->pushError(_('Your form failed to validate, please check the errors and try again.'));
		}
		
		$this->view->setValidator($validator);
		$this->register = $register;
		
		$this->renderLayout('register');
		
		return true;
	}
}