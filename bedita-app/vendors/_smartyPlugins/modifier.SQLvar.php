<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------
 * Type:     modifier
 * Name:     mysqlvar
 * Purpose:  variable setup to insert into an SQL string 
 * 			 insert NULL add slashes
 * ------------------------------------------------------------
 */
function smarty_modifier_SQLvar($var) {
//function mysqlvar($var) {
	if(is_numeric($var)) return $var ;
	else if(is_string($var)) return ("'".addslashes($var)."'") ;
	else return " NULL " ;

	return $var ;
}

?>