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

/**
 * Short description for class.
 *
 * Controller entrata modulo Documento e gestione documenti
 * 
 */
class DocumentsController extends AppController {
	var $name = 'Documents';

	var $helpers 	= array('BeTree', 'BeToolbar', 'Fck');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array(
		'Stream', 'Area', 'Section', 'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Document', 'Tree',
		'Image', 'Video', 'Audio', 'BEFile'
		) ;
	protected $moduleName = 'documents';
	
	 /**
	 * Show content tree and doc list
	 */
	 function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		
		// Preleva l'albero delle aree e sezioni
		$tree = $this->BeTree->expandOneBranch($id) ;
		
		$documents = $this->BeTree->getDiscendents($id, null, $conf->objectTypes['documentAll'], $order, $dir, $page, $dim)  ;
		$this->params['toolbar'] = &$documents['toolbar'] ;
		
		// Setup dei dati da passare al template
		$this->set('tree', 		$tree);
		$this->set('documents', $documents['items']);
		$this->set('toolbar', 	$documents['toolbar']);
	 }

	 /**
	  * Preleva il documento selezionato.
	  * Se non viene passato nessun id, presente il form per un nuovo documento
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
		$bedita_items = $this->BeTree->getDiscendents(null, null, array($ot['image'], $ot['audio'], $ot['video']))  ;
		$this->params['toolbar'] = &$bedita_items['toolbar'] ;
		$this->set('bedita_items', 	$bedita_items['items']);
		$this->set('toolbar', 		$bedita_items['toolbar']);
		// end#bedita_items
		$this->set('object',	$obj);
		$this->set('multimedia',$obj['multimedia']);
		$this->set('attachments',$obj['attachments']);
		$this->set('galleries', (count($galleries['items'])==0) ? array() : $galleries['items']);
		$this->set('tree', 		$tree);
		$this->set('parents',	$parents_id);		
		$this->selfUrlParams = array("id", $id);    		
	 }

	 /**
	  * Creates/updates new document
	  */
	 function save() {
	 	
 		$this->checkWriteModulePermission();
 		
 	 	if(empty($this->data)) 
 	 	    throw new BeditaException( __("No data", true));
 		
		$new = (empty($this->data['id'])) ? true : false ;
		
	 	// Verifica i permessi di modifica dell'oggetto
	 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
	 			throw new BeditaException(__("Error modify permissions", true));
	 	
	 	// Formatta le custom properties
	 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;

	 	// Formatta i campi d tradurre
	 	$this->BeLangText->setupForSave($this->data["LangText"]) ;
	 	
	 	if(!isset($this->data["attachments"])) $this->data["attachments"] = array() ;
	 	if(!isset($this->data["multimedia"])) $this->data["multimedia"] = array() ;
		$this->Transaction->begin() ;
 		
		// Salva i dati
		if(!$this->Document->save($this->data)) {
	 		throw new BeditaException(__("Error saving document", true), $this->Document->validationErrors);
	 	}

		/**
 		* inserimento nell'albero
 		*/
		if(($parents = $this->Tree->getParent($this->Document->id)) !== false) {
			if(!is_array($parents)) $parents = array($parents) ;
		} else {
			$parents = array() ;
		}
		if(!isset($this->data['destination'])) $this->data['destination'] = array() ;

		// rimuove
		$remove = array_diff($parents, $this->data['destination']) ;
		foreach ($remove as $parent_id) {
			$this->Tree->removeChild($this->Document->id, $parent_id) ;
		}
		
		// Inserisce
		$add = array_diff($this->data['destination'], $parents) ;
		foreach ($add as $parent_id) {
			$this->Tree->appendChild($this->Document->id, $parent_id) ;
		}
		
	 	// aggiorna i permessi
		$perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
		if(!$this->Permission->saveFromPOST(
	 			$this->Document->id, $perms,
	 			(empty($this->data['recursiveApplyPermissions'])?false:true), 'document')
	 		) {
	 			throw new BeditaException( __("Error saving permissions", true));
	 	}	 	
 		$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Document saved", true)." - ".$this->data["title"]);
		$this->eventInfo("document [". $this->data["title"]."] saved");
	 }
	
	 /**
	  * Delete a document.
	  */
	function delete() {
		$this->checkWriteModulePermission();
		$documents_to_del = array();
		if(!empty($this->params['form']['documents_to_del'])) {
			$documents_to_del = $this->params['form']['documents_to_del'];
		} else {
			if(empty($this->data['id'])) throw new BeditaException(__("No data", true));
			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			$documents_to_del = $this->data['id'];
		}
		$this->Transaction->begin() ;
		// Delete data
		$dToDel = split(",",$documents_to_del);
		for($i=0;$i<count($dToDel);$i++) {
			if(!$this->Document->delete($dToDel[$i]))
				throw new BeditaException( sprintf(__("Error deleting document: %d", true), $dToDel[$i]));
		}
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $documents_to_del);
		$this->eventInfo("documents $documents_to_del deleted");
	}

	 /**
	  * Torna un'array associativo che rappresneta l'albero aree/sezioni
	  * a partire dai dati passati via POST.
	  *
	  * @param unknown_type $data
	  * @param unknown_type $tree
	  */
	 private function _getTreeFromPOST(&$data, &$tree) {
	 	$tree = array() ;
	 	$IDs  = array() ;
	 	
	 	// Crea i diversi rami
	 	$arr = preg_split("/;/", $data) ;
	 	for($i = 0 ; $i < count($arr) ; $i++) {
	 		$item = array() ;
	 		$tmp = split(" ", $arr[$i] ) ;
	 		foreach($tmp as $val) {
	 			$t  = split("=", $val) ;
	 			$item[$t[0]] = ($t[1] == "null") ? null : ((integer)$t[1]) ; 
	 		}
	 		
	 		$IDs[$item["id"]] 				= $item ;
	 		$IDs[$item["id"]]["children"] 	= array() ;
	 	}

		// Crea l'albero
		foreach ($IDs as $id => $item) {
			if(!isset($item["parent"])) {
				$tree[] = $item ;
				$IDs[$id] = &$tree[count($tree)-1] ;
			}
			
			if(isset($IDs[$item["parent"]])) {
				$IDs[$item["parent"]]["children"][] = $item ;
				$IDs[$id] = &$IDs[$item["parent"]]["children"][count($IDs[$item["parent"]]["children"])-1] ;
			}
		}
		
		unset($IDs) ;
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
	 		) ;
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false ;
	 }

	 
	 function preRenderFilter($view, $layout) {
	 	$i=0;
	 }
	 
}

	