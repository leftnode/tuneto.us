<?php


class Root_Controller extends Artisan_Controller {
	/// The name of the layout to use. Can be overwritten.
	protected $layout = 'tunetous';
	
	/**
	 * Render the overall application so everything can be included at once.
	 * @param string $view The name of the view to render.
	 * @retval bool Returns true.
	 */
	protected function renderLayout($view) {
		/* Global CSS */
		$this->css_tunetous = DIR_CSS . 'tunetous.css';
		
		/* Global JS */
		$this->js_jquery = DIR_JAVASCRIPT . 'jquery.js';

		/* Render the header, which includes the CSS and JS. */
		$this->render('root/header', 'header');
		
		/* Render the menu. */
		$this->user = TuneToUs::getUser();
		$this->is_logged_in = ttu_user_is_logged_in();
		$this->render('root/menu', 'menu');
		
		/* Render the message list, if one exists. */
		$this->message_list = TuneToUs::getMessenger()->display();
		$this->render('root/message-list', 'message-list');
		
		/* Render the body. */
		$this->render($view, 'body');
		
		/* And the footer. */
		$this->render('root/footer', 'footer');
		
		return true;
	}
	
	
	
	
	
	protected function pushErrorAndRedirect($message, $url) {
		TuneToUs::getMessenger()->pushError($message);
		$this->redirect($this->url($url));
	}
	
	protected function pushSuccessAndRedirect($message, $url) {
		TuneToUs::getMessenger()->pushSuccess($message);
		$this->redirect($this->url($url));
	}
	
	protected function redirect($url) {
		header("Location: " . $url);
		exit;
	}
	
	protected function verifyUserSession($ajax=false) {
		if ( true === $ajax ) {
			exit(Language::__('error_not_logged_in'));
		} else {
			if ( false === ttu_user_is_logged_in() ) {
				TuneToUs::getMessenger()->pushError(Language::__('error_not_logged_in'));
				$this->redirect($this->url('account/login'));
			}
		}
		
		return true;
	}
	
	protected function ajaxResponse($status, $msg) {
		$response = array(
			's' => intval($status),
			'm' => trim($msg)
		);
		
		echo json_encode($response);
		return true;
	}
	
}