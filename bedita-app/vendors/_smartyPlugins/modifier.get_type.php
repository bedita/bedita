<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     get_type
 * Purpose:  torna una stringa che rappresenta il tipo di dato
 * -------------------------------------------------------------
 */

/////////////////////////////////////////////
function smarty_modifier_get_type($item) {	
	if(is_integer($item)) 	return "integer" ;
	if(is_bool($item)) 		return "bool" ;
	if(is_float($item)) 	return "float" ;
	if(is_string($item)) 	return "string" ;
	
	return "stream" ;
}
/////////////////////////////////////////////



?>
