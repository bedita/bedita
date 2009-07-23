<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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
 * Behavior class to send email notification to users. 
 * Currenly handles this scenarios:
 * 	- new notes or comments added to an object
 *  - object has been modified
 *  - user profile created or modified
 * Notifications events are triggered accordingo to user preferences 
 * (on comments/notes and objects created) 
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class NotifyBehavior extends ModelBehavior {
 
	private $modelNameToUserField = array("Comment" => "comments", "EditorNote" => "notes");
	private $notifyMsg = null;
	
	function setup(&$model, $settings=array()) {
	}
	
	function afterSave($model, $created) {
		
		$data =& $model->data[$model->alias];
		$users = array();
		$creator = array();
		
		if ($model->name == "Comment" || $model->name == "EditorNote") {
			$c = ClassRegistry::init($model->name)->find("first", array(
					"conditions" => array(
						"ReferenceObject.id" => $data["object_id"]
					),
					"contain" => array("ReferenceObject")
				)
			);
			
			$userField = $this->modelNameToUserField[$model->name];
			$userModel = ClassRegistry::init("User");
			$conditions = array("(" .$userField . "='all' 
								OR (" .$userField . "='mine' AND id='". $c["ReferenceObject"]["user_created"] ."'
								))");
			if($userField == "notes") { // don't send mail to editor itself
				$conditions[] = "id <> " . $data["user_modified"];
			}
			$users = $userModel->getUsersToNotify($conditions);
			$data['author'] = empty($c['author']) ? 
				$userModel->field("userid",array("id" => $data["user_modified"])) : $c['author'];
			$data['email'] = $c['email'];
			$data['url'] = $c['url'];
			$data['object_title'] = $c["ReferenceObject"]["title"];
			
			$this->prepareAnnotationMail($users, $model);
		} else if($model->name == "User") {
			
			$this->prepareUserSettingsMail($model, $created);
			
		} else if (!$created && !empty($data["user_modified"]) 
				&& !empty($data["user_created"])) {
			$userModel = ClassRegistry::init("User");
			$data['author'] = $userModel->field("userid",array("id" => $data["user_modified"]));
			$creator = $userModel->getUsersToNotify(array("notify_changes" => "1", 
				"id = " . $data["user_created"], "id <> " . $data["user_modified"]));
			if(!empty($creator)) {
				$this->prepareObjectChangeMail($creator, $model);
			}
		}
	}
	
	public function prepareUserSettingsMail($userModel, $created) {
		$modData =& $userModel->data[$userModel->alias];
		if(!empty($modData["email"])) { // send only if email field is present...

			$this->loadMessages();
			$msgType = $created ? "newUser" : "updateUser";
			$detailMsg = (!empty($modData["passwd"]) && !$created) ? "Your password was changed!" : ""; 
			$detailMsg .= ($modData["valid"] == 0) ? "\nYour account is blocked!" : "";
			
			$params = array("author" => "",
				"title" => "",
				"url" => Configure::read("beditaUrl"),
				"beditaUrl" => Configure::read("beditaUrl"),
				"text" => "Real Name: ". $modData["realname"] . "\nUserid: " .  
					$modData["userid"] . "\n" . $detailMsg . "\n",
			);
			
			$users = array(0=>array("User" => $modData));
			$this->createMailJob($users, $userModel, $msgType, $params);
		}
	}
	
	public function prepareAnnotationMail(array &$users, $model) {
		
		$this->loadMessages();
		$modData =& $model->data[$model->alias];
		
		$params = array("author" => $modData["author"],
			"title" => $modData["object_title"],
			"url" => Configure::read("beditaUrl") . "/view/" . $modData["object_id"],
			"beditaUrl" => Configure::read("beditaUrl"),
			"text" => $modData["description"],
		);
		$msgType = strtolower($model->alias); // note or comment 
		$this->createMailJob($users, $model, $msgType, $params);
	}
	
	public function prepareObjectChangeMail(array &$users, $model) {
		
		$this->loadMessages();
		$modData =& $model->data[$model->alias];
		$params = array("author" => $modData["author"],
			"title" => $modData["title"],
			"url" => Configure::read("beditaUrl") . "/view/" . $modData["id"],
			"text" => $modData["description"],
			"beditaUrl" => Configure::read("beditaUrl"),
		);
		$this->createMailJob($users, $model, "contentChange", $params);
	}
	
	protected function createMailJob(array &$users, $model, $msgType, array &$params) {
		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("default");
		$data = array();
		$data["status"] = "unsent";

		$modData =& $model->data[$model->alias];
		$conf = Configure::getInstance();
		foreach ($users as $u) {
			
			$data["recipient"] = $u['User']['email'];
			$params["user"] = $u['User']['userid'];
			$lang = isset($u['User']['lang']) ? $u['User']['lang'] : "eng"; 
			$subject = $this->getNotifyText($msgType, "subject", $params, $lang);

			$data["mail_params"] = serialize(array("reply_to" => $conf->mailOptions["reply_to"], 
						"sender" => $conf->mailOptions["sender"], 
						"subject" => $subject,
						"signature" => $conf->mailOptions["signature"]
			));
			$data["mail_body"] = $this->getNotifyText($msgType, "mail_body", $params, $lang);			
					
			$jobModel->create();
			if (!$jobModel->save($data)) {
				throw new BeditaException(__("Error creating mail jobs"),true);
			}
		}
		
	}
	
	protected function loadMessages() {
		// load local messages if present
		$appPath = (defined("BEDITA_CORE_PATH"))? BEDITA_CORE_PATH . DS : APP;
		$localMsg = $appPath."config".DS."notify".DS."local.msg.php";
		$notify = array();
		if (file_exists ($localMsg) ) {
			require_once($localMsg);
		} else {
			require_once($appPath."config".DS."notify".DS."default.msg.php");
		}
		$this->notifyMsg = &$notify;
	}

	protected function getNotifyText($msgType, $field ,array &$params, $lang) {

		if(isset($this->notifyMsg[$msgType][$lang][$field])) {
			$text = $this->notifyMsg[$msgType][$lang][$field];
		} else {
			$text = $this->notifyMsg[$msgType]["eng"][$field]; // default fallback
		}
		
		$text = str_replace("[\$user]", $params["user"], $text);
		$text = str_replace("[\$author]", $params["author"], $text);
		$text = str_replace("[\$title]", $params["title"], $text);
		$text = str_replace("[\$text]", $params["text"], $text);
		$text = str_replace("[\$url]", $params["url"], $text);
		$text = str_replace("[\$beditaUrl]", $params["beditaUrl"], $text);
		return $text;		
	}

}
 
?>