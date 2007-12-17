<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     coolMenu_create
 * Purpose:  Crea un oggetto coolMenu e ne definisce le proprieta'. Se c'e' un altro menu con lo stesso nome lo sostituisce
 			 I valori vengo salvati nelle variabili di Smarty in : ["coolMenu"][$name]...
 			 Proprieta':
			 name			Nome del menu. Obbligatorio.
			 
			 pxBetween 		Spazio tra i top items. In pixel o %. Default: 0. Non attivo se si usa "menuPlacement"
			 menuPlacement	Piazza i top item. Default 0. 3 modi diversi x il suo uso:
			 				1 - Indica l'allineamento: "right", "center", "bottom", "bottomcenter"
							2 - Indicazione di pixel (array con un valore per item) se rows=1 indica "left" else "top"
							3 - come sopra ma in percentuale
			 fromTop		Top position (pixel e %). Default: 0. Ignorata con: menuPlacement > 0 AND rows=0 (in columns) 
			 fromLeft		Left position come sopra. Ignarata per menuPlacement > 0 AND rows=1
			 wait			Tempo in millisecondi di attesa per l'apertura di un menu. Deafult: 100
			 resizeCheck	Se 1 esegue il refresh per il ridimensionamento della pagina. DEfault: 1. Non valido per NS4
			 zIndex			zIndex. Default: 400. A causa bug Opera viene settato in tutti i CSS
			 onlineRoot		URL di root per un URL http://. Default: "./"
			 offlineRoot	URL di root per un URL file://. Default: "./"
			 rows			1 = Menu in righe; 0 = menu in colonne. Default: 1.
			 fillImg		Immagine di riempimento dell'items. Path o relativo (con online.. o offline...) o assoluto. Deafult: ""
			 frame			1 se si usano i frame (diverse parti di menu in frame diversi). Default: 0
			 frameStartLevel Definisce quale livello deve andare in un altro frame. DEfault: 0
			 openOnClick	Se 1 apre un menu con un click invece che onMouseOver. DEfault: 0
			 closeOnClick	Se 1 chiude con un click invece che mouse out. DEfault: 0
			 
			 useBar			Se 1 disegna una Barra che contiene i menu. Default: 0
			 barWidth		Larghezza in pixel o %. Default: "menu". La stessa larghezza del menu
			 barheight		Altezza in pixel o %. Default: "menu". La stessa altezza del menu
			 barX			Left position in pixel o %. Default: "menu". La stessa del menu
			 barY			Top position in pixel o %. Default: "menu". La stessa del menu
			 barBorderX		Dimensioni in Pixel larghezzza bordo. Default: 0
			 barBorderY		Dimensioni in Pixel altezza bordo. Default: 0
			 barClass		Classe CSS da applicare alla bar. Default: ""
			 barBorderClass Classe CSS da applicare al bordo della bar. Default: ""
 *			
 * -------------------------------------------------------------
 */
function smarty_function_coolMenu_create($params, &$smarty) 
{
	// Array con i valori di Default.
	$_coolMenu_create_Default_menu = array(
		 "pxBetween" 		=>	0,
		 "menuPlacement" 	=>	0,
		 "fromTop" 			=>	0,
		 "fromLeft" 		=>	0,
		 "wait" 			=>	400,
		 "resizeCheck" 		=>	0,
		 "zIndex" 			=>	400,
		 "onlineRoot" 		=>	"./",
		 "offlineRoot" 		=>	"./",
		 "rows" 			=>	1,
		 "fillImg" 			=>	"",
		 "frame" 			=>	0,
		 "frameStartLevel" 	=>  0,
		 "openOnClick" 		=>	0,
		 "closeOnClick" 	=>	0,
		 "useBar" 			=>	0,
		 "barWidth" 		=>	"menu",
		 "barHeight" 		=>	"menu",
		 "barX" 			=>	"menu",
		 "barY" 			=>	"menu",
		 "barBorderX" 		=>	0,
		 "barBorderY" 		=>	0,
		 "barClass" 		=>	"",
		 "barBorderClass" 	=>  "",
	);

    extract($params);
	
	// Se non c'e' il nome torna errore
	
	if (empty($name)) {
        $smarty->trigger_error("coolMenu_create: missing 'name' parameter");
        return;
    }
	
	// Crea e definisce le proprieta' del menu
	$newMenu = $_coolMenu_create_Default_menu ;
	
	$keys = array_keys($newMenu) ;
	foreach($keys as $key) {
		if(isset($$key) && !empty($$key)) $newMenu[$key] = $$key ;
		
			// Per alcune variabili deve settare il tipo
			switch(strtolower($key)) {
				case "barborderx":
				case "barbordery":
					settype($newMenu[$key], "integer"); 
					break ;
				case "fromtop":
				case "fromleft":
				case "barwidth":
				case "barheight":
				case "barx":
				case "bary":
					if(preg_match("/[0-9]+\s*$/", $newMenu[$key])) settype($newMenu[$key], "integer") ;
					else settype($newMenu[$key], "string") ;
					break ;
			}

	}
	
	// Assegna il valore
	$vars = &$smarty->get_template_vars() ;
	$vars["coolMenu"][$name] = $newMenu ;
}


?>