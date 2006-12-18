<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     comma2HR<br>
 * Date:     May 26, 2005
 * Purpose:  convert ; to <<hr>>
 * Input:<br>
 *         - contents = contents to replace
 *         - preceed_test = if true, includes preceeding break tags
 *           in replacement
 * Example:  {$text|comma2HR}

 */
function smarty_modifier_comma2HR($string)
{
    return str_replace(";","<hr>",$string);
}


?>
