<?php

require_once 'DataModeler/DataObject.php';

class Page extends DataObject {
	public function updateViewCount() {
		$view_count = $this->getViewCount();
		$this->setViewCount(++$view_count);
		return $this;
	}
}