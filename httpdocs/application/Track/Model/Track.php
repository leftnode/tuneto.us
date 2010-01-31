<?php

class Track_Validator extends Artisan_Validator {
	
	public function init($model) {
		$this->model = array(
			'upload' => array(
				'name' => array('label' => 'Track Name', 'rule_list' => array('not_empty' => true, 'max_length' => 64)),
				'description' => array('label' => 'Description', 'rule_list' => array('not_empty' => true, 'min_length' => 6))
			)
		);
		
		if ( true === exs($model, $this->model) ) {
			$this->model_name = $model;
		}
	}
} 