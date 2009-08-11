<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     get_type
 * Purpose:  returns a string representing item type
 * -------------------------------------------------------------
 */

/////////////////////////////////////////////
function smarty_modifier_get_type($item) {	
	if(is_integer($item)) 	return "integer" ;
	if(is_bool($item)) 		return "bool" ;
	if(is_float($item)) 	return "float" ;
	if(is_string($item)) 	return "string" ;
	if(is_object($item))	return get_class($item) . " class";
	
	return gettype($item) ;
}
/////////////////////////////////////////////



?>
