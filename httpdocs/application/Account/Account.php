<?php

require_once 'application/Root/Root.php';

class Account_Controller extends Root_Controller {
	
	public function indexGet() {
		if ( false === User_Session::get()->isLoggedIn() ) {
			// Redirect to index, give error.
		}
		
		$this->renderLayout('index');
	}
	
	/*public function registerGet() {
		if ( true === User_Session::get()->isLoggedIn() ) {
			// Redirect to index, give error
		}
		
		$this->renderLayout('register');
	}*/
	
	public function registerPost() {
		try {
			$register = (array)$this->getParam('register');
			
			if ( false === is_array($register) || 0 === count($register) ) {
				throw new TuneToUs_Exception('No registration information was available. Please try again.');
			}
			
			/* Attempt to validate the data. */
			
			
			
		} catch ( TuneToUs_Exception $e ) {
			
			
		} catch ( Exception $e ) {
			
			
		}
	}
}