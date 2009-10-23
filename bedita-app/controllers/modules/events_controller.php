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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class EventsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText');
	var $uses = array('BEObject','Event','Category','Area','Tree') ;
	protected $moduleName = 'events';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $conf->objectTypes['event']["id"];
		$filter["count_annotation"] = array("Comment","EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
	 }

	public function view($id = null) {
		$this->viewObject($this->Event, $id);
	}

	public function save() {
 		$this->checkWriteModulePermission();
		$this->Transaction->begin() ;
		$this->saveObject($this->Event);
	 	$this->Transaction->commit();
 		$this->userInfoMessage(__("Event saved", true)." - ".$this->data["title"]);
		$this->eventInfo("event [". $this->data["title"]."] saved");
	 }

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Event");
		$this->userInfoMessage(__("Events deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Events $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Event");
		$this->userInfoMessage(__("Events deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Events $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Event);
	}

	public function saveCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->data["label"])) 
 	 	    throw new BeditaException( __("No data", true));
		$this->Transaction->begin() ;
		if(!$this->Category->save($this->data)) {
			throw new BeditaException(__("Error saving tag", true), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category saved", true)." - ".$this->data["label"]);
		$this->eventInfo("category [" .$this->data["label"] . "] saved");
	}
	
	public function deleteCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->data["id"])) 
 	 	    throw new BeditaException( __("No data", true));
 	 	$this->Transaction->begin() ;
		if(!$this->Category->del($this->data["id"])) {
			throw new BeditaException(__("Error saving tag", true), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category deleted", true) . " -  " . $this->data["label"]);
		$this->eventInfo("Category " . $this->data["id"] . "-" . $this->data["label"] . " deleted");
	}


	protected function forward($action, $esito) {
	  	$REDIRECT = array(
				"cloneObject"	=> 	array(
										"OK"	=> "/events/view/{$this->Event->id}",
										"ERROR"	=> "/events/view/{$this->Event->id}" 
										),
                "view"              =>  array(
                                            "ERROR" => "/events"
                                        ), 
				"save"				=> 	array(
	 										"OK"	=> "/events/view/{$this->Event->id}",
	 										"ERROR"	=> "/events" 
	 									), 
	 			"delete" 			=>	array(
	 										"OK"	=> $this->Session->read('backFromView'),
	 										"ERROR"	=> $this->referer()
	 									), 
	 			"saveCategories" 	=> array(
	 										"OK"	=> "/events/categories",
	 										"ERROR"	=> "/events/categories"
	 									),
	 			"deleteCategories" 	=> array(
	 										"OK"	=> "/events/categories",
	 										"ERROR"	=> "/events/categories"
	 									),
				"deleteSelected" =>	array(
											"OK"	=> $this->referer(),
											"ERROR"	=> $this->referer() 
										),
				"addItemsToAreaSection"	=> 	array(
											"OK"	=> $this->referer(),
											"ERROR"	=> $this->referer() 
										),
				"changeStatusObjects"	=> 	array(
											"OK"	=> $this->referer(),
											"ERROR"	=> $this->referer() 
										)
	 		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}

?>
