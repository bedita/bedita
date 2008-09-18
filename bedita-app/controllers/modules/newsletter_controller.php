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
	var $helpers 	= array('BeTree', 'BeToolbar', 'Paginator');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');

	var $uses = array('MailAddress', 'MailGroup','MailGroupAddress') ;
	
	var $paginate = array(
			'MailAddress' => array('limit' => 10, 'order' => array('MailAddress.email' => 'asc')),
			'MailGroupAddress' => array('limit' => 10, 
										'order' => array('MailAddress.email' => 'asc'),
										'contain' => array("MailAddress" => array("Card"))
										)
		);
	
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
	function subscribers($group_id=null) {
		
		if (!empty($group_id)) {
			$subscribers = $this->paginate("MailGroupAddress", array("mail_group_id" => $group_id));
		} else {
			$subscribers = $this->paginate("MailAddress");
		}
		$this->set("subscribers", $subscribers);
		$this->set("group_id", $group_id);
		$this->MailGroup->containLevel("minimum");
		$this->set("groups", $this->MailGroup->find("all", array("order" => "group_name ASC")));
		
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
	 
	 	
	public function saveSubscriber() {

		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));

		$this->Transaction->begin() ;
		
		if (!$this->MailAddress->save($this->data))
			throw new BeditaException(__("Error saving address", true), $this->MailAddress->validationErrors);
		
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Mail address saved", true)." - ".$this->data["MailAddress"]["email"]);
		$this->eventInfo("mail address [". $this->data["MailAddress"]["email"]."] saved");
		
	}
	
	public function changeStatusAddress() {
		$this->changeStatusObjects("MailAddress");
	}
	
	/**
	  * Manage groups.
	  */
	function groups() {

		
	 }
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"saveSubscriber"	=> 	array(
							"OK"	=> "/newsletter/viewsubscriber/".@$this->MailAddress->id,
							"ERROR"	=> "/newsletter/viewsubscriber/".@$this->MailAddress->id 
							),
			"changeStatusAddress"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=>  $this->referer() 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
	
}	

?>