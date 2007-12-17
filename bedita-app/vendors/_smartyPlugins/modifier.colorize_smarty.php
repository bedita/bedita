<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     colorize_smarty
 * Version:  1.0
 * Author:   xho - Christiano Presuttu
 * Purpose:  Colora i tag di smarty in una stringa tramite un output filter. Usa Smarty e la fuzione esterna colorizeString
 * Input:    stringa da modificare
 * -------------------------------------------------------------
 */
//error_reporting(E_ALL);
function smarty_modifier_colorize_smarty($string, $color="red") {

    if ( empty($string)) {
        $smarty->trigger_error("modifier_colorizeSmarty: missing argument");
        return;
    }

	// definiamo le variabili
	$ld = "{";
	$rd = "}";
	$pattern = "/{$ld}\s*(.*?)\s*{$rd}/s";
	$replace = "<span style='color: $color;'>$0</span>";
	
	// get timestamp
	if(!empty($string)) {
		$value = preg_replace($pattern, $replace, $string);
		return $value;
	}
	
}


function colorizeString($matches) {

	for ($i = 0; $i < sizeof($matches); $i++) {
		$string = "<span style='color: red;'>{" . $matches[$i] . "}</span>";
	}
	
	echo $string; exit;
	
	return $string;

}


?>

