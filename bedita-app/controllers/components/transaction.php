<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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

/**
 * Componente per la gestione di transazioni su + model contemporaneamente
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class TransactionComponent extends Object {
	
	private static $dbConfig	= 'default' ;
	private static $db			= null ;
	private static $transFS		= null;
	const INIT=0;
	const START=10;
	const ROLLBACK=20;
	const COMMIT=30;
	private $status = NULL;
	
	function __construct($dbConfigName = 'default', $pathTmp = '/tmp') {
		$this->init($dbConfigName, $pathTmp) ;
	} 
	
	function init($dbConfigName = 'default', $pathTmp = '/tmp') {
		if(!isset(self::$transFS)) {
			self::$transFS = new TransactionFS($pathTmp) ;
		}
		self::$dbConfig 		= (isset($dbConfigName))?$dbConfigName:'default' ;
		self::$transFS->tmpPath = $pathTmp ;
		
		$this->setupDB() ;
		$this->status = self::INIT;
		
	}

	public function started() {
		return ($this->status === self::START);
	}

	/**
	 * Start a transaction
	 *
	 * @return unknown
	 */
	public function begin() {
		Configure::write("bedita.transaction",1);
		$this->setupDB() ;
		self::$transFS->begin() ;
		if(!self::$db->execute('START TRANSACTION')) return false ;
		$this->status = self::START;
		return true  ;
	}
	
	public function end() {
		// status should be START, COMMIT or ROLLBACK
		if($this->status === self::START)
			return $this->commit();
		if($this->status === self::COMMIT || $this->status === self::ROLLBACK)
			return true;
		else 
			throw new BeditatException(__("Bad transaction state",true));
	}

	public function commit() {
		$this->setupDB() ;
		
		self::$transFS->commit() ;
		if(!self::$db->execute('COMMIT')) return false ;
		$this->status = self::COMMIT;
		Configure::delete("bedita.transaction");
		return true;
	}

	public function rollback() {

		$ret = false;		
		try {
			$this->setupDB() ;
			
	        self::$transFS->rollback() ;
			if(!self::$db->execute('ROLLBACK') && $this->status === self::START) 
			  throw new BeditaException("Rollback error!");
			$this->status = self::ROLLBACK;
			$ret = true;
		} catch (BeditaException $be) {
			
			$this->log($be->errorTrace());
						
        } catch (Exception $e) {

        	$errTrace =  $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
        	$this->log($errTrace);
       	
        }
					
		Configure::delete("bedita.transaction");
        return $ret;
	}
	
	//////////////////////////////////////////
	//////////////////////////////////////////
	private function setupDB() {
		if(isset(self::$db)) return ;
		
		if(!class_exists('ConnectionManager')) {
			App::import('Model', 'ConnectionManger') ;
		}
		
		if(isset(self::$dbConfig))
			self::$db =& ConnectionManager::getDataSource(self::$dbConfig);
	}

	/**
	 * Create a file in $path from data $arrData
	 *
	 * @param string $path		path to new file
	 * @param string $arrData	data for file
	 * @return string
	 */
	function makeFromData($path, &$arrData) {
		return self::$transFS->makeFileFromData($path, $arrData) ;
	}
	
	
	/**
	 * Create a new file, from an old one
	 *
	 * @param string $path				path of the new file
	 * @param string $pathFileSource	source file
	 * @return string
	 */
	function makeFromFile($path, $pathFileSource) {
		return self::$transFS->makeFileFromFile($path, $pathFileSource) ;
	}

	/**
	 * Replace data in file
	 *
	 * @param string path		file
	 * @param string $arrDati	data
	 * @return boolean
	 */
	function replaceData($path, &$arrDati) {
		return self::$transFS->replaceFileData($path, $arrDati) ;
	}	
	
	/**
	 * Remove a file
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	function rm($path) {
		return self::$transFS->rmFile($path) ;
	}

	/**
	 * Move a file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function mv($newPath, $pathSource) {
		return self::$transFS->mvFile($newPath, $pathSource) ;
	}

	/**
	 * Copy a file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function cp($newPath, $pathSource) {
		return self::$transFS->cpFile($newPath, $pathSource) ;
	}
	
	/**
	 * Create a directory
	 *
	 * @param unknown_type $newDir
	 * @param unknown_type $mode
	 * @return unknown
	 */
	function mkdir($newDir, $mode = 0775) {
		return self::$transFS->mkdir($newDir, $mode) ;
	}

	/**
	 * Remove a directory
	 *
	 * @param unknown_type $rmDir
	 * @return unknown
	 */
	function rmdir($rmDir) {
		return self::$transFS->rmdir($rmDir) ;
	}

	/**
	 * Move a directory and its content
	 *
	 * @param unknown_type $pathNewParent
	 * @param unknown_type $oldPath
	 * @return unknown
	 */
	function mvDir($pathNewParent, $oldPath) {
		return self::$transFS->mvDir($pathNewParent, $oldPath) ;
	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * 
 * On object creation, if there are pending operations, 'rollback' is called.
 * Operations are permanent, after 'commit'.
 * File to delete copies are moved to a temporary directory.
 * 
 * sample data structure:
 * array(
 *	array("cmd" => <comando>, "roolCmd" => <comando>, "params" => array(..)),
 *	array("cmd" => <comando>, "roolCmd" => <comando>, "params" => array(..)),
 *	...................................................
 * )
 * "cmd"		command executed
 * "roolCmd"	rollback command
 * "params"	rollback parameters
*/
class TransactionFS {
	var $commands				= null ;			// array con i dati
	var $errorMsg				= "" ;				// Ultimo msg d'errore
	var $error					= false ;			// true se c'e' stato un errore
	var $tmpPath				= '/tmp';
	
	function __construct($tmpPath = '/tmp') {
		$this->commands = array() ;
		$this->tmpPath 	= $tmpPath ;
	}

	function begin() {
		$this->rollback() ;
		
		$this->commands = array() ;
	}
	
	function commit() {
		while(count($this->commands)) {
			$item = array_pop($this->commands) ;
			$cmd = $item["cmd"] ;
			switch($item["cmd"]){
				case 'replaceFileData': @unlink($item["params"]["source"]); break ;
				case 'rmFile': 			@unlink($item["params"]["source"]); break ;
			}
		}

		return true ;
	}
	
	function rollback() {
		while(count($this->commands)) {
			$item = array_pop($this->commands) ;

			call_user_func_array(array(&$this, $item["rollCmd"]), $item["params"]) ;
		}

		return true ;
	}
	
	
	
	/**
	 * Create a file from data
	 *
	 * @param string $path		file path
	 * @param string $arrDati	data
	 * @return string
	 */
	function makeFileFromData($path, &$arrDati) {

		if(($fp  = fopen($path, "wb")) === false) {
            throw new BEditaIOException("Error opening file $path");
		}
		
		$ret = fwrite(fopen($path, "wb"), $arrDati, strlen($arrDati)) ;

		// Save the operation
		$item = array("cmd"	=> "makeFile", "rollCmd" => "_rollMakeFile", 	
		  "params" => array("path" => $path)) ;
		array_push($this->commands, $item);

		return $path ;
	}
	
	
	/**
	 * Create a file from another one
	 *
	 * @param string $path				file path
	 * @param string $pathFileSource	source file path
	 * @return string
	 */
	function makeFileFromFile($path, $pathFileSource) {
		if(!copy($pathFileSource, $path)) 
		    throw new BEditaIOException("Error in file copy $pathFileSource - $path");
		
		// Save the operation
		$item = array("cmd"	=> "makeFile", "rollCmd" => "_rollMakeFile", 
		  "params" => array("path" => $path)) ;
		array_push($this->commands, $item);

		return $path ;
	}

	/**
	 * Replace file data
	 *
	 * @param string path		File path
	 * @param string $arrDati	Data
	 * @return boolean
	 */
	function replaceFileData($path, &$arrDati) {
		// temporary file
		$tmpfname = tempnam ($this->tmpPath, "UF");
		if(!is_string($tmpfname)) 
            throw new BEditaIOException("Bad temp file $tmpfname");

		// old data, save and copy
		if(!copy($path, $tmpfname)) 
            throw new BEditaIOException("Error in file copy $path - $tmpfname");
		
        if(fwrite(fopen($path, "wb"), $arrDati, strlen($arrDati)) == -1) 
            throw new BEditaIOException("Error opening/writing file $path");

		// save the operation
		$item = array("cmd"	=> "replaceFileData", "rollCmd" => "_rollReplaceFileData", 	
		  "params" => array("source" => $tmpfname, "dest" => $path)) ;
		array_push($this->commands, $item);

		return true ;
	}	
	
	/**
	 * Remove a file
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	function rmFile($path) {
		$tmpfname = tempnam ($this->tmpPath, "UF");
		if(!is_string($tmpfname))
            throw new BEditaIOException("Bad temp file $tmpfname");
		
		// old data, save and copy
		if(!copy($path, $tmpfname))
           throw new BEditaIOException("Error in file copy $path - $tmpfname");
		
		if(!unlink($path))
           throw new BEditaIOException("Error in unlink $path");
		
		// Save the operation
		$item = array("cmd"	=> "rmFile", "rollCmd" => "_rollRmFile", 	
		  "params" => array("source" => $tmpfname, "dest" => $path)) ;
		array_push($this->commands, $item);
		
		return true ;
	}

	/**
	 * Move a file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function mvFile($newPath, $pathSource) {
		// salva e copia i vecchi dati
		if(!copy($pathSource, $newPath))
           throw new BEditaIOException("Error in file copy $pathSource - $newPath");
		
		if(!unlink($pathSource))
          throw new BEditaIOException("Error in unlink $pathSource");
		
		// Save the operation
		$item = array("cmd"	=> "mvFile", "rollCmd" => "_rollMvFile", 	
		  "params" => array("source" => $newPath, "dest" => $pathSource)) ;
		array_push($this->commands, $item);

		return true ;
	}

	/**
	 * Copy a file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function cpFile($newPath, $pathSource) {
		// old data, save and copy
		if(!copy($pathSource, $newPath))
           throw new BEditaIOException("Error in file copy $pathSource - $newPath");
		
		// Save the operation
		$item = array("cmd"	=> "cpFile", "rollCmd" => "_rollCpFile", 	
		  "params" => array("source" => $newPath)) ;
		array_push($this->commands, $item);

		return true ;
	}
	
	/**
	 * 
	 * Change directory
	 *
	 * @param string $newDir
	 * @return unknown
	 */
	function chdir($newDir) {
		$oldDir = getcwd() ;
		if(!chdir($newDir))
            throw new BEditaIOException("Error changing dir $newDir");
		$newDir = getcwd() ;
		
		// Save the operation
		$item = array("cmd"	=> "chdir", "rollCmd" => "_rollChdir", 	
		  "params" => array("path" => $oldDir)) ;
		array_push($this->commands, $item) ;
		
		return true ;
	}
	
	/**
	 * Create a directory
	 *
	 * @param unknown_type $newDir
	 * @param unknown_type $mode
	 * @return unknown
	 */
	function mkdir($newDir, $mode = 0777) {
		if(!mkdir($newDir, $mode)) 
			throw new BEditaIOException("Error creating dir $newDir");
		$oldDir = getcwd() ;
		if(!chdir($newDir)) 
			throw new BEditaIOException("Error changing dir $newDir");
		$newDir = getcwd() ;
		if(!chdir($oldDir)) 
			throw new BEditaIOException("Error changing dir $oldDir");

		// Save the operation
		$item = array("cmd"	=> "mkdir", "rollCmd" => "_rollMkdir", 	"params" => array("path" => $newDir)) ;
		array_push($this->commands, $item); 
		
		return true ;
	}

	/**
	 * Remove a directory
	 *
	 * @param unknown_type $rmDir
	 * @return unknown
	 */
	function rmdir($rmDir) {

		$oldDir = getcwd() ;
		if(!chdir($rmDir)) 
		   throw new BEditaIOException("Error changing dir $rmDir");
		$rmDir = getcwd() ;
		if(!chdir($oldDir))
            throw new BEditaIOException("Error changing dir $oldDir");
		$rmPerms = fileperms($rmDir);
		
		// Cancella
		if(!rmdir($rmDir)) 
            throw new BEditaIOException("Error removing dir $rmDir");
		
		// Save the operation
		$item = array("cmd"	=> "rmdir", "rollCmd" => "_rollRmdir", 	
		      "params" => array("path" => $rmDir, "perms" => $rmPerms)) ;
		array_push($this->commands, $item);
		
		return true ;
	}

	/**
	 * Move a directory and its content
	 *
	 * @param unknown_type $pathNewParent
	 * @param unknown_type $oldPath
	 * @return unknown
	 */
	function mvDir($pathNewParent, $oldPath) {

		$pathDir = getcwd() ;
		if(!chdir($oldPath))
           throw new BEditaIOException("Error changing dir $oldPath");
		if(!chdir("..")) 
           throw new BEditaIOException("Error changing dir $oldPath/..");
		$oldParentPath = getcwd() ;
		if(!chdir($pathDir)) 
           throw new BEditaIOException("Error changing dir $pathDir");
		
		// Move
		$args = array() ; $ret = 0 ;
		exec("mv $oldPath $pathNewParent", $args, $ret) ;
		if($ret) 
           throw new BEditaIOException("Error in 'mv $oldPath $pathNewParent'");
		
		// new path
		$name = basename($oldPath) ;
		if(substr($pathNewParent, -1) == "/") $newPath = "$oldParentPath"."$name" ;
		else $newPath = "$oldParentPath/$name" ;
		
		// Save the operation
		$item = array("cmd"	=> "mvDir", "rollCmd" => "_rollMvDir", 	
		  "params" => array("path" => $newPath, "target" => $oldParentPath)) ;
		array_push($this->commands, $item);
		
		return $newPath ;
	}


	//////////////////////////////////////////////////
	/**
	 * Rollback functions
	 */
	private function _rollMakeFile($path) {
		if(!unlink($path)) return false ;
		return true ;
	}
	
	private function _rollReplaceFileData($source, $dest) {
		if(!copy($source, $dest)) return false ;
		if(!unlink($source)) return false ;
		return true ;
	}

	private function _rollRmFile($source, $dest) {
		if(!copy($source, $dest)) return false ;
		if(!unlink($source)) return false ;
		return true ;
	}
	
	private function _rollMvFile($source, $dest) {
		if(!copy($source, $dest)) return false ;
		if(!unlink($source)) return false ;
		return true ;
	}

	private function _rollCpFile($source) {
		if(!unlink($source)) return false ;
		return true ;
	}

	function _rollChdir($path) {
		if(!chdir($path)) return false ;
		return true ;
	}

	function _rollMkdir($path) {
		if(!rmdir($path)) return false ;
		return true ;
	}
	
	function _rollRmdir($path, $perms) {
		if(!mkdir($path, $perms)) return false ;
		return true ;
	}

	function _rollMvDir($path, $target) {
		// Move
		$args = array() ; $ret = 0 ;
		exec("mv $path $target", $args, $ret) ;
		if($ret) return false ;
		return true ;
	}
}

?>