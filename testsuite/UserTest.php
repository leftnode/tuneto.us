<?php

require_once 'PHPUnit/Framework.php';
require_once 'httpdocs/lib/Object/User.php';

require_once 'DataModeler/DataModelerException.php';

class UserTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @expectedException DataModelerException
	 */
	public function testUserHasNonEmptyEmailAddress() {
		$user = new User();
		$user->setEmailAddress('');
	}
	
	/**
	 * @expectedException DataModelerException
	 */
	public function testUserHasValidEmailAddress() {
		$user = new User();
		$user->setEmailAddress('abc');
	}
}