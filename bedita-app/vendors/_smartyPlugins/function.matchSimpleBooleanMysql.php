<?php

/*
 * Smarty plugin
 * --------------------------------------------------------------------
 * Type:     function
 * Name:     matchSimpleBooleanMysql
 * Version:  1.0
 * Author:   giangi
 * Purpose:  Return an array with 2 values:
 *				 a string for the search with MATCH in MySQL("match" ->);
 *				 and 1 array with the words (with '.' ) for the search with REGEXP ("regexp" ->) in MySQL.
 *           Simple search: words should be all present, and accept strings
 * Input:    var = Smarty var name expression
 * --------------------------------------------------------------------
 */
function smarty_function_matchSimpleBooleanMysql($params, &$smarty)
{
	// setup variables
   if (@empty($params["var"])) {
       throw new SmartyException("assign: missing 'var' parameter");
   }
	
	// parsing
	$expression = (string)@$params["expression"] ;
	
   if (!@empty($expression)) {
		$parse = new parseSimpleExpSearch() ;
		$expression = $parse->parse($expression) ;
   }
	
	$vars = &$smarty->getTemplateVars();
	$vars[$params["var"]] =  $expression ;
}




///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
/*
ParserExpression.
Parse a string and return an array of words (letters and numbers) and the strings " ... "
Notation:
literal	:= (a-z|A-Z|0-9)(a-z|A-Z|_|0-9|.)*
string	:= ('"') (.)* ('"')		???

Discard the elements not matching

Functions to pass to the class are:
letterale(string lett)
stringa(string text)

If it's not necessary to manage an object type, don't set the specific function
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
	
		// If inside string, return all characters until the end of the string
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
	
		// Left trim
		$expression = preg_replace("/^\s+/xi", "", $expression) ;
		
		// Search literals (dots included)
		if(preg_match("/^\s*([a-zA-Z_0-9][a-zA-Z_0-9\.]+)/xi", $expression, $matches)) {
			$expression = substr($expression, strlen($matches[0])) ;
			$this->_LEX_BUFFER = $matches[1] ;
			
			// Verify if contains dots
			if(preg_match("/\./xi", $matches[0])) {
				return $this->LEX_LETTERAL_WITH_POINTS ;
			} else {
				return $this->LEX_LETTERAL ;
			}
		}
/*
		// search for literals without dots
		if(preg_match("/^\s*([a-zA-Z_0-9][a-zA-Z_0-9]+)/xi", $expression, $matches)) {
			$expression = substr($expression, strlen($matches[0])) ;
			$this->_LEX_BUFFER = $matches[1] ;
			return $this->LEX_LETTERAL ;
		}
*/
		// String start
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
Parse a string, return an expression to use in the binary search MATCH of MySQL.
a "+" is put before every word, strings are not changed.
Discard other symbols
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
	Parameters:
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