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
 * @author 			giangi@qwerg.com
 */

/**
 * Home pages
 * 
 */
class PagesController extends AppController {
	var $name = 'Pages';

	var $helpers = array();
	var $components = array();
	var $uses = null;

	/**
	 * Home
	 */
	 function home() {
	 	$this->action = "index" ;
	 }

	 function display() {
	 	$this->action = "index" ;
	 }
	 
	 function changePasswd() {
	 }
	 
	 function login() {
	 }

	 function logout() {
	 	$this->action = "login" ; //same login page
	 }
	 
}
	