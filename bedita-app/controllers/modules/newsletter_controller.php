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
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');

	var $uses = array('MailAddress', 'MailGroup') ;
	
	protected $moduleName = 'newsletter';
	
    public function index() {
		
		//$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
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
		
		//$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
	 }

	 /**
	  * Get subscriber detail.
	  * If id is null, empty document
	  *
	  * @param integer $id
	  */
	function viewsubscriber($id = null) {

		$mailAddress = null;
		
		if (!empty($id)) {
			
			if( !($mailAddress = $this->MailAddress->findById($id)) ) {
				 throw new BeditaException(sprintf(__("Error loading subscriber: %d", true), $id));
			}

		}

		$this->set("groupsByArea", $this->MailGroup->getGroupsByArea(null, $id));
		
		$this->set("subscriber", $mailAddress);
	 }
	 
	 /**
	  * Manage groups.
	  */
	function groups() {

		
	 }
	 
	
	public function saveSubscriber() {

		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));

		$this->Transaction->begin() ;
		
		if (!$this->MailAddress->save($this->data))
			throw new BeditaException(__("Error saving address", true), $this->MailAddress->validationErrors);
		
		$this->Transaction->commit() ;
		
	}
	
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"saveSubscriber"	=> 	array(
							"OK"	=> "/newsletter/viewsubscriber/".@$this->MailAddress->id,
							"ERROR"	=> "/newsletter/viewsubscriber/".@$this->MailAddress->id 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
	
}	

?>