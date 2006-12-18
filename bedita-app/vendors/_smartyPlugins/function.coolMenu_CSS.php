<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     coolMenu_CSS
 * Purpose:  Crea e/o modifica i dati per una classe CSS. E l'inserisce tra le variabili del menu dato.
 			 Al menu viene assegnata una classe di default con i valori di default per ogni classe inserita:
			 
			 .clCMAbs{position:absolute; width:10; height:10; left:0; top:0; visibility:hidden ; z-index:300}.
			 
 			 I valori vengo salvati nelle variabili di Smarty in : ["coolMenu"][$menu]["CSS"][$name]...
			 Parametri:
			 	menu		Nome del menu in questione. Obbligatorio
				name		Nome della Classe CSS da modificare e/o inserire. Default: "clCMAbs"
				value		Una stringa con il formato CSS, es.:
							position:absolute; width:10; height:10; left:0; top:0; visibility:hidden; z-index:300
 *			
 * -------------------------------------------------------------
 */
function smarty_function_coolMenu_CSS($params, &$smarty) 
{
	// Array con i valori di Default per una Classe CSS.
	$_coolMenu_CSS_clCMAbs = array(
		 "position" 	=>	"absolute",
		 "left" 		=>	"0",
		 "top" 			=>	"0",
		 "z-index" 		=>	"300",
	);
	
	$_coolMenu_CSS_Default = array(
		 "position" 	=>	"absolute",
		 "left" 		=>	"0",
		 "top" 			=>	"0",
		 "z-index" 		=>	"300",
	);

	$vars = &$smarty->get_template_vars() ;
	
    extract($params);
	
	// Se non c'e' il nome del menu torna errore
	if (empty($menu)) {
        $smarty->trigger_error("coolMenu_CSS: missing 'menu' parameter");
        return;
    }
	
	// Se non c'e' il valore torna errore
	if (empty($value)) {
        $smarty->trigger_error("coolMenu_CSS: missing 'value' parameter");
        return;
    }
	
	// Se il menu non e' stato creato torna errore
	if (!isset($vars["coolMenu"][$menu])) {
        $smarty->trigger_error("coolMenu_CSS: missing menu: '$menu'");
        return;
    }
	
	// Se non c'e' la classe di default la inserisce
	if(!isset($vars["coolMenu"][$menu]["CSS"]["clCMAbs"]))
		$vars["coolMenu"][$menu]["CSS"]["clCMAbs"] = $_coolMenu_CSS_clCMAbs ;
	
	// Se il nome non e' definito setta la classe di default
	if (empty($name)) $name = "clCMAbs" ;
	
	// Crea e definisce le proprieta' del menu
	$_coolMenu_create_Default_menu ;
	
	// Se la classe CSS non e' ancora presente ne inserisce i valori di default
	if(!isset($vars["coolMenu"][$menu]["CSS"][$name])) {
		$vars["coolMenu"][$menu]["CSS"][$name] = $_coolMenu_CSS_Default ;
	}
	
	// preleva tutti i valori passati
	$arrValues = preg_split("/\s*;\s*/", $value);
	foreach($arrValues as $valueCSS) {
		$valueCSS = trim($valueCSS);
		if(empty($valueCSS)) continue ;
		
		// Preleva il valore e lo inserisce
		$temp = preg_split("/\s*:\s*/", $valueCSS);
		if(count($temp) < 2) continue ;
		
		$temp[0] = trim($temp[0]); $temp[1] = trim($temp[1]); 
		if(empty($temp[0]) || empty($temp[1])) continue ;
		
		$vars["coolMenu"][$menu]["CSS"][$name][$temp[0]] = $temp[1] ;
	}
	
}


?>