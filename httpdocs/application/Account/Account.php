<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->dashboardGet();
	}
	
	public function dashboardGet() {
		$this->setSectionTitle(Language::__('your_dashboard'));
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
		
		$this->user = TuneToUs::getUser()->model();
		$this->renderLayout('update');
	}
	
	public function uploadGet() {
		$this->setSectionTitle(Language::__('account_upload_track'));
		
		$this->upload = array();
		$this->renderLayout('upload');
	}
	
	
	/**
	 * Attempts to log the user in given their nickname and password.
	 * 
	 */
	public function loginPost() {
		try {
			
			/* Logged in users can't re-login. */
			if ( true === ttu_user_is_logged_in() ) {
				$this->redirect($this->url('account/dashboard'));
			}
			
			/* Get the login information. */
			$login = (array)$this->getParam('login');
			
			/* Ensure they actually exist in the system. */
			$nickname = er('nickname', $login);
			$password = er('password', $login);
			
			$user = TuneToUs::getDataModel()->where('nickname = ?', $nickname)
				->where('status = ?', STATUS_ENABLED)
				->limit(1)
				->loadFirst(new User());
			
			$password_salt = $user->getPasswordSalt();
			$password_user = $user->getPassword();
			$password_hashed = crypt_compute_hash($password, $password_salt);
			
			if ( $password_user !== $password_hashed ) {
				throw new TuneToUs_Exception(Language::__('error_account_not_found'));
			}
			
			if ( false === $user->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_account_not_found'));
			}
			
			/* Update the last time they were last logged in. */
			$user->setDateLastlogin(time());
			TuneToUs::getDataModel()->save($user);
			
			/* Send the actual login information. */
			$user_id = $user->id();
			ttu_user_login($user_id);
			
			$this->redirect($this->url('account/dashboard'));
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) { }
		
		parent::renderLayout('login');
		
		return true;
	}
	
	/**
	 * Alias for logoutGet() in case they send a post method for logging out.
	 * @retval bool Returns true.
	 */
	public function logoutPost() {
		$this->logoutGet();
		return true;
	}
	
	/**
	 * Updates the user's privacy settings.
	 * @retval bool Returns true.
	 */
	public function privacyPost() {
		
	}
	
	/**
	 * Attempt to let a user register.
	 * @retval bool Returns true.
	 */
	public function registerPost() {
		try {
			$register = (array)$this->getParam('register');
			
			/* Automatic form validation. */
			$validator = $this->buildValidator();
			$validator->load('register')->setData($register)->validate();
			
			/* Must have a unique nickname. */
			$nickname = er('nickname', $register);
			$user = TuneToUs::getDataModel()->where('nickname = ?', $nickname)
				->loadFirst(new User());
			
			if ( true === $user->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_nickname_taken'));
			}
			
			/* Must have a unique email address. */
			$email_address = er('email_address', $register);
			$user = TuneToUs::getDataModel()->where('email_address = ?', $email_address)
				->loadFirst(new User());
			
			if ( true === $user->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_email_address_taken'));
			}
			
			/* Fairly simple salt and password creation. */
			$password = er('password', $register);
			$password_salt = crypt_create_salt();
			$password_hashed = crypt_compute_hash($password, $password_salt);
		
			/* The content directory is where all of their information will be stored. */
			$user = new User();
			$content_directory = $user->createContentDirectory();
			
			/* Save the user. */
			$user->setContentDirectory($content_directory)
				->setNickname($nickname)
				->setPassword($password_hashed)
				->setPasswordSalt($password_salt)
				->setEmailAddress($email_address)
				->setName(er('name', $register))
				->setGender(er('gender', $register))
				->setStatus(STATUS_ENABLED);
			$user_id = TuneToUs::getDataModel()->save($user);
			
			if ( false === $user->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_account_creation_failed'));
			}
			
			/* If they successfully created an account, log them in. */
			ttu_user_login($user_id);
			
			$this->pushSuccessAndRedirect(Language::__('success_account_created'), 'account/dashboard');
			
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError(Language::__('error_form_validation_error'));
		}
		
		$this->getView()->setValidator($validator);
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
	
	/**
	 * Upload a track to the system.
	 * 
	 * @retval 
	 */
	public function uploadPost() {
		try {
			$user = TuneToUs::getUser();
			$content_directory = $user->getContentDirectory();
			
			$upload = (array)$this->getParam('upload');
			
			$track_file = (array)$this->getFilesParam('track');
			$track_image_file = (array)$this->getFilesParam('image');
			
			$validator = $this->buildValidator();
			$validator->load('upload')->setData($upload)->validate();

			$uploader = new Uploader_Track($track_file);
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
				->setStatus(STATUS_DISABLED);
			$track_id = TuneToUs::getDataModel()->save($track);
			
			if ( false === $track->exists() ) {
				throw new TuneToUs_Exception(_('An error occurred when uploading your track. Please try again.'));
			}
			
			$track_queue = new Track_Queue();
			$track_queue->setTrackId($track_id)
				->setOutput('')
				->setStatus(STATUS_ENABLED);
			$track_queue_id = TuneToUs::getDataModel()->save($track_queue);
			
			if ( false === $track_queue->exists() ) {
				throw new TuneToUs_Exception(_('An error occurred when queueing your track. Please try again.'));
			}
			
			TuneToUs::getMessenger()->pushSuccess(Language::__('success_account_track_uploaded'));
			$this->redirect($this->url('account/dashboard'));
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError(Language::__('error_form_validation_error'));
		}
		
		$this->getView()->setValidator($validator);
		
		$this->setSectionTitle(Language::__('account_upload_track'));
		$this->upload = $upload;
		
		$this->renderLayout('upload');
		
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