<?php

/*
 * Smarty plugin
 * --------------------------------------------------------------------
 * Type:     function
 * Name:     matchSimpleBooleanMysql
 * Version:  1.0
 * Author:   giangi
 * Purpose:  Torna un array con 2 valori:
 *				 una stringa per la ricerca con MATCH in MySQL("match" ->);
 *				 e 1 array con le parole (con '.' ) èer la ricerca con REGEXP
 * 			 ("regexp" ->) sempre in MySQL.
 *           Ricerca semplice:
 *           le parole devono esere tutte presenti e accetta stringhe
 * Input:    var = Smarty var name expression
 * --------------------------------------------------------------------
 */
function smarty_function_matchSimpleBooleanMysql($params, &$smarty)
{
	// setup variabili
   if (@empty($params["var"])) {
       $smarty->trigger_error("assign: missing 'var' parameter");
       return;
   }
	
	// Esegue il parsing
	$expression = (string)@$params["expression"] ;
	
   if (!@empty($expression)) {
		$parse = new parseSimpleExpSearch() ;
		$expression = $parse->parse($expression) ;
   }
	
	$vars = &$smarty->get_template_vars();
	$vars[$params["var"]] =  $expression ;
}




///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
/*
ParserExpression.
Data una stringa, torna l'elenco delle parole (lettere e numeri) e le stringhe (" ... ")
Notazione:
literal	:= (a-z|A-Z|0-9)(a-z|A-Z|_|0-9|.)*
string	:= ('"') (.)* ('"')		???

tutto il resto viene scartato

Le funzioni da passare alla classe sono per:
letterale(string lett)
stringa(string text)

Se non si vuole gestire un tipo di oggetto non si setta la funzione specifica
*/
class ParserExpression {
	var $fnc_letterale					= null ;
	var $fnc_letterale_with_points 	= null ;
	var $fnc_stringa						= null ;
	var $fnc_errore						= null ;
	
	var $_LEX_BUFFER = "" ;
	var	$_LEX_INSIDE_STRING = false ;
	var	$_LEX_START_STRING = "" ;

	var $LEX_LETTERAL 	= 0 ;
	var $LEX_LETTERAL_WITH_POINTS = 10 ;
	var $LEX_NULL		 	= 1 ;
	var $LEX_STRING		= 4 ;
	var $LEX_END_STRING	= 5 ;

	var $LEX_ERROR_STRING = 6 ;
	var $LEX_ERROR 	= 1000 ;
	var $LEX_EOF	= 2000 ;
	
	function lex(&$expression) {
	
		// Se e' all'interno di una stringa torna tutti i caratteri fino a fine stringa
		if($this->_LEX_INSIDE_STRING) {
			$regexp = "/^([^(?!\\\\)\\". $this->_LEX_START_STRING ."]*)/xi" ;
//			$regexp = "/^([^\\". $this->_LEX_START_STRING ."]*)/xi" ;
			if(preg_match($regexp, $expression, $matches)) {
				$expression = substr($expression, strlen($matches[0])+1) ;
				$this->_LEX_BUFFER = $matches[1] ;
				$this->_LEX_INSIDE_STRING = false ;
				
				return $this->LEX_END_STRING ;
			} else {			
				return $this->LEX_ERROR_STRING ;
			}
			return ;
		}
	
		// Elimina gli spazi iniziali
		$expression = preg_replace("/^\s+/xi", "", $expression) ;
		
		// cerca i letterali anche con i punti
		if(preg_match("/^\s*([a-zA-Z_0-9][a-zA-Z_0-9\.]+)/xi", $expression, $matches)) {
			$expression = substr($expression, strlen($matches[0])) ;
			$this->_LEX_BUFFER = $matches[1] ;
			
			// Verifica la presenza di punti
			if(preg_match("/\./xi", $matches[0])) {
				return $this->LEX_LETTERAL_WITH_POINTS ;
			} else {
				return $this->LEX_LETTERAL ;
			}
		}
/*
		// cerca i letterali senza punti
		if(preg_match("/^\s*([a-zA-Z_0-9][a-zA-Z_0-9]+)/xi", $expression, $matches)) {
			$expression = substr($expression, strlen($matches[0])) ;
			$this->_LEX_BUFFER = $matches[1] ;
			return $this->LEX_LETTERAL ;
		}
*/
		// Inizio stringa
		if(preg_match("/^\s*(\")/xi", $expression, $matches)) {
			$expression = substr($expression, 1) ;
			$this->_LEX_START_STRING = $matches[1] ;
			$this->_LEX_INSIDE_STRING = true ;
			return $this->LEX_STRING ;
		}
	

		$expression = preg_replace("/^\s+/xi", "", $expression) ;
		if(strlen($expression)) {
//echo ":".$expression.":";		
			$this->_LEX_BUFFER = "$expression" ;
			return $this->LEX_NULL ;
		}
		
		return $this->LEX_EOF ;
	}


