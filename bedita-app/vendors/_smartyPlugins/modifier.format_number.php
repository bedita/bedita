<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     format_number
 * Purpose:  format number with decimal separator and thousands separator
 * -------------------------------------------------------------
 */

/////////////////////////////////////////////
function smarty_modifier_format_number($size, $decimal = 0, $sep_dec = "", $sep_migliaia = ".") {
   return number_format($size, $decimal, $sep_dec, $sep_migliaia) ; 
}
/////////////////////////////////////////////



?>
