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
class TranslationsController extends ModulesController {

	var $helpers 	= array('BeTree','BeToolbar');
	var $uses = array("BEObject","LangText");
	var $components = array("BeLangText","Permission");
	protected $moduleName = 'translations';
	
	public function index($order = "", $dir = true, $page = 1, $dim = 20) {
		$lt = $this->trPaginatedList($this->data,$order,$dir,$page,$dim);
		$this->set("objects_translated",$lt['objects_translated']);
		$this->set("translations",		$lt['objects_status']);
		$this->set("translations_title",$lt['objects_title']);
		$this->params['toolbar'] = &$lt['toolbar'] ;
	}

	public function view($id=null,$lang=null) {
		if(Configure::read("langOptionsIso") == true) {
			Configure::load('langs.iso') ;
		}
		$lang_view = ($lang) ? true : false;
		if($id) {
			$object_type_id = $this->BEObject->findObjectTypeId($id);
			$modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
			
			if(!($obj = $modelLoaded->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			if(!empty($obj['RelatedObject'])) {
				$obj['relations']=$this->objectRelationArray($obj['RelatedObject']);
			}
			$this->set("object_master",$obj);
			if(!$lang_view) {
				$langs = $this->LangText->langsForObject($id);
				$langs[] = $obj['lang'];
				$this->set("object_master_langs",$langs);
			}
		} else {
			throw new BeditaException(sprintf(__("No object id specified", true)));
		}
		if($lang_view) {
			$translation = array();
			$lang_text=$this->LangText->find('all',
				array(
					'fields'=>array('id','name','text','long_text'),
					'conditions'=>array("LangText.object_id = '$id'","LangText.lang = '$lang'")
				)
			);
			$this->BeLangText->setupForViewLangText($lang_text) ;
			$lang_text['lang']=$lang;
			$this->set("object_translation",$lang_text);
		}
	}

	public function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify object permits
		if(!$new && !$this->Permission->verify($this->data['master_id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		foreach($this->data['LangText'] as $k => $v) {
			$this->data['LangText'][$k]['lang'] = $this->data['translation_lang'];
			if(empty($this->data['LangText'][$k]['object_id'])) { // if object_id is defined => it's a translation for an attach
				$this->data['LangText'][$k]['object_id'] = $this->data['master_id'];
			}
			if( ($new && ($this->data['LangText'][$k]['name'] == 'created_on')) || ($this->data['LangText'][$k]['name'] == 'modified_on') ) {
				$this->data['LangText'][$k]['text'] = time();
			} else if( ($new && ($this->data['LangText'][$k]['name'] == 'created_by')) || ($this->data['LangText'][$k]['name'] == 'modified_by') ) {
				$this->data['LangText'][$k]['text'] = $this->BeAuth->user['userid'];
			}
		}
		$this->Transaction->begin();
		$this->LangText->unbindModel(array('belongsTo'=>array('BEObject')));
		$this->LangText->saveAll($this->data['LangText']);
		$this->Transaction->commit();
		$this->userInfoMessage(__("Translation saved", true));
		$this->eventInfo("translation saved");
	}
	
	public function delete() {
		$this->checkWriteModulePermission();
		$id = $this->data['master_id'];
		$lang = $this->data['translation_lang'];
		$this->Transaction->begin();
		$this->LangText->unbindModel(array('belongsTo'=>array('BEObject')));
		$this->LangText->deleteAll(
			array("LangText.object_id = '$id'","LangText.lang = '$lang'")
		);
		$this->Transaction->commit();
		$this->userInfoMessage(__("Translation deleted", true) . " - $lang,$id ");
		$this->eventInfo("translation $lang for object $id deleted");
	}

	public function deleteTranslations() {
		$this->checkWriteModulePermission();
		$objectsToDel = array();
		$objectsListDesc = "";
		if(!empty($this->params['form']['objects_selected'])) {
			$objectsListDesc = $this->params['form']['objects_selected'];
			$objectsToDel = split(",",$objectsListDesc);
			
		} else {
			if(empty($this->data['id'])) 
				throw new BeditaException(__("No data", true));
			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			$objectsToDel = array($this->data['id']);
			$objectsListDesc = $this->data['id'];
		}
		$this->Transaction->begin() ;
		foreach ($objectsToDel as $id) {
			$this->LangText->unbindModel(array('belongsTo'=>array('BEObject')));
			$res = $this->LangText->deleteAll(
				array("LangText.id = '$id'")
			);
			if(!$res) {
				throw new BeditaException(__("Error deleting translation: ", true) . $id);
			}
		}
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Translation deleted", true) . " - $objectsListDesc ");
		$this->eventInfo("translation(s) $objectsListDesc deleted");
	}

	public function changeStatusTranslations($newStatus) {
		$objectsToModify = array();
		$objectsListDesc = "";
		if(!empty($this->params['form']['objects_selected'])) {
			$objectsListDesc = $this->params['form']['objects_selected'];
			$objectsToModify = split(",",$objectsListDesc);
		} else {
			if(empty($this->data['id'])) 
				throw new BeditaException(__("No data", true));
			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) {
				throw new BeditaException(__("Error saving status for translations", true));
			}
			$objectsToModify = array($this->data['id']);
			$objectsListDesc = $this->data['id'];
		}
		$this->Transaction->begin() ;
		foreach ($objectsToModify as $id) {
			$this->LangText->id = $id;
			if(!$this->LangText->saveField('text',$newStatus))
				throw new BeditaException(__("Error saving status for translation: ", true) . $id);
		}
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Translation updated", true) . " - $objectsListDesc ");
		$this->eventInfo("translation(s) $objectsListDesc updated to status $newStatus");
	}

	private function trPaginatedList($data, $order, $dir, $page, $dim) {
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		$filter = array();
		$filter['lang'] = (!empty($data['translation_lang'])) ? $data['translation_lang'] : null;
		$filter['status'] = (!empty($data['translation_status'])) ? $data['translation_status'] : null;
		$filter['obj_id'] = (!empty($data['translation_object_id'])) ? $data['translation_object_id'] : null;
		return $this->LangText->findObjs($filter,$order,$dir,$page,$dim);
	}

	protected function forward($action, $esito) {
		$REDIRECT = array(
			"page"	=> 						array("ERROR"	=> "/translations/index"),
			"save"	=> 						array("OK"	=> "/translations/view/".$this->data['master_id']."/".$this->data['translation_lang']),
			"view"	=> 						array("ERROR"	=> "/translations"),
			"delete"	=> 					array("OK"	=> "/translations"),
			"deleteTranslations"	=> 		array("OK"	=> "/translations"),
			"changeStatusTranslations"	=> 	array("OK"	=> "/translations")
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}

?>