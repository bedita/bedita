<?
/**
 * @author giangi@qwerg.com
 * 
 * Componente per la manipolazione delel custom properties
 * 
 */
class BeCustomPropertyComponent extends Object {
	static $SWITCH_USER		= 'user' ;
	static $SWITCH_GROUP	= 'group' ;
	
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
	 * Aggiunge 1 o + permessi a 1 o + moduli.
	 * 
	 *
	 * @param mixed $names	Se una stringa, e' il nome di modulo solo
	 * 						se un array, {0..N} nomi di moduli
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid o nome gruppo
	 * 							switch  PermissionComponent::SWITCH_USER o PermissionComponent::SWITCH_GROUP
	 * 							flag	insieme di bit con le operazioni sopra definite
	 * @return boolean
	 */
	function add($names, &$perms) {
		$this->array2perms($perms, $formatedPerms) ;
		
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			
			for($i=0; $i < count($formatedPerms) ; $i++) {
				$item = &$formatedPerms[$i] ;
				
				if($this->PermissionModule->replace($name, $item['name'], $item['switch'], $item['flag']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Formatta, per salvataggio, le custom properties passate
	 *
	 * @param unknown_type $data
	 */
	function setupForSave(&$data) {
		$tmp = array() ;
		if(!@count($data)) return ;
		
		foreach($data as $name => $value) {
//			if(!(isset($value["name"])  && isset($value["type"]) && isset($value["value"]))) continue ;
			
			switch($value["type"]) {
				case "integer" : 	{ settype($value["value"], "integer") ; } break ;
				case "bool" : 		{ settype($value["value"], "boolean") ; } break ;
				case "float" : 		{ settype($value["value"], "double") ; } break ;
				case "string" :		{ settype($value["value"], "string") ; } break ;
			}
			
			$tmp[$name] = $value["value"] ;
		}
		
		$data = $tmp ;
	}
	
}

?>