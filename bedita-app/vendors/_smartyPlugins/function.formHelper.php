<?php
/**
 * Smarty plugin
 * @file function.formHelper.php
 */

/*
 * @param array
 * @param Smarty
 * @return string of valid XHTML
 */

function smarty_function_formHelper($params, &$smarty)
{
	extract($params);
	
    if (@empty($fnc)) {
        $smarty->trigger_error("function_formHelper: missing 'fnc' argument");
        return ;
    }
    if (@empty($args)) $args = "" ;
	$vs = &$smarty->getTemplateVars() ;
	$form = &$vs["form"] ;
	
	eval("echo \$form->$fnc($args);") ;
}
?>