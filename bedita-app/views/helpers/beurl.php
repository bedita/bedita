<?
/**
 * Beurl helper library.
 *
 * Torna url di base (html->here - html->base)
 *
 * @package		
 * @subpackage	
 */
class BeurlHelper extends Helper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html');
	var $paginationParams = array("page","limit","order","sort","direction");
		
	/**
	* 
	*/
	function here() {
		return str_replace($this->Html->base, "", $this->Html->here) ;
	}
	
	/**
	 * filtra i parametri passati da cake eliminado quelli relativi alla paginazione
	 * 
	 * @return array paramsFiltered
	 */
 	function filterPaginatorParams() {
 		
 		$paramsFiltered = array();
 		//pr($this->params["pass"]);
 		if (!empty($this->params["pass"])) {
	 		foreach ($this->params["pass"] as $key => $param) {
	 			if (!in_array($key, $this->paginationParams, true)) $paramsFiltered[$key] = $param;
	  		}
 		}
 		
 		return $paramsFiltered; 
 		
 	}
 	
 	/**
 	 * return controller's name
 	 */
 	 function controllerName() {
 	 	return $this->params["controller"];
 	 }
}

?>