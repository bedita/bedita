<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     sbodyer
 * Purpose:  apre un file, elimina da body a body e mette in variabile
 * -------------------------------------------------------------
 */


function smarty_modifier_sbodyer($path)
{
	$stream = fread(fopen($path, "r"), filesize($path)) ;

//"/<body(\s+\w+\s*\=\s*\"(.*)\")*\s*>(.*)<\/body\s*>/si"
// matches[0]: body con TAG; matches[1]: body senza TAG
$bodyStream = "" ;
if(preg_match("/<body[^>]*>(.*)<\/body\s*>/si", $stream, $matches)){
	$bodyStream = $matches[1] ;
} 

	return $bodyStream;
}



?>