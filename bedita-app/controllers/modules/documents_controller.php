<?php
/**
 *
 * @filesource
 * @copyright		
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 * @author 			giangi@qwerg.com d.domenico@channelweb.it
 */

class DocumentsController extends ModulesController {
	var $name = 'Documents';

	var $helpers 	= array('BeTree', 'BeToolbar', 'Fck');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array(
		'Stream', 'Area', 'Section', 'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Document', 'Tree',
		'Image', 'Video', 'Audio', 'BEFile', 'User', 'Group', 'ObjectCategory'
		) ;
	protected $moduleName = 'documents';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = $conf->objectTypes['documentAll'];
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	 /**
	  * Get document.
	  * If id is null, empty document
	  *
	  * @param integer $id
	  */
	 function view($id = null) {
		$conf  = Configure::getInstance() ;
		$this->setup_args(array("id", "integer", &$id)) ;
		$obj = null ;
		if($id) {
			$this->Document->bviorHideFields = array('Version', 'Index', 'current') ;
			if(!($obj = $this->Document->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading document: %d", true), $id));
			}
			$multimedia_id = array();
			// Get multimedia objects
			for($i=0; $i < @count($obj['multimedia']) ; $i++) {
				$m = $this->Document->am($obj['multimedia'][$i]) ;
				$type = $conf->objectTypeModels[$m['object_type_id']] ;
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments', 'LangText');
				if(!($Details = $this->{$type}->findById($obj['multimedia'][$i]['id']))) {
					continue ;
				}
				$Details['priority'] = $m['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
				$obj['multimedia'][$i]= $Details;
				$multimedia_id[]=$obj['multimedia'][$i]['id'];
			}
			// Get attachments
			for($i=0; $i < @count($obj['attachments']) ; $i++) {
				$m = $this->Document->am($obj['attachments'][$i]) ;
				$type = $conf->objectTypeModels[$m['object_type_id']] ;
				
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments', 'LangText');
				if(!($Details = $this->{$type}->findById($obj['attachments'][$i]['id']))) {
					continue ;
				}
				$Details['priority'] = $m['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
			
				$obj['attachments'][$i]= $Details;
				$multimedia_id[]=$obj['attachments'][$i]['id'];
			}
		}
		if(isset($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;
		}
		$tree = $this->BeTree->getSectionsTree() ;
		if(isset($id)) {
			$parents_id = $this->Tree->getParent($id) ;
			if($parents_id === false) array() ;
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);
		} else {
			$parents_id = array();
		}
		$galleries = $this->BeTree->getDiscendents(null, null, $conf->objectTypes['gallery'], "", true, 1, 10000);
		// begin#bedita_items
		$ot = &$conf->objectTypes ; 
		$bedita_items = $this->BeTree->getDiscendents(null, null, array($ot['befile'], $ot['image'], $ot['audio'], $ot['video']))  ;
		foreach($bedita_items['items'] as $key => $value) {
			if(!empty($multimedia_id) && in_array($value['id'],$multimedia_id)) {
				unset($bedita_items['items'][$key]);
			} else {
				// get details
				$type = $conf->objectTypeModels[$value['object_type_id']];
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments');
				if(($Details = $this->{$type}->findById($value['id']))) {
					$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
					$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $Details);	
				}
			}
		}
		$this->params['toolbar'] = &$bedita_items['toolbar'] ;
		$this->set('bedita_items', 	$bedita_items['items']);
		$this->set('toolbar', 		$bedita_items['toolbar']);
		// end#bedita_items
		$this->set('object',	$obj);
		$this->set('multimedia',$obj['multimedia']);
		$this->set('attachments', $obj['attachments']);
		$this->set('galleries', (count($galleries['items'])==0) ? array() : $galleries['items']);
		$this->set('tree', 		$tree);
		$this->set('parents',	$parents_id);		
		$this->selfUrlParams = array("id", $id);
		$this->setUsersAndGroups();
	 }

	/**
	 * Creates/updates new document
	 */
	function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify object permits
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		// Format translations for fields
		$this->data['title'] = $this->data['LangText'][$this->data['lang']]['title'];
		$this->data['description'] = $this->data['LangText'][$this->data['lang']]['description'];
		$this->data['abstract'] = $this->data['LangText'][$this->data['lang']]['abstract'];
		$this->data['body'] = $this->data['LangText'][$this->data['lang']]['body'];
		$this->BeLangText->setupForSave($this->data["LangText"]) ;
		if(!isset($this->data["attachments"])) $this->data["attachments"] = array() ;
		if(!isset($this->data["multimedia"])) $this->data["multimedia"] = array() ;
		$this->data["ObjectCategory"] = $this->ObjectCategory->saveTagList($this->params["form"]["tags"]);
		$this->Transaction->begin() ;
		// Save data
		if(!$this->Document->save($this->data)) {
	 		throw new BeditaException(__("Error saving document", true), $this->Document->validationErrors);
	 	}
		if(!isset($this->data['destination'])) 
			$this->data['destination'] = array() ;
		$this->BeTree->updateTree($this->Document->id, $this->data['destination']);
	 	// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->Document->id, $this->data['Permissions'], 
	 			!empty($this->data['recursiveApplyPermissions']), 'event');
 		$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Document saved", true)." - ".$this->data["title"]);
		$this->eventInfo("document [". $this->data["title"]."] saved");
	 }
	
	 /**
	  * Delete a document.
	  */
	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}

	function addToAreaSection() {
		$this->checkWriteModulePermission();
		if(!empty($this->params['form']['objects_to_del'])) {
			$objects_to_assoc = split(",",$this->params['form']['objects_to_del']);
			$destination = $this->data['destination'];
			$this->Section->bviorHideFields = array('ObjectType', 'Version', 'Index', 'current') ;
			if(!($section = $this->Section->findById($destination))) {
				throw new BeditaException(sprintf(__("Error loading section: %d", true), $destination));
			}
			$this->Transaction->begin() ;
			for($i=0; $i < count($objects_to_assoc) ; $i++) {
				if(!$this->Section->appendChild($objects_to_assoc[$i],$section['id'])) {
					throw new BeditaException( __("Append child", true));
				}
			}
			$this->Transaction->commit() ;
			$this->userInfoMessage(__("Documents associated to area/section", true) . " - " . $section['title']);
			$this->eventInfo("documents associated to area " . $section['id']);
		}
	}

	protected function forward($action, $esito) {
		$REDIRECT = array(
			"save"	=> 	array(
							"OK"	=> "/documents/view/{$this->Document->id}",
							"ERROR"	=> "/documents/view/{$this->Document->id}" 
							), 
			"delete" =>	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents/view/{@$this->params['pass'][0]}" 
							),
			"addToAreaSection"	=> 	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	