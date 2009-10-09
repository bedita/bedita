<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     BE_install_isPresent
 * Purpose:  return TRUE if the module passed is installed
 * -------------------------------------------------------------
 */
function smarty_function_BE_install_isPresent($params, &$smarty)
{
	extract($params);
    if (empty($var)) {
        $smarty->trigger_error("BE_install_isPresent: missing 'var' parameter");
        return;
    }

    if (empty($value)) {
        $smarty->trigger_error("BE_install_isPresent: missing 'value' parameter");
        return;
    }
	
    $smarty->assign($var, BE_install_present($value));
}

/* vim: set expandtab: */

?>
