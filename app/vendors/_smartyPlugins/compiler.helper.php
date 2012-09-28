<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {helper} compiler function plugin
 *
 * Type:     compiler function<br>
 * Name:     helper<br>
 * Purpose:  helps with objects like CakePHP Helpers
 * @link http://avhdesignz.de
 * @author René Haber <reen@avhdesignz.de>
 * @param string containing var-attribute and value-attribute
 * @param Smarty_Compiler
 */
function smarty_compiler_helper($tag_attrs, &$compiler)
{
	$args = explode('->', $tag_attrs);
	$arg0 = $args[0];
	unset($args[0]);
	$arg1 = implode('->', $args);
	$arg1 = preg_replace('/\$(\w+)/', '$this->_tpl_vars[\'\1\']', $arg1);
	return('echo $this->_tpl_vars[\'' . $arg0 . '\']->' . $arg1 .';');
}

/* vim: set expandtab: */

?>