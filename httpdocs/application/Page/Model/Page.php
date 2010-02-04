<?php

class Page_Validator extends Artisan_Validator {
	
	public function init($model) {
		$this->model = array(
			'contact_us' => array(
				'from_email_address' => array('label' => 'Your Email Address', 'rule_list' => array('not_empty' => true, 'email' => true, 'max_length' => 255)),
				'from_name' => array('label' => 'Your Name', 'rule_list' => array('not_empty' => true, 'min_length' => 3, 'max_length' => 64)),
				'subject' => array('label' => 'Subject', 'rule_list' => array('not_empty' => true, 'min_length' => 6, 'max_length' => 128)),
				'message' => array('label' => 'Message', 'rule_list' => array('not_empty' => true, 'max_length' => 4096))
			)
		);
		
		if ( true === exs($model, $this->model) ) {
			$this->model_name = $model;
		}
	}
}