<?php


class Root_Controller extends Artisan_Controller {

	public function renderLayout($view) {
		$this->setLayout('tunetous');
		
		$this->css_tunetous = DIR_CSS . 'tunetous.css';
		$this->js_jquery = DIR_JAVASCRIPT . 'jquery.js';

		$this->render('root/header', 'header');
		
		$this->user = API::getUser();
		$this->is_logged_in = ttu_user_is_logged_in();
		$this->render('root/menu', 'menu');
		
		$this->message_list = $this->getMessage()->display();
		$this->render('root/message-list', 'message-list');
		
		// Render the body
		$this->render($view, 'body');
		
		$this->render('root/footer', 'footer');
	}
	
	
	protected function getMessage() {
		return Message::get();
	}
	
	protected function pushErrorAndRedirect($message, $url) {
		$this->getMessage()->pushError($message);
		$this->redirect($this->url($url));
	}
	
	protected function pushSuccessAndRedirect($message, $url) {
		$this->getMessage()->pushSuccess($message);
		$this->redirect($this->url($url));
	}
	
	protected function redirect($url) {
		header("Location: " . $url);
		exit;
	}
	
	protected function verifyUserAuthentication($ajax=false) {
		if ( true === $ajax ) {
			
		} else {
			if ( false === ttu_user_is_logged_in() ) {
				$this->pushErrorAndRedirect(ERROR_NOT_LOGGED_IN, 'index/index');
			}
		}
	}
}