<?php

class Image {
	private $image_location;
	private $image = NULL;
	
	public function __construct($image_location) {
		$this->image_location = $image_location;
		$this->load();
	}

	public function __destruct() {
		if ( true === is_resource($this->image) ) {
			imagedestroy($this->image);
		}
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
		
		
		$init_width = imagesx($this->image);
		$init_height = imagesy($this->image);

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
		imagecopyresampled($resized_image, $this->image, 0, 0, 0, 0, $width, $height, $init_width, $init_height);
		$this->image = $resized_image;
		
		return $this;
	}
	
	public function writeJpg($image_destination=NULL) {
		$this->write('jpg', $image_destination);
	}
	
	public function writePng($image_destination=NULL) {
		imagecolortransparent($this->image, imagecolorallocatealpha($this->image, 0, 0, 0, 0));
		$this->write('png', $image_destination);
	}
	
	private function write($type, $image_destination=NULL) {
		if ( true === empty($image_destination) ) {
			$image_destination = $this->image_location;
		}
		
		$type = strtolower(trim($type));
		
		switch ( $type ) {
			case 'jpeg':
			case 'jpg': {
				imagejpeg($this->image, $image_destination, 100);
				
				break;
			}
			
			case 'png': {
				imagesavealpha($this->image, true);
				imagepng($this->image, $image_destination, 0);
				break;
			}
		}
		
		return true;
	}
	
	private function load() {
		if ( false === is_file($this->image_location) ) {
			return false;
		}
		
		$file_type = mime_content_type($this->image_location);
		$file_name = $this->image_location;

		$this->image = NULL;
		switch ( $file_type ) {
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg': {
				$this->image = imagecreatefromjpeg($file_name);
				break;
			}
			
			case 'image/x-png':
			case 'image/png': {
				$this->image = imagecreatefrompng($file_name);
				break;
			}
			
			case 'image/gif': {
				$this->image = imagecreatefromgif($file_name);
				imagealphablending($this->image, false);
				imagesavealpha($this->image, true);
				break;
			}
		}
		
		if ( false === is_resource($this->image) ) {
			return false;
		}
		
		return true;
	}
}