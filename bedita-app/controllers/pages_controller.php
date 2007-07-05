<?php
/* SVN FILE: $Id: pages_controller.php 2951 2006-05-25 22:12:33Z phpnut $ */

/**
 *
 * PHP version 5
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
 * Short description for class.
 *
 * Controller principale, d'entrata
 * 
 */
class PagesController extends AppController {
	var $name = 'Pages';

	var $helpers = array('Bevalidation');
	var $components = array('BeAuth');

	// This controller does not use a model
	 var $uses = null;

	/**
	 * Home
	 *
	 */
	 function home() {
		// Esegue il render della pagina
		$this->render("index");
	 }

	 function display() {
		// Esegue il render della pagina
		$this->render("index");
	 }
}
	