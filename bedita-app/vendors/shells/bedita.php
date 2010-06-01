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

App::import('Model', 'DataSource');
App::import('Model', 'Stream');
App::import('Component', 'Transaction');
App::import('vendor', "splitter_sql");
App::import('vendor', "Archive_Tar", true, array(), "Tar.php");
require_once 'bedita_base.php';

/**
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class DataSourceTest extends DataSource {
	
	function executeQuery($db,$script) {
		$sql = file_get_contents($script);
		$queries = array();
		$SplitterSql = new SplitterSql() ;
		$SplitterSql->parse($queries, $sql) ;
		foreach($queries as $q) {	
			if(strlen($q)>1) {
				$res = $db->execute($q);
				if($res === false) {
					throw new Exception("Error executing query: ".$q."\n" . "db error msg: " . $db->error ."\n");
				}
			}
		}
	}
	
	function executeInsert($db,$script) {
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
	
	function createChunks($script) {
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
}

class DumpModel extends AppModel {
	var $useTable = "objects";
};

class DbDump {
	
	private $model = NULL;
	
	public function __construct() {
		$this->model = new DumpModel();
	}
	
	public function tableList() {
   		$tables = $this->model->query("show tables");
    	$res = array();
    	foreach ($tables as $k=>$v) {
    		$t1 = array_values($v);
    		$t2 = array_values($t1[0]);
    		if (strncasecmp($t2[0], 'view_', 5) !== 0 && 
    			strncasecmp($t2[0], 'cake_', 5) !== 0) // exclude views and cake_ util tables
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

/**
 * Main bedita shell script: basic methods services, including
 * - import/export of complete instances
 * - fresh init/install
 * - cache, compiled templates cleanups
 * - application and media files consistency check
 *
 */
class BeditaShell extends BeditaBaseShell {

	const DEFAULT_TAR_FILE 	= 'bedita-export.tar' ;
	const DEFAULT_ARCHIVE_FILE 	= 'bedita-export.tar.gz' ;
	
	/**
	 * initialize BEdita
	 *
	 */
	function init() {
		$this->out("CHECKING DATABASE CONNECTION");
		$res = $this->checkAppDbConnection();
		$this->hr();
		if(!$res) {
			$this->out("HINT: check database existence/setup and edit \$default array in bedita-app/config/database.php, have a look to CakePHP documentation.");
			$this->out("");
			return;
		}
		$this->out("");
		$this->hr();
		$this->out("CHECKING MEDIA ROOT");
		$mediaRoot = Configure::read("mediaRoot");
		$res = @$this->checkAppDirPerms($mediaRoot);
		$this->hr();
		if(!$res) {
			$this->out("HINT: edit \$config['mediaRoot'] in bedita-app/config/bedita.sys.php, if necessary uncomment it.");
			$ans = $this->in("Proceed anyway? [y/n]");
			if($ans != "y") {
		   		$this->out("Bye");
				return;
			}				
		}

		$this->out("CHECKING MEDIA URLs");
		$mediaUrl = Configure::read("mediaUrl");
		$res = $this->checkAppUrl($mediaUrl);
		$this->hr();
		if(!$res) {
			$this->out("HINT: edit \$config['mediaUrl'] in bedita-app/config/bedita.sys.php, if necessary uncomment it.");
			$ans = $this->in("Proceed anyway? [y/n]");
			if($ans != "y") {
		   		$this->out("Bye");
				return;
			}				
		}
				
		$this->out("");
		$this->hr();
		$this->out("INITIALIZE DATABASE");
		$this->initDb();
		$this->out("");
		$res = $this->in("Do you want to check BEdita status? [y/n]");
		if($res != "y") {
       		$this->out("Bye");
       		return;
		}
		$this->hr();
		$this->out("BEdita STATUS");
		$this->hr();
		$this->checkApp();
	}
	
