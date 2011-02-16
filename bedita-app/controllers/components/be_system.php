<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * Component for low level operations
 * 
 *  - importing/exporting bedita data: database, media.
 *  - system information
 *  - .....
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
 class BeSystemComponent extends Object {
	
	//var $uses = array('BEObject', 'Stream');
	//var $components = array('Transaction');
	
	private $basepath;
	
	function __construct() {
	}

	private function mysqlInfo($db, array& $res) {
	    if ($cid = @mysqli_connect($db->config['host'], $db->config['login'], $db->config['password'])) {
			$res['dbServer'] = @mysqli_get_server_info($cid);
			$res['dbClient'] = @mysqli_get_client_info();
        	@mysql_close($cid);		
		}
	}
	
	private function postgresInfo($db, array& $res) {
	    if ($cid = @pg_connect("host=" .$db->config['host'] . " dbname=" . $db->config['database'] . 
	    	" user=" . $db->config['login'] . " password=" . $db->config['password'])) {
	    	$info = @pg_version($cid);
	    	$res['dbServer'] = $info["server"];
			$res['dbClient'] = $info["client"];
        	@pg_close($cid);		
		}
	}
	
	public function systemInfo() {
		$res = array();
	    $db = ConnectionManager::getDataSource('default');
		$driverMethod = $db->config['driver'] . "Info";
	    $this->{$driverMethod}($db, $res);
	    $dbNames = array("mysql" => "MySQL", "postgres" => "PostgreSQL");
		$res['db'] = $dbNames[$db->config['driver']];
		$res['dbHost'] = $db->config['host'];
		$res['dbName'] = $db->config['database'];
		$res['phpVersion'] = phpversion();
		$res['phpExtensions'] = get_loaded_extensions();
		$res['osVersion'] = php_uname();
		$res['beditaPath'] = APP;
		$res['cakePath'] = CAKE_CORE_INCLUDE_PATH;
		return $res;
	}
	
/*	
	public function update($sqlDataFile=null,$media=null) {
	}

	public function import($exportFile) {
	}
	
	public function export() {
		
		$this->$basepath = $this->setupTempDir();

		// step 1 - save db data to sql file
		$sqlFileName = $this->basepath."bedita-data.sql";
		$this->saveDump($sqlFileName);
		
		// step 2 - save MEDIA_ROOT to export folder
		$this->copyFolder(MEDIA_ROOT,$this->basepath.'media');
		
		// step 3 - compress export folder
		$this->compressFolder($this->basepath);
	}
	
	function saveDump($sqlFileName) {
		$dbDump = new DbDump();
		$tables = $dbDump->tableList();
		$handle = fopen($sqlFileName, "w");
		if($handle === FALSE) 
			throw new Exception("Error opening file: ".$sqlFileName);
		$dbDump->tableDetails($tables, $handle);
		fclose($handle);
	}
	
	function extractFile($file,$destPath) {
		$zipFile = self::DEFAULT_ZIP_FILE;
    	if (isset($file)) {
            $zipFile = $file;
    	}
    	$zip = new ZipArchive;
		if ($zip->open($zipFile) === TRUE) {
			$zip->extractTo($destPath);
			$zip->close();
		} else {
			throw new Exception("Error extracting file: ".$zipFile);
		}
	}
	
	function compressFolder($folderPath,$expFile) {
		$zip = new ZipArchive;
		$res = $zip->open($expFile, ZIPARCHIVE::CREATE);
		$folder=& new Folder($folderPath);
        $tree= $folder->tree($folderPath, false);
        foreach ($tree as $files) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
       				$contents = file_get_contents($file);
        			if ( $contents === false ) {
						throw new Exception("Error reading file content: $file");
        			}
					$p = substr($file, strlen($folderPath));
					if(!$zip->addFromString("media".DS.$p, $contents )) {
						throw new Exception("Error adding $p to zip file");
					}
					unset($contents);
                }
            }
        }
		$zip->close();
	}
	
	function executeScript($script) {
		$db =& ConnectionManager::getDataSource('default');
		$sql = file_get_contents($script);
		$queries = array();
		$SplitterSql = new SplitterSql() ;
		$SplitterSql->parse($queries, $sql) ;
		foreach($queries as $q) {	
			if(strlen($q)>1) {
				$res = $db->execute($q);
				if($res === false) {
					throw new Exception("Error executing query: ".$q);
				}
			}
		}
	}
	
	function executeInsert($sqlFileName) {
		$db =& ConnectionManager::getDataSource('default');
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
	
	function copyFolder($from,$to) {
		$folder = new Folder($to);
		$ls = $folder->read();
		if(count($ls[0]) > 0 || count($dls[1]) > 0) {
			$this->removeMediaFiles();
		}
		$copts=array('to'=>$to,'from'=>$from,'chmod'=>0755);
		$res = $folder->copy($copts);
	}

	private function setupTempDir() {
    	$basePath = getcwd().DS."export-tmp".DS;
		if(!is_dir($basePath)) {
			if(!mkdir($basePath))
				throw new Exception("Error creating temp dir: ".$basePath);
		} else {
    		$this->__clean($basePath);
		}
    	return $basePath;
    }
	
    private function removeMediaFiles() {
       $this->__clean(MEDIA_ROOT . DS. 'imgcache');
       $folder= new Folder(MEDIA_ROOT);
       $dirs = $folder->read();
       foreach ($dirs[0] as $d) {
       	    if($d !== 'imgcache') {
       	    	$folder->delete(MEDIA_ROOT . DS. $d);
       	    }
       }  	
    }
	
    private function __clean($path) {
        $folder=& new Folder($path);
        $list = $folder->ls();
        foreach ($list[0] as $d) {
        	if($d[0] != '.') { // don't delete hidden dirs (.svn,...)
	        	if(!$folder->delete($folder->path.DS.$d)) {
	                throw new Exception("Error deleting dir $d");
	            }
        	}
        }
        foreach ($list[1] as $f) {
        	$file = new File($folder->path.DS.$f);
        	if(!$file->delete()) {
                throw new Exception("Error deleting file $f");
            }
        }
        return ;
    }
	
	public function checkMedia() {
		$stream = new Stream();
        // check filesystem
		$folder=& new Folder(MEDIA_ROOT);
        $tree= $folder->tree(MEDIA_ROOT, false);
		$mediaOk = true;
        foreach ($tree as $files) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
                    $file=& new File($file);
					$p = substr($file->pwd(), strlen(MEDIA_ROOT));
					if(stripos($p, "/imgcache/") !== 0) {
						$f = $stream->findByPath($p);
						if($f === false) {
							$mediaOk = false;
						}
					}
                }
            }
        }
        // check db
		$allStream = $stream->find("all");
		$mediaOk = true;
        foreach ($allStream as $v) {
        	$p = $v['Stream']['path'];
        	if(!file_exists(MEDIA_ROOT.$p)) {
					$mediaOk = false;
        	}
        }
	}
}

class DumpModel extends AppModel {
	var $useTable = "objects";
}

class DbDump {
	
	private $model = NULL;
	
	public function __construct() {
		$this->model = new DumpModel();
	}
	
	public function tableList() {
   		$tables = $this->model->execute("show tables");
    	$res = array();
    	foreach ($tables as $k=>$v) {
    		$t1 = array_values($v);
    		$t2 = array_values($t1[0]);
    		if (strncasecmp($t2[0], 'view_', 5) !== 0) // exclude views
    			$res[]=$t2[0] ;
    	}
    	return $res;
    }
    
    public function tableDetails($tables, $handle) {
    	fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
    	foreach ($tables as $t) {
    		$this->model->setSource($t); 
    		$select = $this->model->find('all');
			foreach ($select as $sel) {
				$fields = "";
				$values = "";
				$count = 0;
				foreach ($sel['DumpModel'] as $k=>$v) {
					if($count > 0) {
						$fields .= ",";
						$values .= ",";
					}
					$fields .= "`$k`";
					if($v == NULL)
						$values .= "NULL";					
					else 
						$values .= "'".addslashes($v)."'";
					$count++;
				}
				$res = "INSERT INTO $t (".$fields.") VALUES ($values);\n";
    			fwrite($handle, $res);
			}
    	}
    	return $res;
    }
*/
}
?>