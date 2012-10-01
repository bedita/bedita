<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     sbodyer
 * Purpose:  open a file, delete from body to /body and load into variable
 * -------------------------------------------------------------
 */


function smarty_modifier_sbodyer($path)
{
	$stream = fread(fopen($path, "r"), filesize($path)) ;

//"/<body(\s+\w+\s*\=\s*\"(.*)\")*\s*>(.*)<\/body\s*>/si"
// matches[0]: body with TAG; matches[1]: body without TAG
$bodyStream = "" ;
if(preg_match("/<body[^>]*>(.*)<\/body\s*>/si", $stream, $matches)){
	$bodyStream = $matches[1] ;
} 

	return $bodyStream;
}



?>