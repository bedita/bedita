<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------
 * Type:     modifier
 * Name:     mysqlvar
 * Purpose:  setup variabile da inserire in una stringa SQL 
 * 			 inserisce NULL aggiunge gli slash
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