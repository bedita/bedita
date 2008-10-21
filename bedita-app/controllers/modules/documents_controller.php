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
 * @version			$Revision: $
 * @modifiedby 		$LastChangedBy: $
 * @lastmodified	$LastChangedDate: $
 * 
 * $Id: $
 */
class DocumentsController extends ModulesController {
	var $name = 'Documents';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Document', 'Tree') ;
	protected $moduleName = 'documents';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
    	$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['document']["id"]);
		
		if (!empty($this->params["form"]["searchstring"])) {
			$types["search"] = addslashes($this->params["form"]["searchstring"]);
			$this->set("stringSearched", $this->params["form"]["searchstring"]);
		}
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	 public function view($id = null) {
		$this->viewObject($this->Document, $id);
	 }

	public function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Document);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Document saved", true)." - ".$this->data["title"]);
		$this->eventInfo("document [". $this->data["title"]."] saved");
	 }

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}


	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/documents/view/".@$this->Document->id,
							"ERROR"	=> "/documents/view/".@$this->Document->id 
							),
			"view"	=> 	array(
							"ERROR"	=> "/documents" 
							), 
			"save"	=> 	array(
							"OK"	=> "/documents/view/".@$this->Document->id,
							"ERROR"	=> "/documents/view/".@$this->Document->id 
							), 
			"delete" =>	array(
							"OK"	=> "/documents",
							"ERROR"	=> $this->referer()
							),
			"addItemsToAreaSection"	=> 	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents" 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>