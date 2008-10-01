<?php
/**
 * BEdita - a semantic content management framework
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * 
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 * See the Affero GNU General Public License for more details.
 * 
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 * @link		http://www.bedita.com
 * @version		$Revision$
 * @author		$Author$
 * @date		$Date$
 * 
 * $Id$
 */

class HomeController extends AppController {

	var $uses = "BEObject";
	var $helpers = array();

	 function index() {
	 	$conf  = Configure::getInstance();
	 	
	 	$user = $this->Session->read("BEAuthUser");
	 	$lastModBYUser = array();
	 	$lastMod = array();
	 	
	 	$lastModBYUser = $this->BEObject->find("all", array(
		 								"contain" 		=> array("ObjectType"),
		 								"fields"		=> array("id", "title", "modified", "ObjectType.module"),
		 								"conditions" 	=> array(
		 														"user_modified = '" . $user["id"] . "'"
	 														),
		 								"order"			=> array("modified DESC"),
		 								"limit"			=> 5
	 								)
	 						);
	 	
	 	$lastMod = $this->BEObject->find("all", array(
		 								"contain" 		=> array("ObjectType"),
		 								"fields"		=> array("id", "title", "modified", "ObjectType.module"),
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

	 	$this->layout = "empty";
	 	
	 	if (!empty($this->params["form"]["searchstring"])) {
		 	$conf  = Configure::getInstance();
			$filter = array("search" => addslashes($this->params["form"]["searchstring"]));

		 	$user = $this->Session->read("BEAuthUser");

		 	$objects = $this->BEObject->findObjs($user["id"], null, $filter, null, true, $page, $dim);
		 	// get objects module
		 	foreach ($objects["items"] as $key => $o) {
		 		$condition = "id=".$o['object_type_id'];
		 		$objects["items"][$key]["module"] = $this->BEObject->ObjectType->field("module", $condition);
		 	}
		 	$this->set("objects", $objects);
	 	}
	 }
	 
}

