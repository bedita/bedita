<?php

/**
 *
 * @filesource
 * @copyright		
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 * @author
 */

/**
 * Home pages
 * 
 */
class PagesController extends AppController {
	var $name = 'Pages';
	var $helpers = array();
	var $components = array('Session', 'Cookie');
	var $uses = null;

	 function display() {
	 	$this->action = "index" ;
	 }
	 
	 function changePasswd() {
	 }
	
	function changeLang($lang = null) {
		if (!empty($lang)) {
			$this->Session->write('Config.language', $lang);
			$this->Cookie->write('bedita.lang', $lang, null, '+350 day'); 
		}
		$this->redirect($this->referer(null, true));
	}
	 
	 function login() {
	 }
	 
}

?>