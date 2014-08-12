<?
/*
 * Smarty plugin
 * ------------------------------------------------------------
 * Type:       modifier
 * Name:       bbcode2html
 * Purpose:    Converts BBCode style tags to HTML
 * Author:     André Rabold
 * Version:    1.3c
 * Remarks:    Notice that this function does not check for
 *             correct syntax. Try not to use it with invalid
 *             BBCode because this could lead to unexpected
 *             results ;-)
 * What's new: - Fixed a bug with <li>...</li> tags (thanks
 *               to Rob Schultz for pointing this out)
 *
 *             Version 1.3b
 *             - Added more support for phpBB2:
 *               [list]...[/list:u] unordered lists
 *               [list]...[/list:o] ordered lists
 *             
 *             Version 1.3
 *             - added support for phpBB2 like tag identifier
 *               like [b:b6a0cef7ea]This is bold[/b:b6a0cef7ea]
 *               (thanks to Rob Schultz)
 *             - added support for quotes within the quote tag
 *               so [quote="foo"]bar[/quote] does work now
 *               correctly
 *             - removed str_replace functions
 *
 *             Version 1.2
 *             - now supports CSS classes:
 *                  ng_email      (mailto links)
 *                  ng_url        (www links)
 *                  ng_quote      (quotes)
 *                  ng_quote_body (quotes)
 *                  ng_code       (source code)
 *                  ng_list       (html lists)
 *                  ng_list_item  (list items)
 *             - replaced slow ereg_replace() functions
 *             - Alterned [quote] and [code] to use CSS classes
 *               instead of HTML <blockquote />, <hr />, ... tags.
 *             - Additional BBCode tags [list] and [*] to display
 *               nice HTML lists. Example:
 *                 [list]
 *                   [*]first item
 *                   [*]second item
 *                   [*]third item
 *                 [/list]
 *               The [list] tag can have an additional parameter:
 *                 [list]   unorderer list with bullets
 *                 [list=1] ordered list 1,2,3,4,...
 *                 [list=i] ordered list i,ii,iii,iv,...
 *                 [list=I] ordered list I,II,III,IV,...
 *                 [list=a] ordered list a,b,c,d,...
 *                 [list=A] ordered list A,B,C,D,...
 *             - produces well-formed output
 *             - cleaned up the code
 * ------------------------------------------------------------
 */
