<?php

require_once 'DataModeler/DataObject.php';

class User extends DataObject {
	private $favorite_list = NULL;
	private $following_list = NULL;
	private $follower_list = NULL;
	
	public function setFavoriteList(DataIterator $list) {
		$this->favorite_list = $list;
		return $this;
	}
	
	public function setFollowingList(DataIterator $list) {
		$this->following_list = $list;
		return $this;
	}
	
	public function setFollowerList(DataIterator $list) {
		$this->follower_list = $list;
		return $this;
	}
	
	public function setEmailAddress($email_address) {
		if ( false === filter_var($email_address, FILTER_VALIDATE_EMAIL) ) {
			throw new TuneToUs_Exception(_('The email address for this user is not valid.'));
		}
		
		$this->__set('email_address', $email_address);
		return $this;
	}
	
	
	public function getFavoriteList() {
		return $this->favorite_list;
	}
	
	public function getFollowingList() {
		return $this->following_list;
	}
	
	public function getFollowerList() {
		return $this->follower_list;
	}


	public function createDirectory() {
		/**
		 * While it's not good to use sha1() on a uniqid, this isn't for
		 * encryption purposes, just random string generation purposes.
		 */
		$uniqid = uniqid(NULL, true);
		$uniqid_sha1 = sha1($uniqid);
		
		$char1 = substr($uniqid_sha1, 0, 1);
		$char2 = substr($uniqid_sha1, 1, 1);
		
		$directory = $char1 . DS . $char2 . DS . $uniqid;
		$full_path = DIR_PRIVATE . $directory;
		if ( false === is_dir($full_path) ) {
			@mkdir($full_path, 0777, true);
		}
		
		return $directory;
	}
}