	function parse(&$target, $expression) {

		while(strlen($expression)) {		
			$result = $this->lex($expression) ;

			switch($result) {
				case $this->LEX_LETTERAL: 	  					{ if(isset($this->fnc_letterale)) call_user_func(array(&$target, $this->fnc_letterale), $this->_LEX_BUFFER) ; } break ;
				case $this->LEX_LETTERAL_WITH_POINTS: 	  	{ if(isset($this->fnc_letterale_with_points)) call_user_func(array(&$target, $this->fnc_letterale_with_points), $this->_LEX_BUFFER) ; } break ;

				case $this->LEX_NULL:		  { $expression = substr($expression, 1) ; }
				case $this->LEX_STRING: 	  break ;
				case $this->LEX_END_STRING:  { if(isset($this->fnc_stringa)) call_user_func(array(&$target, $this->fnc_stringa), $this->_LEX_BUFFER) ; } break ;
				
				case $this->LEX_ERROR:
				{
				
					if(isset($this->fnc_errore)) {
						call_user_func(array(&$target, $this->fnc_errore), $this->_LEX_BUFFER) ;
						return false ;
					}
					return false ;
				}
				break ;
				case $this->LEX_EOF: return true ;
			}
		}
		
		return true ;
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////
/*
Da una stringa torna un'espressione da utilizzare nella ricerca MATCH binaria di MySQL.
viene inserito un + d'avanti ad ogni parola, vengo lasciate immutate le stringhe.
Scartati tutti gli altri simboli.
*/
class parseSimpleExpSearch  {
	var $listParams = array() ;
	
	var $listWords 			= array() ;
	var $listWordsWithPoints = array() ;
	var $parser 				= null ;
	var $resultExpresison	= "" ;
	var $errorMsg				= "" ;
	var $dati ;
	
	function parseSimpleExpSearch() {
		$this->parser = new ParserExpression() ;
		
		$this->parser->fnc_letterale					= "letterale" ;
		$this->parser->fnc_letterale_with_points 	= "letteraleWithPoints" ;
		$this->parser->fnc_stringa						= "stringa" ;
		$this->parser->fnc_errore						= "errore" ;
	}
	
	function letterale($str) {
		$this->listWords[] = "+$str*" ; 
	}
	
	function letteraleWithPoints($str) {
		$this->listWordsWithPoints[] = preg_replace("/\./", "\\\\\\.", $str) ; 
	}
	
	
	function stringa($str){ 
		$this->listWords[] = "\"$str\"" ; 
	}
	
	function errore($msg){
		$this->errorMsg = $msg ;
	}
	
	/*
	Parametri:
	*/
	function parse($expression, $labels = false) {
		$ret = array() ;
		if(strlen($expression)) {
			if(!$this->parser->parse($this, $expression)) return false ;
		}	else {
			return "" ;
		}
		
		$ret["match"] 		= implode($this->listWords, " ") ;
		$ret["regexp"] 	= $this->listWordsWithPoints ;
		
		return $ret ;
	}
}

?>