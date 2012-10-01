<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     append
 * Purpose:  append a value to a template variable
 * -------------------------------------------------------------
 */
function smarty_function_append($params, &$smarty)
{
	extract($params);
    if (empty($var)) {
        throw new SmartyException("append: missing 'var' parameter");
    }

    if (!in_array('value', array_keys($params))) {
        throw new SmartyException("append: missing 'value' parameter");
    }

    $smarty->append($var, $value);
}

/* vim: set expandtab: */

?>