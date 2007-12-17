<?php

/*
* Smarty plugin
*
* Type: function
* Name: lastrss_load
* Version: 0.1
* Date: August 8, 2004
* Author: Monte Ohrt <monte@ispi.net> and Stu Wilson <dammitjanet@rainbowfly.net>
* Purpose: fetch rss feed, assign to template var
* Requires: * php must be compiled --with-xml
* * lastrss lib: http://lastrss.webdot.cz/
* Install: * put lastrss.php in include_php path or in plugin dir
* * put function.lastrss_load.php in plugin dir
* * call from within a template
*
* Input: file = local file or URL of rss
* assign = template var to assign parsed data
* cache_dir = directory to store cache (opt, default=$smarty->cache_dir)
* cache_lifetime = number of seconds cache file is valid (opt, default=3600)
*
* Examples: {lastrss_load
* file="http://www.php.net/news.rss"
* assign="phpnews"}
* {section name=rss loop=$phpnews.items}
* <a href="{$phpnews.items[rss].link}">
* {$phpnews.items[rss].title}</a><br>
* {/section}
*
*/

function smarty_function_lastrss_load($params, &$smarty)
{
if ($params['file'] == '') {
	$smarty->trigger_error("lastrss_load: missing 'file' parameter");
	return;
}

if ($params['assign'] == '') {
	$smarty->trigger_error("lastrss_load: missing 'assign' parameter");
	return;
}

require_once('lastRSS.php');

if(class_exists('lastRSS')) {
		$_rss =& new lastRSS();
		if(isset($params['cache_dir'])) {
			$_rss->cache_dir = $params['cache_dir'];
		} else {
			$_rss->cache_dir = $smarty->cache_dir;
		}

		if(isset($params['cache_lifetime'])) {
			$_rss->cache_time=$params['cache_lifetime'];
		} else {
			$_rss->cache_time=3600; // one hour
		}

		if($rs = $_rss->get($params['file'])) {
			$smarty->assign($params['assign'], $rs);
		} else {
			$smarty->trigger_error("lastrss_load: unable to read '{$params['file']}'");
		}

		} else {
		$smarty->trigger_error("lastrss_load: unable to load lastRSS library");
		}
}

/* vim: set expandtab: */

?>