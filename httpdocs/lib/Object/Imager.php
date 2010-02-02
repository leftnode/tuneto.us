<?php

class Imager {
	private $imagepath = NULL;
	
	private $directory = NULL;
	private $filename = NULL;

	private $image = NULL;
	
	const TYPE_JPG = 'jpg';
	const TYPE_GIF = 'gif';
	const TYPE_PNG = 'png';
	
	public function __construct($imagepath) {
		$this->setImagepath($imagepath)->load();
	}

	public function __destruct() {
		if ( true === is_resource($this->image) ) {
			imagedestroy($this->image);
		}
	}
	
	public function setImagepath($imagepath) {
		if ( false === is_file($imagepath) ) {
			throw new Exception(Language::__(''));
		}
		
		$this->imagepath = $imagepath;
		return $this;
	}
	
	public function setDirectory($directory) {
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$this->directory = $directory;
		return $this;
	}
	
	public function setImageResource($image) {
		$this->image = $image;
		return $this;
	}
	
	public function getImagepath() {
		return $this->imagepath;
	}
	
	public function getDirectory() {
		return $this->directory;
	}
	
	public function getFilename() {
		return $this->filename;
	}
	
	public function getImageResource() {
		return $this->image;
	}
	
	
	public function resizeTo($width, $height) {
		/*
		$width = intval($width);
		$height = intval($height);
		
		$init_width = imagesx($this->image);
		$init_height = imagesy($this->image);
		
		$resized_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($resized_image, $this->image, 0, 0, 0, 0, $width, $height, $init_width, $init_height);
		$this->image = $resized_image;
		*/
		
		return true;
	}
	
	public function resize($max_width, $max_height) {
		$image = $this->getImageResource();
		
		$init_width = imagesx($image);
		$init_height = imagesy($image);

		/* If it's wider than taller, we want to use the max width. */
		if ( $init_width > $max_width && $init_height > $max_height ) {
			if ( $init_width > $init_height ) {
				$ratio = $max_width / $init_width;
				$height = intval($init_height * $ratio);
				$width = intval($max_width);
			} else {
				$ratio = $max_height / $init_height;
				$width = intval($init_width * $ratio);
				$height = intval($max_height);
			}
		} else {
			$width = $init_width;
			$height = $init_height;
		}
		
		$resized_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $width, $height, $init_width, $init_height);
		$this->setImageResource($resized_image);
		
		return $this;
	}
	
	public function writeJpg($filename=NULL) {
		$this->write(self::TYPE_JPG, $filename);
	}
	
	public function writePng($filename=NULL) {
		$image = $this->getImageResource();
		imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 0));
		$this->setImageResource($image);
		$this->write(self::TYPE_PNG, $filename);
	}
	
	private function write($type, $filename=NULL) {
		$imagepath = $this->getImagepath();
		
		if ( true === empty($filename) ) {
			$filename = basename($imagepath);
		}
		
		$filename = preg_replace('/\.[a-z0-9]+$/i', NULL, $filename) . '.' . $type;
		
		$directory = $this->getDirectory();
		$destination = $directory . $filename;
		
		$image = $this->getImageResource();
		
		$this->setFilename($filename);
		
		switch ( $type ) {
			case self::TYPE_JPG: {
				imagejpeg($image, $destination, 100);
				break;
			}
			
			case self::TYPE_PNG: {
				imagesavealpha($image, true);
				imagepng($image, $destination, 0);
				break;
			}
		}
		
		return true;
	}
	
	private function load() {
		$imagepath = $this->getImagepath();
		
		if ( false === is_file($imagepath) ) {
			throw new Exception(Language::__(''));
		}
		
		$mimetype = mime_content_type($imagepath);
		$image = NULL;
		
		switch ( $mimetype ) {
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg': {
				$image = imagecreatefromjpeg($imagepath);
				break;
			}
			
			case 'image/x-png':
			case 'image/png': {
				$image = imagecreatefrompng($imagepath);
				break;
			}
			
			case 'image/gif': {
				$image = imagecreatefromgif($imagepath);
				imagealphablending($image, false);
				imagesavealpha($image, true);
				break;
			}
		}
		
		if ( false === is_resource($image) ) {
			throw new Exception(Language::__(''));
		}
		
		$this->setImageResource($image);
		
		return true;
	}
	
	private function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}
	
	
}