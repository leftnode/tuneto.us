<?php


class Root_Controller extends Artisan_Controller {

	public function renderLayout($view) {
		$this->setLayout('tunetous');
		
		$this->css_tunetous = DIR_CSS . 'tunetous.css';
		$this->render('root/header', 'header');
		
		$this->render('root/menu', 'menu');
		
		// Render the body
		$this->render($view, 'body');
		
		$this->render('root/footer', 'footer');
	}
}