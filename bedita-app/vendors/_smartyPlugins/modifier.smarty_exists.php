<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     smarty_exists
 * Version:  1.0
 * Author:   by GeC a.k.a. Gerben (released under LGPL) ;)
 * Purpose:  extends smarty with (remote) file existens check
 * usage:	smarty syntax: {if "http://www.gerben.info"|smarty_exists}{/if}
 * -------------------------------------------------------------
 */

function smarty_modifier_smarty_exists($url) {

//return (@fclose(@fopen($url, "r")));
return (file_exists($url));
}

?>