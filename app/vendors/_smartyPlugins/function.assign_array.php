<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     assign_array
 * Version:  1.0
 * Author:   Jens Lehmann <jenslehmann@goldmail.de>
 * Credits:  Monte Ohrt <monte@ispi.net>
 * Purpose:  assign an array to a template variable
 * Input:    var       =  name of the array
 *           values    =  list of values (seperated by delimiter)
 *           delimiter =  value delimiter, default is ","
 *
 * Examples: {assign_array var="foo" values="bar1,bar2"}
 *           {assign_array var="foo" values="bar1;bar2;bar3" delimiter=";"}
 * -------------------------------------------------------------
 */
function smarty_function_assign_array($params, &$smarty)
{
    extract($params);

  if(empty($delimiter)) {
    $delimiter = ',';
  }

    if (empty($var)) {
        throw new SmartyException("assign_array: missing 'var' parameter");
    }

    if (!in_array('values', array_keys($params))) {
        throw new SmartyException("assign_array: missing 'values' parameter");
    }

    $smarty->assign($var, explode($delimiter,$values) );
}

/* vim: set expandtab: */

?>