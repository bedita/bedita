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
 * Controller entrata modulo Multimedia e gestione degli oggetti Image e AudioVideo
 * 
 */
class MultimediaController extends AppController {
	var $name = 'Multimedia';

	var $helpers 	= array('Bevalidation', 'BeTree', 'BeToolbar');
	var $components = array('BeAuth', 'BeTree', 'Transaction', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	// This controller does not use a model
	var $uses = array('Stream', 'Image', 'AudioVideo', 'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Tree') ;
	protected $moduleName = 'multimedia';
	
	 /**
	 * Entrata.
	 * Visualizza l'albero delle aree e l'elenco degli oggetti multimediali
	 * 
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
		
		$ot = &$conf->objectTypes ; 
		$multimedia = $this->BeTree->getDiscendents($id, null, array($ot['image'], $ot['audiovideo']), $order, $dir, $page, $dim)  ;
		$this->params['toolbar'] = &$documents['toolbar'] ;
		
		// Setup dei dati da passare al template
		$this->set('tree', 			$tree);
		$this->set('multimedia', 	$multimedia['items']);
		$this->set('toolbar', 		$multimedia['toolbar']);
		$this->set('selfPlus',		$this->createSelfURL(false)) ;
		$this->set('self',			($this->createSelfURL(false)."?")) ;
	 }


	 /**
	  * Preleva l'oggetto selezionato.
	  * Se non viene passato nessun id, presente il form per un nuovo oggetto
	  *
	  * @param integer $id
	  */
	 function view($id = null) {
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(array("id", "integer", &$id)) ;
	 	
		// Preleva l'oggetto selezionato
		$obj = null ;
		if($id) {
			// Preleva il tipo di oggetto
			$rec = $this->BEObject->recursive ;
			$this->BEObject->recursive = -1 ;
			if(!($ret = $this->BEObject->read('object_type_id', $id))) throw new BeditaException(sprintf(__("Error get object: %d", true), $id));
			$this->BEObject->recursive = $rec ;
			$model = $conf->objectTypeModels[$ret['Object']['object_type_id']] ;
			
			$this->{$model}->bviorHideFields = array('Version', 'Index', 'current', 'images', 'multimedia', 'attachments') ;
			if(!($obj = $this->{$model}->findById($id))) {
				$this->Session->setFlash(sprintf(__("Error loading object: %d", true), $id));
				return ;		
			}
			
			$imagePath 	= $this->BeFileHandler->path($id) ;
			$imageURL 	= $this->BeFileHandler->url($id) ;
		}
	
		// Formatta i campi in lingua
		if(isset($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;
		}
		
		// Setup dei dati da passare al template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
		$this->set('conf',		$conf) ;
		$this->set('CACHE',		'imgcache/');
		$this->set('MEDIA_URL',	MEDIA_URL);
		$this->set('MEDIA_ROOT',MEDIA_ROOT);
	 }

	 /**
	  * Cancella un'area.
	  */
	 function delete($id = null) {
	 	if(!isset($this->data['id'])) throw new BeditaException(sprintf(__("No data", true), $id));
		
	 	$this->Transaction->begin() ;
		 	
	 	// Cancellla i dati
	 	if(!$this->BeFileHandler->del($this->data['id'])) throw new BeditaException(sprintf(__("Error deleting object: %d", true), $id));
		 	
	 	$this->Transaction->commit() ;
	 }
	
	/**
	 * Accesso via Ajax.
	 * Utilizzato per visualizzare l'item di un oggetto multimediale in un form
	 * 
	 * @param string $filename	Il nome del file da visualizare nel form
	 */
	function get_item_form($filename = null) {
		$filename = $this->params['form']['filename'] ;
		
		// Preleva l'id dell'oggetto a partire dal filename
		if(!($id = $this->Stream->getIdFromFilename($filename))) throw new BeditaException(sprintf(__("Error get id object: %d", true), $id));
		
		$this->_get_item_form($id) ;
	}
	 
	/**
	 * Accesso via Ajax.
	 * Utilizzato per visualizzare l'item di un oggetto multimediale in un form
	 * 
	 * @param integer $id	Id dell'oggetto da linkare
	 */
	function get_item_form_by_id($id =null) {
//$this->params['form']['id'] = 10020 ;
		$this->_get_item_form($this->params['form']['id']) ;
	}

	private function _get_item_form($id) {
		$conf  = Configure::getInstance() ;
		
		foreach ($this->params['form'] as $k =>$v) {
			$$k = $v ;
		}
		
		// Preleva il tipo di oggetto
		$rec = $this->BEObject->recursive ;
		$this->BEObject->recursive = -1 ;
		if(!($ret = $this->BEObject->read('object_type_id', $id))) throw new BeditaException(sprintf(__("Error get object: %d", true), $id));
		$this->BEObject->recursive = $rec ;
		$model = $conf->objectTypeModels[$ret['Object']['object_type_id']] ;
			
		$this->{$model}->bviorHideFields = array('Version', 'Index', 'current', 'images', 'multimedia', 'attachments') ;
		if(!($obj = $this->{$model}->findById($id))) {
			$this->Session->setFlash(sprintf(__("Error loading object: %d", true), $id));
			return ;		
		}
		
		$imagePath 	= $this->BeFileHandler->path($id) ;
		$imageURL 	= $this->BeFileHandler->url($id) ;
	
		// Setup dei dati da passare al template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		$this->set('priority',	@$priority);
		$this->set('index',		@$index);
		$this->set('cols',		@$cols);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", @$id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
		$this->set('conf',		$conf) ;
		$this->set('CACHE',		'imgcache/');
		$this->set('MEDIA_URL',	MEDIA_URL);
		$this->set('MEDIA_ROOT',MEDIA_ROOT);
		
		$this->layout = "empty" ;
	}

	/**
	 * Presenta il form per l'upload di oggetti multimedia
	 *
	 */
	function frm_upload() {
	}
	
	/**
	 * Presenta il form per la selezione di oggetti multimedia
	 *
	 */
	function frm_upload_bedita() {
		$order = ""; $dir = true; $page = 1; $dim = 20 ;
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		
		$ot = &$conf->objectTypes ; 
		$multimedia = $this->BeTree->getDiscendents(null, null, array($ot['image'], $ot['audiovideo']), $order, $dir, $page, $dim)  ;
		for($i=0; $i < count($multimedia['items']) ; $i++) {
			$id = $multimedia['items'][$i]['id'] ;
			$ret = $this->Stream->findById($id) ;
			
			$multimedia['items'][$i] = array_merge($multimedia['items'][$i], $ret['Stream']) ;
			$multimedia['items'][$i]['bedita_type'] = $conf->objectTypeModels[$multimedia['items'][$i]['object_type_id']] ;
		}
		$this->params['toolbar'] = &$multimedia['toolbar'] ;
		
		// Setup dei dati da passare al template
		$this->set('multimedia', 	$multimedia['items']);
		$this->set('toolbar', 		$multimedia['toolbar']);
		$this->set('selfPlus',		$this->createSelfURL(false)) ;
		$this->set('self',			($this->createSelfURL(false)."?")) ;
	}

	/**
	 * Presenta il form per l'aggiunta di oggetti multimedia tramite URL
	 *
	 */
	function frm_upload_url() {
	}
	 
	 /**
	  * Torna un'array associativo che rappresenta l'albero aree/sezioni
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
/*	  	
	 			"save"	=> 	array(
	 									"OK"	=> "./view/{$this->Document->id}",
	 									"ERROR"	=> "./view/{$this->Document->id}" 
	 								), 
*/	 								
	 			"delete"	=> 	array(
	 									"OK"	=> "./",
	 									"ERROR"	=> "./view/{@$this->params['pass'][0]}" 
	 								), 
	 		) ;
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false ;
	 }

	 
	 function preRenderFilter($view, $layout) {
	 	$i=0;
	 }
	 
}

	