<?php
/*
 * Smarty plugin "LinkUrl"
 * Purpose: links URLs und shortens it to a specific length
 * Home: http://www.cerdmann.com/linkurl/
 * Copyright (C) 2005 Christoph Erdmann
 * 
 * This library is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA 
 * -------------------------------------------------------------
 * Author:   Christoph Erdmann <smarty@cerdmann.com>
 * Internet: http://www.cerdmann.com
 *
 * Changelog:
 * 2004-11-24 New parameter allows truncation without linking the URL
 * 2004-11-20 In braces enclosed URLs are now better recognized
 * Usage example:
 * {$var|linkurl:"50,_blank"}
 * -------------------------------------------------------------
 */

function smarty_modifier_linkurl($string, $length=50, $target="_blank", $link=true)
	{
	if (!function_exists('kuerzen')) {
	function kuerzen($string,$length)
		{
		$returner = $string;
		if (strlen($returner) > $length)
			{
			$url = preg_match("=[^/]/[^/]=",$returner,$treffer,PREG_OFFSET_CAPTURE);
			$cutpos = $treffer[0][1]+2;
			$part[0] = substr($returner,0,$cutpos);
			$part[1] = substr($returner,$cutpos);

			$strlen1 = $cutpos;
			if ($strlen1 > $length) return substr($returner,0,$length-3).'...';
			$strlen2 = strlen($part[1]);
			$cutpos = $strlen2-($length-3-$strlen1);
			$returner = $part[0].'...'.substr($part[1],$cutpos);
			}
		return $returner;
		}
	}
	//UPDATE make sure there is an http:// on all URLs
	$string = preg_replace("/([^www])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i"," http://$2", $string);
	
	if ($link == true)
		{
		$pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>\)]+)([\s\n<>\)]|$)#sme';
		$string = preg_replace($pattern,"'$1<a href=\"$2$3\" title=\"$2$3\" target=\"$target\">'.kuerzen('$2$3',$length).'</a>$4'",$string);
		}
	elseif ($link == false)
		{
		$pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>\)]+)([\s\n<>\)]|$)#sme';
		$string = preg_replace($pattern,"kuerzen('$2$3',$length)",$string);
		}

	return $string;
	}

?>