function smarty_modifier_bbcode2html2($message) {
  $preg = array(
    // Font and text manipulation ( [color] [size] [font] [align] )
    '/\[color=(.*?)(?::\w+)?\](.*?)\[\/color(?::\w+)?\]/si'   => "<span style=\"color:\\1\">\\2</span>",
    '/\[size=(.*?)(?::\w+)?\](.*?)\[\/size(?::\w+)?\]/si'     => "<span style=\"font-size:\\1\">\\2</span>",
    '/\[font=(.*?)(?::\w+)?\](.*?)\[\/font(?::\w+)?\]/si'     => "<span style=\"font-family:\\1\">\\2</span>",
    '/\[align=(.*?)(?::\w+)?\](.*?)\[\/align(?::\w+)?\]/si'   => "<div style=\"text-align:\\1\">\\2</div>",
    '/\[b(?::\w+)?\](.*?)\[\/b(?::\w+)?\]/si'                 => "<b>\\1</b>",
    '/\[i(?::\w+)?\](.*?)\[\/i(?::\w+)?\]/si'                 => "<i>\\1</i>",
    '/\[u(?::\w+)?\](.*?)\[\/u(?::\w+)?\]/si'                 => "<u>\\1</u>",
    '/\[center(?::\w+)?\](.*?)\[\/center(?::\w+)?\]/si'       => "<div style=\"text-align:center\">\\1</div>",
    '/\[code(?::\w+)?\](.*?)\[\/code(?::\w+)?\]/si'           => "<div class=\"ng_code\">\\1</div>",
    // [email]
    '/\[email(?::\w+)?\](.*?)\[\/email(?::\w+)?\]/si'         => "<a href=\"mailto:\\1\" class=\"ng_email\">\\1</a>",
    '/\[email=(.*?)(?::\w+)?\](.*?)\[\/email(?::\w+)?\]/si'   => "<a href=\"mailto:\\1\" class=\"ng_email\">\\2</a>",
	// [page]
    '/\[page(?::\w+)?\]www\.(.*?)\[\/page(?::\w+)?\]/si'        => "<a href=\"http://www.\\1\" target=\"_blank\" class=\"ng_page\">\\1</a>",
    '/\[page(?::\w+)?\](.*?)\[\/page(?::\w+)?\]/si'             => "<a href=\"\\1\" target=\"_blank\" class=\"ng_page\">\\1</a>",
    '/\[page=(.*?)(?::\w+)?\](.*?)\[\/page(?::\w+)?\]/si'       => "<a href=\"\\1\" target=\"_blank\" class=\"ng_page\">\\2</a>",
	// [url]
    '/\[url(?::\w+)?\]www\.(.*?)\[\/url(?::\w+)?\]/si'        => "<a href=\"http://www.\\1\" target=\"_self\" class=\"ng_url\">\\1</a>",
    '/\[url(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'             => "<a href=\"\\1\" target=\"_self\">\\1</a>",
    '/\[url=(.*?)(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'       => "<a href=\"\\1\" target=\"_self\">\\2</a>",
    // [img]
    '/\[img(?::\w+)?\](.*?)\[\/img(?::\w+)?\]/si'             => "<img src=\"\\1\" border=\"0\" />",
    '/\[img=(.*?)x(.*?)(?::\w+)?\](.*?)\[\/img(?::\w+)?\]/si' => "<img width=\"\\1\" height=\"\\2\" src=\"\\3\" border=\"0\" />",
    // [address]
    '/\[address(?::\w+)?\](.*?)\[\/address(?::\w+)?\]/si'         => "<address>\\1</address>",
	// [quote]
    '/\[quote(?::\w+)?\](.*?)\[\/quote(?::\w+)?\]/si'         => "<div class=\"quote\">\\1</div>",
    '/\[quote=(?:&quot;|"|\')?(.*?)["\']?(?:&quot;|"|\')?\](.*?)\[\/quote(?::\w+)?\]/si'   => "<div class=\"ng_quote\">Quote \\1:<div class=\"ng_quote_body\">\\2</div></div>",
    // [list]
    '/\[\*(?::\w+)?\]\s*([^\[]*)/si'                          => "<li class=\"ng_list_item\">\\1</li>",
    '/\[list(?::\w+)?\](.*?)\[\/list(?::\w+)?\]/si'           => "<ul class=\"ng_list\">\\1</ul>",
    '/\[list(?::\w+)?\](.*?)\[\/list:u(?::\w+)?\]/s'          => "<ul class=\"ng_list\">\\1</ul>",
    '/\[list=1(?::\w+)?\](.*?)\[\/list(?::\w+)?\]/si'         => "<ol class=\"ng_list\" style=\"list-style-type:decimal;\">\\1</ol>",
    '/\[list=i(?::\w+)?\](.*?)\[\/list(?::\w+)?\]/s'          => "<ol class=\"ng_list\" style=\"list-style-type:lower-roman;\">\\1</ol>",
    '/\[list=I(?::\w+)?\](.*?)\[\/list(?::\w+)?\]/s'          => "<ol class=\"ng_list\" style=\"list-style-type:upper-roman;\">\\1</ol>",
    '/\[list=a(?::\w+)?\](.*?)\[\/list(?::\w+)?\]/s'          => "<ol class=\"ng_list\" style=\"list-style-type:lower-alpha;\">\\1</ol>",
    '/\[list=A(?::\w+)?\](.*?)\[\/list(?::\w+)?\]/s'          => "<ol class=\"ng_list\" style=\"list-style-type:upper-alpha;\">\\1</ol>",
    '/\[list(?::\w+)?\](.*?)\[\/list:o(?::\w+)?\]/s'          => "<ol class=\"ng_list\" style=\"list-style-type:decimal;\">\\1</ol>",
    // the following lines clean up our output a bit
    '/<ol(.*?)>(?:.*?)<li(.*?)>/si'         => "<ol\\1><li\\2>",
    '/<ul(.*?)>(?:.*?)<li(.*?)>/si'         => "<ul\\1><li\\2>",
	
	'/\n/si'         => "<br>\n",
  );
  $message = preg_replace(array_keys($preg), array_values($preg), $message);
  return $message;
}
?>