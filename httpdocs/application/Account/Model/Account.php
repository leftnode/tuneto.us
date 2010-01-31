<?php

class Account_Validator extends Artisan_Validator {
	
	public function init($model) {
		$this->model = array(
			'register' => array(
				'email_address' => array('label' => 'Email Address', 'rule_list' => array('not_empty' => true, 'email' => true, 'max_length' => 255)),
				'password' => array('label' => 'Password', 'rule_list' => array('not_empty' => true, 'min_length' => 6)),
				'nickname' => array('label' => 'Nick Name', 'rule_list' => array('not_empty' => true, 'min_length' => 3, 'max_length' => 64)),
			)
		);
		
		if ( true === exs($model, $this->model) ) {
			$this->model_name = $model;
		}
	}
} 