<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
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
		$this->loadCategories($filter["object_type_id"]);
	 }

	public function view($id = null) {
		$this->viewObject($this->Event, $id);
	}

	public function save() {
 		$this->checkWriteModulePermission();
		$this->Transaction->begin() ;
		$this->saveObject($this->Event);
	 	$this->Transaction->commit();
 		$this->userInfoMessage(__("Event saved")." - ".$this->request->data["title"]);
		$this->eventInfo("event [". $this->request->data["title"]."] saved");
	 }

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Event");
		$this->userInfoMessage(__("Events deleted") . " -  " . $objectsListDeleted);
		$this->eventInfo("Events $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Event");
		$this->userInfoMessage(__("Events deleted") . " -  " . $objectsListDeleted);
		$this->eventInfo("Events $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Event);
	}

	public function saveCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->request->data["label"])) 
 	 	    throw new BeditaException( __("No data"));
		$this->Transaction->begin() ;
		if(!$this->Category->save($this->request->data)) {
			throw new BeditaException(__("Error saving tag"), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category saved")." - ".$this->request->data["label"]);
		$this->eventInfo("category [" .$this->request->data["label"] . "] saved");
	}
	
	public function deleteCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->request->data["id"])) 
 	 	    throw new BeditaException( __("No data"));
 	 	$this->Transaction->begin() ;
		if(!$this->Category->delete($this->request->data["id"])) {
			throw new BeditaException(__("Error saving tag"), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category deleted") . " -  " . $this->request->data["label"]);
		$this->eventInfo("Category " . $this->request->data["id"] . "-" . $this->request->data["label"] . " deleted");
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
	 										"OK"	=> $this->fullBaseUrl . $this->Session->read('backFromView'),
	 										"ERROR"	=> $this->referer()
	 									), 
				"deleteSelected" =>	array(
											"OK"	=> $this->referer(),
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
				"moveItemsToAreaSection"	=> 	array(
											"OK"	=> $this->referer(),
											"ERROR"	=> $this->referer() 
										),
				"removeItemsFromAreaSection"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
				"changeStatusObjects"	=> 	array(
											"OK"	=> $this->referer(),
											"ERROR"	=> $this->referer() 
										),
				"assocCategory"	=> 	array(
											"OK"	=> $this->referer(),
											"ERROR"	=> $this->referer() 
										),
				"disassocCategory"	=> 	array(
											"OK"	=> $this->referer(),
											"ERROR"	=> $this->referer() 
										)
	 		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}

?>