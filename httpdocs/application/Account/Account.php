<?php

require_once 'application/Root/Root.php';

/**
 * Handles all account management, including registration, uploading a track,
 * changing privacy settings, logging in/out, and updating a user's photo.
 * 
 * @author vmc <vmc@leftnode.com>
 */
class Account_Controller extends Root_Controller {
	
	/**
	 * Alias for dashboardGet().
	 */
	public function indexGet() {
		$this->dashboardGet();
	}
	
	/**
	 * Displays the central dashboard for the currently logged in user. Can
	 * only be used if the user is logged in.
	 * 
	 * @retval bool Returns true.
	 */
	public function dashboardGet() {
		$this->verifyUserSession();
		
		try {
			$user = TuneToUs::getUser();
			$this->user = $user;
			$this->track_iterator = TuneToUs::getDataModel()
				->where('user_id = ?', $user->id())
				->where('status <> ?', STATUS_DISABLED)
				->limit(10)
				->loadAll(new Track());
		} catch ( Exception $e ) { }
			
		$this->setSectionTitle(Language::__('account_your_dashboard'));
		$this->renderLayout('dashboard');
		
		return true;
	}
	
	/**
	 * Displays a management interface for a list of favorite tracks the logged
	 * in user has made.
	 * 
	 * @retval bool Returns true.
	 */
	public function favoriteListGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(Language::__('account_favorites'));
		$this->renderLayout('favorite-list');
		
		return true;
	}
	
	/**
	 * Displays the login form. This uses the parent class' renderLayout()
	 * method so it doesn't display the menu.
	 * 
	 * @retval bool Returns true.
	 */
	public function loginGet() {
		parent::renderLayout('login');
		return true;
	}
	
	/**
	 * Logs a logged in user out of the system.
	 */
	public function logoutGet() {
		try {
			ttu_user_logout();
		} catch ( Exception $e ) { }
		
		$this->redirect($this->url('index/index'));
	}

	/**
	 * Displays the registration form. If the user is logged in, this method redirects
	 * them to the dashboard.
	 * 
	 * @retval bool Returns true.
	 */
	public function registerGet() {
		if ( true === ttu_user_is_logged_in() ) {
			$this->redirect($this->url('account/dashboard'));
		}
		
		$this->register = array();
		parent::renderLayout('register');
		
		return true;
	}
	
	/**
	 * Displays the form to manage the user's settings.
	 * 
	 * @retval bool Returns true.
	 */
	public function settingsGet() {
		$this->verifyUserSession();
		
		$user = TuneToUs::getUser();
		$this->setting_allow_followers = $user->getSettingAllowFollowers();
		$this->setting_email_new_follower = $user->getSettingEmailNewFollower();
		$this->setting_email_finished_processing = $user->getSettingEmailFinishedProcessing();
		
		$this->setSectionTitle(Language::__('account_settings'));
		$this->renderLayout('settings');
		
		return true;
	}
	
	/**
	 * Displays a list of tracks the user has uploaded. Gives them an interface
	 * to disable or enable tracks.
	 * 
	 * @retval bool Returns true.
	 */
	public function trackListGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(Language::__('account_track_list'));
		$this->renderLayout('track-list');
		
		return true;
	}
	
	/**
	 * Displays the form to upload a new track.
	 * 
	 * @retval bool Returns true.
	 */
	public function uploadGet() {
		$this->verifyUserSession();
		
		$this->setSectionTitle(Language::__('account_upload_track'));
		
		$this->track = array();
		$this->renderLayout('upload');
		
		return true;
	}
	
	
	
	public function followPost($follower_id, $following_id) {
		$this->verifyUserSession(true);
		
		try {
			
			
		} catch ( TuneToUs_Exception $e ) {
			
		} catch ( Exception $e ) {
			
		}
		
		return true;
	}
	
	/**
	 * Attempts to log the user in given their nickname and password. Uses
	 * the parent class' renderLayout() to render the layout to not show the
	 * account menu.
	 * 
	 * @retval bool Returns true.
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
	 * 
	 * @retval bool Returns true.
	 */
	public function logoutPost() {
		$this->logoutGet();
		return true;
	}

	/**
	 * Attempt to let a user register. Will continue to let the user attempt
	 * to register until they give up or successfully register.
	 * 
	 * @retval bool Returns true.
	 */
	public function registerPost() {
		try {
			/* Logged in users can not register. */
			if ( true === ttu_user_is_logged_in() ) {
				$this->redirect($this->url('account/dashboard'));
			}
			
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
	
	/**
	 * Displays the form to manage the user's settings.
	 * 
	 * @retval bool Returns true.
	 */
	public function settingsPost() {
		$this->verifyUserSession();
		
		try {
			$user = TuneToUs::getUser();
			
			$setting_allow_followers = intval(er('setting_allow_followers', $_POST, 0));
			$setting_email_new_follower = intval(er('setting_email_new_follower', $_POST, 0));
			$setting_email_finished_processing = intval(er('setting_email_finished_processing', $_POST, 0));
			
			$user->setSettingAllowFollowers($setting_allow_followers)
				->setSettingEmailNewFollower($setting_email_new_follower)
				->setSettingEmailFinishedProcessing($setting_email_finished_processing);
			
			TuneToUs::getDataModel()->save($user);

		} catch ( TuneToUs_Exception $e ) {
			TuneToUs::getMessenger()->pushError($e->getMessage());
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError(Language::__('error_form_validation_error'));
		}
		
		$this->redirect($this->url('account/settings'));
		
		return true;
	}
	
	/**
	 * Updates the currently logged in user's photo.
	 * 
	 * @retval bool Returns true.
	 */
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
	
	/**
	 * Renders a custom layout for most of the account pages.
	 * 
	 * @retval bool Returns true.
	 */
	protected function renderLayout($view) {
		$this->content = $this->render($view);
		parent::renderLayout('account/layout');
		
		return true;
	}

	/**
	 * Each account section has a title, and this method sets that.
	 * 
	 * @retval Account_Controller Returns this for chaining.
	 */
	private function setSectionTitle($title) {
		$this->__set('section_title', $title);
		return $this;
	}
}