<?php

class PagesController extends FrontendController {

	var $helpers 	= array();
	var $uses = array() ;
	
	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		// uncomment to use ctp file instead of tpl file for templates
//		$this->view = "View"; 
	}
}

?>