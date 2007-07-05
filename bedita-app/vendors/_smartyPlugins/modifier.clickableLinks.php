<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     clickableLinks
 * Purpose:  change an email address or URL into a clickable HTML link
 * Da fare: deve funzionare anche con l'a capo e coi caratteri speciali nell'url(la virgola o il # ad ex.)
 * Da fare passare i parametri target (_blank oppure _self(default))
 * Da fare: passare un parametro "truncate" che tronca ad un dato valore gli url troppo lungi
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