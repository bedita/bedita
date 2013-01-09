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
 * Module Multimedia: management of Image, Audio, Video objects
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class MultimediaController extends ModulesController {
	var $name = 'Multimedia';

	var $helpers 	= array('BeTree', 'BeToolbar', 'MediaProvider', 'ImageInfo');
	var $components = array('BeFileHandler', 'BeUploadToObj');

	// This controller does not use a model
	var $uses = array('Application','Stream', 'Image', 'Audio', 'Video', 'BEObject', 'Tree', 'User', 'Group','Category','BEFile') ;
	protected $moduleName = 'multimedia';
	
	 function index($id = null, $order = "id", $dir = 0, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		$filter["object_type_id"] = array(
			$conf->objectTypes['b_e_file']["id"],
			$conf->objectTypes['image']["id"],
			$conf->objectTypes['audio']["id"],
			$conf->objectTypes['video']["id"],
			$conf->objectTypes['application']["id"]
		);
		$filter["mediatype"] = 1;
		$bedita_items = $this->BeTree->getChildren($id, null, $filter, $order, $dir, $page, $dim)  ;
		
	 	foreach($bedita_items['items'] as $key => $value) {
	 		$model = $this->loadModelByObjectTypeId($value['object_type_id']);
			$model->containLevel("minimum");
			if(($details = $model->findById($value['id']))) {
				$details['filename'] = substr($details['uri'],strripos($details['uri'],"/")+1);
				$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $details);	
			}
		}
		$this->params['toolbar'] = &$bedita_items['toolbar'] ;
		// template data
		$this->set('tree',$this->BeTree->getSectionsTree());
		$this->set('objects', $bedita_items['items']);
		$this->setSessionForObjectDetail($bedita_items['items']);
	 }

	function view($id = null) {
		$conf  = Configure::getInstance() ;
		$this->setup_args(array("id", "integer", &$id)) ;
		// Get object by $id
		$obj = null ;
		$parents_id = array();
		if($id) {
			$objEditor = ClassRegistry::init("ObjectEditor");
			$objEditor->cleanup($id);
			$model = ClassRegistry::init($this->BEObject->getType($id));
			if (!in_array("multimedia", $model->objectTypesGroups)) {
				throw new BeditaException(__("Error loading object", true));
			}
			$model->containLevel("detailed");
			if(!($obj = $model->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			if (isset($obj["Category"])) {
				$objCat = array();
				foreach ($obj["Category"] as $oc) {
					$objCat = $oc["name"];
				}
				$obj["Category"] = $objCat;
			}
			
			if (!empty($obj['RelatedObject'])) {
				$obj["relations"] = $this->objectRelationArray($obj['RelatedObject']);
			}
			if (!empty($obj['Annotation'])) {
				$this->setupAnnotations($obj);
			}
			unset($obj['Annotation']);
			
			$imagePath 	= $this->BeFileHandler->path($id) ;
			$imageURL 	= $this->BeFileHandler->url($id) ;

			$treeModel = ClassRegistry::init("Tree");
			$parents_id = $treeModel->getParent($id) ;
			if($parents_id === false)
				$parents_id = array() ;
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);

			$previews = $this->previewsForObject($parents_id, $id, $obj['status']);

			$this->historyItem["object_id"] = $id;
			// concurrent access
			if($this->modulePerms & BEDITA_PERMS_MODIFY) {
				$objEditor->updateAccess($id, $this->BeAuth->user["id"]);
			}

			$this->set('objectProperty', $this->BeCustomProperty->setupForView($obj, Configure::read("objectTypes." . $model->name . ".id")));
		} else {
			Configure::write("defaultStatus", "on"); // set default ON for new objects
		}

		// data for template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		// get users and groups list. 
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
		$this->set('tree', $this->BeTree->getSectionsTree());
		$this->set('parents',	$parents_id);
		$this->setSessionForObjectDetail();
	 }

	function saveAjax() {
		$this->layout = "ajax";
		try {
			if(!empty($this->data['upload_choice'])) {
				if($this->data['upload_choice'] == 'new_file_new_obj') {
					$this->data['uri'] = $this->Stream->field('uri',array('id'=>$this->data['upload_other_obj_id']));
					$this->data['name'] = $this->params['form']['Filedata']['name'];
					$this->data['mime_type'] = $this->Stream->field('mime_type',array('id'=>$this->data['upload_other_obj_id']));
					$id = $this->BeUploadToObj->cloneMediaObject($this->data);
					$this->set("redirUrl","/multimedia/view/".$id);
				} else { // new_file_old_obj
					$id = $this->data['upload_other_obj_id'];
					$this->data['id'] = $id;
					$this->data['name'] = $this->params['form']['Filedata']['name'];
					$this->params['form']['forceupload'] = true;
					$this->save();
					$this->set("redirUrl","/multimedia/view/".$id);
				}
			} else {
				$this->save();
				$this->set("redirUrl","/multimedia/view/".$this->Stream->id);
			}
		} catch(BEditaFileExistException $ex) {
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
			$this->setResult(self::ERROR);
			$this->set("errorFileExist","true");
			$this->set("errorMsg", $ex->getMessage());
			$this->set("objectId", $ex->getObjectId());
			$this->set("objectTitle", $this->BEObject->field("title", array("id" => $ex->getObjectId())));
		} catch(BeditaException $ex) {
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
			$this->setResult(self::ERROR);
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->set("errorMsg", $ex->getMessage());
		}
	}

	function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
			
		$new = (empty($this->data['id'])) ? true : false ;
		
		// Verify object permits
//		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
//			throw new BeditaException(__("Error modifying permissions", true));
		
		// Format custom properties
		$this->BeCustomProperty->setupForSave() ;	
		
		$this->Transaction->begin() ;
		// save data
		$this->data["Category"] = $this->Category->saveTagList($this->params["form"]["tags"]);
	
		if (!empty($this->params['form']['Filedata']['name'])) {
			if(!empty($this->data['url'])) {
				unset($this->data['url']);
			}
			$this->Stream->id = $this->BeUploadToObj->upload($this->data) ;
		} elseif (!empty($this->data['url'])) {
			$this->Stream->id = $this->BeUploadToObj->uploadFromURL($this->data) ;
		} else {
			if(!empty($this->data['url'])) {
				unset($this->data['url']);
			}
			$model = (!empty($this->data["id"]))? $this->BEObject->getType($this->data["id"]) : "BEFile";
			
			if ($model == "Video") {
				$this->data["thumbnail"] = $this->BeUploadToObj->getThumbnail($this->data);
			}
			
			if (!empty($this->params['form']['mediatype'])) {
				$objetc_type_id = Configure::read("objectTypes." . Inflector::underscore($model) . ".id");
				$this->data['Category'] = array_merge($this->data['Category'], $this->Category->checkMediaType($objetc_type_id, $this->params['form']['mediatype']));
			}

			if(!$this->{$model}->save($this->data)) {
				throw new BeditaException(__("Error saving multimedia", true), $this->{$model}->validationErrors);
			}
			$this->Stream->id = $this->{$model}->id;
		}

		// update permissions
		if(!isset($this->data['Permission'])) 
			$this->data['Permission'] = array() ;
//		$this->Permission->saveFromPOST($this->Stream->id, $this->data['Permission'], 
//				!empty($this->data['recursiveApplyPermissions']), 'document');
		if(isset($this->data['destination'])) {
			$this->BeTree->updateTree($this->Stream->id, $this->data['destination']);
		}
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Multimedia object saved", true)." - ".$this->data["title"]);
		$this->eventInfo("multimedia object [". $this->data["title"]."] saved");
	}
	
	public function cloneObject() {
		$this->data['status']='draft';
		$this->data['fixed'] = 0;
		$this->Stream->id = $this->BeUploadToObj->cloneMediaObject($this->data);
	}

	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Stream");
		$this->userInfoMessage(__("Multimedia deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("multimedia $objectsListDeleted deleted");
	}

	function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Stream");
		$this->userInfoMessage(__("Multimedia deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("multimedia $objectsListDeleted deleted");
	}

	/**
	 * Form page to upload multimedia objects
	 */
	function frm_upload() {
	}
	
	/**
	 * Form page to select bedita multimedia objects
	 */
	function frm_upload_bedita() {
		$order = ""; $dir = true; $page = 1; $dim = 20 ;
		$conf  = Configure::getInstance() ;
		$this->setup_args(
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		$ot = &$conf->objectTypes ; 
		$multimedia = $this->BeTree->getDescendants(null, null, array($ot['image']["id"], $ot['audio']["id"], $ot['video']["id"]), $order, $dir, $page, $dim)  ;
		for($i=0; $i < count($multimedia['items']) ; $i++) {
			$id = $multimedia['items'][$i]['id'] ;
			$ret = $this->Stream->findById($id) ;
			$multimedia['items'][$i] = array_merge($multimedia['items'][$i], $ret['Stream']) ;
			$multimedia['items'][$i]['bedita_type'] = $conf->objectTypes[$multimedia['items'][$i]['object_type_id']]["model"] ;
		}
		$this->params['toolbar'] = &$multimedia['toolbar'] ;
		// Data for template
		$this->set('multimedia', 	$multimedia['items']);
		$this->set('toolbar', 		$multimedia['toolbar']);
	}

	/**
	 * Form page to upload multimedia through URL
	 */
	function frm_upload_url() {
	}
	
	/**
	 * Form page to open add multimedia method choice (modal)
	 */
	function add_multimedia() {
			
		$this->layout = null;
		//$view = "multimedia/add_multimedia.tpl";
		//$this->render(null, null, VIEWS . $view);		
		
	}
	
	protected function forward($action, $esito) {

		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/multimedia/view/".@$this->Stream->id,
							"ERROR"	=> "/multimedia/view/".@$this->Stream->id 
							),
			"save"  =>  array(
							"OK"    => "/multimedia/view/".@$this->Stream->id,
							"ERROR" => "/multimedia/view/".@$this->data['id'] 
							),
			"saveAjax" =>	array(
							"OK"	=> self::VIEW_FWD.'upload_ajax_response',
							"ERROR"	=> self::VIEW_FWD.'upload_ajax_response'
							),
			"view"	=> 	array(
							"ERROR"	=> "/multimedia"
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
							)
						);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>