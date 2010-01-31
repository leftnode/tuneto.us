<?php


class Uploader {
	protected $fileName = NULL;
	protected $uploadData = array();
	protected $destinationDirectory = NULL;
	
	protected $allowOverwrite = false;
	
	const ERROR_UPLOAD_NO_DATA = 'No upload data was present.';
	const ERROR_UPLOAD_FILE_EXISTS = 'The destination file already exists.';
	const ERROR_UPLOAD_FAILED = 'Uploading the file failed. It could not be created properly.';
	const ERROR_UPLOAD_DESTINATION_DOES_NOT_EXIST = 'The destination for this upload does not exist.';
	const ERROR_UPLOAD_INI_SIZE = 'Your file is too large!';
	const ERROR_UPLOAD_FORM_SIZE = 'Your file is too large!';
	const ERROR_UPLOAD_PARTIAL = 'Only part of your file was uploaded!';
	const ERROR_UPLOAD_NO_FILE = 'No file was present to be uploaded.';
	const ERROR_UPLOAD_NO_TMP_DIR = 'A temporary directory does not exist to copy your upload to.';
	const ERROR_UPLOAD_CANT_WRITE = 'The temporary upload file can not be created.';
	const ERROR_UPLOAD_EXTENSION = 'This type of upload is not allowed.';
	const ERROR_UPLOAD_INCORRECT_TYPE = 'You are attempting to upload an incorrect file type.';

	public function __construct($uploadData = array(), $allow_overwrite=false) {
		$this->setUploadData($uploadData);
		$this->setAllowOverwrite($allow_overwrite);
	}

	public function __destruct() {
		
	}
	
	public function getFilename() {
		return $this->fileName;
	}
	
	public function setAllowOverwrite($overwrite) {
		$this->allowOverwrite = $overwrite;
		return $this;
	}
	
	public function setUploadData($uploadData) {
		if ( false === is_array($uploadData) || 0 == count($uploadData) ) {
			throw new TuneToUs_Exception(self::ERROR_UPLOAD_NO_DATA);
		}

		$error = NULL;
		switch ( $uploadData['error'] ) {
			case UPLOAD_ERR_INI_SIZE: {
				$error = self::ERROR_UPLOAD_INI_SIZE;
				break;
			}
			
			case UPLOAD_ERR_FORM_SIZE: {
				$error = self::ERROR_UPLOAD_FORM_SIZE;
				break;
			}
			
			case UPLOAD_ERR_PARTIAL: {
				$error = self::ERROR_UPLOAD_PARTIAL;
				break;
			}
			
			case UPLOAD_ERR_NO_FILE: {
				$error = self::ERROR_UPLOAD_NO_FILE;
				break;
			}
			
			case UPLOAD_ERR_NO_TMP_DIR: {
				$error = self::ERROR_UPLOAD_NO_TMP_DIR;
				break;
			}
			
			case UPLOAD_ERR_CANT_WRITE: {
				$error = self::ERROR_UPLOAD_CANT_WRITE;
				break;
			}
			
			case UPLOAD_ERR_EXTENSION: {
				$error = self::ERROR_UPLOAD_EXTENSION;
				break;
			}
		}
		
		if ( false === empty($error) ) {
			throw new TuneToUs_Exception($error);
		}
		
		$this->uploadData = $uploadData;
		$this->fileName = er('name', $uploadData);
		$this->fileName = uniqid('ttu-') . '-' . $this->fileName;
		
		return true;
	}
	
	public function setDestinationDirectory($dest_dir) {
		$dest_dir = DIR_PRIVATE . $dest_dir;
		if ( false === is_dir($dest_dir) ) {
			throw new TuneToUs_Exception(self::ERROR_UPLOAD_DESTINATION_DOES_NOT_EXIST);
		}
		
		/* Add the DIRECTORY_SEPARATOR to the last character of the dest_dir if its not one already. */
		$dest_dir = rtrim($dest_dir, DS) . DS;
		
		$this->destinationDirectory = $dest_dir;
		return $this;
	}
	
	public function getDestinationDirectory() {
		return $this->destinationDirectory;
	}
	
	public function upload() {
		if ( 0 == count($this->uploadData) ) {
			throw new TuneToUs_Exception(self::ERROR_UPLOAD_NO_DATA);
		}
		
		$final_file = $this->destinationDirectory . $this->fileName;
		if ( true === is_file($final_file) && false === $this->allowOverwrite ) {
			throw new TuneToUs_Exception(self::ERROR_UPLOAD_FILE_EXISTS);
		}
		
		if ( false === @move_uploaded_file($this->uploadData['tmp_name'], $final_file) ) {
			throw new TuneToUs_Exception(self::ERROR_UPLOAD_FAILED);
		}
		
		return true;
	}
}