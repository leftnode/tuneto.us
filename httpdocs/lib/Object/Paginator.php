<?php

class Paginator {
	private $page = 0;
	private $per_page = 0;
	private $total = 0;
	
	private $iterator = NULL;
	
	public function __construct() {
		$this->per_page = 10;
	}
	
	public function set($iterator) {
		$this->iterator = $iterator->fetch();
		return $this;
	}
	
	public function setPerPage($per_page) {
		$this->per_page = intval($per_page);
		return $this;
	}
	
	public function get() {
		$page = intval(er('page', $_GET));
		$this->page = ( 0 == $page ? 1 : $page );

		$this->total = $this->iterator->length();
		$this->iterator->limit($this->per_page)->page($this->page);
		
		return $this->iterator;
	}
	
	public function getPageList() {
		$view = API::buildView();
		
		$page_last = ceil($this->total / $this->per_page);
		
		if ( $this->page > $page_last ) {
			$this->page = $page_last;
		}
		
		$view->page = $this->page;
		$view->page_last = $page_last;
	
		return $view->render('root', 'paginator-link-list');
	}
}