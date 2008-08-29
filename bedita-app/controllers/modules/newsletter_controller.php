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
 * @author 			andrea@chialab.it
 */

class NewsletterController extends ModulesController {

	var $name = 'Newsletter';
	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array('BEObject','Tree', 'ObjectCategory') ;
	protected $moduleName = 'newsletter';
	
    public function index() {
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
	 }

	 /**
	  * Get newsletter detail.
	  * If id is null, empty document
	  *
	  * @param integer $id
	  */
	function view($id = null) {
		
	 }

	 /**
	  * Get all newsletters.
	  */
	function newsletters() {
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
	 }
	 	
	 /**
	  * Get all subscribers.
	  */
	function subscribers() {
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
	 }
	 
	 
}	

?>