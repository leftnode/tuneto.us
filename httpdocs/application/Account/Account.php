<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		$this->dashboardGet();
	}
	
	public function dashboardGet() {
		$this->verifyUserSession();
		
		$user = TuneToUs::getUser();
		$this->user = $user;
		$this->track_iterator = TuneToUs::getDataModel()
			->where('user_id = ?', $user->id())
			->where('status <> ?', STATUS_DISABLED)
			->limit(10)
			->loadAll(new Track());
		
		$this->setSectionTitle(Language::__('account_your_dashboard'));
		$this->renderLayout('dashboard');
		
		return true;
	}
	
	public function favoriteListGet() {
		$this->verifyUserSession();
		
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

	public function privacyGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(_('Privacy Settings'));
		$this->renderLayout('privacy');
	}
	
	
	
	public function registerGet() {
		if ( true === ttu_user_is_logged_in() ) {
			$this->redirect($this->url('account/dashboard'));
		}
		
		$this->register = array();
		parent::renderLayout('register');
	}
	
	public function settingsGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(_('Your Settings'));
		$this->renderLayout('settings');
	}
	
	public function trackListGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(_('Your Tracks'));
		$this->renderLayout('track-list');
	}
	

	public function uploadGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(Language::__('account_upload_track'));
		
		$this->upload = array();
		$this->renderLayout('upload');
	}
	
	
	
	public function followPost($follower_id, $following_id) {
		$this->verifyUserSession(true);
		
		try {
			$follower_id = intval($follower_id);
			$following_id = intval($following_id);
		
			/* Make sure there isn't a connection already. */
			$user_follower = TuneToUs::getDataModel()
				->where('follower_id = ?', $follower_id)
				->where('following_id = ?', $following_id)
				->loadFirst(new User_Follower());
			
			if ( false === $user_follower->exists() ) {
				$follower_user = TuneToUs::getDataModel()
					->where('user_id = ?', $follower_id)
					->loadFirst(new User());
				
				$following_user = TuneToUs::getDataModel()
					->where('user_id = ?', $following_id)
					->loadFirst(new User());

				if ( true === $following_user->exists() && true === $following_user->exists() ) {
					$user_follower = new User_Follower();
					$user_follower->setFollowerId($follower_id)
						->setFollowingId($following_id);
					
					TuneToUs::getDataModel()->save($user_follower);
					
					$this->ajaxResponse(STATUS_SUCCESS, 'you are now following this user.');
				}
			}
			
		} catch ( TuneToUs_Exception $e ) {
			
		} catch ( Exception $e ) {
			
		}
		
		return true;
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
			
			$user = TuneToUs::getDataModel()
				->where('nickname = ?', $nickname)
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
			$validator->load('register')
				->setData($register)
				->validate();
			
			/* Must have a unique nickname. */
			$nickname = er('nickname', $register);
			$user = TuneToUs::getDataModel()
				->where('nickname = ?', $nickname)
				->loadFirst(new User());
			
			if ( true === $user->exists() ) {
				throw new TuneToUs_Exception(Language::__('error_nickname_taken'));
			}
			
			/* Must have a unique email address. */
			$email_address = er('email_address', $register);
			$user = TuneToUs::getDataModel()
				->where('email_address = ?', $email_address)
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
			$directory = $user->createDirectory();
			
			/* Save the user. */
			$user->setDirectory($directory)
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
	
	public function updatePhotoPost() {
		$this->verifyUserSession();
		
		try {
			$image_file_data = (array)$this->getFilesParam('image');
			
			$user = TuneToUs::getUser();
			$directory = $user->getDirectory();
			$destination_directory = DIR_PRIVATE . $directory;
			
			$image_name = er('name', $image_file_data);
			if ( true === empty($image_name) ) {
				throw new TuneToUs_Exception(Language::__('error_select_image'));
			}
		
			$uploader = new Uploader();
			$uploader->setData($image_file_data)
				->setDirectory($destination_directory)
				->upload();
			
			$image_filename = $uploader->getFilename();
		
			$image_id = ttu_create_image_record($directory, $image_filename);
			
			$user->setImageId($image_id);
			TuneToUs::getDataModel()->save($user);
			
			TuneToUs::getMessenger()->pushSuccess(Language::__('success_photo_updated'));
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError(Language::__('error_form_validation_error'));
		}

		$this->redirect($this->url('account/dashboard'));
		
		return true;
	}
	
	
	/**
	 * Upload a track to the system.
	 * 
	 * @retval bool Returns true.
	 */
	public function uploadPost() {
		$this->verifyUserSession();
		
		try {
			$user = TuneToUs::getUser();
			
			$track_data = (array)$this->getParam('track');
			
			$track_file_data = (array)$this->getFilesParam('track');
			$image_file_data = (array)$this->getFilesParam('image');

			$directory = $user->getDirectory();
			$destination_directory = DIR_PRIVATE . $directory;
			
			$track_name = er('name', $track_file_data);
			if ( true === empty($track_name) ) {
				throw new TuneToUs_Exception(Language::__('error_select_track'));
			}
			
			$validator = $this->buildValidator();
			$validator->load('track')
				->setData($track_data)
				->validate();

			/* Attempt to upload the image with the track. */
			$image_id = 0;
			$image_name = er('name', $image_file_data);
			if ( false === empty($image_name) ) {
				$uploader = new Uploader();
				$uploader->setData($image_file_data)
					->setDirectory($destination_directory)
					->upload();
				
				$image_filename = $uploader->getFilename();
			
				$image_id = ttu_create_image_record($directory, $image_filename);
			}

			$uploader = new Uploader_Track();
			$uploader->setData($track_file_data)
				->setDirectory($destination_directory)
				->upload();
			
			/* If the upload went well, create a new Track record and Track_Queue record. */
			$track = new Track();
			$track->setUserId($user->id())
				->setImageId($image_id)
				->setDirectory($directory)
				->setFilename($uploader->getFilename())
				->setName(er('name', $track_data))
				->setDescription(er('description', $track_data))
				->setLength(0)
				->setViewCount(0)
				->setStatus(STATUS_PROCESSING);
			$track_id = TuneToUs::getDataModel()->save($track);
			
			if ( false === $track->exists() ) {
				throw new TuneToUs_Exception(_('An error occurred when uploading your track. Please try again.'));
			}
			
			$track_queue = new Track_Queue();
			$track_queue->setTrackId($track_id)
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
		$this->track = $track_data;
		
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