<?php

class TranslationsController extends ModulesController {
	
	var $uses = array("BEObject","LangText");
	var $components = array("BeLangText","Permission");
	protected $moduleName = 'translations';
	
	public function index() {
		$objects_status=$this->LangText->find('all',
			array(
				'fields'=>array('id','object_id','name','text','long_text','lang'),
				'conditions'=>array("LangText.name = 'status'")
			)
		);
		$res=$this->LangText->find('all',
			array(
				'fields'=>array('id','object_id','name','text','long_text','lang'),
				'conditions'=>array("LangText.name = 'title'")
			)
		);
		foreach($res as $k => $v) {
			$objects_title[$v['LangText']['object_id']] = $v['LangText']['text'];
		}
		$this->set("translations",$objects_status);
		$this->set("translations_title",$objects_title);
	}

	public function view($id=null,$lang=null) {
		if($id) {
			$object_type_id = $this->BEObject->findObjectTypeId($id);
			$modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
			if(!($obj = $modelLoaded->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			$this->set("object_master",$obj);
			if(!$lang) {
				$langs = $this->LangText->langsForObject($id);
				$langs[] = $obj['lang'];
				$this->set("object_master_langs",$langs);
			}
		} else {
			throw new BeditaException(sprintf(__("No object id specified", true)));
		}
		if($lang) {
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
			$this->data['LangText'][$k]['object_id'] = $this->data['master_id'];
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

	protected function forward($action, $esito) {
		$REDIRECT = array(
			"save"	=> 	array("OK"	=> "/translations/view/".$this->data['master_id']."/".$this->data['translation_lang']),
			"view"	=> 	array("ERROR"	=> "/translations"),
			"delete"	=> 	array("OK"	=> "/translations")
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}

?>