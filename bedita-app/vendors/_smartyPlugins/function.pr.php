<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     dump
 * Version:  1.0
 * Author:   giangi
 * Purpose:  implement pr functions
 * Input:    var       =  Smarty var name
 * -------------------------------------------------------------
 */
function smarty_function_pr($params, &$smarty)
{
	$vars = &$smarty->getTemplateVars();
	if (empty($params["var"])) return ;
	
	// Stampa
	pr($params["var"]);
}

/* vim: set expandtab: */

?>