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
 * @author
 */

/**
 * Home
 * 
 */
class HomeController extends AppController {
	
	var $uses = "BEObject";
	var $helpers = array();

	 function index() {
	 	$conf  = Configure::getInstance();
	 	$types = array($conf->objectTypes['gallery'], $conf->objectTypes['document'], $conf->objectTypes['shortnews'], $conf->objectTypes['event']);
	 	$user = $this->Session->read("BEAuthUser");
	 	$lastModBYUser = array();
	 	$lastMod = array();
	 	
	 	$lastModBYUser = $this->BEObject->find("all", array(
		 								"restrict" 		=> array("ObjectType"),
		 								"fields"		=> array("id", "title", "modified", "ObjectType.module"),
		 								"conditions" 	=> array(
		 														"user_modified = '" . $user["id"] . "'",
	 															"object_type_id" => $types
	 														),
		 								"order"			=> array("modified DESC"),
		 								"limit"			=> 5
	 								)
	 						);
	 	
	 	$lastMod = $this->BEObject->find("all", array(
		 								"restrict" 		=> array("ObjectType"),
		 								"fields"		=> array("id", "title", "modified", "ObjectType.module"),
		 								"conditions" 	=> array(
		 														"user_modified <> '" . $user["id"] . "'",
		 														"object_type_id" => $types
	 														),
		 								"order"			=> array("modified DESC"),
		 								"limit"			=> 10
	 								)
	 						);
	 	
	 	$this->set("lastModBYUser", $lastModBYUser);
	 	$this->set("lastMod", $lastMod);
	 }
	 
	 public function search($page=1, $dim=5) {
	 	$this->layout = "empty";
	 	
	 	
	 	if (!empty($this->params["form"]["searchstring"])) {
		 	$conf  = Configure::getInstance();
		 	$filter = array($conf->objectTypes['gallery'], 
		 					$conf->objectTypes['document'], 
		 					$conf->objectTypes['shortnews'], 
		 					$conf->objectTypes['event'],
		 					"search" => addslashes($this->params["form"]["searchstring"])		
		 			);
		 	
		 	$user = $this->Session->read("BEAuthUser");
		 
		 	$objects = $this->BEObject->findObjs($user["id"], null, $filter, null, true, $page, $dim);
		 	// get objects module
		 	foreach ($objects["items"] as $key => $o) {
		 		$objects["items"][$key]["module"] = $this->BEObject->ObjectType->field("module", array(
		 						"conditions" => array("id" => $o["object_type_id"])
		 					) 
		 				);
		 	}
		 
		 	$this->set("objects", $objects);
	 	}
	 }
	 
}

