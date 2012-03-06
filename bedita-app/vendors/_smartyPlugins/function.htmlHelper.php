<?php
/**
 * Smarty plugin
 * @file function.htmlHelper.php
 */

/*
 * @param array
 * @param Smarty
 * @return string of valid XHTML
 */

function smarty_function_htmlHelper($params, &$smarty)
{
	extract($params);
	
    if (@empty($fnc)) {
       throw new SmartyException("function_htmlHelper: missing 'fnc' argument");
    }
    if (@empty($args)) $args = "" ;
	
	$vs = &$smarty->getTemplateVars() ;
	$html = &$vs["html"] ;
	
	eval("echo \$html->$fnc($args);") ;
}
?>