<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
 
 
/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     mb_truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 *           This version also supports multibyte strings.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   Guy Rutenberg <guyrutenberg@gmail.com> based on the original 
 *           truncate by Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_mb_truncate($string, $length = 80, $etc = '...', $charset='UTF-8',
                                  $break_words = false, $middle = false)
{
    if ($length == 0)
        return '';
 
    if (mb_strlen($string) > $length) {
        $length -= min($length, mb_strlen($etc));
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length+1, $charset));
        }
        if(!$middle) {
            return mb_substr($string, 0, $length, $charset) . $etc;
        } else {
            return mb_substr($string, 0, $length/2, $charset) . $etc . mb_substr($string, -$length/2, (mb_strlen($string)-$length/2), $charset);
        }
    } else {
        return $string;
    }
}
 
/* vim: set expandtab: */
?>