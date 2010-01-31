<?php

require_once 'DataModeler/DataObject.php';

class Track extends DataObject {
	public function getFormattedLength() {
		$length = $this->getLength();
		
		$hours = floor($length / 3600);
		if ( $hours > 0 ) {
			$length -= $hours * 3600;
		}
		
		$minutes = floor($length / 60);
		if ( $minutes > 0 ) {
			$length -= $minutes * 60;
		}
		
		$seconds = floor($length % 60);
		
		$format = $seconds . 's';
		if ( $minutes > 0 ) {
			$format = $minutes . 'm ' . $format;
		}
		
		if ( $hours > 0 ) {
			$format = $hours . 'h ' . $format;
		}
		
		return $format;
	}
}