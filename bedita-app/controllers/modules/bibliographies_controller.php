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
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BibliographiesController extends ModulesController {
	var $name = 'Bibliographies';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Bibliography', 'Tree') ;
	protected $moduleName = 'bibliographies';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $conf->objectTypes['bibliography']["id"];
		$filter["count_annotation"] = array("Comment","EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);		
	 }

	 public function view($id = null) {

		$this->viewObject($this->Bibliography, $id);

	 }

	public function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Bibliography);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Book saved", true)." - ".$this->data["title"]);
		$this->eventInfo("Book [". $this->data["title"]."] saved");
	 }
	 
	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Bibliography");
		$this->userInfoMessage(__("Bibliography deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("bibliographies $objectsListDeleted deleted");
	}


	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/bibliographies/view/{$this->Bibliography->id}",
							"ERROR"	=> "/bibliographies/view/{$this->Bibliography->id}" 
							),
			"save"	=> 	array(
							"OK"	=> "/bibliographies/view/{$this->Bibliography->id}",
							"ERROR"	=> "/bibliographies/view/{$this->Bibliography->id}" 
							), 
			"delete" =>	array(
							"OK"	=> "/bibliographies",
							"ERROR"	=> "/bibliographies/view/{@$this->params['pass'][0]}" 
							),
			"addItemsToAreaSection"	=> 	array(
							"OK"	=> "/bibliographies",
							"ERROR"	=> "/bibliographies" 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/bibliographies",
							"ERROR"	=> "/bibliographies" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>