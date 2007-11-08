<?
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
		if(!class_exists('Tree')) 		loadModel('Tree') ;
		if(!class_exists('BEObject')) 	loadModel('BEObject') ;
		
		$this->Tree 	= new Tree() ;
		$this->Object 	= new BEObject() ;
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
	 * pu˜ accedere almeno in lettura.
	 *
	 */
	function getSectionsTree() {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : '' ;
		
		$tree = $this->Tree->getAll(null, $userid, null, array($conf->objectTypes['area'],$conf->objectTypes['section'])) ;
		
		return $tree ;	
	}

	/**
	 * Torna l'albero con espanso solo quello dove l'oggetto selezionato  presente.
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
	function getChildren($id = null, $status = null, $filter = 0xFF, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : '' ;
		
		if(isset($id)) {
			$objs = &  $this->Tree->getChildren($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		} else {
			$objs = &  $this->Object->findObjs($userid, $status, $filter, $order, $dir, $page, $dim) ;
		}
		
		return  $objs ;
	}
	
	/**
	 * Torna l'elenco dei discendenti di un data nodo dell'albero
	 *
	 * @param integer $id		ID del nodo
	 */
	function getDiscendents($id = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000) {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : '' ;
		
		if(isset($id)) {
			$objs = &  $this->Tree->getDiscendents($id, $userid, $status, $filter, $order, $dir, $page, $dim) ;
		} else {
			$objs = &  $this->Object->findObjs($userid, $status, $filter, $order, $dir, $page, $dim) ;
		}
		
		return  $objs ;
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