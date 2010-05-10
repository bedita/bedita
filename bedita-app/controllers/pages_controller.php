<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008, 2009, 2010 ChannelWeb Srl, Chialab Srl
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
class PagesController extends AppController {
	
	var $uses = array();
	var $helpers = array('BeTree');

	protected function initAttributes() {
		if($this->action === 'changeLang') { // skip auth check, on lang change
			$this->skipCheck = true;
		}
	}
		
	function changeLang($lang = null) {
		if (!empty($lang)) {
			$this->Session->write('Config.language', $lang);
			$this->Cookie->write('bedita.lang', $lang, null, '+350 day'); 
		}
		$this->redirect($this->referer());
	}
	
	/**
	 * Print an object 
	 */
	public function printme() {
		$id = $this->params["form"]["id"];
		$printLayout = $this->params["form"]["printLayout"];
		if (!empty($this->params["form"]["printcontext"])) {
			$publication_url = ClassRegistry::init("Area")->field("public_url", array("id" => $this->params["form"]["printcontext"]));
			if (!empty($publication_url)) {
				$this->redirect($publication_url . "/printme/" . $id . "/" . $printLayout);
			}
		}
		$object_type_id = ClassRegistry::init("BEObject")->findObjectTypeId($id);
		$objectModel = $this->loadModelByObjectTypeId($object_type_id);
		$objectModel->containLevel("detailed");
		if (!$objectData = $objectModel->findById($id)) {
			throw new BeditaException(__("Error finding object", true));
		}
		if (!empty($objectData['RelatedObject'])) {
			$objectData['relations'] = $this->objectRelationArray($objectData['RelatedObject']);
		}
		if (!empty($objectData['Annotation'])) {
			$this->setupAnnotations($objectData);
		}
		$this->layout = "print";
		$this->set("printLayout", $printLayout);
		$this->set("object", $objectData);
		if (file_exists(APP."views".DS."pages".DS.$printLayout.".tpl"))
			$this->render($printLayout);
		else
			$this->render("print");
		
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
		
		$conf = Configure::getInstance();
		// default
		$objectTypeIds = $conf->objectTypes["related"]["id"];
		
		if (!empty($relation)) {
			
			$relTypes = $this->mergeAllRelations();
			
			if (!empty($relTypes[$relation])) {
				
				if (!empty($main_object_id)) {
					$main_object_type_id = ClassRegistry::init("BEObject")->field("object_type_id", array("id" => $main_object_id));
				}
				
				$objectTypeName = Configure::read("objectTypes." . $main_object_type_id . ".name");

				if (!empty($relTypes[$relation][$objectTypeName])) {
					$ot = $relTypes[$relation][$objectTypeName];
				} elseif (key_exists("left", $relTypes[$relation]) 
							&& key_exists("right", $relTypes[$relation])
							&& is_array($relTypes[$relation]["left"])
							&& is_array($relTypes[$relation]["right"])
							) {
				
					if (in_array($objectTypeName, $relTypes[$relation]["left"])) {
						if (!empty($relTypes[$relation]["right"]))
							$ot = $relTypes[$relation]["right"];
					} elseif (in_array($objectTypeName, $relTypes[$relation]["right"])) {
						if (!empty($relTypes[$relation]["left"]))
							$ot = $relTypes[$relation]["left"];
					} elseif (empty($relTypes[$relation]["left"]) && !empty($relTypes[$relation]["right"])) { 
						$ot = $relTypes[$relation]["right"];
					} elseif (empty($relTypes[$relation]["right"]) && !empty($relTypes[$relation]["left"])) {
						$ot = $relTypes[$relation]["left"];
					}
				}
				
				if (!empty($ot)) {
					$objectTypeIds = array();
					foreach ($ot as $val) {
						$objectTypeIds[] = $conf->objectTypes[$val]["id"];
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
			
		$objects = $this->BeTree->getChildren($id, null, $filter, "modified", false, $page, $dim=20) ;
		
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
			if (empty($main_object_id) || $objects[$key]["BEObject"]["id"] != $main_object_id) {
				$obj["BEObject"]["module"] = $obj["ObjectType"]["module"];
				// for media file get mime_type and size too
				if ($this->params["form"]["relation"] == "download") {
					$streamFields = ClassRegistry::init("Stream")->find("first", array(
							"conditions" => array(
								"id" => $obj["BEObject"]["id"]
							),
							"fields" => array("mime_type", "size")
						)
					);
					$obj["BEObject"]["mime_type"] = $streamFields["Stream"]["mime_type"];
					$obj["BEObject"]["size"] = $streamFields["Stream"]["size"];
				}
				$objRelated[] = array_merge($obj["BEObject"], array("ObjectType" => $obj["ObjectType"]));
			}
		}
		
		$this->set("objsRelated", $objRelated);
		$this->set("rel", $this->params["form"]["relation"]);
		$tplname = (empty($tplname))? "elements/form_assoc_object.tpl" : str_replace(".", "/", $tplname) . ".tpl";
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
			throw new BeditaAjaxException(__("Missing referenced object. Save new item before adding a note", true), array("output" => "json"));
		
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
	 * load an editor note
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
	
	/**
	  * Add Link with Ajax...
	  */
	public function addLink() {
		$this->ajaxCheck();
		$this->layout = "ajax";
	 	$this->data = $this->params['form'];
		$this->data["status"] = "on";
	 	$this->Transaction->begin() ;
		$linkModel = $this->loadModelByType("Link");
		$this->data['url'] = $linkModel->checkUrl($this->data['url']);
		
		$link = $linkModel->find('all',array('conditions' =>array('url' => $this->data['url'])));
		if(!empty($link)) {
			$linkModel->id = $link[0]['id'];
			if(empty($this->data['title'])) {
				$this->data['title'] = $link[0]['title'];
			}
		} else {
			if(empty($this->data['title'])) { // try to read title from URL directly
				$this->data['title'] = $linkModel->readHtmlTitle($this->data['url']);
			}
			if(!$linkModel->save($this->data)) {
				throw new BeditaAjaxException(__("Error saving link", true), $linkModel->validationErrors);
			}
		}
 		$this->Transaction->commit() ;
		if(empty($link)) {
			$this->eventInfo("link [". $this->data["title"]."] saved");
		}
		$this->data["id"] = $linkModel->id;
		$this->set("objRelated", $this->data);
	 }

	private function ajaxCheck() {
		if (!$this->RequestHandler->isAjax()) {
			exit;
		}
		$this->layout="ajax";
	}
	
	/**
	 * Provides on line Help contents, called via AJAX like /pages/helpOnline/$controller/$action
	 * 2 arguments at least mandatory
	 */
	public function helpOnline() {
		$args = func_get_args();
		$count = func_num_args();
		if($count < 2) {
			throw new BeditaException(__("Error invoking online help", true));
		}
		$module = $args[0];
		$action = $args[1];
		
		$path = " " . $module . " " . $action;
		$url = Configure::read("helpBaseUrl");

		// add language choice
		$langChoice = "/lang:" . $this->currLang;
		
		// help online URL convention is <base-url>/<module-name>-module/<module-name>-<action-name>
		// example: <base-url>/events-module/events-view
		$url .= "/$module-module/$module-$action" . $langChoice;

		$this->log($url);	
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
	 * Ajax update of current object editors/viewers
	 *
	 * @param int $objectId - object id
	 */
	public function updateEditor($objectId) {
		// TODO: check perms on object/module
		$this->ajaxCheck();
		$objectEditor = ClassRegistry::init("ObjectEditor"); 
	 	$user = $this->Session->read("BEAuthUser");
		$objectEditor->cleanup($objectId);
	 	$objectEditor->updateAccess($objectId, $user["id"]);
		$res = $objectEditor->loadEditors($objectId);
		$this->set("editors", $res);
	}
	
	public function showAjaxMessage() {
		$this->ajaxCheck();
		$methodName = 'user'.ucfirst($this->params['form']['type']).'Message';
		$this->{$methodName}($this->params['form']['msg']);
		$this->render(null, null, "/elements/flash_messages");
	}
	
	/**
	 * Show object revision information (specific revision)
	 *
	 * @param int $id, object id
	 * @param int $rev, revision number
	 */
	public function revision($id, $rev) {
		$beObject = ClassRegistry::init("BEObject"); 
		$modelName = $beObject->getType($id);
		$model = $this->loadModelByType($modelName);
		$this->viewRevision($model, $id, $rev);
	}
	
	
	
}

?>