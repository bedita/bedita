<?
/**
 * @author giangi@qwerg.com
 * 
 * Componente per la manipolazione delle proprieta' in lingue diverse
 * dall'oggetto di cui fanno parte.
 * 
 */
class BeLangTextComponent extends Object {
	
	var $controller			= null ;
	
	function __construct() {
	} 
	
	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
	}
	
	/**
	 * Formatta, per salvataggio, i campi in lingua passati
	 * via POST.
	 *
	 * @param unknown_type $data
	 */
	function setupForSave(&$data) {
		$formatted = array() ;
		if(!@count($data)) return ;
		
		foreach($data as $id => $value) {
			if(!(isset($value["name"])  && isset($value["lang"]))) continue ;

			$value["text"] 		= isset($value["text"])?trim($value["text"]) : null ;
			$value["long_text"] 	= isset($value["long_text"])?trim($value["long_text"]) : null ;
			
			if(empty($value["text"])  && empty($value["long_text"])) continue ;
			
			$formatted[$value["lang"]] = $value ;
		}
		
		$tmp = array() ;
		foreach($formatted as $lang => $value) {
			$tmp[] = $value ;
		}	
		
		$data = $tmp ;
	}
	
	/**
	 * Formatta, per la vista, i campi in lingua.
	 * Forma un array:
	 * 	nome_campo array:
	 * 				lingua_1 testo
	 * 				...............
	 * 				lingua_n testo
	 *
	 * @param unknown_type $data
	 */
	function setupForView(&$data) {
		$tmp = array() ;
		
		for($i=0; $i < count($data) ; $i++) {
			$item = &$data[$i] ;
			
			if(!isset($tmp[$item["name"]]))	$tmp[$item["name"]] = array() ;
			$tmp[$item["name"]][$item["lang"]] = (!@empty($item["text"])) ? @$item["text"] : @$item["long_text"] ;
		}
		
		$data = $tmp ;
	}

}

?>