	function initDb() {
        $dbCfg = 'default';
    	if (isset($this->params['db'])) {
            $dbCfg = $this->params['db'];
    	}
		
		$sqlScriptPath = APP_DIR . DS ."config" . DS . "sql" . DS;
		$sqlDataDump = $sqlScriptPath . 'bedita_init_data.sql';
    	if (isset($this->params['data'])) {
            if(file_exists($sqlScriptPath . $this->params['data'])) {
    			$sqlDataDump = $sqlScriptPath .$this->params['data'];
            } else {
    			$sqlDataDump = $this->params['data'];
            	if(!file_exists($sqlDataDump)) {
	        		$this->out("data file $sqlDataDump not found");
					return;
            	}
            }
    	}
    	
    	$db = ConnectionManager::getDataSource($dbCfg);
    	$hostName = $db->config['host'];
    	$dbName = $db->config['database'];
		$this->out("Updating bedita db config: $dbCfg - [host=".$hostName.", database=".$dbName."]");
		$res = $this->in("ACHTUNG! Database $dbName will be replaced, proceed? [y/n]");
		if($res != "y") {
       		$this->out("Bye");
			return;
		}
		$this->hr();

        $transaction = new TransactionComponent($dbCfg);
		$transaction->begin();
        
        $this->DataSourceTest = new DataSourceTest();
		$script = $sqlScriptPath . "bedita_schema.sql";
		$this->out("Update schema from $script");
		$this->DataSourceTest->executeQuery($db,$script);
        
		if (isset($this->params['nodata'])) {
			$this->out("No data inserted");
		} else {
	        $this->out("Load data from $sqlDataDump");
			$this->DataSourceTest->executeInsert($db, $sqlDataDump);
		}
       	$this->out("$dbCfg database updated");
		$transaction->commit();
		
		BeLib::getObject("BeConfigure")->cacheConfig();
		
		if (isset($this->params['media'])) {
            $this->extractMediaZip($this->params['media']);
    	}

		$this->out("checking media files");
		$this->checkMedia();
		$this->out("bye");       
    }

