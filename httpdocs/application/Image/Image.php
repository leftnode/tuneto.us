<?php

/**
 * Displays an image directly to the browser.
 * 
 * @author vmc <vmc@leftnode.com>
 */
class Image_Controller extends Artisan_Controller {
	
	/// Do not display any layout with the image.
	protected $layout = NULL;
	
	/**
	 * Display large images to the browser.
	 * 
	 * @param integer $image_id The ID of the image to display.
	 */
	public function viewLargeGet($image_id) {
		$this->viewGet($image_id, IMAGE_LARGE);
	}
	
	/**
	 * Display thumbnail images to the browser.
	 * 
	 * @param integer $image_id The ID of the image to display.
	 */
	public function viewThumbnailGet($image_id) {
		$this->viewGet($image_id, IMAGE_THUMBNAIL);
	}
	
	/**
	 * Display micro images to the browser.
	 * 
	 * @param integer $image_id The ID of the image to display.
	 */
	public function viewMicroGet($image_id) {
		$this->viewGet($image_id, IMAGE_MICRO);
	}
	
	/**
	 * Display images to the browser. If the image does not exist,
	 * a generic image is displayed in the same size.
	 * 
	 * @param integer $image_id The ID of the image to display.
	 * @param string $size The size of the image to display.
	 */
	private function viewGet($image_id, $size) {
		try {
			$image_id = intval($image_id);
			
			$image = TuneToUs::getDataModel()
				->where('image_id = ?', $image_id)
				->loadFirst(new Image());
			
			$image_exists = false;
			if ( true === $image->exists() ) {
				$directory = $image->getDirectory();
				
				switch ( $size ) {
					case IMAGE_LARGE: {
						$filename = $image->getLarge();
						break;
					}
					
					case IMAGE_THUMBNAIL: {
						$filename = $image->getThumbnail();
						break;
					}
					
					case IMAGE_MICRO:
					default: {
						$filename = $image->getMicro();
						break;
					}
				}
				
				$filepath = DIR_PRIVATE . $directory . DS . $filename;
				if ( true === is_file($filepath) ) {
					$image_exists = true;
				}
			}
			
			if ( false === $image_exists ) {
				switch ( $size ) {
					case IMAGE_LARGE: {
						$filepath = DIR_SITE_ROOT . DIR_IMAGE . 'image-not-found-large.jpg';
						break;
					}
					
					case IMAGE_THUMBNAIL: {
						$filepath = DIR_SITE_ROOT . DIR_IMAGE . 'image-not-found-thumbnail.jpg';
						break;
					}
					
					case IMAGE_MICRO:
					default: {
						$filepath = DIR_SITE_ROOT . DIR_IMAGE . 'image-not-found-micro.jpg';
						break;
					}
				}
			}
			
			$image = imagecreatefromjpeg($filepath);
			header('Content-Type: image/jpeg');
			imagejpeg($image, NULL, 100);
		} catch ( Exception $e ) { }
		
		exit;
	}
}