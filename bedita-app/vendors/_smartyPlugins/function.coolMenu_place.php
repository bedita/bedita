<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     coolMenu_place
 * Purpose:  Scrive Il menu nella pagina HTML
 			 Proprieta':
			 name		Il nome del menu da visualizzare.
 * -------------------------------------------------------------
 */
function smarty_function_coolMenu_place($params, &$smarty)
{
    extract($params);
	
	// Se non c'e' il nome del menu torna errore
	if (empty($menu)) {
        $smarty->trigger_error("coolMenu_init: missing 'menu' parameter");
        return;
    }
	
	// Inizia il processo di scrittura
	$vars = &$smarty->get_template_vars() ;
	
	// Se il menu non e' stato creato torna errore
	if (!isset($vars["coolMenu"][$menu])) {
        $smarty->trigger_error("coolMenu_init: missing menu: '$menu'");
        return;
    }
	
	$name = $menu ;
	$menu = &$vars["coolMenu"][$menu] ;
	
	// Scrive i CSS
	$textCSS = "<style type='text/css'>\n";
	
	if(is_array($menu["CSS"])) {
		foreach($menu["CSS"] as $key => $value) {
			$textCSS .= ".$key { " ;
			
			if(is_array($value)) {
				foreach($value as $k => $v) {
					$textCSS .= "$k : $v ; " ;
				}
				$textCSS .= "}\n" ;
			}
		}
	}
	$textCSS .= "</style>\n";
	//echo $textCSS ;
	
	// INIZIO Scrittura codice javascript
	
	$textJScript = "<script language='JavaScript' type='text/javascript'>\n" . "<!--"."\n";
	
	// Creazione oggetto coolMenu
	$textJScript .= "var $name=new makeCM(\"$name\");\n" ;
	
	$labels = array(
		"pxBetween", "fromTop", "fromLeft", "wait", "resizeCheck", "zIndex", "onlineRoot", "offlineRoot", 
		"rows", "fillImg", "frame", "frameStartLevel", "openOnClick", "closeOnClick", "useBar", "barWidth", 
		"barHeight", "barX", "barY", "barBorderX", "barBorderY", "barClass", "barBorderClass", "menuPlacement"
	) ;
	foreach($labels as $label) {
		$textJScript .= "$name.$label = " . _coolMenu_write_val_javascript($menu[$label]) . ";\n" ;
	}
	
	// Creazione chiamate  per creazione livelli
	for($i=0; $i < count($menu["level"]) ; $i++) {
		$textJScript .= "$name.level[$i] = new cm_makeLevel(" .
						_coolMenu_write_val_javascript($menu["level"][$i]["width"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["height"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["regClass"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["overClass"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["borderX"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["borderY"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["borderClass"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["rows"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["align"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["offsetX"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["offsetY"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["arrow"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["arrowWidth"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["arrowHeight"]) . ", " .
						_coolMenu_write_val_javascript($menu["level"][$i]["roundBorder"]) .
						");\n" ;
	}
	
	// Inserisce i dati
	_coolMenu_write_valData_javascript($name, $menu["data"], $textJScript) ;
	
	$textJScript .= "$name.construct() ; \n" ;
	
	// FINE Scrittura codice javascript
	$textJScript .=  "// -->"."\n"."</script>"."\n";
	echo $textJScript ;
}

function _coolMenu_write_val_javascript(&$val) {
	if(is_string($val)) {
		return ("\"" .addslashes($val). "\"");
	} else if(is_bool($val)) {
		if($val) return  "true" ;
		else return "false" ;
	} else if(is_array($val)) {
		$text = "new Array(" ;
		for($i=0; $i < count($val) ; $i++) {
			$text .= _coolMenu_write_val_javascript($val[$i]) ;
		}
		$text .= ")" ;
		
		return $text ;
	} else {
		return "$val" ;
	}
}

function _coolMenu_write_valData_javascript($name, &$data, &$textJScript) {
	for($i=0; $i < count($data) ; $i++) {
		$textJScript .= "$name.makeMenu(" .
						_coolMenu_write_val_javascript($data[$i]["name"]) . ", " .
						
						_coolMenu_write_val_javascript($data[$i]["parent"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["text"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["link"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["target"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["width"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["height"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["regImg"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["overImg"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["regClass"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["overClass"]) . ", " .
						
						_coolMenu_write_val_javascript($data[$i]["align"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["rows"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["nolink"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["onclick"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["onmouseover"]) . ", " .
						_coolMenu_write_val_javascript($data[$i]["onmouseout"]) .
						");\n" ;
						
		if(is_array($data[$i]["children"])) {
			_coolMenu_write_valData_javascript($name, $data[$i]["children"], $textJScript) ;
		}
	}
	
}

?>