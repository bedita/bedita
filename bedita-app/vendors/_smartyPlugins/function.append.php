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
        $smarty->trigger_error("append: missing 'var' parameter");
        return;
    }

    if (!in_array('value', array_keys($params))) {
        $smarty->trigger_error("append: missing 'value' parameter");
        return;
    }

    $smarty->append($var, $value);
}

/* vim: set expandtab: */

?>
