<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     linkalize
 * Purpose:  change an email address or URL into a clickable HTML link
 * TODO: fix with newline and special characters on url (comma or # etc.)
 * TODO: pass target parameters (_blank or _self(default))
 * TODO: pass a parameter "truncate" that truncate at a specified value the urls that are too long
 * -------------------------------------------------------------
 */
function smarty_modifier_linkalize($string, $target)
{
	// define vars
	$string = linkalize($string, $target);
    return $string;
}

 function linkalize($text, $target) {
 
		$text = preg_replace("/([^www])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i",
		"http://$2", $text);
		//make sure there is an http:// on all URLs

		$text = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i",
		"<A TARGET=\"$target\" HREF=\"$1\">$1</A>", $text);
		//make all URLs links

		$text = preg_replace("/[\w-\.]+@(\w+[\w-]+\.){0,3}\w+[\w-]+\.[a-zA-Z]{2,4}\b/i","<a
		href=\"mailto:$0\">$0</a>",$text);

      return $text;
}

?>