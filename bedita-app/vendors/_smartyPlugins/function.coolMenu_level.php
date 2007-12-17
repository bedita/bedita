<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     coolMenu_level
 * Purpose:  Inserisce in un oggetto coolMenu le proprieta' dei diversi livelli.
 			 I livelli sono riconsciuti in ordine di chiamata: lev. 0: prima chiamata a questa func., .... 
			 lev. N: N-esima chiamata a questa func.
 			 I valori vengo salvati nelle variabili di Smarty in : ["coolMenu"][$menu][level][].
			 Le proprieta' non passate sono tutte poste a null, salvo eccezioni indicate.
 			 Proprieta':
		 	 menu		Nome del menu in questione. Obbligatorio
			 
			 width			Larghezza
			 height			Altezza
			 regClass		Classe CSS per items in stato normale
			 overClass		Classe CSS per rollover
			 borderX		Bordo X in pixel. Deafult: 0
			 borderY		Bordo Y in pixel. Deafult: 0
			 borderClass	Classe CSS per il bordo
			 rows			1: sviluppo in righe; 0: sviluppo in colonne
			 align			Seguenti valori: "left", "right", "center", "top", "bottom", "righttop", "bottomleft", "lefttop", "topleft"
			 offsetX		offset orizzontale in pixel o %
			 offsetY		offset verticale in pixel o %
			 arrow			Nome immagine per indicare la presenza di un ulteriore livello (URL relativo a inlineRoot o offline.. oppure assoluto)
			 arrowWidth		Dimensione orizzontale IMG
			 arrowHeight	Dimensione verticale IMG
			 roundBorder	Bordo attorno in pixel o %
 *			
 * -------------------------------------------------------------
 */
function smarty_function_coolMenu_level($params, &$smarty) 
{
	// Array con i valori di Default.
	$_coolMenu_level_Default_values = array(
		 "width" 			=>	0,
		 "height" 			=>	0,
		 "regClass" 		=>	false,
		 "overClass" 		=>	false,
		 "borderX" 			=>	0,
		 "borderY" 			=>	0,
		 "borderClass" 		=>	false,
		 "rows" 			=>	0,
		 "align" 			=>	false,
		 "offsetX" 			=>	-1,
		 "offsetY" 			=>	-1,
		 "arrow" 			=>	false,
		 "arrowWidth" 		=>	0,
		 "arrowHeight" 		=>	0,
		 "roundBorder" 		=>	0,
	);
	
    extract($params);
	
	// Se non c'e' il nome del menu torna errore
	if (empty($menu)) {
        $smarty->trigger_error("coolMenu_level: missing 'menu' parameter");
        return;
    }
	
	// Se il menu non e' stato creato torna errore
	$vars = &$smarty->get_template_vars() ;
	
	if (!isset($vars["coolMenu"][$menu])) {
        $smarty->trigger_error("coolMenu_level: missing menu: '$menu'");
        return;
    }

	// Crea e definisce le proprieta' del menu
	$newLevel = $_coolMenu_level_Default_values ;
	
	$keys = array_keys($newLevel) ;
	foreach($keys as $key) {
		if(isset($$key) && !empty($$key)) {
			$newLevel[$key] = $$key ;
			
			// Per alcune variabili deve settare il tipo
			switch(strtolower($key)) {
				case "borderx":
				case "bordery":
				case "arrowwidth":
				case "arrowheight":
					settype($newLevel[$key], "integer"); 
					break ;
					
				case "offsetx":
				case "offsety":
				case "roundborder":
					if(preg_match("/[0-9]+\s*$/", $newLevel[$key])) settype($newLevel[$key], "integer") ;
					else settype($newLevel[$key], "string") ;
					break ;

			}
		}
	}
	
	// Assegna il valore
	if(!isset($vars["coolMenu"][$menu]["level"])) $vars["coolMenu"][$menu]["level"] = array() ;
	
	$vars["coolMenu"][$menu]["level"][] = $newLevel ;
}


?>