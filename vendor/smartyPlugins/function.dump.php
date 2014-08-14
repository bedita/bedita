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
	if (isset($params["var"])) {
		$vars = &$params["var"] ;
	} else {
		$vars = $smarty->getTemplateVars();
	}
	
	echo html_entity_decode("<pre>");
	ob_start();
	print_r($vars);
	$_output = ob_get_contents(); 
	ob_end_clean();
	echo htmlentities($_output);
	echo html_entity_decode("</pre>");
}

/* vim: set expandtab: */

?>
