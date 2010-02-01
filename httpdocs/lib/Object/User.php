<?php

require_once 'DataModeler/DataObject.php';

class User extends DataObject {
	
	
	public function setEmailAddress($email_address) {
		if ( false === filter_var($email_address, FILTER_VALIDATE_EMAIL) ) {
			throw new TuneToUs_Exception(_('The email address for this user is not valid.'));
		}
		
		$this->__set('email_address', $email_address);
		return $this;
	}

	public function setContentDirectory($content_directory) {
		// Must not exist within DIR_PRIVATE.
		$full_path = DIR_PRIVATE . $content_directory;
		
		if ( false === is_dir($full_path) ) {
			throw new TuneToUs_Exception(_('The content directory for this user does not exist.'));
		}
		
		$this->__set('content_directory', $content_directory);
		return $this;
	}
	
	
	public function createContentDirectory($email_address) {
		if ( true === empty($email_address) ) {
			throw new TuneToUs_Exception(_('This user does not have an email address and thus the content directory can not be created.'));
		}
		
		$first_char = strtolower(substr($email_address, 0, 1));
		$second_char = strtolower(substr($email_address, 1, 1));
		
		if ( '@' == $first_char || ord('\0') == $first_char ) {
			$first_char = '_';
		}
		
		if ( '@' == $second_char || ord('\0') == $second_char ) {
			$second_char = '_';
		}
		
		$first_path = DIR_PRIVATE . $first_char . DS;
		if ( false === is_dir($first_path) ) {
			@mkdir($first_path);
		}
		
		$second_path = $first_path . $second_char . DS;
		if ( false === is_dir($second_path) ) {
			@mkdir($second_path);
		}
		
		$email_hash = sha1($email_address);
		
		$full_path = $second_path . $email_hash . DS;
		if ( false === is_dir($full_path) ) {
			@mkdir($full_path);
		}
		
		$content_directory = $first_char . DS . $second_char . DS . $email_hash;
		return $content_directory;
	}
}