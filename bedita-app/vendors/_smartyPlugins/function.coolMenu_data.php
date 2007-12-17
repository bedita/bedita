<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     coolMenu_data
 * Purpose:  Inserisce in un oggetto coolMenu i dati da visualizzare.
 			 2 sono i modi.
			 1 - Passando per ogni chiamata un items. 
			 	 Parametri:
			 	 menu		Nome del menu nel quale inserire i dati. Obligatorio
				 name		Nome da assegnare all'item. Obbligatorio
				 parent		Nome dell'items Parent, Se '', l'item inserito e' a livello + alto. Default: ''
				 text		Testo da visualizzare. Default: ''
				 link		Default: ''.
				 target		Target dove ha effetto il link. Default: ''
				 regImg		Path (realitvo o assoluto) per l'immagine al posto del testo. Default: ''
				 overImg	Path (realitvo o assoluto) per l'immagine al posto del testo in roolover. Default: ''
				 nolink		Blocca il link dell'items. Default: false
				 
				 onclick		[Da definire]. Default: false
				 onmouseover	[Da definire]. Default: false
				 onmouseout		[Da definire]. Default: false
				 
				 I parametri che seguono servono se non presenti nel livello definito x l'item
				 o per sosvrascrivere quello predefinito di livello:
				 
	 			 width			Larghezza
			 	 height			Altezza
				 regClass		Classe CSS per items in stato normale
				 overClass		Classe CSS per rollover
				 rows			1: sviluppo in righe; 0: sviluppo in colonne
				 align			Seguenti valori: "left", "right", "center", "top", "bottom", "righttop", "bottomleft", "lefttop", "topleft"
				 
			 2 - Passando un array associativo dove ogni item puo' avere i campi sopra indicati, meno "nome", + un array "children" 
			     con gli items figli.
				 Parametri:
				 menu		Nome del menu nel quale inserire i dati. Obligatorio
				 values		(Array) [vedere descrizione]
				 
				 
 *			
 * -------------------------------------------------------------
 */
function smarty_function_coolMenu_data($params, &$smarty) 
{
	// Array con i valori di Default.
	$_default_values = array(
		 "name"		=> '',
		 "parent"	=> '',
		 "text"		=> '',
		 "link"		=> false,
		 "target"	=> '',
		 "regImg"	=> false,
		 "overImg"	=> false,
		 "nolink"	=> false,
		 "onclick"	=> false,
		 "onmouseover"	=> false,
		 "onmouseout"	=> false,
		 "width" 			=>	false,
		 "height" 			=>	false,
		 "regClass" 		=>	false,
		 "overClass" 		=>	false,
		 "rows" 			=>	-1,
		 "align" 			=>	false,
	);
	
	// Se non c'e' il nome del menu torna errore
	if (empty($params["menu"])) {
        $smarty->trigger_error("coolMenu_level: missing 'menu' parameter");
        return;
    }
	
	// Se il menu non e' stato creato torna errore
	$vars = &$smarty->get_template_vars() ;
	
	if (!isset($vars["coolMenu"][$params["menu"]])) {
        $smarty->trigger_error("coolMenu_level: missing menu: '".$params["menu"]."'");
        return;
    }
	
	$menu = &$vars["coolMenu"][$params["menu"]] ;
	
	// Setup dati per l'inserimento dei dati
	if(!isset($menu["index"])) $menu["index"] = 0 ;												// numero di items presenti
	if(!isset($menu["data"]) || !is_array($menu["data"])) $menu["data"] = array() ;				// array dati
	if(!isset($menu["parents"]) || !is_array($menu["parents"])) $menu["parents"] = array() ;	// nomeItems => reference Items
	
	// Verifica se deve processare un array o un item singolo
	if(empty($params["values"]) || !is_array($params["values"])) {	// Item singolo
		// Se non c'e' il nome torna errore
		if (empty($params["name"])) {
	        $smarty->trigger_error("coolMenu_level: missing 'name' parameter");
	        return;
	    }
		
		$params["children"] = array() ;
		$values[] = $params ;
		
		_coolMenu_data_setupDati($menu, $values, $_default_values) ;
	} else {			// Item multiplo
		_coolMenu_data_setupDati($menu, $params["values"], $_default_values) ;
	}
//exit;
}


/*
_coolMenu_data_setupDati.
Setta i dati nel menu.
menu			menu dove inserire i dati
values			array di array associativi con i dati
default_values	valori di default
*/
function _coolMenu_data_setupDati(&$menu, &$values, &$_default_values) {
//print_r($values);
	for($i = 0; $i < count($values) ; $i++) {
		
		// Inserisce un nuovo item
		$newItems = $_default_values ;
		
		$keys = array_keys($newItems) ;
		foreach($keys as $key) {
			if(isset($values[$i][$key]) && !empty($values[$i][$key])) $newItems[$key] = $values[$i][$key] ;
		}
		
		// Setta nome
		if(empty($newItems["name"])) $newItems["name"] = "items" . $menu["index"] ;
		
		// Inserisce nell'albero se il parent c'e' ed e' valido, altrimenti inserisce come items radice
		if(isset($newItems["parent"]) && isset($menu["parents"][$newItems["parent"]])) {
			$menu["parents"][$newItems["parent"]]["children"][] = $newItems ;
			$newItems = &$menu["parents"][$newItems["parent"]]["children"][count($menu["parents"][$newItems["parent"]]["children"])-1] ;
		} else {
			$menu["data"][] = $newItems ;
			$newItems = &$menu["data"][count($menu["data"])-1] ;
		}
		
		$menu["parents"][$newItems["name"]] = &$newItems ;
		
		$menu["index"]++ ;
		
		for($x=0; $x < count($values[$i]["children"]) ; $x++) {
			$values[$i]["children"][$x]["parent"] = $newItems["name"] ;
		}
		_coolMenu_data_setupDati($menu, $values[$i]["children"], $_default_values) ;
		
		unset($newItems);
	}
}


?>