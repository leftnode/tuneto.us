<?php


class Uploader {
	private $directory = NULL;
	private $filename = NULL;
	private $upload_directory = NULL;
	private $data = array();
	private $overwrite = true;

	public function __construct() {

	}

	public function __destruct() {
		
	}
	
	public function setOverwrite($overwrite) {
		$this->overwrite = $overwrite;
		return $this;
	}
	
	public function setDirectory($directory) {
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$this->directory = $directory;
		return $this;
	}
	
	public function setData($data) {
		if ( false === is_array($data) || 0 == count($data) ) {
			throw new Exception(Language::__(''));
		}

		$error = NULL;
		switch ( $data['error'] ) {
			case UPLOAD_ERR_INI_SIZE: {
				$error = Language::__('');
				break;
			}
			
			case UPLOAD_ERR_FORM_SIZE: {
				$error = Language::__('');
				break;
			}
			
			case UPLOAD_ERR_PARTIAL: {
				$error = Language::__('');
				break;
			}
			
			case UPLOAD_ERR_NO_FILE: {
				$error = Language::__('');
				break;
			}
			
			case UPLOAD_ERR_NO_TMP_DIR: {
				$error = Language::__('');
				break;
			}
			
			case UPLOAD_ERR_CANT_WRITE: {
				$error = Language::__('');
				break;
			}
			
			case UPLOAD_ERR_EXTENSION: {
				$error = Language::__('');
				break;
			}
		}
		
		if ( false === empty($error) ) {
			throw new Exception($error);
		}
		
		$this->data = $data;
		
		$filename = $data['name'];
		$filename = str_replace(' ', '-', $filename);
		$filename = substr(sha1(mt_rand(0, 100000)), 0, 16) . '-' . $filename;
		$this->setFilename($filename);
		
		return $this;
	}

	public function getDirectory() {
		return $this->directory;
	}
	
	public function getFilename() {
		return $this->filename;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function upload() {
		$data = $this->getData();
		if ( 0 === count($data) || true === empty($data) ) {
			throw new Exception(Language::__(''));
		}
		
		$directory = $this->getDirectory();
		$filename = $this->getFilename();
		
		$filepath = $directory . $filename;
		
		if ( false === @move_uploaded_file($data['tmp_name'], $filepath) ) {
			throw new Exception(Language::__(''));
		}
		
		return true;
	}
	
	protected function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}
}