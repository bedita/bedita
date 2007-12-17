<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     coolMenu_header
 * Purpose:  inserisce il codice javascript per la scrittura di menu
 *			 Il file coolmenu4.js deve essere in plugins di SMARTY,
 *			 oppure deve essere passato l'URL dello stesso file nel sito
 * -------------------------------------------------------------
 */
function smarty_function_coolMenu_header($params, &$smarty)
{
    extract($params);
	
    if (empty($src)) {
        echo '<script language="JavaScript" src="/scripts/coolmenus4.js" type="text/javascript"></script>'."\n";
       
    } else {
        echo '<script language="JavaScript" src="'.$src.'" type="text/javascript"></script>'."\n";
    }
}

/* vim: set expandtab: */

?>