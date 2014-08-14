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
 * Name:     timelineBiblio<br>
 * Date:     giugno 22, 2005
 * Purpose:  convert ; to <<hr>> e bbcode [i][/i] to <i></i>
 * Input:<br>
 *         - contents = contents to replace
 *         - preceed_test = if true, includes preceeding break tags
 *           in replacement
 * Example:  {$text|timelineBiblio}

 */
function smarty_modifier_timelineBiblio($string)
{
	$preg = array(
	'/\[i(?::\w+)?\](.*?)\[\/i(?::\w+)?\]/si' => "<i>\\1</i>",
    '/\[b(?::\w+)?\](.*?)\[\/b(?::\w+)?\]/si' => "<b>\\1</b>",
	);
	$string = preg_replace(array_keys($preg), array_values($preg), $string);
   	$string = str_replace(";","<hr>",$string);
  return $string;
}


?>