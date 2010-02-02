<?php

class Account_Validator extends Artisan_Validator {
	
	public function init($model) {
		$this->model = array(
			'register' => array(
				'nickname' => array('label' => 'Nick Name', 'rule_list' => array('not_empty' => true, 'min_length' => 3, 'max_length' => 64)),
				'email_address' => array('label' => 'Email Address', 'rule_list' => array('not_empty' => true, 'email' => true, 'max_length' => 255)),
				'password' => array('label' => 'Password', 'rule_list' => array('not_empty' => true, 'min_length' => 6)),
				'name' => array('label' => 'Your Name', 'rule_list' => array('max_length' => 64)),
				'gender' => array('label' => 'Gender', 'rule_list' => array('not_empty' => true, 'in_array' => array(GENDER_MALE, GENDER_FEMALE)))
			),
			'upload' => array(
				'name' => array('label' => 'Track Name', 'rule_list' => array('not_empty' => true, 'min_length' => 3, 'max_length' => 128)),
				'description' => array('label' => 'Track Description', 'rule_list' => array('not_empty' => true, 'min_length' => 3))
			),
			'update' => array(
				'email_address' => array('label' => 'Email Address', 'rule_list' => array('not_empty' => true, 'email' => true, 'max_length' => 255)),
				'name' => array('label' => 'Your Name', 'rule_list' => array('max_length' => 64)),
				'gender' => array('label' => 'Gender', 'rule_list' => array('not_empty' => true, 'in_array' => array(GENDER_MALE, GENDER_FEMALE))),
				'country' => array('label' => 'Country', 'rule_list' => array('max_length' => 64)),
				'biography' => array('label' => 'Biography', 'rule_list' => array('max_length' => 4096)),
				'interests' => array('label' => 'Interests', 'rule_list' => array('max_length' => 4096)),
				'music' => array('label' => 'Favorite Music', 'rule_list' => array('max_length' => 4096)),
				'movies' => array('label' => 'Favorite Music', 'rule_list' => array('max_length' => 4096)),
				'books' => array('label' => 'Favorite Books', 'rule_list' => array('max_length' => 4096)),
				'website1' => array('label' => 'Your Website', 'rule_list' => array('max_length' => 128))
			)
		);
		
		if ( true === exs($model, $this->model) ) {
			$this->model_name = $model;
		}
	}
} 