<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     date_format2
 * Purpose:  format datestamps via date() - as opposed to date_format which uses the 
 *           less flexible strftime()
 * Input:    string: input date string
 *           format: date format for output
 *           default_date: default date if $string is empty
 * -------------------------------------------------------------
 */
require_once $this->_get_plugin_filepath('shared','make_timestamp');
function smarty_modifier_date_format2($string, $format="d/m/Y H:i:s", $default_date=null)
{
    	if($string != '') {
    	return date($format, smarty_make_timestamp($string));
	} elseif (isset($default_date) && $default_date != '') {		
    	return date($format, smarty_make_timestamp($default_date));
	} else {
		return;
	}
}
?>