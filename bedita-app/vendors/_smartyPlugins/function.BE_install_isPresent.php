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
        throw new SmartyException("BE_install_isPresent: missing 'var' parameter");
   }

    if (empty($value)) {
        throw new SmartyException("BE_install_isPresent: missing 'value' parameter");
     }
	
    $smarty->assign($var, BE_install_present($value));
}

/* vim: set expandtab: */

?>