<?php
/**
 * @author giangi@qwerg.com
 * 
 * API.
 * Accesse e gestione dell'albero dei contenuti.
 * 
 * I permessi sono espressi in un integer che raprresenta una combinazione 
 * di bit definiti nel file di configurazione (bedita.ini.php):
 * BEDITA_PERMS_READ	0x1
 * BEDITA_PERMS_MODIFY	0x2
 * 
 */
class BeTreeComponent extends Object {
	var $controller		= null ;
	var $Tree			= null ;
	var $Object			= null ;
	
	var $uses = array('Tree') ;
	
	function __construct() {
		if(!class_exists('Tree')) 	
		  App::import('Model', 'Tree') ;
		if(!class_exists('BEObject')) 	
		  App::import('Model', 'BEObject') ;
		if(!class_exists('Area')) 	
		  App::import('Model', 'Area') ;
		if(!class_exists('Section')) 	
		  App::import('Model', 'Section') ;
		
		$this->Tree 	= new Tree() ;
		$this->BEObject 	= new BEObject() ;
		$this->Area = new Area();
		$this->Section = new Section();
	} 

	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
	}

	/**
	 * Torna l'albero delle aree e delle sezioni a cui l'utente connesso
	 * pu� accedere almeno in lettura.
	 *
	 */
	function getSectionsTree() {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : '' ;
		
		$tree = $this->Tree->getAll(null, $userid, null, array($conf->objectTypes['area'],$conf->objectTypes['section'])) ;
		
		return $tree ;	
	}
	
	function getAreaForSection($section_id) {
		$area_id = $this->Tree->getRootForSection($section_id);
		if(empty($area_id)) {
			$this->log("No area found for section ".$section_id);
			throw new BeditaException(__("No area found for section $section_id",true));
		}
		return $this->Area->findById($area_id);
	}

	/**
	 * Torna l'albero con espanso solo quello dove l'oggetto selezionato � presente.
	 * Mantiene solo i figli.
	 *
	 * @param integer $id
	 */
	function expandOneBranch($id = null) {
		$tree = $this->getSectionsTree() ;
		
		for($i=0; $i < count($tree) ; $i++) {
			if(!isset($id) || !$this->_searchRootDeleteOther($tree[$i], $id)) {
				unset($tree[$i]['children']) ;
			} 
		}
		
		return $tree ;
	}
	
	/**
	 * Torna l'elenco dei figli di un data nodo dell'albero
	 *
	 * @param integer $id		ID del nodo
	 */
	function getChildren($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : null ;
		
		if(isset($id)) {
			$objs = &  $this->Tree->getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		} else {
			$objs = &  $this->BEObject->findObjs($userid, $status, $filter, $order, $dir, $page, $dim) ;
		}
		
		return  $objs ;
	}
	
	/**
	 * Return discendents of a tree node
	 *
	 * @param integer $id		node ID
	 */
	function getDiscendents($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		// Get user data
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : null ;
		if(isset($id)) {
			$objs = &  $this->Tree->getDiscendents($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		} else {
			$objs = &  $this->BEObject->findObjs($userid, $status, $filter, $order, $dir, $page, $dim) ;
		}
		return  $objs ;
	}
	
	/**
	 * Stante un path "/<...>/<...>/..." torna l'idi dell'oggetto puntato
	 * 
	 * @param integer $path	Path dell'oggetto da trovare
	 */
	function getIdFromNickname($path) {
		return $this->_getIdFromNickname($path) ;
	}
	
	/**
	 * Array of parent objects of $id...
	 *
	 * @param integer $id
	 */
	public function getParents($id = null) {
		$parents_id = array();
		if(isset($id)) {
			$parents_id = $this->Tree->getParent($id) ;
			if($parents_id === false) 
				$parents_id = array(); // ???
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);
		}
		return $parents_id;
	}

	/**
	 * update tree position of object $id with new $destination array
	 *
	 * @param integer $id
	 * @param array $destination
	 */
	public function updateTree($id, $destination) {

		$currParents = $this->getParents($id);
		// remove
		$remove = array_diff($currParents, $destination) ;
		foreach ($remove as $parent_id) {
			$this->Tree->removeChild($id, $parent_id) ;
		}
		// insert
		$add = array_diff($destination, $currParents) ;
		foreach ($add as $parent_id) {
			$this->Tree->appendChild($id, $parent_id) ;
		}

	}
	
	private function _getIdFromNickname($path) {
		$nickname = basename($path) ;
		if(@empty($nickname)) return null ;
		
		$parent_id = $this->_getIdFromNickname(dirname($path)) ;
		
		return $this->Tree->getIdFromNickname($nickname,  $parent_id) ;
	}
	
	/////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////
	/**
	 * Cancella i rami che non contengono id
	 *
	 * @param array $trees	albero dove cercare
	 * @param integer $id	oggetto da cercare
	 */
	private function _searchRootDeleteOther(&$tree, $id) {
		// Se la radice cercata
		if($tree['id'] == $id) {
			for($i=0; $i < count($tree['children']) ; $i++) {
				unset($tree['children'][$i]['children']) ;
			}
			
			return true ;
		}
		
		// Cerca tra i discendenti
		$found = null ;
		for($i=0; $i < count($tree['children']) ; $i++) {
			if($this->_searchRootDeleteOther($tree['children'][$i], $id)) {
				$found = $i ;
			} 
		}
		
		// Se ha trovato cancella i rami da escludere
		if(isset($found)) {
			$tmp = $tree['children'][$found] ;
			
			unset($tree['children']) ;
			$tree['children'] = array($tmp) ;
			
			return true ;
		}
		
		return false ;
	}
}

?>