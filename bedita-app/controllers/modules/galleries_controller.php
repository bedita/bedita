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
class GalleriesController extends ModulesController {
	var $name = 'Galleries';
	var $helpers 	= array('Beurl', 'BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText');
    var $uses = array('BEObject', 'Gallery', 'Tree', 'Category') ;
	protected $moduleName = 'galleries';
	protected $categorizableModels = array('Gallery');
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $conf->objectTypes['gallery']["id"];
		$filter["count_annotation"] = array("Comment","EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
		$this->loadCategories($filter["object_type_id"]);
	}

    public function view($id = null) {

    	$this->viewObject($this->Gallery, $id);

    }
    
	public function save() {
        $this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Gallery);
        $this->Transaction->commit() ;
		$this->userInfoMessage(__("Gallery saved", true) . "<br />" . $this->data["title"]);
		$this->eventInfo("gallery ". $this->data["title"]." saved");
	}

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Gallery");
		$this->userInfoMessage(__("Galleries deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("galleries $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Gallery");
		$this->userInfoMessage(__("Galleries deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("galleries $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Gallery);
	}

	protected function forward($action, $esito) {
		$REDIRECT = array("cloneObject"	=> 	array(
							"OK"	=> "/galleries/view/".@$this->Gallery->id,
							"ERROR"	=> "/galleries/view/".@$this->Gallery->id 
							),
						"save"	=> 	array(
							"OK"	=> "./view/{$this->Gallery->id}",
							"ERROR"	=> "./view/{$this->Gallery->id}"
							),
						"saveCategories" 	=> array(
							"OK"	=> "/galleries/categories",
							"ERROR"	=> "/galleries/categories"
							),
						"deleteCategories" 	=> array(
							"OK"	=> "/galleries/categories",
							"ERROR"	=> "/galleries/categories"
							),
						"delete"	=> 	array(
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
						);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito];
		return false;
	}
}

?>