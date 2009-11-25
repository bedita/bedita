<?php
///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
class SplitterSql {
	
	var $fnc_stringa			= null ;
	var $fnc_command			= null ;
	var $fnc_error				= null ;

	var $_LEX_BUFFER 			= "" ;
	var	$_LEX_INSIDE_STRING 	= false ;
	var	$_LEX_START_STRING 		= "" ;
	var $_BUFFER_COMMAND		= "" ;
	var $_LEX_DELIMITER_COMMAND	= ";" ;
	
	var	$LEX_STRING 		= 4 ;
	var $LEX_END_STRING		= 5 ;
	var	$LEX_INIT_COMMAND 	= 6 ;
	var	$LEX_COMMAND 		= 7 ;
	var $LEX_DELIMITER		= 8 ;
	var $LEX_COMMENT		= 9 ;
	
	var $LEX_ERROR_STRING 	= 108 ;
	var $LEX_ERROR_COMMAND 	= 109 ;
	
	var $LEX_ERROR 	= 1000 ;
	var $LEX_EOF	= 2000 ;
	
	function parse(&$queries, $SQL) {
		$queries = array() ;
		while(strlen($SQL)) {		
			$result = $this->lex($SQL) ;
			switch($result) {
				case $this->LEX_STRING: 	break ;
				case $this->LEX_COMMENT: 	break ;
				case $this->LEX_DELIMITER: 	break ;
				case $this->LEX_END_STRING: { 
					$this->_BUFFER_COMMAND  .= $this->_LEX_START_STRING . $this->_LEX_BUFFER . $this->_LEX_START_STRING ;
					$this->_LEX_BUFFER		 = "" ;
				} break ;

				case $this->LEX_INIT_COMMAND: { 
					$this->_BUFFER_COMMAND  .= " " . $this->_LEX_BUFFER ;
					$this->_LEX_BUFFER		 = "" ;
				} break ;

				case $this->LEX_COMMAND: { 
					$this->_BUFFER_COMMAND  .= " " . $this->_LEX_BUFFER ;

					$queries[] = $this->_BUFFER_COMMAND ;
					
					$this->_BUFFER_COMMAND 	= "" ; 
					$this->_LEX_BUFFER		= "" ;
				} break ;
				
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

	function lex(&$expression) {
	
		// If inside a string, return all characters until end of string
		if($this->_LEX_INSIDE_STRING) {
			$regexp = "/^([^\\". $this->_LEX_START_STRING ."]*)/xi" ;
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
		
		// If a comment, delete it
		if(preg_match("/^\s*--(.*)/xi", $expression, $matches)) {
			$expression = preg_replace("/^\s*--(.*)/xi", "", $expression) ;
			return $this->LEX_COMMENT ;
		}
		
		// If a comment on more lines, delete it
//		if(preg_match("/\*.*?\*\// si", $expression, $matches)) {
//			$expression = substr($expression, strlen($matches[0])+2) ;
//			$this->_LEX_INSIDE_COMMENT = true ;
//			return $this->LEX_COMMENT ;
//		}

		// Search for delimiter command
		if(preg_match("/^\s* delimiter \s+ (.+)/xi", $expression, $matches)) {
			$expression = substr($expression, strlen(($matches[0]))) ;
			$this->_LEX_DELIMITER_COMMAND = trim($matches[1]) ;
			
			return $this->LEX_DELIMITER ;
		}
		
		// Start of string
		if(preg_match("/^\s*(\"|\')/xi", $expression, $matches)) {
			$expression = substr($expression, 1) ;
			$this->_LEX_START_STRING = $matches[1] ;
			$this->_LEX_INSIDE_STRING = true ;
			return $this->LEX_STRING ;
		}
	
		// Get the command
		$reg = preg_replace("/\//", "\\/", $this->_LEX_DELIMITER_COMMAND);
		if(preg_match("/.*?(\"|\'|".$reg.")/si", $expression, $matches)) {

			// Get the command
			if($matches[1] == $this->_LEX_DELIMITER_COMMAND) {
				$this->_LEX_BUFFER = substr($matches[0], 0, strlen($matches[0])-strlen($this->_LEX_DELIMITER_COMMAND)) ;
				$expression = substr($expression, strlen($matches[0])) ;
			
				return $this->LEX_COMMAND ;
			} else {
				$this->_LEX_BUFFER = substr($expression, 0, strlen($matches[0]) - 1 ) ;
				$expression = substr($expression, strlen($matches[0]) - 1 ) ;

				return $this->LEX_INIT_COMMAND ;
			}
		}

		return $this->LEX_EOF ;
	}
}

?>