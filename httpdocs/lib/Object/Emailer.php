<?php

require_once 'lib/Object/Phpmailer.php';

class Emailer {
	private $config = array();
	
	private $subject = NULL;
	private $body = NULL;
	private $alt_body = NULL;
	
	private $from = NULL;
	private $from_name = NULL;
	
	private $template = NULL;
	
	public function __construct(array $config) {
		$this->setConfig($config)
			->setTemplate(new Artisan_Template());
	}
	
	public function __destruct() {
		unset($this->subject, $this->body, $this->alt_body, $this->template);
	}
	
	public function setConfig(array $config) {
		$this->config = $config;
		return $this;
	}
	
	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}
	
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}
	
	public function setAltBody($alt_body) {
		$this->alt_body = $alt_body;
		return $this;
	}
	
	public function setFrom($from) {
		$this->from = $from;
		return $this;
	}
	
	public function setFromName($from_name) {
		$this->from_name = $from_name;
		return $this;
	}
	
	
	
	public function getConfig() {
		return $this->config;
	}
	
	public function getSubject() {
		return $this->subject;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function getAltBody() {
		return $this->alt_body;
	}
	
	public function getFrom() {
		return $this->from;
	}
	
	public function getFromName() {
		return $this->from_name;
	}
	
	public function getTemplate() {
		return $this->template;
	}
	
	
	
	public function send($email_name, $to_address, $replace_list=array()) {
		$this->load($email_name);
		
		if ( true === empty($this->subject) || true === empty($this->body) ) {
			return false;
		}
		
		if ( count($replace_list) > 0 ) {
			$body = $this->getTemplate()
				->setReplaceList($replace_list)
				->setTemplateCode($this->getBody())
				->parse();
			
			$this->setBody($body);
			
			$alt_body = $this->getTemplate()
				->setTemplateCode($this->getAltBody())
				->parse();
			
			$this->setAltBody($alt_body);
		}
		
		if ( false === is_array($to_address) ) {
			$to_address = array($to_address);
		}
		
		try {
			$config = $this->getConfig();
			
			$from = $this->getFrom();
			$from = ( true === empty($from) ? $config['from'] : $from );
			
			$from_name = $this->getFromName();
			$from_name = ( true === empty($from_name) ? $config['from_name'] : $from_name );
			
			$email = new PHPMailer();
			$email->IsHTML($config['html']);
			$email->From     = $from;
			$email->FromName = $from_name;
			$email->Subject  = $this->getSubject();
			$email->Body     = $this->getBody();
			
			if ( true === $config['html'] ) {
				$email->AltBody = $this->getAltBody();
			}

			foreach ( $to_address as $address ) {
				$email->AddAddress($address);
			}
			
			$email->Send();
		} catch ( phpmailerException $e ) {
			return false;
		} catch ( Exception $e ) {
			return false;
		}
		
		return true;
	}
	
	private function load($email_name) {
		$config = $this->getConfig();
		$email = TuneToUs::getDataModel()
			->where('name = ?', $email_name)
			->where('locale = ?', $config['locale'])
			->loadFirst(new Email());
		
		if ( true === $email->exists() ) {
			$this->setSubject($email->getSubject())
				->setBody($email->getBody())
				->setAltBody($email->getAltBody());
		}
	
		return true;
	}
	
	private function setTemplate(Artisan_Template $template) {
		$this->template = $template;
		return $this;
	}
}