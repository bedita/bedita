<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class NewsletterController extends ModulesController {

	var $name = 'Newsletter';
	var $helpers 	= array('BeTree', 'BeToolbar', 'Paginator');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeMail');

	var $uses = array('BEObject', 'Card', 'MailGroup', 'MailMessage', 'MailTemplate') ;
	
	protected $moduleName = 'newsletter';
	
    public function index() {
    	
    	$firstDayOfmonth = date("Y") . "-" . date("m") . "-01 00:00:00";
		$this->MailMessage->recursive = -1;
		$sentThisMonth = $this->MailMessage->find("count", array(
				"conditions" => array(
					"mail_status" => "sent",
					"start_sending >= '" . $firstDayOfmonth . "'"
				)
			)
		);
		
		$sentThisYear = $this->MailMessage->find("count", array(
				"conditions" => array(
					"mail_status" => "sent",
					"start_sending >= '" . date("Y") . "-01-01 00:00:00'"
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
		
		
		$this->Card->contain(array("BEObject"));
		$subscribedWeek = $this->Card->find("count", array(
				"conditions" => array(
					"Card.newsletter_email IS NOT NULL AND Card.newsletter_email <> ''",
					"BEObject.created >= '" . date("Y-m-d", mktime(0,0,0,date("m"),  date('d')-7,  date("Y"))) . " 00:00:00'",
					"BEObject.object_type_id" => Configure::read("objectTypes.card.id")
				)
			)
		);
		
		$subscribedMonth = $this->Card->find("count", array(
				"conditions" => array(
					"Card.newsletter_email IS NOT NULL AND Card.newsletter_email <> ''",
					"BEObject.created >= '" . $firstDayOfmonth . "'",
					"BEObject.object_type_id" => Configure::read("objectTypes.card.id")
				)
			)
		);
		
		$subscribedTotal = $this->Card->find("count", array(
				"conditions" => array(
					"Card.newsletter_email IS NOT NULL AND Card.newsletter_email <> ''",
					"BEObject.object_type_id" => Configure::read("objectTypes.card.id")
				)
			)
		);

		$this->set("sentThisMonth", $sentThisMonth);
		$this->set("sentThisYear", $sentThisYear);
		$this->set("queued", $queued);
		$this->set("sentTotal", $sentTotal);
		$this->set("templates", $templates);
		$this->set("recentMsg", $recentMsg);
		$this->set("subscribedWeek", $subscribedWeek);
		$this->set("subscribedMonth", $subscribedMonth);
		$this->set("subscribedTotal", $subscribedTotal);

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
		$filter["object_type_id"] = Configure::read("objectTypes.mailtemplate.id");
		foreach ($pub as $key => $p) {
			$temp = $this->BeTree->getChildren($p["id"], null, $filter);
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
		$filter["object_type_id"] = Configure::read("objectTypes.mailmessage.id");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
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



	 /**
	  * Get list detail
	  */
	public function view_mail_group() {
		
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
			foreach ($this->viewVars["tree"] as $k => $p) {
				$this->viewVars["tree"][$k]["public_url"] = $areaModel->field("public_url", array("Area.id" => $p["id"]));
			}
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
	
	function loadContentToNewsletter() {
		$objects = array();
		$contents_id = explode( ",", trim($this->params["form"]["object_selected"],","));
		$beObject = ClassRegistry::init("BEObject");
		foreach ($contents_id as $id) {
			$object_type_id = $beObject->findObjectTypeId($id);
			$model = $this->loadModelByObjectTypeId($object_type_id);
			$model->containLevel("default");
			$obj = $model->find("first", array(
					"conditions" => array("BEObject.id" => $id)
				)
			);
			$obj["relations"] = $this->objectRelationArray($obj["RelatedObject"]);
			if (!empty($this->params["form"]["txt"])) {
				if (!empty($obj["description"]))
					$obj["description"] = html_entity_decode($obj["description"], ENT_QUOTES, "UTF-8");
				if (!empty($obj["abstract"]))
					$obj["abstract"] = html_entity_decode($obj["abstract"], ENT_QUOTES, "UTF-8");
				if (!empty($obj["body"]))
					$obj["body"] = html_entity_decode($obj["body"], ENT_QUOTES, "UTF-8");
			}
			$objects[] = $obj;
			
		}
		
		$this->layout = null;
		$this->set("objects", $objects);
		$tpl = (empty($this->params["form"]["txt"]))? "contents_to_newsletter_ajax.tpl" : "contents_to_newsletter_txt_ajax.tpl";
		$this->render(null, null, VIEWS . "newsletter/inc/" . $tpl);
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