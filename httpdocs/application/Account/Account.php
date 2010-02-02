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
	
	public function messageListGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(_('Messages'));
		$this->renderLayout('message-list');
	}
	
	public function photoGet($user_id, $type) {
		try {
			$user_id = intval($user_id);
			$user = TuneToUs::getDataModel()
				->where('user_id = ?', $user_id)
				->where('status = ?', STATUS_ENABLED)
				->loadFirst(new User());
			
			$photo_found = false;
			if ( true === $user->exists() ) {
				$content_directory = $user->getContentDirectory();
				
				switch ( $type ) {
					case PHOTO: {
						$photo = $user->getPhoto();
						break;
					}
					
					case PHOTO_THUMBNAIL:
					default: {
						$photo = $user->getPhotoThumbnail();
						break;
					}
				}
				
				$photopath = DIR_PRIVATE . $content_directory . DS . $photo;
				
				if ( true === is_file($photopath) ) {
					$photo_found = true;
				}
			}
			
			if ( false === $photo_found ) {
				switch ( $type ) {
					case PHOTO: {
						$photopath = DIR_SITE_ROOT . DIR_IMAGE . 'anonymous-photo.jpg';
						break;
					}
					
					case PHOTO_THUMBNAIL:
					default: {
						$photopath = DIR_SITE_ROOT . DIR_IMAGE . 'anonymous-photo-thumbnail.jpg';
						break;
					}
				}
			}
			
			$image = imagecreatefromjpeg($photopath);
			header('Content-Type: image/jpeg');
			imagejpeg($image, NULL, 100);
			
			exit;
		} catch ( Exception $e ) { }
	}
	
	
	public function privacyGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(_('Privacy Settings'));
		$this->renderLayout('privacy');
	}
	
	public function profileGet($profile_id) {
		$profile = TuneToUs::getDataModel()
			->where('user_id = ?', $profile_id)
			->where('status = ?', STATUS_ENABLED)
			->loadFirst(new User());
		
		if ( true === $profile->exists() ) {
			$user = TuneToUs::getUser();
			$this->track_iterator = TuneToUs::getDataModel()
				->where('user_id = ?', $profile->id())
				->where('status <> ?', STATUS_DISABLED)
				->limit(10)
				->loadAll(new Track());
			
			$user_is_logged_in = ttu_user_is_logged_in();
			
			$this->user_is_logged_in = $user_is_logged_in;
			$this->user = $user;
			$this->profile = $profile;
			
			$this->can_follow = false;
			if ( true === $user_is_logged_in && $user->id() != $profile->id() ) {
				$user_follow = TuneToUs::getDataModel()
					->where('follower_id = ?', $user->id())
					->where('following_id = ?', $profile->id())
					->loadFirst(new User_Follow());
					
				if ( false === $user_follow->exists() ) {
					$this->can_follow = true;
				}
			}
			
			$view = 'profile';
		} else {
			$view = 'profile-disabled';
		}
		
		parent::renderLayout($view);
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
	
	public function updateGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(Language::__('account_update_your_profile'));
		
		$this->profile = TuneToUs::getUser();
		$this->user = $this->profile->model();
		
		$this->renderLayout('update');
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
		$this->verifyUserSession();
		
		try {
			$user_form_data = (array)$this->getParam('user');
			$photo = (array)$this->getFilesParam('photo');
			
			$validator = $this->buildValidator();
			$validator->load('update')
				->setData($user_form_data)
				->validate();
			
			$user = TuneToUs::getUser();
			$user_id = $user->id();
			$content_directory = $user->getContentDirectory();
			
			$photo_name = er('name', $photo);
			if ( false === empty($photo_name) ) {
				$destination_directory = DIR_PRIVATE . $content_directory;
				
				$uploader = new Uploader();
				$uploader->setData($photo)
					->setDirectory($destination_directory)
					->upload();
				
				/* Resize the main photo and thumbnail. */
				$image_filename = $uploader->getFilename();
				$image_location = $destination_directory . DS . $image_filename;
				
				$image = new Image($image_location);
				$image->resize(300, 300)
					->setDirectory($destination_directory)
					->writeJpg($image_filename);
				$photo_fullsize = $image->getFilename();
				
				$image = new Image($image_location);
				$image->resize(96, 96)
					->setDirectory($destination_directory)
					->writeJpg('tn-' . $image_filename);
				$photo_thumbnail = $image->getFilename();
				
				$user->setPhoto($photo_fullsize)
					->setPhotoThumbnail($photo_thumbnail);
			}
			
			/* Check the email address to ensure it isn't used elsewhere, but only do this if it's changed. */
			$user_form_data_email_address = er('email_address', $user_form_data);
			$user_email_address = $user->getEmailAddress();
			
			if ( $user_email_address != $user_form_data_email_address ) {
				$user_email = TuneToUs::getDataModel()
					->where('email_address = ?', $user_form_data_email_address)
					->loadFirst(new User());
				
				if ( true === $user_email->exists() ) {
					throw new TuneToUs_Exception(Language::__('error_email_address_taken'));
				}
			}
			
			$user->setEmailAddress($user_form_data_email_address)
				->setName(er('name', $user_form_data))
				->setGender(er('gender', $user_form_data))
				->setCountry(er('country', $user_form_data))
				->setBiography(er('biography', $user_form_data))
				->setInterests(er('interests', $user_form_data))
				->setMusic(er('music', $user_form_data))
				->setMovies(er('movies', $user_form_data))
				->setBooks(er('books', $user_form_data))
				->setWebsite1(er('website1', $user_form_data));
			TuneToUs::getDataModel()->save($user);
			
			TuneToUs::getMessenger()->pushSuccess(Language::__('success_profile_updated'));
			
		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		}
		
		$this->getView()->setValidator($validator);
		
		$this->setSectionTitle(Language::__('account_update_your_profile'));
		
		$this->profile = TuneToUs::getUser();
		$this->user = $user_form_data;
		
		$this->renderLayout('update');
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
			$content_directory = $user->getContentDirectory();
			
			$upload = (array)$this->getParam('upload');
			
			$track_file = (array)$this->getFilesParam('track');
			$track_image_file = (array)$this->getFilesParam('image');
			
			$validator = $this->buildValidator();
			$validator->load('upload')
				->setData($upload)
				->validate();

			$uploader = new Uploader_Track($track_file);
			$uploader->setOverwrite(true)
				->setUploadDirectory(DIR_PRIVATE . $content_directory)
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