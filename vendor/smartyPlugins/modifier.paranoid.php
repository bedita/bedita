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
 * Name:     paranoid<br>
 * Date:     AUG 31, 2007
 * Purpose:  sanitarizza stringa
 * Input:<br>
 *         - contents = contents to replace
 * Example:  {$text|paranoid}

 */
 function  smarty_modifier_paranoid($string, $allowed = false) {
		 if (!empty($allowed)) {
		 	$allow = null;
		 	$allowed = (is_array($allowed)) ? $allowed : array($allowed);	 
             	foreach($allowed as $value) {
                 	$allow .= "\\$value";
             	}
         } else {
         	$allow = "";
         }
 
         if (is_array($string)) {
             foreach($string as $key => $clean) {
                 $cleaned[$key] = preg_replace("/[^{$allow}a-zA-Z0-9]/", "", $clean);
             }
         } else {
             $cleaned = preg_replace("/[^{$allow}a-zA-Z0-9]/", "", $string);
         }
         return $cleaned;
     }
	 

?>