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
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText', 'BeMail');

	var $uses = array('BEObject', 'Card', 'MailGroup', 'MailMessage', 'MailTemplate', 'MailGroupCard') ;
	
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
		$mg = $this->MailGroup->find("all", array(
				"contain" => array("Area" => "BEObject.title")
			)
		);
		$result = array();
		foreach($mg as $k => $v) {
			$v["MailGroup"]["subscribers"] = $this->MailGroupCard->find("count", array(
					"conditions" => array("mail_group_id" => $v["MailGroup"]["id"])
				)
			);
			$v["MailGroup"]["publishing"] = $v["Area"]["BEObject"]["title"];
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
	public function view_mail_group($id=null) {
		$obj = null;
		if($id!=null) {
			$this->MailGroup->containLevel("default");
			$o = $this->MailGroup->find('first',
							array("conditions" => "MailGroup.id=" . $id)
						);
			$obj = $o['MailGroup'];
			
			// get paginated subscribers
			$filter = array(
				"object_type_id" => Configure::read("objectTypes.card.id"),
				"Card.*" => "",
				"mail_group" => $id
			);
			$card = $this->BeTree->getChildren(null, null, $filter, null, true, 1, 10) ;
			$this->set("subscribers", $card["items"]);
			$this->params['toolbar'] = &$card['toolbar'] ;
			
		}
		$this->set('object',	$obj);
		$this->set("areasList", $this->BEObject->find('list', array(
										"conditions" => "object_type_id=" . Configure::read("objectTypes.area.id"), 
										"order" => "title", 
										"fields" => "BEObject.title"
										)
									)
								);
		$this->set("groups", $this->MailGroup->find("all", array("order" => "group_name ASC")));
	}



	public function saveMailGroups() {
		$this->checkWriteModulePermission();
		if(empty($this->data["MailGroup"]["group_name"])) 
			throw new BeditaException( __("Missing list name", true));
		if(empty($this->data["MailGroup"]["area_id"])) 
 			throw new BeditaException( __("Missing publishing", true));
		$this->Transaction->begin() ;
		if(!$this->MailGroup->save($this->data)) {
			throw new BeditaException(__("Error saving mail group", true), $this->MailGroup->validationErrors);
		}
		
		$mail_group_id = $this->MailGroup->id;
		
		// add subscribers
		if (!empty($this->params["form"]["addsubscribers"])) {
			$subscribers = explode(",", $this->params["form"]["addsubscribers"]);
			foreach ($subscribers as $sub) {
				$sub = trim($sub);
				// if it's not already present save card and join group
				if ( !($card_id = $this->Card->field("id", array("newsletter_email" => $sub))) ) {
					$dataCard = array("title" => $sub, "name" => $sub, "newsletter_email" => $sub);
					$dataCard["joinGroup"][0]["mail_group_id"] = $mail_group_id;
					$dataCard["joinGroup"][0]["status"] = "confirmed";
					$this->Card->create();
					if (!$this->Card->save($dataCard))
						throw new BeditaException(__("Error adding subscribers", true), $this->Card->validationErrors . "Email: " . $sub);
				
				// join group, if not already joined
				} elseif (!$this->MailGroupCard->field("id", array("mail_group_id" => $mail_group_id, "card_id" => $card_id)) ) {
					$dataJoin["MailGroupCard"]["mail_group_id"] = $mail_group_id;
					$dataJoin["MailGroupCard"]["card_id"] = $card_id;
					$dataJoin["MailGroupCard"]["status"] = "confirmed";
					$dataJoin["MailGroupCard"]["hash"] = md5($card_id . microtime() . $this->data["MailGroup"]["group_name"]);
						
					$this->MailGroupCard->create();
					if (!$this->MailGroupCard->save($dataJoin))
						throw new BeditaException(__("Error join group", true), $this->MailGroupCard->validationErrors);
				}
				
			}
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Mail Group saved", true)." - ".$this->data["MailGroup"]["group_name"]);
		$this->eventInfo("mail group [" .$this->data["MailGroup"]["group_name"] . "] saved");
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
		$this->userInfoMessage(__("Mail ready to be sent on ", true) . $this->data["start_sending"]);
		$this->eventInfo("mail [". $this->data["title"]."] prepared for sending");
	}
	
	public function testNewsletter($to) {
		$this->saveMessage();
		// send html test
		$this->BeMail->sendMailById($this->data["id"],$to, true);
		// send txt test
		$this->BeMail->sendMailById($this->data["id"],$to, false);
		$this->userInfoMessage(__("Test mail sent to ", true) . $to);
		$this->eventInfo("test mail [". $this->data["title"]."] sent");
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
		//$msg = $this->BeMail->lockMessages();
//		$this->BeMail->createJobs(array(93));
//		$this->BeMail->sendQueuedJobs(array(93));
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
	
	function loadContentToNewsletter($template_id=null) {
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
			if (empty($obj["description"]))
				$obj["description"] = "";
			if (empty($obj["abstract"]))
				$obj["abstract"] = "";
			if (empty($obj["body"]))
				$obj["body"] = "";
			
			if (!empty($this->params["form"]["txt"])) {
				$obj["description"] = html_entity_decode($obj["description"], ENT_QUOTES, "UTF-8");
				$obj["abstract"] = html_entity_decode($obj["abstract"], ENT_QUOTES, "UTF-8");
				$obj["body"] = html_entity_decode($obj["body"], ENT_QUOTES, "UTF-8");
			}
			$objects[] = $obj;
			
		}
		
		// get template
		if (!empty($template_id)) {
			$field = (!empty($this->params["form"]["txt"]))? "abstract" : "body";
			$contentModel = ClassRegistry::init("Content");
			$bodyTemplate = $contentModel->field($field, array("id" => $template_id));
			// get template block between first and last <!--bedita content block--> delimeter
			if (preg_match("/<!--bedita content block-->([\s\S]*)<!--bedita content block-->/", $bodyTemplate, $matches)) {
				// delete other <!--bedita content block-->
				$contentTemplate = str_replace("<!--bedita content block-->","",$matches[1]);
 
				// get truncate number of chars
				if (preg_match("/\[" . preg_quote("$") ."body\|truncate:(\d*)\]/", $contentTemplate, $bodyMatches)) {
					$truncateNumber = $bodyMatches[1];
				}
			}

		}
		
		$this->layout = null;
		$this->set("objects", $objects);
		$this->set("contentTemplate", (!empty($contentTemplate))? $contentTemplate : "" );
		$this->set("truncateNumber", (!empty($truncateNumber))? $truncateNumber : "" );
		$tpl = (empty($this->params["form"]["txt"]))? "contents_to_newsletter_ajax.tpl" : "contents_to_newsletter_txt_ajax.tpl";
		$this->render(null, null, VIEWS . "newsletter/inc/" . $tpl);
	}
	
	/**
	 * load paginated list of list's subscribers
	 *
	 * @param int $mail_group_id
	 */
	public function listSubscribers($mail_group_id) {
		// get paginated subscribers
		$filter = array(
			"object_type_id" => Configure::read("objectTypes.card.id"),
			"Card.*" => "",
			"mail_group" => $mail_group_id
		);
		
		$page = (!empty($this->passedArgs["page"]))? $this->passedArgs["page"] : 1;
		$dir = (isset($this->passedArgs["dir"]))? $this->passedArgs["dir"] : true;
		$dim = (!empty($this->passedArgs["dim"]))? $this->passedArgs["dim"] : 10;
		$order = (!empty($this->passedArgs["order"]))? $this->passedArgs["order"] : "newsletter_email";
		
		$card = $this->BeTree->getChildren(null, null, $filter, $order, $dir, $page, $dim) ;
		$this->set("subscribers", $card["items"]);
		$this->params['toolbar'] = &$card['toolbar'] ;
		$this->layout = null;
		$this->render(null, null, VIEWS . "newsletter/inc/list_subscribers.tpl");
	}
	
	/**
	 * copy or move a list of subscribers
	 *
	 * @param unknown_type $old_group_id
	 */
	public function addCardToGroup($old_group_id=null) {
		$this->checkWriteModulePermission();
		if(!empty($this->params['form']['objects_selected'])) {
			
			$this->Transaction->begin();
			
			$groupname = $this->MailGroup->field("group_name", array("id" => $this->params["form"]["destination"]));
			
			$data["MailGroupCard"]["mail_group_id"] = $this->params["form"]["destination"];
			$data["MailGroupCard"]["status"] = "confirmed";
			
			foreach ($this->params['form']['objects_selected'] as $card_id) {
				
				// move to group => delete from previous group
				if ($this->params["form"]["operation"] == "move" && !empty($old_group_id)) {
					$this->MailGroupCard->deleteAll(array(
														"card_id" => $card_id, 
														"mail_group_id" => $old_group_id	
														)
													);
				}
				
				// save if not already exists
				$join_id = $this->MailGroupCard->field("id", "card_id=".$card_id." AND mail_group_id=".$this->params["form"]["destination"] );
				
				if (!$join_id) {
					$data["MailGroupCard"]["card_id"] = $card_id;
					$data["MailGroupCard"]["hash"] = md5($card_id . microtime() . $groupname);
					$this->MailGroupCard->create();
					if (!$this->MailGroupCard->save($data))
						throw new BeditaException(__("Error adding subscriber " . $card_id . " in group " . $data["MailGroupCard"]["mail_group_id"], true));
				}
				
			}
			
			$this->Transaction->commit();
			//$this->userInfoMessage(__("Subscribers associated to list", true) . " - " . $groupname);
			$this->eventInfo("Subscribers associated to list " . $this->params["form"]["destination"]);
			// set to forward callback
			$this->MailGroup->id = $old_group_id;
		}
	}
	
	public function changeCardStatus($mail_group_id=null) {
		$this->checkWriteModulePermission();
		if(!empty($this->params['form']['objects_selected'])) {
		
			$this->Transaction->begin() ;
			foreach ($this->params['form']['objects_selected'] as $id) {
				
				$this->Card->id = $id;
				if(!$this->Card->saveField('mail_status',$this->params['form']["newStatus"]))
					throw new BeditaException(__("Error saving status for item: ", true) . $id);
			}
			$this->Transaction->commit() ;
			
			$this->eventInfo("Change mail_status to " . $this->params['form']["newStatus"] . " at subscribers  " . implode(",",$this->params["form"]["objects_selected"]));
		}
		$this->MailGroup->id = $mail_group_id;
	}
	
	public function unlinkCard($mail_group_id) {
		$this->checkWriteModulePermission();
		if(!empty($this->params['form']['objects_selected'])) {
			$this->MailGroupCard->deleteAll(array(
					"mail_group_id" => $mail_group_id,
					"card_id" => $this->params['form']['objects_selected']
				)
			);
		}
		$this->MailGroup->id = $mail_group_id;
	}
	 
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/newsletter/view/".@$this->MailMessage->id,
							"ERROR"	=> $this->referer() 
							),
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
							"OK"	=> "/newsletter/view_mail_group/" . @$this->MailGroup->id,
							"ERROR"	=> "/newsletter/view_mail_group/" . @$this->MailGroup->id
							),
			"deleteMailGroups"=> array(
							"OK"	=> "/newsletter/mailGroups",
							"ERROR"	=> "/newsletter/mailGroups"
							),
			"addCardToGroup" => array(
							"OK"	=> "/newsletter/listSubscribers/" . $this->MailGroup->id,
							"ERROR"	=> "/newsletter/listSubscribers/" . $this->MailGroup->id
							),
			"changeCardStatus" => array(
							"OK"	=> "/newsletter/listSubscribers/" . $this->MailGroup->id,
							"ERROR"	=> "/newsletter/listSubscribers/" . $this->MailGroup->id
							),
			"unlinkCard" 	=> array(
							"OK"	=> "/newsletter/listSubscribers/" . $this->MailGroup->id,
							"ERROR"	=> "/newsletter/listSubscribers/" . $this->MailGroup->id
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
	
}	

?>