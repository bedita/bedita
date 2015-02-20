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
class DocumentsController extends ModulesController {
	var $name = 'Documents';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeLangText', 'BeFileHandler', 'BeSecurity');

	var $uses = array('BEObject', 'Document', 'Tree','Category') ;
	protected $moduleName = 'documents';
	protected $categorizableModels = array('Document');

	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
    	$conf  = Configure::getInstance() ;
		$filter['object_type_id'] = $conf->objectTypes['document']['id'];
		$filter['count_annotation'] = array('Comment', 'EditorNote');
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
		$this->loadCategories($filter['object_type_id']);
	 }

	public function view($id = null) {
		$this->viewObject($this->Document, $id);
		$this->set('autosave', true);
	}

	public function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Document);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Document saved", true)." - ".$this->data["title"]);
		$this->eventInfo("document [". $this->data["title"]."] saved");
	 }

	public function autosave() {
		$this->layout = 'ajax';
		$this->Transaction->begin();
		$this->autoSaveObject($this->Document);
		$this->Transaction->commit();
		$time = strftime(Configure::read("dateTimePattern"), time());
		$this->userInfoMessage(__("Document Saved on", true)."<br/>".$time);
		$this->eventInfo("document [". $this->data["title"]."] saved");
		$this->render(null, null, "/elements/flash_messages");
	 }

	 public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Document);
	}
}

?>