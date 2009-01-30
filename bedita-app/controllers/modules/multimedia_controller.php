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
 * Module Multimedia: management of Image, Audio, Video objects
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class MultimediaController extends ModulesController {
	var $name = 'Multimedia';

	var $helpers 	= array('BeTree', 'BeToolbar', 'MediaProvider');
	var $components = array('BeTree', 'Permission', 'BeFileHandler', 'SwfUpload', 'BeUploadToObj');

	// This controller does not use a model
	var $uses = array('Stream', 'Image', 'Audio', 'Video', 'BEObject', 'Tree', 'User', 'Group','Category','BEFile') ;
	protected $moduleName = 'multimedia';
	
	 /**
	 * Show multimedia item list
	 */
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
			$conf->objectTypes['befile']["id"],
			$conf->objectTypes['image']["id"],
			$conf->objectTypes['audio']["id"],
			$conf->objectTypes['video']["id"]
		);
		$filter["mediatype"] = 1;
		$bedita_items = $this->BeTree->getChildren($id, null, $filter, $order, $dir, $page, $dim)  ;
		
	 	foreach($bedita_items['items'] as $key => $value) {
	 		$model = $this->loadModelByObjectTypeId($value['object_type_id']);
			$model->containLevel("minimum");
			if(($details = $model->findById($value['id']))) {
				$details['filename'] = substr($details['path'],strripos($details['path'],"/")+1);
				$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $details);	
			}
		}
		$this->params['toolbar'] = &$bedita_items['toolbar'] ;
		// template data
		$this->set('areasectiontree',$this->BeTree->getSectionsTree());
		$this->set('objects', $bedita_items['items']);
		$this->setPrevNext($bedita_items['items']);
	 }

	 /**
	  * Show object for $id
	  * If $id is not passed, show new multimedia object page
	  * @param integer $id
	  */
	function view($id = null) {
		$conf  = Configure::getInstance() ;
		$this->setup_args(array("id", "integer", &$id)) ;
		// Get object by $id
		$obj = null ;
		if($id) {
			$model = ClassRegistry::init($this->BEObject->getType($id));
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
			
			$imagePath 	= $this->BeFileHandler->path($id) ;
			$imageURL 	= $this->BeFileHandler->url($id) ;
		}
		// data for template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		// get users and groups list. 
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
		$this->sessionForObjectDetail();
	 }

	function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
			
		$new = (empty($this->data['id'])) ? true : false ;
		
		// Verify object permits
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		
		$this->Transaction->begin() ;
		// save data
		$this->data["Category"] = $this->Category->saveTagList($this->params["form"]["tags"]);
	
		if (!empty($this->params['form']['Filedata']['name'])) {
			$this->Stream->id = $this->BeUploadToObj->upload($this->data) ;
		} elseif (!empty($this->data['url'])) {
			$this->Stream->id = $this->BeUploadToObj->uploadFromURL($this->data) ;	
		} else {
			$model = (!empty($this->data["id"]))? $this->BEObject->getType($this->data["id"]) : "BEFile";
			
			if(!$this->{$model}->save($this->data)) {
				throw new BeditaException(__("Error saving multimedia", true), $this->{$model}->validationErrors);
			}
			$this->Stream->id = $this->{$model}->id;
			
			if (!empty($this->params['form']['mediatype'])) {
				$objetc_type_id = Configure::read("objectTypes." . strtolower($model) . ".id");
				$this->data['Category'] = array_merge($this->data['Category'], $this->Category->checkMediaType($objetc_type_id, $this->params['form']['mediatype']));
			}
			
			// if it's new object set id just saved in $this->data["id"] to execute an update
			if (empty($this->data["id"])) {
				$this->data["id"] = $this->{$model}->id;
			}
			$this->{$model}->create();
			if(!$this->{$model}->save($this->data)) {
				throw new BeditaException(__("Error saving multimedia", true), $this->{$model}->validationErrors);
			}
		}

		// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->Stream->id, $this->data['Permissions'], 
				!empty($this->data['recursiveApplyPermissions']), 'document');
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Multimedia object saved", true)." - ".$this->data["title"]);
		$this->eventInfo("multimedia object [". $this->data["title"]."] saved");
	}
	
	public function cloneObject() {
		$this->data['status']='draft';
		$this->data['fixed'] = 0;
		$this->Stream->id = $this->BeUploadToObj->cloneMediaObject($this->data);
	}
	
	 /**
	 * Delete multimedia object
	 */
	function delete($id = null) {
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
		$multimedia = $this->BeTree->getDiscendents(null, null, array($ot['image']["id"], $ot['audio']["id"], $ot['video']["id"]), $order, $dir, $page, $dim)  ;
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
			"delete"	=> 	array(
							"OK"	=> "./",
							"ERROR"	=> $this->referer()
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/multimedia",
							"ERROR"	=> "/multimedia"
							)
						);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>