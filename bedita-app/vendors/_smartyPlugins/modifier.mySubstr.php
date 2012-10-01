<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     	modifier
 * Name:     	mySubstr
 * Author:		Bato
 * Purpose:  	Return part of a string.
 *				If start is non-negative, the returned string will start at the start'th position in string, counting from zero. 
 *				For instance, in the string 'abcdef', the character at position 0 is 'a', the character at position 2 is 'c', and so forth.
 *				If start is negative, the returned string will start at the start'th character from the end of string.
 *				If length[optional] is given and is positive, the string returned will contain at most length characters beginning 
 *				from start (depending on the length of string). If string is less than start characters long, FALSE will be returned.
 *
 * Example:		string|mySubstr:"2":"4"		if string="abcdefg" return "cdef"
 *
 * Parameters:	string, start[optional, default=0], lenght[optional]
 * -------------------------------------------------------------
 */


function smarty_modifier_mySubstr($string, $start=0, $length=false) {

	if ($length) return substr($string, $start, $length);
	
	else return substr($string, $start);

}


?>