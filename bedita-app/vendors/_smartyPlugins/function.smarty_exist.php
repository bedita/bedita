<?
// by GeC a.k.a. Gerben (released under LGPL) ;)
// extends smarty with (remote) file existens check, smarty syntax: {if "http://www.gerben.info"|smarty_exists}{/if}

function smarty_exists($url) {

return (@fclose(@fopen($url, "r")));
}
?>