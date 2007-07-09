<?
/**
 * @author giangi@qwerg.com
 * 
 * API.
 * Accesse e gesione dell'albero dei contenuti.
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
	
	var $uses = array('Tree') ;
	
	function __construct() {
		if(!class_exists('Tree')) 	loadModel('Tree') ;
		
		$this->Tree = new Tree() ;
	} 

	/**
	 * Torna l'albero delle aree e delle sezioni a cui l'utente conesso
	 * pu˜ accedere almeno in lettura.
	 *
	 */
	function getSectionsTree() {
		$conf  = Configure::getInstance() ;
		
		// Preleva l'utente connesso
		$userid = (isset($this->controller->BeAuth->user["userid"])) ? $this->controller->BeAuth->user["userid"] : '' ;
		
		$tree = $this->Tree->getAll(null, $userid, null, ($conf->objectTypes['area'] | $conf->objectTypes['section'])) ;
		
		return $tree ;	
	}
		
	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
	}
	
}

?>