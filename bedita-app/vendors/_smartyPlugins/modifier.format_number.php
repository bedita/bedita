<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     format_number
 * Purpose:  formatta il numero con decimale e separatore delle 
 *			  migliaia 
 * -------------------------------------------------------------
 */

/////////////////////////////////////////////
function smarty_modifier_format_number($size, $decimal = 0, $sep_dec = "", $sep_migliaia = ".") {
   return number_format($size, $decimal, $sep_dec, $sep_migliaia) ; 
}
/////////////////////////////////////////////



?>
