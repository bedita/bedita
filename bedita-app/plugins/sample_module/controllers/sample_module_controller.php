<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 * Sample Module Controller
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class SampleModuleController extends ModulesController {
	
	public $uses = array("SampleObject");
	var $helpers 	= array('BeTree', 'BeToolbar');
	
	protected $moduleName = 'sample_module';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $this->getModuleObjectTypes($this->moduleName);
		$filter["count_annotation"] = array("Comment","EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
	}
	
	public function view($id = null) {
		$this->viewObject($this->SampleObject, $id);
		$this->set("objectTypeId", Configure::read("objectTypes.sample_object.id"));
	}
	
	public function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->SampleObject);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Skeleton object saved", true)." - ".$this->data["title"]);
		$this->eventInfo("skeleton_object [". $this->data["title"]."] saved");
	}
	
	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("SampleObject");
		$this->userInfoMessage(__("Sample object deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("SampleObject $objectsListDeleted deleted");
	}
	
	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("SampleObject");
		$this->userInfoMessage(__("Sample object", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("SampleObject $objectsListDeleted deleted");
	}
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/".$this->moduleName."/view/".@$this->SampleObject->id,
							"ERROR"	=> "/".$this->moduleName."/view/".@$this->SampleObject->id 
							),
			"view"	=> 	array(
							"ERROR"	=> "/".$this->moduleName 
							), 
			"save"	=> 	array(
							"OK"	=> "/".$this->moduleName."/view/".@$this->SampleObject->id,
							"ERROR"	=> $this->referer()
							),
			"delete" =>	array(
							"OK"	=> $this->fullBaseUrl . $this->Session->read('backFromView'),
							"ERROR"	=> $this->referer()
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
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
	
}
?>