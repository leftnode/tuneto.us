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
		parent::renderLayout('login');
	}
	
	public function logoutGet() {
		try {
			TTU::getMessenger()->pushSuccess(_('You are now logged out.'));
			ttu_user_logout();
		} catch ( Exception $e ) { }
		
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
		
		$this->user = TTU::getUser()->model();
		$this->renderLayout('update');
	}
	
	public function uploadGet() {
		$this->setSectionTitle(_('Upload New Track'));
		
		$this->track = array();
		$this->renderLayout('upload');
	}
	
	
	
	public function loginPost() {
		try {
			
			if ( true === ttu_user_is_logged_in() ) {
				$this->redirect($this->url('account/dashboard'));
			}
			
			$login = (array)$this->getParam('login');
			
			$nickname = er('nickname', $login);
			$password = er('password', $login);
			
			$user = TTU::getDataModel()->where('nickname = ?', $nickname)
				->where('status = ?', STATUS_ENABLED)
				->limit(1)
				->loadFirst(new User());
			
			$password_salt = $user->getPasswordSalt();
			$password_user = $user->getPassword();
			$password_hashed = crypt_compute_hash($password, $password_salt);
			
			if ( $password_user !== $password_hashed ) {
				throw new TuneToUs_Exception(_('Your account can not be found.'));
			}
			
			if ( false === $user->exists() ) {
				throw new TuneToUs_Exception(_('Your account can not be found.'));
			}
			
			// Update the last time they were last logged in.
			$user->setDateLastlogin(time());
			TTU::getDataModel()->save($user);
			
			$user_id = $user->id();
			ttu_user_login($user_id);
			
			$this->pushSuccessAndRedirect(_('You have successfully logged in!'), 'account/dashboard');
			
		} catch ( TuneToUs_Exception $e ) {
			
		} catch ( Exception $e ) {
			
			
		}
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
			$password = er('password', $register);
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
			TTU::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TTU::getMessenger()->pushError(_('Your form failed to validate, please check the errors and try again.'));
		}
		
		$this->view->setValidator($validator);
		$this->register = $register;
		
		parent::renderLayout('register');
		
		return true;
	}
	
	public function updatePost() {
		$this->setLayout(NULL);
		
		try {
			
			
		} catch ( TuneToUs_Exception $e ) {
			
		} catch ( Exception $e ) {
			
		}
	}
	
	public function uploadPost() {
		try {
			$user = TTU::getUser();
			$content_directory = $user->getContentDirectory();
			
			$upload = (array)$this->getParam('upload');
			$track_data = (array)$this->getFilesParam('track');
			
			$validator = $this->buildValidator();
			$validator->load('upload')->setData($upload)->validate();

			$uploader = new Uploader_Track($track_data);
			$uploader->setAllowOverwrite(true)
				->setDestinationDirectory($content_directory)
				->upload();
			
			/* If the upload went well, create a new Track record and Track_Queue record. */
			$track = new Track();
			$track->setUserId($user->id())
				->setPath($content_directory)
				->setFilename($uploader->getFilename())
				->setName(er('name', $upload))
				->setDescription(er('description', $upload))
				->setLength(0)
				->setViewCount(0)
				->setListenCount(0)
				->setStatus(STATUS_DISABLED);
			$track_id = TTU::getDataModel()->save($track);
			
			if ( false === $track->exists() ) {
				throw new TuneToUs_Exception(_('An error occurred when uploading your track. Please try again.'));
			}
			
			$track_queue = new Track_Queue();
			$track_queue->setTrackId($track_id)
				->setOutput('')
				->setStatus(STATUS_ENABLED);
			$track_queue_id = TTU::getDataModel()->save($track_queue);
			
			if ( false === $track_queue->exists() ) {
				throw new TuneToUs_Exception(_('An error occurred when queueing your track. Please try again.'));
			}
			
			$this->pushSuccessAndRedirect(_('Your track was successfully uploaded. Please give us a moment while we convert it to the proper format.'), 'account/dashboard');
		} catch ( TuneToUs_Exception $e ) {
			$this->pushErrorAndRedirect($e->getMessage(), 'account/index');
		} catch ( Exception $e ) {
			$this->pushErrorAndRedirect(ERROR_GENERAL, 'index/index');
		}
		
		return true;
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