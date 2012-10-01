<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     clickableLinks
 * Purpose:  change an email address or URL into a clickable HTML link
 * TODO: Fix with newline and other special characters in the url (comma or # etc.)
 * TODO: pass the target parameters (_blank or _self(default))
 * TODO: pass a parameter "truncate" that truncates at a certain value the urls that are too long
 * -------------------------------------------------------------
 */
function smarty_modifier_clickableLinks($text)
{
	$text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '<a href="\\1" target="_blank">\\1</a>', $text);
	
	$text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '\\1<a href="http://\\2" target="_blank">\\2</a>', $text);
	
	$text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',
    '<a href="mailto:\\1">\\1</a>', $text);
	return $text;
}

?>