<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     dump
 * Version:  1.0
 * Author:   giangi
 * Purpose:  implement print_r PHP function
 * Input:    var       =  Smarty var name
 * -------------------------------------------------------------
 */
function smarty_function_dump($params, &$smarty)
{
	$vars = &$smarty->get_template_vars();
	if (!empty($params["var"])) {
		$vars = &$params["var"] ;
	}
	
	// Stampa
	ob_start();
	echo "<pre>";
	print_r($vars);
	$_output = ob_get_contents(); 
	ob_end_clean();
	echo htmlentities($_output);
	echo "</pre>";
}

/* vim: set expandtab: */

?>