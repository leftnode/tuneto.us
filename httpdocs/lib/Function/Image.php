<?php

function ttu_create_image($directory, $filename, $width, $height, $type) {
	$width = intval($width);
	$height = intval($height);
	
	$filepath = DIR_PRIVATE . $directory . DS . $filename;
	
	$image = new Imager($filepath);
	$image->resize($width, $height)
		->setDirectory(DIR_PRIVATE . $directory)
		->writeJpg($type . '-' . $filename);
	$image_filename = $image->getFilename();
	return $image_filename;
}

function ttu_create_image_record($directory, $filename) {
	$fullsize  = ttu_create_image($directory, $filename, 1000, 1000, IMAGE_FULLSIZE);
	$large     = ttu_create_image($directory, $filename, 300, 300, IMAGE_LARGE);
	$thumbnail = ttu_create_image($directory, $filename, 96, 96, IMAGE_THUMBNAIL);
	$micro     = ttu_create_image($directory, $filename, 36, 36, IMAGE_MICRO);
	
	$image = new Image();
	$image->setDirectory($directory)
		->setFullsize($fullsize)
		->setLarge($large)
		->setThumbnail($thumbnail)
		->setMicro($micro);
	
	$image_id = TuneToUs::getDataModel()->save($image);
	
	return $image_id;
}