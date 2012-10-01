<?php
/**
 * Smarty plugin
 * @file function.JavascriptHelper.php
 */

/*
 * @param array
 * @param Smarty
 * @return string of valid XHTML
 */

function smarty_function_javascriptHelper($params, &$smarty)
{
	extract($params);
	
    if (@empty($fnc)) {
        throw new SmartyException("function_JavascriptHelper: missing 'fnc' argument");
    }
    if (@empty($args)) $args = "" ;
	
	$vs = &$smarty->getTemplateVars() ;

	$js = &$vs["javascript"] ;
	
	eval("echo \$js->$fnc(\"$args\");") ;
}
?>