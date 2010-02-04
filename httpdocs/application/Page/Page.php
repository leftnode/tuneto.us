<?php

require_once 'application/Root/Root.php';

class Page_Controller extends Root_Controller {
	
	public function indexGet($page_name) {
		$this->viewGet($page_name);
	}
	
	public function contactUsGet() {
		$this->contact_us = array();
		$this->renderLayout('contact-us');
		return true;
	}
	
	public function viewGet($page_name) {
		try {
			$page = TuneToUs::getDataModel()
				->where('name = ?', $page_name)
				->loadFirst(new Page());
			
			if ( true === $page->exists() ) {
				$page->updateViewCount();
				TuneToUs::getDataModel()->save($page);
				
				$this->page = $page;
				$view = 'page';
			} else {
				header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
				$view = 'page-not-found';
			}
		} catch ( Exception $e ) { }
		
		$this->renderLayout($view);
		
		return true;
	}
	
	
	public function contactUsPost() {
		try {
			$contact_us_data = (array)$this->getParam('contact_us');
			
			/* Automatic form validation. */
			$validator = $this->buildValidator();
			$validator->load('contact_us')
				->setData($contact_us_data)
				->validate();
			
			$contact_us = new Contact_Us();
			$contact_us->setFromEmailAddress(er('from_email_address', $contact_us_data))
				->setFromName(er('from_name', $contact_us_data))
				->setSubject(er('subject', $contact_us_data))
				->setMessage(er('message', $contact_us_data));
			TuneToUs::getDataModel()->save($contact_us);
			
			$emailer = TuneToUs::getEmailer();
			$emailer->setFrom(er('from_email_address', $contact_us_data))
				->setFromName(er('from_name', $contact_us_data))
				->send('contact-us', er('from', $emailer->getConfig()), array(
					'from_name' => er('from_name', $contact_us_data),
					'subject' => er('subject', $contact_us_data),
					'message' => er('message', $contact_us_data)
				)
			);
			
			TuneToUs::getMessenger()->pushSuccess(Language::__('success_contact_sent'));
			$this->redirect($this->url('page/contactus'));
		} catch ( Exception $e ) {
			TuneToUs::getMessenger()->pushError(Language::__('error_form_validation_error'));
		}

		$this->getView()->setValidator($validator);
		$this->contact_us = $contact_us_data;
		
		$this->renderLayout('contact-us');
		
		return true;
	}
}