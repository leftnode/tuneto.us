<?php


class Root_Controller extends Artisan_Controller {
	protected $layout = 'tunetous';
	
	protected function renderLayout($view) {
		/* Global CSS */
		$this->css_tunetous = DIR_CSS . 'tunetous.css';
		
		/* Global JS */
		$this->js_jquery = DIR_JAVASCRIPT . 'jquery.js';

		/* Render the header, which includes the CSS and JS. */
		$this->render('root/header', 'header');
		
		/* Render the menu. */
		$this->user = TTU::getUser();
		$this->is_logged_in = ttu_user_is_logged_in();
		$this->render('root/menu', 'menu');
		
		/* Render the message list, if one exists. */
		$this->message_list = TTU::getMessenger()->display();
		$this->render('root/message-list', 'message-list');
		
		/* Render the body. */
		$this->render($view, 'body');
		
		/* And the footer. */
		$this->render('root/footer', 'footer');
	}
	
	protected function pushErrorAndRedirect($message, $url) {
		TTU::getMessenger()->pushError($message);
		$this->redirect($this->url($url));
	}
	
	protected function pushSuccessAndRedirect($message, $url) {
		TTU::getMessenger()->pushSuccess($message);
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