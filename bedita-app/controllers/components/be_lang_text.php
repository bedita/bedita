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
	function startup(&$controller) {
		$this->controller 	= $controller;
	}
	
	function setupForSave(&$data) {
		$result = array() ;
		if(!@count($data)) return ;
		foreach($data as $lang => $attributes) {
			foreach($attributes as $attribute => $value) {
				if($attribute != 'type') {
					$formatted = array() ;
					$formatted['lang'] = $lang ;
					$formatted['name'] = $attribute ;
					if(strlen($value) <= 255)
						$formatted['text'] = $value ;
					else
						$formatted['long_text'] = $value ;
					$translation[]=$formatted;
				}
			}
		}
		$data = $translation ;
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