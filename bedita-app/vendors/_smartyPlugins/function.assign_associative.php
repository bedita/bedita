<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     assign_associative
 * Version:  1.0
 * Author:   Giangi
 * Purpose:  assign an array to a template variable
 * Input:    var       =  name of the array
 * -------------------------------------------------------------
 */
function smarty_function_assign_associative($params, &$smarty)
{
    if (empty($params["var"])) {
        throw new SmartyException("assign_array: missing 'var' parameter");
    }
	$var = @$params["var"] ;
    unset($params["var"]);
	
	$vs = &$smarty->getTemplateVars() ;
	$smarty->assign($var, $params );
}

/* vim: set expandtab: */

?>