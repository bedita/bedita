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
class PagesController extends AppController {
	
	var $uses = array();
	var $helpers = array('BeTree');

	protected function beditaBeforeFilter() {
		if($this->action === 'changeLang') { // skip auth check, on lang change
			$this->skipCheck = true;
		}
	}
	
	function changePasswd() {
	}
	
	function changeLang($lang = null) {
		if (!empty($lang)) {
			$this->Session->write('Config.language', $lang);
			$this->Cookie->write('bedita.lang', $lang, null, '+350 day'); 
		}
		$this->redirect($this->referer());
	}
	

	/* AJAX CALLS */

	/**
	 * called via ajax
	 * Show list of objects for relation, append to section,...
	 * 
	 * @param int $main_object_id, object id of main object used to exclude association with itself 
	 * @param string $relation, relation type
	 * @param int $main_object_type_id, object_type_id of main object. Used if $main_object_id is not defined or empty
	 * @param string $objectType name of objectType to filter. It has to be a string that defined a group of type
	 * 							  defined in bedita.ini.php (i.e. 'related' 'leafs',...)
	 * 							  Used if $this->parmas["form"]["objectType"] and $relation are empty	
	 * 
	 **/
	public function showObjects($main_object_id=null, $relation=null, $main_object_type_id=null, $objectType="related") {
		$this->ajaxCheck();
		$id = (!empty($this->params["form"]["parent_id"]))? $this->params["form"]["parent_id"] : null;
		
		// default
		$objectTypeIds = Configure::read("objectTypes.related.id");
		
		if (!empty($relation)) {
			
			$relTypes = array_merge(Configure::read("objRelationType"), Configure::read("defaultObjRelationType"));
			
			if (!empty($relTypes[$relation])) {
				
				if (!empty($main_object_id)) {
					$main_object_type_id = ClassRegistry::init("BEObject")->field("object_type_id", array("id" => $main_object_id));
				}
				
				$objectTypeName = Configure::read("objectTypes." . $main_object_type_id . ".name");
				
				if (!empty($relTypes[$relation][$objectTypeName])) {
					$objectTypeIds = $relTypes[$relation][$objectTypeName];
				} elseif (key_exists("left", $relTypes[$relation]) 
							&& key_exists("right", $relTypes[$relation])
							&& is_array($relTypes[$relation]["left"])
							&& is_array($relTypes[$relation]["right"])
							) {
				
					if (in_array($main_object_type_id, $relTypes[$relation]["left"])) {
						if (!empty($relTypes[$relation]["right"]))
							$objectTypeIds = $relTypes[$relation]["right"];
					} elseif (in_array($main_object_type_id, $relTypes[$relation]["right"])) {
						if (!empty($relTypes[$relation]["left"]))
							$objectTypeIds = $relTypes[$relation]["left"];
					} elseif (empty($relTypes[$relation]["left"])) { 
						$objectTypeIds = $relTypes[$relation]["right"];
					} elseif (empty($relTypes[$relation]["right"])) {
						$objectTypeIds = $relTypes[$relation]["left"];
					} else {
						$objectTypeIds = array(0);	
					}
				}

			}
			
		} else {
			$objectTypeIds = Configure::read("objectTypes." . $objectType . ".id");
		}
		
		// set object_type_id filter
		if (!empty($this->params["form"]["objectType"])) {
			$filter["object_type_id"] = array($this->params["form"]["objectType"]);
		} else {
			$filter["object_type_id"] = $objectTypeIds;
		}
		
		// set lang filter
		if (!empty($this->params["form"]["lang"]))
			$filter["lang"] = $this->params["form"]["lang"]; 
		
		// set search filter
		if (!empty($this->params["form"]["search"]))
			$filter["query"] = addslashes($this->params["form"]["search"]);
		
		$page = (!empty($this->params["form"]["page"]))? $this->params["form"]["page"] : 1;
			
		$objects = $this->BeTree->getChildren($id, null, $filter, "title", true, $page, $dim=20) ;
		
		foreach ($objects["items"] as $key => $obj) {
			if ($obj["id"] != $main_object_id)
				$objects["items"][$key]["moduleName"] = ClassRegistry::init("ObjectType")->field("module", array("id" => $obj["object_type_id"]));
			else
				unset($objects["items"][$key]);
		}
		$this->set("objectsToAssoc", $objects);
		
		$tree = $this->BeTree->getSectionsTree() ;
		$this->set('tree',$tree);
		
		$this->set("relation", $relation);
		
		$this->set("main_object_id", $main_object_id);
		$this->set("object_type_id", $main_object_type_id);
		$this->set("objectType", $objectType);
		$this->set("objectTypeIds", (is_array($objectTypeIds))? $objectTypeIds : array($objectTypeIds) );
				
		if (!empty($this->params["form"]))
			$this->render("list_contents_to_assoc");
	} 
	
