<?php

/**
 * Singleton based class for storing messages, both successes
 * and errors to be displayed on each page load. This uses
 * a burst-session type of system where after each message is displayed
 * it is immediately deleted.
 * @author vmc <vmc@leftnode.com>
 */
class Messenger {
	private static $instance = NULL;
	
	private $messageList = array();
	
	const MSG = "_message_list";
	const MSG_ERROR = "error";
	const MSG_SUCCESS = "success";
	
	private function __construct() {

	}
	
	private function __clone() {

	}
	
	public static function get() {
		if ( true === is_null(self::$instance) ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	public function push($type, $message) {
		$this->load();
		
		if ( $type != self::MSG_ERROR && $type != self::MSG_SUCCESS ) {
			$type = self::MSG_ERROR;
		} 
		
		$this->messageList[] = array(
			'type' => $type,
			'message' => $message
		);
		
		$this->write();
	}
	
	public function pushError($message) {
		$this->push(self::MSG_ERROR, $message);
	}
	
	public function pushSuccess($message) {
		$this->push(self::MSG_SUCCESS, $message);
	}
	
	public function display() {
		$this->load();
		
		$list_parsed = NULL;
		foreach ( $this->messageList as $message ) {
			$list_parsed .= '<li class="' . $message['type'] . '" onclick="$(this).fadeOut()">' . $message['message'] . '</li>';
		}
		
		$message_list = '<ul class="messageList">' . $list_parsed . '</ul>';
		
		$this->messageList = array();
		$this->write();
		
		return $message_list;
	}
	
	public function load() {
		$this->messageList = er(self::MSG, $_SESSION, array());
	}
	
	public function write() {
		$_SESSION[self::MSG] = $this->messageList;
	}
}