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

	var $uses = array('BEObject', 'Card', 'MailGroup', 'MailMessage', 'MailTemplate') ;
	
	protected $moduleName = 'newsletter';
	
    public function index() {
		$this->MailMessage->recursive = -1;
		
		$sentThisMonth = $this->MailMessage->find("count", array(
				"conditions" => array(
					"mail_status" => "sent",
					"start_sending LIKE '" . date("Y-m-%", time()) . "'"
				)
			)
		);
		
		$sentThisYear = $this->MailMessage->find("count", array(
				"conditions" => array(
					"mail_status" => "sent",
					"start_sending LIKE '" . date("Y-%", time()) . "'"
				)
			)
		);
		
		$queued = $this->MailMessage->find("count", array(
				"conditions" => array( "mail_status" => array("unsent", "pending") )
			)
		);
		
		$sentTotal = $this->MailMessage->find("count", array(
				"conditions" => array( "mail_status" => "sent" )
			)
		);
		
		$this->MailTemplate->BEObject->recursive = -1;
		$templates = $this->MailTemplate->BEObject->find("all", array(
				"conditions" => array("object_type_id" => Configure::read("objectTypes.mailtemplate.id")),
				"fields" => array("id", "title"),
				"order" => "title ASC"
			)
		);
		
		$this->MailMessage->containLevel("minimum");
		$recentMsg = $this->MailMessage->find("all", array(
				"conditions" => array("BEObject.object_type_id" => Configure::read("objectTypes.mailmessage.id")),
				"order" => "MailMessage.start_sending DESC",
				"limit"	=> 5
			)
		);

		$this->set("sentThisMonth", $sentThisMonth);
		$this->set("sentThisYear", $sentThisYear);
		$this->set("queued", $queued);
		$this->set("sentTotal", $sentTotal);
		$this->set("templates", $templates);
		$this->set("recentMsg", $recentMsg);

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
		
		$areaModel = ClassRegistry::init("Area");				
		$pub = $areaModel->find("all", array("contain" => array("BEObject")));
		foreach ($pub as $key => $p) {
			$temp = $this->BeTree->getChildren($p["id"], null, array(Configure::read("objectTypes.mailtemplate.id")));
			$pub[$key]["MailTemplate"] = $temp["items"];
			// set cssUrl to use with template
			if (!empty($this->viewVars["relObjects"]["template"])) {
				foreach ($temp["items"] as $t) {
					if ($t["id"] == $this->viewVars["relObjects"]["template"][0]["id"])	
						$this->set("cssUrl", $p["public_url"] . "/css/" . Configure::read("newsletterCss"));
				}
			} 
		}
		$this->set("templateByArea", $pub);
	 }

	 /**
	  * Get all newsletters.
	  */
	function newsletters($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$types = array(Configure::read("objectTypes.mailmessage.id"));
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		foreach ($this->viewVars["objects"] as $key => $obj) {
			$msg = $this->MailMessage->find("first", array(
												"conditions" => array("MailMessage.id" => $obj["id"]),
												"contain"	=> array("BEObject" => array("RelatedObject"))
												)
			);
			if (!empty($msg["RelatedObject"])) {
				$this->modelBindings["MailTemplate"] = array("BEObject");
				$msg["relations"] = $this->objectRelationArray($msg['RelatedObject']);
				unset($this->modelBindings["MailTemplate"]);
			}

			$this->viewVars["objects"][$key] = $msg;
		}		
	 }
	
	function save() {
		$this->saveMessage();
 		$this->userInfoMessage(__("Mail message saved", true)." - ".$this->data["title"]);
		$this->eventInfo("mail message [". $this->data["title"]."] saved");
	}
	
	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("MailMessage");
		$this->userInfoMessage(__("Mail message deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("mail messages $objectsListDeleted deleted");
	}

	public function mailGroups() {
		$this->MailGroup->recursive = -1;
		$mg = $this->MailGroup->findAll();
		$result = array();
		foreach($mg as $k => $v) {
			$result[]=$v['MailGroup'];
		}
		$this->set("mailGroups", $result);
		$this->set("areasList", $this->BEObject->find('list', array(
										"conditions" => "object_type_id=" . Configure::read("objectTypes.area.id"), 
										"order" => "title", 
										"fields" => "BEObject.title"
										)
									)
								);	
	}

	public function saveMailGroups() {
		$this->checkWriteModulePermission();
		if(empty($this->data["group_name"])) 
			throw new BeditaException( __("No data", true));
		if(empty($this->data["area_id"])) 
 			throw new BeditaException( __("No area", true));
		$this->Transaction->begin() ;
		if(!$this->MailGroup->save($this->data)) {
			throw new BeditaException(__("Error saving mail group", true), $this->MailGroup->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Mail Group saved", true)." - ".$this->data["group_name"]);
		$this->eventInfo("mail group [" .$this->data["group_name"] . "] saved");
	}

	public function deleteMailGroups() {
		$this->checkWriteModulePermission();
		if(empty($this->data["id"])) 
 	 	    throw new BeditaException( __("No data", true));
 	 	$this->Transaction->begin() ;
		if(!$this->MailGroup->del($this->data["id"])) {
			throw new BeditaException(__("Error saving mail group", true), $this->MailGroup->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Mail Group deleted", true) . " -  " . $this->data["group_name"]);
		$this->eventInfo("mail group " . $this->data["id"] . "-" . $this->data["group_name"] . " deleted");
	}

	public function sendNewsletter() {
		if (empty($this->data["MailGroup"]))
			throw new BeditaException(__("Missing invoices", true));
		if (empty($this->data["start_sending"]))
			throw new BeditaException(__("Missing sending date", true));
		
		$this->data["mail_status"] = "unsent";	
		$this->saveMessage();
		$this->userInfoMessage(__("Mail ready to be sended on ", true) . $this->data["start_sending"]);
		$this->eventInfo("mail [". $this->data["title"]."] prepared for sending");
	}
	
	public function testNewsletter($to) {
		$this->saveMessage();
		$this->BeMail->sendMailById($this->data["id"],$to);
		$this->userInfoMessage(__("Test mail sended to ", true) . $to);
		$this->eventInfo("test mail [". $this->data["title"]."] sended");
	}
	
	private function saveMessage() {
		$this->checkWriteModulePermission();
		if (empty($this->data["MailGroup"]))
			$this->data["MailGroup"] = array();
		
		$this->Transaction->begin();
		$this->saveObject($this->MailMessage);
	 	$this->Transaction->commit() ;
	}
	
	public function test() {
//		$this->BeMail->sendMailById(8,"batopa@gmail.com");
		//$data["to"] = "batopa@gmail.com";
		//$data["from"] = "a.pagliarini@channelweb.it";
		//$data["subject"] = "";
		//$data["replayTo"] = "";
		//$data["body"] = "<p>zxczx</p>";
		//$this->BeMail->sendMail($data);
		//pr($data);
//		pr($this->BeMail->lockMessages());
//		$this->BeMail->createJobs(array(14,15));
//		$this->BeMail->sendQueuedJobs(array(14));
		exit;
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
		$this->MailTemplate->containLevel("minimum");
		$templates = $this->MailTemplate->find("all", array(
											"conditions" => array("BEObject.object_type_id" => Configure::read("objectTypes.mailtemplate.id"))
											)
		);
		foreach ($templates as $key => $t) {
			$treeModel = ClassRegistry::init("Tree");
			$pub_id = $treeModel->getParent($t["id"]);
			$areaModel = ClassRegistry::init("Area");
			$templates[$key]["Area"] = $areaModel->find("first", array(
												"conditions" => array("Area.id" => $pub_id),
												"contain" 	 => array("BEObject")
												)
			);
		}
		
		$this->set("objects", $templates);
	 }

	function viewtemplate($id=null) {
		$this->viewObject($this->MailTemplate, $id);
		// get publishing public_url
		if (!empty($this->viewVars["tree"])) {
			$areaModel = ClassRegistry::init("Area");
			$areaModel->bviorCompactResults = false;
			foreach ($this->viewVars["tree"] as $k => $p) {
				$this->viewVars["tree"][$k]["public_url"] = $areaModel->field("public_url", array("Area.id" => $p["id"]));
			}
			$areaModel->bviorCompactResults = true;
		}
		
		if (!empty($id)) {
			$treeModel = ClassRegistry::init("Tree");
			$pub_id = $treeModel->getParent($id);
			$areaModel = ClassRegistry::init("Area");
			$areaModel->containLevel("minimum");
			$this->set("pub", $areaModel->find("first", array(
														"conditions" => array("Area.id" => $pub_id)
														)
												)
											);
		}
	 }
	
	function saveTemplate() {
		$this->checkWriteModulePermission();
		if(empty($this->data["destination"]))
			throw new BeditaException( __("Missing publishing", true));
		$this->Transaction->begin();
		$this->saveObject($this->MailTemplate);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Mail template saved", true)." - ".$this->data["title"]);
		$this->eventInfo("mail template [". $this->data["title"]."] saved");
	}
	
	function invoices() {

		$msg = array();
		$msg = $this->MailMessage->find("all", array(
			"conditions" => array("MailMessage.mail_status" => array("pending", "unsent")),
			"contain"	=> array("BEObject" => array("RelatedObject"), "MailGroup")
			)
		);
		
		$this->modelBindings["MailTemplate"] = array("BEObject");
		foreach ($msg as $k => $m) {
			if (!empty($m["RelatedObject"])) {
				$msg[$k]["relations"] = $this->objectRelationArray($m['RelatedObject']);
			}	
		}
		unset($this->modelBindings["MailTemplate"]);
		
		$pending = $this->MailMessage->find("count", array(
			"conditions" => array("MailMessage.mail_status" => array("pending"))
			)
		);
		
		$nextInvoice = $this->MailMessage->field("start_sending", array("mail_status" => "unsent"), "start_sending ASC");
		
		$this->set("objects", $msg);
		$this->set("scheduled", count($msg));
		$this->set("pending", $pending);
		$this->set("nextInvoiceDate", $nextInvoice);

	}
	 
	 /** AJAX CALLS **/
	function showTemplateDetailsAjax($id) {
		$this->layout = null;
		$temp = $this->MailTemplate->find("first", array(
								"conditions" => array("id" => $id),
								"contain"	 => array()
								)
		);

		$this->set("object", $temp);
		$this->render(null, null, VIEWS . "newsletter/inc/form_message_details.tpl");
	}
	 
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"save"	=> 	array(
							"OK"	=> "/newsletter/view/".@$this->MailMessage->id,
							"ERROR"	=> $this->referer() 
							),
			"sendNewsletter" => 	array(
							"OK"	=> "/newsletter/view/".@$this->MailMessage->id,
							"ERROR"	=> $this->referer() 
							),
			"testNewsletter" => 	array(
							"OK"	=> "/newsletter/view/".@$this->MailMessage->id,
							"ERROR"	=> $this->referer() 
							),
			"delete" =>	array(
							"OK"	=> "/newsletter/newsletters",
							"ERROR"	=> "/newsletter/newsletters"
							),
			"saveTemplate"	=> 	array(
							"OK"	=> "/newsletter/viewtemplate/".@$this->MailTemplate->id,
							"ERROR"	=> $this->referer()
							),
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
							),
			"saveMailGroups"=> array(
							"OK"	=> "/newsletter/mailGroups",
							"ERROR"	=> "/newsletter/mailGroups"
							),
			"deleteMailGroups"=> array(
							"OK"	=> "/newsletter/mailGroups",
							"ERROR"	=> "/newsletter/mailGroups"
							),
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
	
}	

?>