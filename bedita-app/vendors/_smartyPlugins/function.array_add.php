<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     array_add
 * Version:  1.0
 * Author:   Channelweb
 * Purpose:  add items to an array (create new if array doesn't exist)
 * Input:    var       =  name of the array
 * -------------------------------------------------------------
 */
function smarty_function_array_add($params, &$smarty)
{
    if (empty($params["var"])) {
        $smarty->trigger_error("assign_array: missing 'var' parameter");
        return;
    }
	$var = @$params["var"] ;
    unset($params["var"]);

	$vs = &$smarty->getTemplateVars() ;
	if(@is_array($vs[$var])) {
		foreach($params as $key => $value) $vs[$var][$key] = $value ;
	} else {
		$smarty->assign($var, $params );
	}
}

/* vim: set expandtab: */

?>