<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */
App::import('Model', 'Schema');
class DumpModel extends AppModel {
	var $useTable = "objects";
};    

/**
 * Utility class that handles schema/db issues
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeSchema extends CakeSchema
{
	var $useTable = false;

	function executeQuery($db, $script) {
		$sql = file_get_contents($script);
		$queries = array();
		$SplitterSql = new SplitterSql() ;
		$SplitterSql->parse($queries, $sql) ;
		foreach($queries as $q) {	
			if(strlen($q)>1) {
				$res = $db->execute($q);
				if($res === false) {
					throw new BeditaException("Error executing query: ".$q."\n" . "db error msg: " . $db->error ."\n");
				}
			}
		}
	}
	
	function executeInsert($db, $script) {
		// split in blocks
		$blocks = $this->createChunks($script);

		// call query to avoid foreign key checks, on data insert
		$res = $db->execute("SET FOREIGN_KEY_CHECKS=0");
		
		// call parse on every block and populate $queries array
		$queries = array();
		$SplitterSql = new SplitterSql() ;
		foreach($blocks as $key => $block) {
			$SplitterSql->parse($queries, $block) ;
			// call queries (except for views creation)
			foreach($queries as $q) {	
				if(strlen($q)>1) {
					if(strpos($q,"CREATE ALGORITHM") === false) {
						//echo "executing query " . $q . "\n";
						$res = $db->execute($q);
						if($res === false) {
							throw new Exception("Error executing query: ".$q."\n");
						}
					}
				}
			}
		}
	}

	function simpleInsert($db, $sqlFileName) {
		$handle = fopen($sqlFileName, "r");
		if($handle === FALSE) 
			throw new Exception("Error opening file: ".$sqlFileName);
		$q = "";
		while(!feof($handle)) {
			$line = fgets($handle);
			if($line === FALSE && !feof($handle)) {
				throw new Exception("Error reading file line");
			}
			if(strncmp($line, "INSERT INTO ", 12) == 0) {
				if(strlen($q) > 0) {
					$res = $db->execute($q);
					if($res === false) {
						throw new Exception("Error executing query: ".$q."\n");
					}
				}
				$q="";
			}
			$q .= $line;
		}
		// last query...
		if(strlen($q) > 0) {
			$res = $db->execute($q);
			if($res === false) {
				throw new Exception("Error executing query: ".$q."\n");
			}
		}
	}
	
	private function createChunks($script) {
		$chunks = array();
		$handle = fopen($script, "r");
		$data = "";
		$counter=0;$ccounter=0;
		$endchar = ");\n";
		while (!feof($handle)) {
		   $buffer = fgets($handle, 4096);
		   $data.=$buffer;
		   if($counter>500 && ( substr( $buffer, strlen( $buffer ) - strlen( $endchar ) ) == $endchar ) ) { // check if $counter > 500 and $buffer ends with );
		   		$counter=0;
				$chunks[$ccounter++]=$data;
				$data="";
		   } else {
				$counter++;
		   }
		}
		fclose($handle);
		if(empty($chunks)) {
			$chunks[0]=$data;
		}
		return $chunks;
	}

	public function readTables() {
		$schemaTabs = $this->read();
		if(isset($schemaTabs['tables']['missing'])) {
			unset($schemaTabs['tables']['missing']);
		}
		return $schemaTabs['tables'];
    }

    public function tableMetaData($model, $db) {
    	$tableMeta = $this->__columns($model);
    	$tableMeta['indexes'] = $db->index($model);
    	return $tableMeta;
    }
    
	public function tableList() {
		return array_keys($this->readTables());
    }
    
	public function tableDetails(array &$tables, $handle) {

    	fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");

		$dumpModel = new DumpModel();
    	foreach ($tables as $t) {
    		$dumpModel->setSource($t); 
    		$select = $dumpModel->find('all');
			foreach ($select as $sel) {
				$fields = "";
				$values = "";
				$count = 0;
				foreach ($sel['DumpModel'] as $k=>$v) {
					if($v !== NULL) {
						if($count > 0) {
							$fields .= ",";
							$values .= ",";
						}
						$fields .= "`$k`";
						$values .= "'".addslashes($v)."'";
						$count++;
					}
				}
				$res = "INSERT INTO $t (".$fields.") VALUES ($values);\n";
    			fwrite($handle, $res);
			}
    	}
    	return $res;
    }
}

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
