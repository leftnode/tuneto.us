<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->dashboardGet();
	}
	
	public function dashboardGet() {
		$this->setSectionTitle(_('Your Dashboard'));
		$this->renderLayout('dashboard');
		
		return true;
	}
	
	public function favoriteListGet() {
		$this->setSectionTitle(_('Your Favorites'));
		$this->renderLayout('favorite-list');
	}
	
	public function loginGet() {
		
		$this->renderLayout('login');
	}
	
	public function logoutGet() {
		
		TTU::getMessenger()->pushSuccess(_('You are now logged out.'));
		$this->redirect($this->url('index/index'));
	}
	
	public function messageListGet() {
		$this->setSectionTitle(_('Messages'));
		$this->renderLayout('message-list');
	}
	
	public function privacyGet() {
		$this->setSectionTitle(_('Privacy Settings'));
		$this->renderLayout('privacy');
	}
	
	public function registerGet() {
		$this->register = array();
		parent::renderLayout('register');
	}
	
	public function trackListGet() {
		$this->setSectionTitle(_('Your Tracks'));
		$this->renderLayout('track-list');
	}
	
	public function updateGet() {
		$this->setSectionTitle(_('Update Your Profile'));
		
		$this->user = array();
		$this->renderLayout('update');
	}
	
	public function uploadGet() {
		$this->setSectionTitle(_('Upload New Track'));
		
		$this->track = array();
		$this->renderLayout('upload');
	}
	
	
	
	public function loginPost() {
		
	}
	
	public function logoutPost() {
		
	}
	
	public function privacyPost() {
		
	}
	
	public function registerPost() {
		try {
			$register = (array)$this->getParam('register');
			
			/* Automatic form validation. */
			$validator = $this->buildValidator();
			$validator->load('register')->setData($register)->validate();
			
			/* Must have a unique nickname. */
			$nickname = er('nickname', $register);
			$user = TTU::getDataModel()->where('nickname = ?', $nickname)->loadFirst(new User());
			
			if ( true === $user->exists() ) {
				throw new TuneToUs_Exception(_('The nickname you attempted to register with is already in use. Please choose another one'));
			}
			
			/* Must have a unique email address. */
			$email_address = er('email_address', $register);
			$user = TTU::getDataModel()->where('email_address = ?', $email_address)->loadFirst(new User());
			
			if ( true === $user->exists() ) {
				throw new TuneToUs_Exception(_('The email address you attempted to register with is already in use. Please choose another one'));
			}
			
			/* Fairly simple salts and password creation. */
			$password_salt = crypt_create_salt();
			$password_hashed = crypt_compute_hash($password, $password_salt);
		
			/* The content directory is where all of their information will be stored. */
			$user = new User();
			$content_directory = $user->createContentDirectory($email_address);
			
			/* Save the user. */
			$user->setContentDirectory($content_directory)
				->setNickname($nickname)
				->setPassword($password_hashed)
				->setPasswordSalt($password_salt)
				->setEmailAddress($email_address)
				->setName(er('name', $register))
				->setGender(er('gender', $register))
				->setStatus(STATUS_ENABLED);
			$user_id = TTU::getDataModel()->save($user);
			
			if ( $user_id < 1 ) {
				throw new TuneToUs_Exception(_('Failed to create your account. Please try again.'));
			}
			
			ttu_user_login($user_id);
			
			$this->pushSuccessAndRedirect(_('Your account was successfully created!'), 'account/dashboard');
			
		} catch ( TuneToUs_Exception $e ) {
			
		} catch ( Artisan_Exception $e ) {
			
		} catch ( Exception $e ) {
			
		}
		
		
		
		return true;
	}
	
	public function updatePost() {
		$this->setLayout(NULL);
		
		try {
			
			
		} catch ( TuneToUs_Exception $e ) {
			
		} catch ( Exception $e ) {
			
		}
	}
	
	
	
	protected function renderLayout($view) {
		$this->content = $this->render($view);
		
		parent::renderLayout('account/layout');
		
		return true;
	}


	public function setSectionTitle($title) {
		$this->__set('section_title', $title);
		return $this;
	}
}