	/**
	 * called via ajax
	 * load objects selected to main view to prepare association form
	 *
	 * @param int $main_object_id, object id of main object used to exclude association with itself 
	 * @param string $objectType, object type used to filter
	 * @param string $tplname, template name without '.tpl' 
	 * 				 if it contains dots replace it with /
	 * 				 i.e. areas.inc.list_object become areas/inc/list_object.tpl
	 * 				  
	 */
	public function loadObjectToAssoc($main_object_id=null, $objectType=null, $tplname=null) {
		$this->ajaxCheck();
		$conditions = array("BEObject.id" => explode( ",", trim($this->params["form"]["object_selected"],",") ));
		
		if (!empty($objectType))
			$conditions["BEObject.object_type_id"] = Configure::read("objectTypes." . $objectType . ".id");
		
		$objects = ClassRegistry::init("BEObject")->find("all", array(
													"contain" => array("ObjectType"),
													"conditions" => $conditions
												)
										) ;
		$objRelated = array();

		foreach ($objects as $key => $obj) {
			if (empty($main_object_id) || $objects[$key]["BEObject"]["id"] != $main_object_id)
				$obj["BEObject"]["module"] = $obj["ObjectType"]["module"];
				$objRelated[] = array_merge($obj["BEObject"], array("ObjectType" => $obj["ObjectType"]));
		}
		
		$this->set("objsRelated", $objRelated);
		$this->set("rel", $this->params["form"]["relation"]);
		$tplname = (empty($tplname))? "common_inc/form_assoc_object.tpl" : str_replace(".", "/", $tplname) . ".tpl";
		$this->render(null, null, VIEWS . $tplname);
	}
	
	/**
	 * load user or group list
	 */
	public function loadUsersGroupsAjax() {
		$this->ajaxCheck();
		if($this->params['form']['itype'] == 'user') {
			$userModel = ClassRegistry::init("User");
			$userModel->displayField = 'userid';
			$this->set("itemsList", $userModel->find('list', array("order" => "userid")));
		} else if($this->params['form']['itype'] == 'group') {
			$this->set("itemsList", ClassRegistry::init("Group")->find('list', array("order" => "name")));
		}
	}
	
	/**
	 * save editor note
	 * if it fails throw BeditaAjaxException managed like json object
	 */
	public function saveNote() {
		$this->ajaxCheck();
		if (empty($this->data["object_id"]))
			throw new BeditaAjaxException(__("Missing referenced object.\nIf you are creating new item you have to save it before adding a note", true), array("output" => "json"));
		
		$this->Transaction->begin();
		try {
			$editorNoteModel = ClassRegistry::init("EditorNote");
			$this->saveObject($editorNoteModel);
			$this->Transaction->commit();
			$this->set("data", array("id" => $editorNoteModel->id));
			$this->view = "View";
			header("Content-Type: application/json");
			$this->render("json");
		} catch (BeditaException $ex) {
			$errorMsg = "Error saving note";
			throw new BeditaAjaxException(__("Error saving note", true), array_merge($editorNoteModel->validationErrors, array("output" => "json")));
		}
	}
	
	/**
	 * load an editor
	 */
	public function loadNote() {
		$this->ajaxCheck();
		$editorNoteModel = ClassRegistry::init("EditorNote");
		$this->set("note", $editorNoteModel->find("first", array(
									"conditions" => array("EditorNote.id" => $this->params["form"]["id"]))
								)
					);
	}
	
	public function deleteNote() {
		$this->ajaxCheck();
		if (empty($this->params["form"]["id"]))
			throw new BeditaAjaxException(__("Error deleting note, missing id", true), array("output" => "json"));
		
		$this->data["id"] = $this->params["form"]["id"];
		try {
			$objectsListDeleted = $this->deleteObjects("EditorNote");
			$this->eventInfo("editor note $objectsListDeleted deleted");
			$this->set("data", array("id" => $objectsListDeleted));
			$this->view = "View";
			$this->render("json");
		} catch (BeditaException $ex) {
			throw new BeditaAjaxException(__("Error deleting note", true), array("output" => "json"));
		}
	}

	private function ajaxCheck() {
		if (!$this->RequestHandler->isAjax()) {
			exit;
		}
		$this->layout="ajax";
	}
	
	function helpOnline() {
		$args = func_get_args();
		$count = func_num_args();
		if($count > 2) { // controller -> action
			$count = 2;
		}
		$url = Configure::read("helpBaseUrl");
		$path = "";
		$module = (!empty($args[0])) ? $args[0] : null;
		$action = (!empty($args[1])) ? $args[1] : null;
		for($i=0;$i<$count;$i++) {
			$url .= "/" . $args[$i];
			$path .= " " . $args[$i];
		}
		$result = @get_headers($url);
		if(preg_match("|200|",$result[0])) {
			$result = file_get_contents($url);
		} else {
			$result = "404";
		}
		$this->set('module',$module);
		$this->set('action',$action);
		$this->set('path',$path);
		$this->set('result',$result);
	}

	/**
	 * Print a document/object undsoweiter
	 * 	parametri
	 *  l'id dell'oggetto
	 *  il layout di pagina
	 *  la pubblicazione di riferimento per la grafica (context)
	 *  cosa fa:
	 *  in base al context e al layout sceglie le viste opportune
	 *  se il contesto è BEdita stesso (default) prepara un report con tutto l'oggetto e tutti i suoi metadati 
	 *  prendendo le diverse viste (layout) da BEdita-app
	 *  se il contesto è una pubblicazione, prende le viste da templates dentro alle singole pubblicazioni
	 *  che il grafico avrà opportunamente preparato quando ha skinnato il frontend
	 *  
	 *  
	 */
	public function printme() {

		$id 			= $this->data['id'];
		pr($this->data);
	
		exit;
		
	}	
	
	
	 
}

?>