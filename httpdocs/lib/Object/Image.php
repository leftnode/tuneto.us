<?php

class Image {
	private $location;
	private $image = NULL;
	
	public function __construct($location) {
		$this->setLocation($location)
			->load();
	}

	public function __destruct() {
		if ( true === is_resource($this->image) ) {
			imagedestroy($this->image);
		}
	}
	
	public function setLocation($location) {
		$this->location = $location;
		return $this;
	}
	
	public function setImageResource($image) {
		$this->image = $image;
		return $this;
	}
	
	public function getLocation() {
		return $this->location;
	}
	
	public function getImageResource() {
		return $this->image;
	}
	
	
	public function resizeTo($width, $height) {
		$width = intval($width);
		$height = intval($height);
		
		$init_width = imagesx($this->image);
		$init_height = imagesy($this->image);
		
		$resized_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($resized_image, $this->image, 0, 0, 0, 0, $width, $height, $init_width, $init_height);
		$this->image = $resized_image;
		
		return true;
	}
	
	public function resize($max_width, $max_height) {
		$image = $this->getImageResource();
		
		$init_width = imagesx($image);
		$init_height = imagesy($image);

		// If it's wider than taller, we want to use the max width
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
	
	public function writeJpg($location=NULL) {
		$this->write('jpg', $location);
	}
	
	public function writePng($location=NULL) {
		$image = $this->getImageResource();
		imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 0));
		$this->setImageResource($image);
		$this->write('png', $location);
	}
	
	private function write($type, $location=NULL) {
		if ( true === empty($location) ) {
			$location = $this->getLocation();
		}
		
		echo($location);
		echo '<br>';
		
		$type = strtolower(trim($type));
		$image = $this->getImageResource();
		
		$imagedir = rtrim(dirname($location), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$imagename = preg_replace('/\.[a-z0-9]+$/i', NULL, basename($location));
		$location = $imagedir . $imagename . '.' . $type;
		
		switch ( $type ) {
			case 'jpeg':
			case 'jpg': {
				imagejpeg($image, $location, 100);
				break;
			}
			
			case 'png': {
				imagesavealpha($image, true);
				imagepng($image, $location, 0);
				break;
			}
		}
		
		$this->setLocation($imagename);
		
		return true;
	}
	
	private function load() {
		$location = $this->getLocation();
		if ( false === is_file($location) ) {
			return false;
		}
		
		$mimetype = mime_content_type($location);
		$image = NULL;
		
		switch ( $mimetype ) {
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg': {
				$image = imagecreatefromjpeg($location);
				break;
			}
			
			case 'image/x-png':
			case 'image/png': {
				$image = imagecreatefrompng($location);
				break;
			}
			
			case 'image/gif': {
				$image = imagecreatefromgif($location);
				imagealphablending($image, false);
				imagesavealpha($image, true);
				break;
			}
		}
		
		if ( false === is_resource($image) ) {
			return false;
		}
		
		$this->setImageResource($image);
		
		return true;
	}
}