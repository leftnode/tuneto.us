<?php

require_once 'DataModeler/DataObject.php';

class User extends DataObject {
	public function setEmailAddress($email_address) {
		if ( false === filter_var($email_address, FILTER_VALIDATE_EMAIL) ) {
			throw new TuneToUs_Exception(ERROR_INVALID_EMAIL_ADDRESS);
		}
		
		$this->__set('email_address', $email_address);
		return $this;
	}


	public function setUrl($url) {
		if ( false === empty($url) ) {
			if ( false === filter_var($url, FILTER_VALIDATE_URL) ) {
				throw new TuneToUs_Exception(ERROR_INVALID_URL);
			}
			
			$this->__set('url', $url);
		}
		
		return $this;
	}
}