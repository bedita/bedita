<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     assign_concat
 * Purpose:  assign a value to a template variable
 * -------------------------------------------------------------
 */
function smarty_function_assign_concat($params, &$smarty)
{
	extract($params);
    if (empty($params["var"])) {
        $smarty->trigger_error("assign_concat: missing 'var' parameter");
        return;
    }
	$var = $params["var"] ;
	unset($params["var"]) ;
	$value = implode($params, "");
	
    $smarty->assign($var, $value);
}

/* vim: set expandtab: */

?>