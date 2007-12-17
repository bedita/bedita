<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     getUnixTimestamp
 * Purpose:  Torna da una data (aaaa-mm-gg) un unix timestamp (num di seccondi da ...)
 * Authoe: ANDREA
 * -------------------------------------------------------------
 */

function smarty_modifier_getUnixTimestamp($data) {
	$temp = split(" ", $data) ;
	$arrDate = split("-", $temp[0]) ;
	$time = mktime (0, 0, 0, $arrDate[1], $arrDate[0], $arrDate[2]) ;
	return $time ;
}

?>

