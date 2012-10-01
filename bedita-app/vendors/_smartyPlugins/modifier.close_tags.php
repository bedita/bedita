<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty close all unclosed xhtml tags
 *
 * Type:   modifier<br>
 * Name:   close_tags<br>
 * example {$string|truncate:10|close_tags}
 *
 * @param  string
 * @return string
 */
function smarty_modifier_close_tags($string)
{
  // match opened tags
  if(preg_match_all('/<([a-z\:\-]+)[^\/]>/', $string, $start_tags))
  {
    $start_tags = $start_tags[1];

    // match closed tags
    if(preg_match_all('/<\/([a-z]+)>/', $string, $end_tags))
    {
      $complete_tags = array();
      $end_tags = $end_tags[1];
    
      foreach($start_tags as $key => $val)
      {   
        $posb = array_search($val, $end_tags);
        if(is_integer($posb))
        {
          unset($end_tags[$posb]);
        }
        else
        {
          $complete_tags[] = $val;
        }
      }
    }
    else
    {
      $complete_tags = $start_tags;
    }
    
    $complete_tags = array_reverse($complete_tags);
    for($i = 0; $i < count($complete_tags); $i++)
    {
      $string .= '</' . $complete_tags[$i] . '>';
    }
  }
  return $string;
}
?>