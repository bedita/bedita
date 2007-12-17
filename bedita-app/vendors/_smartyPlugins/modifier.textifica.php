<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     textifica
 * Purpose:  formatta il testo
 * converte in html, converte gli url, converte nl2br
 * -------------------------------------------------------------
 */
//{*$newsDetail[i].testo|strip_tags|nl2br|clickableLinks|wordwrap:20:"\n":true*}

function smarty_modifier_textifica($text)
{
	$text = htmlspecialchars($text);

	$text = nl2br($text);

	$text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '<a href="\\1" target="_blank">\\1</a>', $text);
	$text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '\\1<a href="http://\\2" target="_blank">\\2</a>', $text);
	$text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',
    '<a href="mailto:\\1">\\1</a>', $text);

	return $text;
}



?>