<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     parse_links
 * Version:  1.0
 * Author:   xho - Christiano Presuttu
 * Purpose:  Replace occurences of string starting with http:// (ftp, https), www, name@domain.ext with relative html anchor
 * Input:    string to modify, target (default to _blank)
 * -------------------------------------------------------------
 */
//error_reporting(E_ALL);
function smarty_modifier_parse_links($string, $target="_blank") {

    if ( empty($string)) {
        $smarty->trigger_error("modifier_parse_links: missing argument");
        return;
    }

	// define vars
	$string = parseLinks($string, $target);
	
	return $string;
}


function parseLinks($string, $target) {
	
	$string = eregi_replace ("[[:alpha:]]+://www", "www", $string);
	$string = ereg_replace ("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/](\.[a-z0-9-]{2,4})+", "<a href=\"\\0\" target=\"$target\">\\0</a>", $string);
	$string = ereg_replace ("www.[^<>[:space:]]+[[:alnum:]/](\.[a-z0-9-]{2,4})+", "<a href=\"http://\\0\" target=\"$target\">\\0</a>", $string);
	$string = ereg_replace ("[[:alpha:]]+@[^<>[:space:]]+[[:alnum:]/](\.[a-z0-9-]{2,4})+", "<a href=\"mailto:\\0\">\\0</a>", $string);
	
	echo $string; exit;
	
	return $string;
}


?>