    function import() {
        $dbCfg = 'default';
    	if (isset($this->params['db'])) {
            $dbCfg = $this->params['db'];
    	}

		$answerYes = false;
    	if (isset($this->params['y'])) {
            $answerYes = true;
    	}
		
		$this->check_sys_get_temp_dir();
		$tmpBasePath = $this->setupTempDir();
       	$this->out("Using temp dir: $tmpBasePath");
		
		$archFile = self::DEFAULT_ARCHIVE_FILE;
    	if (isset($this->params['f'])) {
            $archFile = $this->params['f'];
    	}

    	if(!file_exists($archFile)) {
    		$this->out("$archFile not found, bye");
    		return;
    	}
    	$this->out("Importing file $archFile");

  		$compress = (substr($archFile, strlen($archFile)-3) == ".gz") ? "gz" : null;
  		$tar = new Archive_Tar($archFile, $compress);
       	if($tar === FALSE) {
       		$this->out("Error opening archive $archFile!!");
       	}
       	$tar->extract($tmpBasePath);
       	
		// check if media files are present
       	$tmpMediaDir = $tmpBasePath."media";
       	if(!file_exists($tmpMediaDir)) {
			$res = $this->in("ACHTUNG! Media files not present in import file, proceed? [y/n]");
			if($res != "y") {
	       		$this->out("Bye");
				return;
			}
       	}
       	
       	$sqlFileName = $tmpBasePath."bedita-data.sql";
		
        $this->hr();
		$db = ConnectionManager::getDataSource($dbCfg);
    	$hostName = $db->config['host'];
    	$dbName = $db->config['database'];
		$this->out("Importing data using bedita db config: $dbCfg - [host=".$hostName.", database=".$dbName."]");
		if(!$answerYes) {
			$res = $this->in("ACHTUNG! Database $dbName will be replaced, proceed? [y/n]");
			if($res != "y") {
	       		$this->out("Bye");
				return;
			}
		}
        $this->hr();
				
        $transaction = new TransactionComponent($dbCfg);
		$transaction->begin();
        
    	$sqlScriptPath = APP_DIR . DS ."config" . DS . "sql" . DS;
		$this->DataSourceTest = new DataSourceTest();
		$script = $sqlScriptPath . "bedita_schema.sql";
		$this->out("Update schema from $script");
		$this->DataSourceTest->executeQuery($db,$script);
        
		$this->out("Load data from $sqlFileName");
        $this->DataSourceTest->simpleInsert($db, $sqlFileName);
		unlink($sqlFileName);
		$this->out("$dbCfg database updated");

		BeLib::getObject("BeConfigure")->cacheConfig();

		$this->hr();
		// import new configuration file, if present
		$newCfgFileName = $tmpBasePath."bedita.cfg.php";
		if (file_exists($newCfgFileName)) {
			// overwrite current cfg file
			$cfgFileName = ROOT.DS.APP_DIR.DS."config".DS."bedita.cfg.php";
			if (file_exists($cfgFileName)) {
				$res = $this->in($cfgFileName. " already exists, overwrite with new configuration? [y/n]");
				if($res == "y") {
	       			$this->importCfg($newCfgFileName, $cfgFileName);
				} else {
					$this->out("Configuration not updated!");
				}
			} else {
				$this->importCfg($newCfgFileName,$cfgFileName);
			}
		} else {
			$this->out("Configuration 'bedita.cfg.php' not present in .tar file");
		}
		$this->hr();
		
		$mediaRoot = Configure::read("mediaRoot");
		
		// update media root dir
		$folder = new Folder($mediaRoot);
		$ls = $folder->ls();
		if(count($ls[0]) > 0 || count($ls[1]) > 0) {
			if($answerYes) {
       			$this->removeMediaFiles();
			} else {
				$res = $this->in($mediaRoot. " is not empty, remove files and folders? [y/n]");
				if($res == "y") {
	       			$this->removeMediaFiles();
				} else {
					$this->out($mediaRoot. " not clean!");
				}
			}
		}
		
		// copy files from tmp dir to media_root
		$copts=array('to'=>$mediaRoot,'from'=>$tmpBasePath.'media','mode'=>0777);
		$this->out("copying from " . $copts['from'] . " to " . $copts['to']);
		$res = $folder->copy($copts);
		$this->out("Cleaning temp dir $tmpBasePath");
		$this->cleanTempDir();
		$this->out("done");
		
		$transaction->commit();		
		$this->out("bye");
    }

    
    public function export() {
        $expFile = self::DEFAULT_ARCHIVE_FILE;
    	if (isset($this->params['f'])) {
            $expFile = $this->params['f'];
    	} else if(isset($this->params['nocompress'])) {
        	$expFile = self::DEFAULT_TAR_FILE;
    	}
    	
    	$this->checkExportFile($expFile);

		$dbDump = new DbDump();
		$tables = $dbDump->tableList();
		$this->check_sys_get_temp_dir();
		$tmpBasePath = $this->setupTempDir();
		$sqlFileName = $tmpBasePath."bedita-data.sql";
		
		$this->out("Creating SQL dump....");
		$handle = fopen($sqlFileName, "w");
		if($handle === FALSE) 
			throw new Exception("Error opening file: ".$sqlFileName);
		$dbDump->tableDetails($tables, $handle);
		fclose($handle);
       	
       	$this->out("Exporting to $expFile");
       	       	       	
		$compress = null;
		if (isset($this->params['compress']) || (substr($expFile, strlen($expFile)-3) == ".gz")) {
            $compress = "gz";
    	}
       	$tar = new Archive_Tar($expFile, $compress);
       	if($tar === FALSE) {
			throw new Exception("Error opening archive $expFile");
       	}
       	
		if(!$tar->addString("bedita-data.sql", file_get_contents($sqlFileName)))
			throw new Exception("Error adding SQL file to archive");
       	
		$this->out("SQL data exported");
       	
    	$cfgFileName = ROOT.DS.APP_DIR.DS."config".DS."bedita.cfg.php";
       	if (file_exists($cfgFileName)) {
	       	if(!$tar->addString("bedita.cfg.php", file_get_contents($cfgFileName)))
				throw new Exception("Error adding configuration file to archive");
	       	
			$this->out("Configuration file exported");
       	}

       	if(!$tar->addString("version.txt", Configure::read("majorVersion")))
			throw new Exception("Error adding version file to archive");
		
		$this->out("Version file exported");
			
       	
       	if (isset($this->params['nomedia'])) { // exclude media files
	       	
       		$this->out("Media files not exported!");
       		
       	} else {
       	
	       	$this->out("Exporting media files");
	       	
			$mediaRoot = Configure::read("mediaRoot");
	       	$folder = new Folder($mediaRoot);
	        $tree= $folder->tree($mediaRoot, false);
	        foreach ($tree as $files) {
	            foreach ($files as $file) {
	                if (!is_dir($file)) {
	     				$contents = file_get_contents($file);
	        			if ( $contents === false ) {
							throw new Exception("Error reading file content: $file");
	       				}
						$p = substr($file, strlen($mediaRoot));	
						if(!$tar->addString("media".$p, $contents)) {
							throw new Exception("Error adding $file to tar file");
						}
	//					echo "before unset ". memory_get_usage()." RAM used.\n";
						unset($contents);
	//					echo 'after unset  '. memory_get_usage()." RAM used.\n";
	                }
	            }
	        }
       	}
		$this->cleanTempDir();
        $this->out("$expFile created");
    }

