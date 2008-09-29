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
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeMail');

	var $uses = array('Card', 'MailGroup', 'MailMessage') ;
	
//	var $paginate = array(
//			'MailAddress' => array('limit' => 10, 'order' => array('MailAddress.email' => 'asc')),
//			'MailGroupAddress' => array('limit' => 10, 
//										'order' => array('MailAddress.email' => 'asc'),
//										'contain' => array("MailAddress" => array("Card"))
//										)
//		);
	
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
		$this->viewObject($this->MailMessage, $id);
		$this->set("groupsByArea", $this->MailGroup->getGroupsByArea(null, null, $id));
	 }

	 /**
	  * Get all newsletters.
	  */
	function newsletters() {
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
	 }
	
	function save() {
		$this->checkWriteModulePermission();
		if (empty($this->data["MailGroup"]))
			$this->data["MailGroup"] = array();
		$this->Transaction->begin();
		$this->saveObject($this->MailMessage);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Mail message saved", true)." - ".$this->data["title"]);
		$this->eventInfo("mail message [". $this->data["title"]."] saved");
	}
	
	 /**
	  * Get all subscribers.
	  */
	function subscribers($group_id=null) {
//		
//		if (!empty($group_id)) {
//			$subscribers = $this->paginate("MailGroupAddress", array("mail_group_id" => $group_id));
//		} else {
//			$subscribers = $this->paginate("MailAddress");
//		}
//		$this->set("subscribers", $subscribers);
//		$this->set("selected_group_id", $group_id);
//		$this->MailGroup->containLevel("minimum");
//		$this->set("groups", $this->MailGroup->find("all", array("order" => "group_name ASC")));
//		
	 }

	 /**
	  * Get subscriber detail.
	  * If id is null, empty document
	  *
	  * @param integer $id
	  */
	function viewsubscriber($id = null) {

//		$mailAddress = null;
//		
//		if (!empty($id)) {
//			if( !($mailAddress = $this->MailAddress->findById($id)) ) {
//				 throw new BeditaException(sprintf(__("Error loading subscriber: %d", true), $id));
//			}
//		}
//
//		$this->set("groupsByArea", $this->MailGroup->getGroupsByArea(null, $id));
//		$this->set("subscriber", $mailAddress);
	 }
	 
	 	
	public function saveSubscriber() {

//		$this->checkWriteModulePermission();
//		if(empty($this->data)) 
//			throw new BeditaException( __("No data", true));
//
//		$this->Transaction->begin() ;
//		
//		if (!$this->MailAddress->save($this->data))
//			throw new BeditaException(__("Error saving address", true), $this->MailAddress->validationErrors);
//		
//		$this->Transaction->commit() ;
//		$this->userInfoMessage(__("Mail address saved", true)." - ".$this->data["MailAddress"]["email"]);
//		$this->eventInfo("mail address [". $this->data["MailAddress"]["email"]."] saved");
		
	}
	
	public function changeStatusAddress() {
//		$this->changeStatusObjects("MailAddress");
	}
	
	public function addAddressToGroup($old_group_id=null) {
//		$this->checkWriteModulePermission();
//		if(!empty($this->params['form']['objects_selected'])) {
//			
//			$this->Transaction->begin();
//			
//			$groupname = $this->MailGroup->field("group_name", array("id" => $this->params["form"]["destination"]));
//			
//			$data["MailGroupAddress"]["mail_group_id"] = $this->params["form"]["destination"];
//			$data["MailGroupAddress"]["status"] = "confirmed";
//			
//			foreach ($this->params['form']['objects_selected'] as $address_id) {
//				
//				// move to group => delete from previous group
//				if ($this->params["form"]["operation"] == "move" && !empty($old_group_id)) {
//					$this->MailAddress->MailGroupAddress->deleteAll(array(
//																		"mail_address_id" => $address_id, 
//																		"mail_group_id" => $old_group_id	
//																		)
//																	);
//				}
//				
//				// save if not already exists
//				$join_id = $this->MailAddress->MailGroupAddress->field("id", "mail_address_id=".$address_id." AND mail_group_id=".$this->params["form"]["destination"] );
//				
//				if (!$join_id) {
//					$data["MailGroupAddress"]["mail_address_id"] = $address_id;
//					$data["MailGroupAddress"]["hash"] = md5($address_id . microtime() . $groupname);
//					$this->MailAddress->MailGroupAddress->create();
//					if (!$this->MailAddress->MailGroupAddress->save($data))
//						throw new BeditaException(__("Error adding subscriber " . $address_id . " in group " . $data["MailGroupAddress"]["mail_group_id"], true));
//				}
//				
//				
//			}
//			
//			$this->Transaction->commit();
//			$this->userInfoMessage(__("Subscribers associated to recipient group", true) . " - " . $groupname);
//			$this->eventInfo("Subscribers associated to recipient group " . $this->params["form"]["destination"]);
//		}
	}
	
	public function deleteAddress() {
//		$this->checkWriteModulePermission();
//		$addressToDel = null;
//		if(!empty($this->params['form']['objects_selected'])) {
//			$addressToDel = $this->params['form']['objects_selected'];
//			$addressToDelList = implode(",",$this->params["form"]["objects_selected"]);
//		} else if (!empty($this->data["MailAddress"]["id"])) {
//			$addressToDel = $addressToDelList = $this->data["MailAddress"]["id"];
//		} 
//			
//		if (!empty($addressToDel)) {
//			$this->Transaction->begin();
//			if (!$this->MailAddress->deleteAll(array("MailAddress.id" => $addressToDel)))
//				throw new BeditaException(__("Error deleting address", true));
//			$this->Transaction->commit();
//			$this->userInfoMessage(__("Subscribers deleted", true) . " - " . $addressToDelList);
//			$this->eventInfo("Subscribers deleted, ids deleted:  " . $addressToDelList);
//		}
	}
	
	/**
	  * Manage groups.
	  */

	function templates() {

		
	 }

	function viewtemplate() {

		
	 }
	
	function invoices() {

		
	 }
	 
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"save"	=> 	array(
							"OK"	=> "/newsletter/view/".@$this->MailMessage->id,
							"ERROR"	=> $this->referer() 
							), 
//			"saveSubscriber"	=> 	array(
//							"OK"	=> "/newsletter/viewsubscriber/".@$this->MailAddress->id,
//							"ERROR"	=> "/newsletter/viewsubscriber/".@$this->MailAddress->id 
//							),
			"changeStatusAddress"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=>  $this->referer() 
							),
			"addAddressToGroup"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=>  $this->referer() 
							),
			"deleteAddress"	=> 	array(
							"OK"	=> "/newsletter/subscribers",
							"ERROR"	=>  "/newsletter/subscribers" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
	
}	

?>