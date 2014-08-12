<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     getUnixTimestamp
 * Purpose:  transform a date 'aaaa-mm-gg' into unix timestamp (number of seconds from '01-01-1970')
 * Author: ANDREA
 * -------------------------------------------------------------
 */

function smarty_modifier_getUnixTimestamp($data) {
	$temp = split(" ", $data) ;
	$arrDate = split("-", $temp[0]) ;
	$time = mktime (0, 0, 0, $arrDate[1], $arrDate[0], $arrDate[2]) ;
	return $time ;
}

?>