    private function extractMediaZip($zipFile) {
		$zip = new ZipArchive;
		if ($zip->open($zipFile) === TRUE) {
			$zip->extractTo(Configure::read("mediaRoot"));
			$zip->close();
  			$this->out("Media files extracted");
		} else {
  			$this->out("Error media file $zipFile not found!!");
		}
    }
    
    private function importCfg($source,$destination) {
    	if (!copy($source, $destination)) {
    		throw new Exception("Error copying " . $source . " to " . $destination);
    	} else {
    		$this->out("Configuration file " . $destination . " updated.");
    	}
    }
    
	public function checkMedia() {

		$stream = ClassRegistry::init("Stream");
        // check filesystem
		$this->out("checkMedia - checking filesystem");
		$mediaRoot = Configure::read("mediaRoot");
		$folder = new Folder($mediaRoot);
        $tree= $folder->tree($mediaRoot, false);
		$mediaOk = true;
        foreach ($tree as $files) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
                    $file = new File($file);
					$p = substr($file->pwd(), strlen($mediaRoot));
					if(stripos($p, "/imgcache/") !== 0) {
						$f = $stream->findByUri($p);
						if($f === false) {
							$this->out("File $p not found on db!!");
							$mediaOk = false;
						}
					}
                }
            }
        }
        if($mediaOk) {
			$this->out("checkMedia - filesystem OK");
        }
        // check db
		$this->out("checkMedia - checking database");
        $allStream = $stream->findAll();
		$mediaOk = true;
        foreach ($allStream as $v) {
        	$p = $v['Stream']['uri'];
        	// if $p is a local path check existence
        	if((stripos($p, "/") === 0) && !file_exists($mediaRoot.$p)) {
					$this->out("File $p not found on filesystem!!");
					$mediaOk = false;
        	}
        }
        if($mediaOk) {
			$this->out("checkMedia - database OK");
        }
	}    
    
    function cleanup() {
		$basePath = TMP;
    	if (isset($this->params['frontend'])) {
    		$basePath = $this->params['frontend'].DS."tmp".DS;
            $this->out('Cleaning dir: '.$basePath);
    		
    	}
        if (isset($this->params['logs'])) {
    	   $this->__clean($basePath . 'logs');
            $this->out('Logs cleaned.');
        }
        Cache::clear();
        $this->__clean($basePath . 'cache' . DS . 'models');
        $this->__clean($basePath . 'cache' . DS . 'persistent');        
        $this->__clean($basePath . 'cache' . DS . 'views');        
        $this->out('Cache cleaned.');
        $this->__clean($basePath . 'smarty' . DS . 'compile');
        $this->__clean($basePath . 'smarty' . DS . 'cache');
        $this->out('Smarty compiled/cache cleaned.');

        if (isset($this->params['media'])) {
       		$this->removeMediaFiles();
        }
    }    

    private function removeMediaFiles() {
		$mediaRoot = Configure::read("mediaRoot");
		$folder= new Folder($mediaRoot);
        $dirs = $folder->ls();
        foreach ($dirs[0] as $d) {
            $folder->delete($mediaRoot . DS. $d);
        }
        $this->out('Media files cleaned.');
    	
    }

    public function checkApp() {
        $appPath = $this->params['app'];
        if (isset($this->params['frontend'])) {
        	$appPath = $this->params['frontend'];
        }
        $this->out('Checking cake app dir: '.$appPath);
        $this->hr();
        // config/core.php
        $this->checkAppFile($appPath.DS."config".DS."core.php");
        // config/database.php
        $this->checkAppFile($appPath.DS."config".DS."database.php");
        if (!isset($this->params['frontend'])) {
	        //config/bedita.sys.php
        	$this->checkAppFile($appPath.DS."config".DS."bedita.sys.php");
        	// config/bedita.cfg.php
	        $this->checkAppFile($appPath.DS."config".DS."bedita.cfg.php");
        }
        // index.php
        $this->checkAppFile($appPath.DS."index.php");
        // webroot/index.php
        $this->checkAppFile($appPath.DS."webroot".DS."index.php");
        if (!isset($this->params['frontend'])) {
	        // webroot/test.php
	        $this->checkAppFile($appPath.DS."webroot".DS."test.php");
        }
        // tmp/cache
        $this->checkAppDirPerms($appPath.DS."tmp".DS."cache");
        // tmp/smarty/compile
        $this->checkAppDirPerms($appPath.DS."tmp".DS."smarty".DS."compile");
        // tmp/logs
        $this->checkAppDirPerms($appPath.DS."tmp".DS."logs");

		// mediaRoot, mediaUrl, beditaUrl
		$this->hr();
		$this->out("Checking media dir and url");
		$this->hr();
		$mediaRoot = Configure::read("mediaRoot");
		@$this->checkAppDirPerms($mediaRoot, "mediaRoot: ");
		$mediaUrl = Configure::read("mediaUrl");
		@$this->checkAppUrl($mediaUrl, "mediaUrl: ");
		@$this->checkAppUrl(Configure::read("beditaUrl"), "beditaUrl: ");
		
		// database connection
		@$this->checkAppDbConnection();
		
		$debugLevel = Configure::read("debug");
		$this->out("Cake debug level: $debugLevel");
		$saveSess = Configure::read("Session.save");
		$this->out("Cake session handling: " .$saveSess);
		if($saveSess !== "database") {
			$this->out("WARNIN: use 'database' as session handler in config/core.php - 'Session.save'");
		}
		$appBaseUrl = Configure::read('App.baseUrl');
		if(empty($appBaseUrl)) {
			$this->out("Using mod_rewrite");
		} else {
			$this->out("NOT USING mod_rewrite");
		}
		
    }

	private function checkAppDirPerms($dirPath, $msg = "") {
		if (is_dir($dirPath)) {
			$this->out($msg . $dirPath. " - perms: ".sprintf("%o",(fileperms($dirPath) & 511)));
			return true;
		} else {
			$this->out($msg. $dirPath . " doesn't exist or it isn't a directory!");
			return false;
		}
	}

	private function checkAppDbConnection() {
		$dbCfg = 'default';
		if (isset($this->params['db'])) {
			$dbCfg = $this->params['db'];
		}
		$db1 = @ConnectionManager::getDataSource($dbCfg);
		$hostName = $db1->config['host'];
		$dbName = $db1->config['database'];
		$this->out("Checking database connection: $dbCfg - [host=".$hostName.", database=".$dbName."]");
		$db = ConnectionManager::getInstance();
		$connected = $db->getDataSource($dbCfg); 
		if ($connected->isConnected()) {
			$this->out("Database connection: ok");
			return true;
		} else {
			$this->out("Database connection: unable to connect");
			return false;
		}
	}

	private function checkAppUrl($url, $msg = "") {
		$headers = get_headers($url); 
		if($headers && !strstr($headers[0], "404")) {
			$this->out($msg . $url . ": ok.");
			return true;
		} else {
			$this->out($msg . $url . ": unreachable.");
			return false;
		}
	}

    private function checkAppFile($filePath, $msg = "") {
        if(!file_exists($filePath)) {
        	$this->out($msg . $filePath . ": NOT FOUND!");
            $sampleFile = $filePath.".sample";
        	if(file_exists($sampleFile)) {
                $res = $this->in("$sampleFile found, create copy? [y/n]");
                if($res == "y") {
                    if(!copy($sampleFile, $filePath)) {
                        throw new Exception("Unable to copy $sampleFile to $filePath");
                    }                	
                }
        	}
        } else {
            $this->out($msg . $filePath . ": ok.");
        }
    }
    
    
    public function modules() {
		if(!array_key_exists("add", $this->params) && 
			!array_key_exists("del", $this->params)) {
			$this->params['list'] = ""; // add default -list option
		}
    	$module = ClassRegistry::init("Module");
		if (isset($this->params['list'])) {
			$mods = $module->find('all', array("conditions" => array("status" =>"on")));
			$modNames = array();
			foreach ($mods as $m) {
				$modNames[$m["Module"]["id"]] = $m["Module"]["name"];
			}
			$this->hr();
			$this->out("Current modules on istance " . Configure::read("projectName") . ":");
			$this->hr();	
			print_r($modNames);
			$modsAvailable = Configure::read("modules");
			$modsNot = array();
			foreach ($modsAvailable as $k => $v) {
				if(!in_array($k, $modNames))
					$modsNot[$v['id']] = $k;
			}
			if(empty($modsNot)) {
				$this->out("\nAll Modules present");
			} else {
				$this->hr();
				$this->out("Modules not present in " . Configure::read("projectName") . ":");
				$this->hr();
				print_r($modsNot);
			}
		}
		if (isset($this->params['add'])) {
			$modName = $this->params['add'];
			if (empty($modName) || $modName == 1) {
	        	$this->out("module name is mandatory");
				return;
			}
			$modsAvailable = Configure::read("modules");
			if(!array_key_exists($modName,$modsAvailable)) {
	        	$this->out("Unknown module name: " . $modName);
				return;
			}
    		$data = $modsAvailable[$modName];
    		if(!isset($data['url']))
    			$data['url'] = $modName;
    		if(!isset($data['label']))
    			$data['label'] = $modName;
    		$data['name'] = $modName;
    		$data['status'] = "on";
    		$namePresent = $module->field("name", array("name" => $modName));
    		if(!empty($namePresent)) {
    			$data['id'] = $module->field("id", array("name" => $modName));
    		} else {
    			$idPresent = $module->field("id", array("id" => $data['id']));
	    		if(!empty($idPresent)) {
		        	$this->out("id " . $idPresent . " already present");
	    			unset($data['id']);	
	    		}
    		}
    		if(!$module->save($data)) {
	        	$this->out("error saving module " . $modName);
				return;
    		}
			$bePermsMod = ClassRegistry::init("PermissionModule");
			$perms =  array(
				array(
					"name" => "administrator",
					"switch" => $bePermsMod->SWITCH_GROUP,
					"flag" => BEDITA_PERMS_READ_MODIFY
				)
			);
			$bePermsMod->add($modName, $perms);
	        $this->out("Module " . $modName . " added/enabled");
		}
		if (isset($this->params['del'])) {
			$modName = $this->params['del'];
			if (empty($modName) || $modName == 1) {
			   	$this->out("module name is mandatory");
				return;
			}
    		$id = $module->field("id", array("name" => $modName));
    		if(empty($id)) {
	        	$this->out("Module " . $modName . " not present");
				return;
    		}
    		$module->id = $id;
    		if(!$module->saveField("status", "off")) {
	        	$this->out("Error removing module");
				return;
    		}
    		$this->out("Module " . $modName . " disabled");
		}    		
    }
    
    public function mimeTypes() {
    	$mimeFile = null;
    	if (isset($this->params['f'])) {
            $mimeFile = $this->params['f'];
    	} else {
    		$this->out("mime.types file is mandatory, bye");
    	}
    	if(!file_exists($mimeFile)) {
    		$this->out("$mimeFile not found, bye");
    		return;
    	}
		$mimeArray = array();
    	$lines = file($mimeFile);
		foreach ($lines as $l) {
			$l = trim($l);
			if(!empty($l) && $l[0] !== "#") {
				$fields = split(' ', $l);
				if(count($fields) > 1) {
					for ($i = 1 ; $i < count($fields); $i++) {
						$k = strtolower($fields[$i]);
						if(!empty($k)) {
							$mimeArray[$k] = $fields[0];
						}
					}
				}
			}
		}
		$beditaMimeFile = APP . 'config' . DS . 'mime.types.php';
		$handle = fopen($beditaMimeFile, 'w');
		fwrite($handle, "<?php\n\$config['mimeTypes'] = array(\n");
		ksort($mimeArray);
		foreach ($mimeArray as $k => $v) {
			fwrite($handle, "  \"$k\" => \"$v\",\n");
		}
		fwrite($handle, ");\n?>");
		fclose($handle);
		$this->out("Mime types updated to: $beditaMimeFile");
    }

    public function updateObjectTypes() {
	
		$objType = ClassRegistry::init("ObjectType");
		// from 1 to 999 - core models
		if(!$objType->deleteAll("id < 1000")){ 
			throw new BeditaException(__("Error removing object types", true));
		}
		
		$types = Configure::read("objectTypes");
		foreach ($types as $k => $v) {
			if(is_numeric($k)) {
				$objType->create();
				if(!$objType->save($v)) {
					throw new BeditaException(__("Error saving object type", true) . " id: $k");
				}
				$this->out("updated type: " . $v["name"]);
			}
		}
		$this->out("done");
	}
	
    
	function help() {
        $this->out('Available functions:');
  		$this->out(' ');
        $this->out('0. init: initialize a new BEdita instance from scratch');
  		$this->out(' ');
        $this->out('1. initDb: initialize database with bedita-db sql scripts');
  		$this->out(' ');
        $this->out('    Usage: initDb [-db <dbname>] [-data <sql>] [-nodata] [-media <zipfile>]');
  		$this->out(' ');
  		$this->out("    -db <dbname>\t use db configuration <dbname> specified in config/database.php");
  		$this->out("    -nodata <sql>   \t don't insert data");
  		$this->out("    -data <sql>     \t use <sql> data dump, use absolute path if not in bedita-db/");
  		$this->out("    -media <zipfile> \t restore media files in <zipfile>");
  		$this->out(' ');
  		$this->out('2. cleanup: cleanup cache, compile, log files');
        $this->out(' ');
        $this->out('    Usage: cleanup [-frontend <frontend path>] [-logs] [-media]');
        $this->out(' ');
        $this->out("    -frontend \t clean files in <frontend path> [use frontend /app path]");
        $this->out("    -logs \t clean log files");
        $this->out("    -media  \t clean media files in 'mediaRoot' (default no)");
        $this->out(' ');
        $this->out('3. checkMedia: check media files on db and filesystem');
        $this->out(' ');
        $this->out('4. export: export media files and data dump');
  		$this->out(' ');
        $this->out('    Usage: export [-f <tar-gz-filename>] [-compress]');
        $this->out(' ');
  		$this->out("    -f <tar-gz-filename>\t file to export, default ".self::DEFAULT_ARCHIVE_FILE);
        $this->out("    -compress \t gz compression (automagically applied if file extension is .gz)");
        $this->out("    -nomedia  \t don't export media files in tar");
        $this->out(' ');
        $this->out('5. import: import media files and data dump');
  		$this->out(' ');
  		$this->out('    Usage: import [-f <tar-gz-filename>] [-db <dbname>] [-y]');
        $this->out(' ');
  		$this->out("    -f <tar-gz-filename>\t file to import, default ".self::DEFAULT_ARCHIVE_FILE);
        $this->out("    -db <dbname>\t use db configuration <dbname> specified in config/database.php");
        $this->out("    -y  answer always 'yes' to questions...");
        $this->out(' ');
        $this->out('6. checkApp: check app files ... (core.php/database.php/index.php...)');
        $this->out(' ');
        $this->out('    Usage: checkApp [-frontend <app-path>]');
        $this->out(' ');
        $this->out("    -frontend \t check files in <frontend path> [use frontend /app path]");
        $this->out(' ');
        $this->out('7. modules: simple operations on BEdita modules list/add/del');
  		$this->out(' ');
  		$this->out('   Usage: modules [-list] [-add <module-name>] [-del <module-name>]');
        $this->out(' ');
        $this->out('8. mimeTypes: update config/mime.types.php from standard mime.types file');
  		$this->out(' ');
  		$this->out('   Usage: mimeTypes -f <mime.types-file>');
        $this->out(' ');
        $this->out('9. updateObjectTypes: update object_types table');
  		$this->out(' ');
	}
}

?>
