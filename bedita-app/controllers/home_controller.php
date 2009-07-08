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
 * BEdita main page
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 *  */
class HomeController extends AppController {

	var $uses = array("BEObject");
	var $helpers = array();

	 function index() {
	 	$conf  = Configure::getInstance();
	 	
	 	$user = $this->Session->read("BEAuthUser");
	 	$lastModBYUser = array();
	 	$lastMod = array();
	 	$excludedObjectTypes = array($conf->objectTypes["editornote"]["id"]);
	 	if (!empty($conf->objectTypes["questionnaireresult"]["id"]))
	 		$excludedObjectTypes[] = $conf->objectTypes["questionnaireresult"]["id"];
	 	
	 	$lastModBYUser = $this->BEObject->find("all", array(
		 								"contain" 		=> array("ObjectType"),
		 								"fields"		=> array("id", "title", "modified", "object_type_id", "ObjectType.module"),
		 								"conditions" 	=> array(
		 														"user_modified = '" . $user["id"] . "'",
	 															'NOT' => array('object_type_id' => $excludedObjectTypes)
	 														),
		 								"order"			=> array("modified DESC"),
		 								"limit"			=> 5
	 								)
	 						);

	 	$lastMod = $this->BEObject->find("all", array(
		 								"contain" 		=> array("ObjectType"),
		 								"fields"		=> array("id", "title", "modified", "object_type_id", "ObjectType.module"),
		 								"conditions" 	=> array(
	 															'NOT' => array('object_type_id' => $excludedObjectTypes)
	 														),
	 									"order"			=> array("modified DESC"),
		 								"limit"			=> 10
	 								)
	 						);
	 	$connectedUser = $this->BeAuth->connectedUser();
	 	$this->set("lastModBYUser", $lastModBYUser);
	 	$this->set("lastMod", $lastMod);
		$this->set("connectedUser", $connectedUser);
		$this->set("noFooter", true);
	 }
	 
	public function search($page=1, $dim=5) {

		$this->layout = null;
		 
		if (!empty($this->params["form"]["searchstring"])) {
			$conf  = Configure::getInstance();
			$filter["query"] = addslashes($this->params["form"]["searchstring"]);
			
			$user = $this->Session->read("BEAuthUser");
			
			$objects = $this->BEObject->findObjects(null, $user["id"], null, $filter, null, true, $page, $dim);
			// get objects module
			foreach ($objects["items"] as $key => $o) {
				$condition = "id=".$o['object_type_id'];
				$objects["items"][$key]["module"] = $this->BEObject->ObjectType->field("module", $condition);
			}
			$this->set("objects", $objects);
		}
	}
	 
	 public function editProfile() {
	 	if (empty($this->data['User']['id']))
	 		throw new BeditaException(__("No user data", true));
	 	
	 	$oldPwd = trim($this->params['form']['oldpwd']);
	 	$pwd = trim($this->data['User']['passwd']);
		$confirmPwd = trim($this->params['form']['pwd']);
		
		$userModel = ClassRegistry::init("User");
		
		if(empty($pwd) && empty($confirmPwd)) {
			unset($this->data['User']['passwd']);
		} else {
			$user = $userModel->find("first", array(
		 			"conditions" => array(
		 				"id" => $this->data["User"]["id"],
		 				"passwd" => md5($oldPwd)
		 			),
		 			"contain" => array()
		 		)
		 	);
		 	
		 	if (!$user)
		 		throw new BeditaException(__("Wrong old user password", true));
		 		
			if (!$this->BeAuth->checkConfirmPassword($pwd, $confirmPwd))
				throw new BeditaException(__("Passwords mismatch",true));
		}
		
		if (empty($this->data["User"]["notify_changes"])) {
			$this->data["User"]["notify_changes"] = null;
		}
	 	if (empty($this->data["User"]["lang"])) {
			$this->data["User"]["lang"] = null;
		}
		if (empty($this->params["form"]["comments"])) {
			$this->data["User"]["comments"] = "never";
		}
		 if (empty($this->params["form"]["notes"])) {
			$this->data["User"]["notes"] = "never";
		}
	 	$this->Transaction->begin();
	 	$this->BeAuth->updateUser($this->data);
	 	$this->Transaction->commit();
	 	$userModel->containLevel("default");
	 	$user = $userModel->findById($this->data["User"]["id"]);
	 	$userModel->compact($user);
	 	$this->Session->write($this->BeAuth->sessionKey, $user);
	 	if (!empty($user["lang"]))
	 		$this->Session->write('Config.language',$user["lang"]);
		$this->eventInfo("user ".$this->data['User']['userid']." updated");
		$this->userInfoMessage(__("User updated",true));
	 }
	 
	 
	protected function forward($action, $esito) {
 	 	$REDIRECT = array(
			"editProfile" => array(
 							"OK"	=> "/home/index",
 							"ERROR"	=> "/home/index"
 						)
 			);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false;
	 }
}

