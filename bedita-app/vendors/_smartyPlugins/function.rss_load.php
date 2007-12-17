<?php

/*
* Smarty plugin
* -------------------------------------------------------------
* Type:     function
* Name:     rss_load
* Version:  1.0
* Date:     June 13, 2003
* Author:    Monte Ohrt <monte@ispi.net>
* Purpose:  fetch rss feed, assign to template var
* Requires: * php must be compiled --with-xml
*           * onyx rss lib: http://www.readinged.com/onyx/rss/
* Install:  * put onyx-rss.php in include_php path
*           * put function.rss_load.php in plugin dir
*           * call from within a template
*
* Input:    file = local file or URL of rss
*           assign = template var to assign parsed data
*           caching = true/false (opt, default true)
*           cache_dir = directory to store cache (opt,
*                       default=$smarty->cache_dir)
*           cache_lifetime = number of seconds cache file
*                            is valid (opt, default=3600)
*           debug_mode = sets onyx debug mode (opt)
*
* Examples: {rss_load
*              file="http://www.php.net/news.rss"
*              assign="phpnews"}
*           {section name=rss loop=$phpnews.items}
*              <a href="{$phpnews.items[rss].link}">
*              {$phpnews.items[rss].title}</a><br>
*           {/section}
* -------------------------------------------------------------
*/
function smarty_function_rss_load($params, &$smarty)
{
    if ($params['file'] == '') {
        $smarty->trigger_error("rss_load: missing 'file' parameter");
        return;
    }
    if ($params['assign'] == '') {
        $smarty->trigger_error("rss_load: missing 'assign' parameter");
        return;
    }
   
   require_once('onyx-rss.php');
   
   if(class_exists('ONYX_RSS')) {
   
      $_rss =& new ONYX_RSS();

      if(isset($params['cache_dir'])) {
         $_rss->setCachePath($params['cache_dir']);      
      } else {   
         $_rss->setCachePath($smarty->cache_dir);
      }
      if(isset($params['cache_lifetime'])) {
         $_rss->setExpiryTime($params['cache_lifetime']);      
      } else {   
         $_rss->setExpiryTime(3600); // one hour
      }
      if(isset($params['debug_mode'])) {
         $_rss->setDebugMode($params['debug_mode']);
      }
      if(!isset($params['caching']) || $params['caching']) {
         $_cache_file = 'rss_cache.' . urlencode($params['file']);
      } else {
         $_cache_file = null;      
      }

      if($_rss->parse($params['file'], $_cache_file)) {
         $smarty->assign($params['assign'], $_rss->RSSData);
      } else {
           $smarty->trigger_error("rss_load: unable to read '". $params['file'] . "'");      
      }
   } else {
        $smarty->trigger_error("rss_load: unable to load ONYX_RSS library");            
   }   
}

/* vim: set expandtab: */

?>