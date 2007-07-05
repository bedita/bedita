<?php
/**
 * Smarty plugin
 * @file function.bevalidatorHelper.php
 */

/*
 * @param array
 * @param Smarty
 * @return string of valid XHTML
 */

function smarty_function_bevalidationHelper($params, &$smarty)
{
	extract($params);
	
    if (@empty($fnc)) {
        $smarty->trigger_error("function_BevalidatorHelper: missing 'fnc' argument");
        return ;
    }
    if (@empty($args)) $args = "" ;
	
	$vs = &$smarty->get_template_vars() ;
	$BEVal = &$vs["bevalidation"] ;
	
	eval("echo \$BEVal->$fnc($args);") ;
}
?>