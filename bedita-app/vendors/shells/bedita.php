<?php 
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008, 2010 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

App::import('Model', 'BeSchema');
App::import('Model', 'Stream');
App::import('Component', 'Transaction');
require_once 'bedita_base.php';

/**
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
/*

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

	var $tasks = array('Cleanup');
	
	/**
	 * Overrides base startup(), don't call initConfig...
	 * @see BeditaBaseShell::startup()
	 */
	function startup() {
		Configure::write('debug', 1);
	}
	
	/**
	 * initialize BEdita
	 *
	 */
	function init() {
		$this->loadTasks();
		$this->out("BEdita CLEANUP");
        $this->Cleanup->execute();
		$this->hr();
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
			$this->out("HINT: edit \$config['mediaRoot'] in bedita-app/config/bedita.cfg.php, if necessary uncomment it.");
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
			$this->out("HINT: edit \$config['mediaUrl'] in bedita-app/config/bedita.cfg.php, if necessary uncomment it.");
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
		
		$sqlScriptPath = APP ."config" . DS . "sql" . DS;
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
    	$driver = $db->config['driver'];
    	$this->out("Updating bedita db config: $dbCfg - $driver [host=".$hostName.", database=".$dbName."]");
		$res = $this->in("ACHTUNG! Database $dbName will be replaced, proceed? [y/n]");
		if($res != "y") {
       		$this->out("Bye");
			return;
		}
		$this->hr();

        $transaction = new TransactionComponent($dbCfg);
		$transaction->begin();
        
        $beSchema = new BeSchema();
		$script = $sqlScriptPath . "bedita_" . $driver . "_schema.sql";
		$this->out("Update schema from $script");
		$beSchema->executeQuery($db, $script);
        
		if (isset($this->params['nodata'])) {
			$this->out("No data inserted");
		} else {
	        $this->out("Load data from $sqlDataDump");
			$beSchema->executeInsert($db, $sqlDataDump);
		}
       	$beSchema->checkSequences($db);
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

    	if(!class_exists("PharData")) {
			$this->out("Phar module needed for this operation. Exiting.");
			return;			
		}
    	
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

  		$phar = new PharData($archFile);
       	$phar->extractTo($tmpBasePath);
       	
		// check if media files are present
       	$tmpMediaDir = $tmpBasePath."media";
       	if(!file_exists($tmpMediaDir) && !$answerYes) {
			$res = $this->in("ACHTUNG! Media files not present in import file, proceed? [y/n]");
			if($res != "y") {
	       		$this->out("Bye");
				return;
			}
       	}
       	
       	// TODO: check version.txt compatibility
       	
       	// check plugins 
       	$currentPlugins = $this->currentPluginModules();
       	$plugins = array();
       	if(file_exists($tmpBasePath."plugins.php")) {
       		include $tmpBasePath."plugins.php";	
       	}
		$missingPlugins = array_diff($plugins, $currentPlugins);
		if(!empty($missingPlugins)) {
	       	$this->out("Some plugins are needed to proceed with import: " . implode(", ", $missingPlugins));
	       	$this->out("Load them and try again.");
	       	return;
       	}
    
       	$sqlFileName = $tmpBasePath."bedita-data.sql";
		
        $this->hr();
		$db = ConnectionManager::getDataSource($dbCfg);
    	$hostName = $db->config['host'];
    	$driver = $db->config['driver'];
    	$dbName = $db->config['database'];
		$this->out("Importing data using bedita db config: $dbCfg - $driver [host=".$hostName.", database=".$dbName."]");
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
        
    	$sqlScriptPath = APP ."config" . DS . "sql" . DS;
		$beSchema = new BeSchema();
		$script = $sqlScriptPath . "bedita_" . $db->config['driver'] . "_schema.sql";
		$this->out("Update schema from $script");
		$beSchema->executeQuery($db, $script);
        
		$this->out("Load data from $sqlFileName");
        $beSchema->simpleInsert($db, $sqlFileName);
		unlink($sqlFileName);
        $beSchema->checkSequences($db);
		$this->out("$dbCfg database updated");

		BeLib::getObject("BeConfigure")->cacheConfig();

		$this->hr();
		// import new configuration file, if present
		$newCfgFileName = $tmpBasePath."bedita.cfg.php";
		if (file_exists($newCfgFileName)) {
			// overwrite current cfg file
			$cfgFileName = APP ."config".DS."bedita.cfg.php";
			if (file_exists($cfgFileName) && !$answerYes) {
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
		$ls = $folder->read();
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

    	if(!class_exists("PharData")) {
			$this->out("Phar module needed for this operation. Exiting.");
			return;			
		}
    	$this->initConfig();
    	$expFile = self::DEFAULT_ARCHIVE_FILE;
    	if (isset($this->params['f'])) {
            $expFile = $this->params['f'];
    	} else if(isset($this->params['nocompress'])) {
        	$expFile = self::DEFAULT_TAR_FILE;
    	}
    	
    	$this->checkExportFile($expFile);

		$beSchema = new BeSchema();
		$tables = $beSchema->tableListOrdered();
		$this->check_sys_get_temp_dir();
		$tmpBasePath = $this->setupTempDir();
		$sqlFileName = $tmpBasePath . "bedita-data.sql";
		$this->out("Creating SQL dump....");
		$handle = fopen($sqlFileName, "w");
		if($handle === FALSE) 
			throw new Exception("Error opening file: ".$sqlFileName);
		$beSchema->tableDetails($tables, $handle);
		fclose($handle);
		
		$this->out("Creating metadata....");
		file_put_contents($tmpBasePath."version.txt", Configure::read('version'));
       	if(!copy(APP."config".DS."bedita.cfg.php", $tmpBasePath . "bedita.cfg.php")) {
       		throw new Exception("Error copying bedita.cfg.php file");
       	}
		
		$plugins = $this->currentPluginModules();
		$pl = "<?php\n\$plugins = " . var_export($plugins, true) . ";\n?>";
       	file_put_contents($tmpBasePath."plugins.php", $pl);
       	
       	if (isset($this->params['nomedia'])) { // exclude media files
	       	
       		$this->out("Media files not exported!");
       		
       	} else {
       	
			$this->out("Exporting media files....");
       		// copy media files
			if(!mkdir($tmpBasePath . "media")) {
	       		throw new Exception("Error creating media directory");
			}

			$folder = new Folder();
        	$allStream = ClassRegistry::init("Stream")->find("all");
			$mediaRoot = Configure::read("mediaRoot");
        	foreach ($allStream as $v) {
	        	$p = $v['Stream']['uri'];
	        	if((stripos($p, "/") === 0) && file_exists($mediaRoot.$p)) {
	        		$dirPath = $tmpBasePath . "media" . substr($p, 0, strrpos($p, "/"));
	        		$folder->create($dirPath);
	        		if(!copy($mediaRoot.$p, $tmpBasePath . "media" . $p)) {
	       				throw new Exception("Error copying bedita.cfg.php file");
	        		}
	       		}
        	}
       		unset($allStream);
        }

        $this->out("Creating archive....");

        $compress = isset($this->params['compress']);
        $tarFile = $expFile;
        if(substr($expFile, strlen($expFile)-3) == ".gz") {
        	$tarFile = substr($expFile, 0, strlen($expFile)-3);
        	$compress = true;
        }
        $phar = new PharData($tarFile);
   		$phar->buildFromDirectory($tmpBasePath);
		if ($compress) {
        	$this->out("Compressing archive....");
   			$phar->compress(Phar::GZ);
   			if($expFile !== $tarFile) {
   				unlink($tarFile);
   			}
		}
		$this->cleanTempDir();
        $this->out("$expFile created");
    }

    
    private function currentPluginModules() {
       	// list plugins
       	$module = ClassRegistry::init("Module");
		$mods = $module->find('all', array("conditions" => array("status" =>"on", 
			"module_type" => "plugin")));
		$plugins = array();
		foreach ($mods as $m) {
			$plugins[] = $m["Module"]["name"];
		}
		return $plugins;
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

    	$this->initConfig();
		$this->hr();
    	$this->out("checkMedia - checking filesystem");
		$this->hr();
    	$mediaNotPresent = array();
		$mediaRoot = Configure::read("mediaRoot");
		$maxDepthLevel = 2;
		if (isset($this->params["level"])) {
			$maxDepthLevel = $this->params["level"];
			$this->out("Using max depth level: " . $maxDepthLevel);
		}
        $excludeDir = array();
        if (isset($this->params['exclude'])) {
            $exclude = $this->params['exclude'];
            $this->out('Excluding dirs: ' . $exclude);
            $excludeDir = explode(',', $exclude);
        }
        $this->streamsCheck($mediaRoot, 0, $maxDepthLevel, $mediaNotPresent, 
                isset($this->params['-remove-files']), isset($this->params['-force-remove']), $excludeDir);
		$this->hr();
		$this->out("Media files not in BEdita - " . count($mediaNotPresent));
		$stream = ClassRegistry::init("Stream");
		if (isset($this->params["create"])) {
			foreach ($mediaNotPresent as $m) {
				$mimeType = $stream->getMimeType($mediaRoot . DS . $m);
				$modelType = BeLib::getTypeFromMIME($mimeType);
				$model = ClassRegistry::init($modelType["name"]);
				$p1 = strrpos($m, "/") + 1;
				$p2 = strrpos($m, ".");
				if(!$p2 || $p2 <= $p1){
					$p2 = strlen($m);
				} 
				$data = array("status" => "on", 
						"title" => substr($m, $p1, $p2-$p1), 
						"uri" => $m, 
						"name" => substr($m, $p1),
						"original_name" => substr($m, $p1),
						"mime_type" => $mimeType);
				$data['Category'] = $stream->getCategoryMediaType($mimeType, $modelType["name"]);
				$model->create();
				if(!$model->save($data)) {
					throw new BEditaSaveStreamObjException(__("Error saving stream object",true), $model->validationErrors) ;
				}
				$id = $model->id;
				$stream->updateStreamFields($id);
			}
        }

		// check db
		$this->hr();
		$this->out("checkMedia - checking database");
		$this->hr();
		$allStream = $stream->find("all");
		$mediaOk = true;
		$dbCount = 0;
		$beUrl = Configure::read('beditaUrl');
        foreach ($allStream as $v) {
        	$p = $v['Stream']['uri'];
        	// if $p is a local path check existence
        	if((stripos($p, "/") === 0) && !file_exists($mediaRoot.$p)) {
					$this->out("File $p not found on filesystem!! - " . $beUrl . '/view/' . $v['Stream']['id']);
					$mediaOk = false;
					$dbCount++;
        	}
        }
		$this->hr();
        if($mediaOk) {
			$this->out("checkMedia - database OK");
        } else {
            $this->out("checkMedia - some files are missing");
            $this->out("Media objects with missing local file: " . $dbCount);
        }
		$this->hr();
	}    


    private function streamsCheck($mediaPath, $level, $maxLevel, 
            array &$mediaFiles, $removeFiles = false, $forceRemove = false, $excludeDir = array()) {
        if ($level > $maxLevel) {
            return;
        }

        $stream = ClassRegistry::init('Stream');
        $mediaRoot = Configure::read('mediaRoot');
        $folder = new Folder($mediaPath);
        $ls = $folder->read();
        foreach ($ls[1] as $f) {
            if ($f[0] !== '.') {
                $filePath = $mediaPath . DS . $f;
                $p = substr($filePath, strlen($mediaRoot));
                $s = $stream->findByUri($p);
                if ($s === false) {
                    $this->out("File $p not found on db!!");
                    $remove = $forceRemove;
                    if ($removeFiles && !$forceRemove) {
                        $res = $this->in("Remove file ${filePath} ? [y/n]");
                        if ($res == 'y') {
                            $remove = true;
                        }
                    }
                    if ($remove) {
                        $this->out("Removing file : ${filePath}");
                        if (!unlink($filePath)) {
                            $this->out("Error removing: ${filePath}");
                        }
                    }
                    $mediaFiles[] = $p;
                }
            }
		}

        if ($level == 0) {
            $excludeDir[] = 'cache'; // exclude cache in check
        } else {
            $excludeDir = array(); // exclude only 0 level dirs
        }
        foreach ($ls[0] as $dir) {
            if ($dir[0] !== '.' && !in_array($dir, $excludeDir)) {
                $this->streamsCheck($mediaPath . DS . $dir, $level+1, $maxLevel, 
                    $mediaFiles, $removeFiles, $forceRemove);				
            }
        }
    }

    private function removeMediaFiles() {
		$mediaRoot = Configure::read("mediaRoot");
		$folder= new Folder($mediaRoot);
        $dirs = $folder->read();
        foreach ($dirs[0] as $d) {
            $folder->delete($mediaRoot . DS. $d);
        }
        $this->out('Media files cleaned.');
    	
    }

    private function checkAppFiles($appPath, $frontend = false) {
        // config/core.php
        $this->checkAppFile($appPath.DS."config".DS."core.php");
        // config/database.php
        $this->checkAppFile($appPath.DS."config".DS."database.php");
        if (!$frontend) {
        	// config/bedita.cfg.php
	        $this->checkAppFile($appPath.DS."config".DS."bedita.cfg.php");
        } else {
            // config/frontend.cfg.php
            $this->checkAppFile($appPath . DS . 'config' . DS . 'frontend.cfg.php');
            if (file_exists($appPath . DS . 'config' . DS . 'paths.php.sample')) {
                // config/paths.php
                $this->checkAppFile($appPath . DS . 'config' . DS . 'paths.php');
            }
        }
        // index.php
        $this->checkAppFile($appPath.DS."index.php");
        // webroot/index.php
        $this->checkAppFile($appPath.DS."webroot".DS."index.php");
        if (!$frontend) {
            // webroot/test.php
            $this->checkAppFile($appPath.DS."webroot".DS."test.php");
        }
        // tmp/cache
        $this->checkAppDirPerms($appPath.DS."tmp".DS."cache");
        // tmp/smarty/compile
        $this->checkAppDirPerms($appPath.DS."tmp".DS."smarty".DS."compile");
        // tmp/logs
        $this->checkAppDirPerms($appPath.DS."tmp".DS."logs");
    }
    
    public function checkApp() {
        $frontend = false;
    	$appPath = $this->params['app'];
        if (isset($this->params['frontend'])) {
        	$appPath = $this->params['frontend'];
        	$frontend = true;
        }
        if($frontend) {
        	$this->out('Checking frontend app dir: '.$appPath);
        	$this->hr();
        	$this->checkAppFiles($appPath, true);
        } else {
        	$this->out('Checking backend app dir: '.$appPath);
        	$this->hr();
        	$this->checkAppFiles($appPath);
        	if(!file_exists(BEDITA_FRONTENDS_PATH)) {
        		$this->hr();
        		$this->out("WARNING: frontend path " . BEDITA_FRONTENDS_PATH . " is missing");
        	} else {
				$folder = new Folder(BEDITA_FRONTENDS_PATH);
				$ls = $folder->read();
				$count = 0;
				foreach ($ls[0] as $dir) {
					if($dir[0] !== '.' ) {
						$count++;
	        			$this->hr();
						$this->out('Checking frontend app dir: '. BEDITA_FRONTENDS_PATH. DS .$dir);
	        			$this->hr();
	        			$this->checkAppFiles(BEDITA_FRONTENDS_PATH. DS .$dir, true);
					}
				}
				if($count === 0 ) {
        			$this->hr();
					$this->out("WARNING: no frontends found in " . BEDITA_FRONTENDS_PATH);
				}
        	}
        }
		// mediaRoot, mediaUrl, beditaUrl
		$this->hr();
		$this->out("Checking media dir and url");
		$this->hr();
		$mediaRoot = Configure::read("mediaRoot");
		if(empty($mediaRoot)) {
			$this->out("WARNING: empty 'mediaRoot' in config/bedita.cfg.php");
		}
		@$this->checkAppDirPerms($mediaRoot, "mediaRoot: ");
		
		$mediaUrl = Configure::read("mediaUrl");
		if(empty($mediaUrl)) {
			$this->out("WARNING: empty 'mediaUrl' in config/bedita.cfg.php");
		}
		@$this->checkAppUrl($mediaUrl, "mediaUrl: ");
		
		$beUrl = Configure::read("beditaUrl");
		if(empty($beUrl)) {
			$this->out("WARNING: empty 'beditaUrl' in config/bedita.cfg.php");
		}
		@$this->checkAppUrl($beUrl, "beditaUrl: ");
		
		// database connection
		@$this->checkAppDbConnection();
		
		$debugLevel = Configure::read("debug");
		$this->out("Cake debug level: $debugLevel");
		$saveSess = Configure::read("Session.save");
		$this->out("Cake session handling: " .$saveSess);
		if($saveSess !== "database") {
			$this->out("WARNING: use 'database' as session handler in config/core.php - 'Session.save'");
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
    	$driver = $db1->config['driver'];
		$dbName = $db1->config['database'];
		$this->out("Checking database connection: $dbCfg - $driver [host=".$hostName.", database=".$dbName."]");
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
		$this->initConfig();
    	if(!array_key_exists("enable", $this->params) && 
			!array_key_exists("disable", $this->params)) {
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
				$this->out("Modules disabled in " . Configure::read("projectName") . ":");
				$this->hr();
				print_r($modsNot);
			}
		}
		if (isset($this->params['enable'])) {
			$modName = $this->params['enable'];
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
					"switch" => PermissionModule::SWITCH_GROUP,
					"flag" => BEDITA_PERMS_READ_MODIFY
				)
			);
			$bePermsMod->add($modName, $perms);
	        $this->out("Module " . $modName . " added/enabled");
		}
		if (isset($this->params['disable'])) {
			$modName = $this->params['disable'];
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
		$this->initConfig();
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
	
	
	public function cleanphp() {
		if (!isset($this->params["f"])) {
			$this->out("Missing -f parameter");
			return;
		}
		$recursive = (isset($this->params["r"]))? true : false;
		$this->hr();
		$this->out("Clean PHP files from leading and trailing spaces");
		$this->hr();
		$this->Cleanup->cleanPHPFiles($this->params["f"], $recursive);
		$this->out("done");
	}
	
	public function exportFilter() {
		$this->initConfig();
		$this->readInputArgs();
		if (!isset($this->params["f"])) {
			$this->out("Missing -f parameter");
			return;
		}
		$expFile = $this->params['f'];
    	$this->checkExportFile($expFile);
		if (!isset($this->params["id"])) {
			$this->out("Missing -id parameter");
			return;
		}
		$modelType = ClassRegistry::init("BEObject")->getType($this->params["id"]);
		$model = ClassRegistry::init($modelType);
		$data = array($model->findbyId($this->params["id"]));
				
		if (!isset($this->params["filter"])) {
			$this->out("Missing -filter parameter");
			return;
		}

        $defaultParams = array('app' => '', 'root' => '', 'working' => '', 'f' => '', 
            'id' => '', 'filter' => '', 'webroot' => '');
        $filterOptions = array_diff_key($this->params, $defaultParams);

        $this->out("Creating file : " . $expFile . " from object: " . $this->params["id"] 
        . " using filter: " . $this->params["filter"]);
        $this->out('Filter options : ' . print_r($filterOptions, true));

        $filterClass = Configure::read("filters.export." . $this->params["filter"]);
        if (empty($filterClass)) {
            $this->out('Export filter class not found for: ' . $this->params['filter']);
            return;
        }
        $filterModel = ClassRegistry::init($filterClass);
		
		$result = $filterModel->export($data, $filterOptions);
		file_put_contents($expFile, $result["content"]);
		$this->out("File created: " . $expFile . " - content type: " . $result["contentType"] 
			. " size: " . $result["size"]);
			
		if (isset($this->params["validate"])) {
			$result = $filterModel->validate($expFile);
			$this->out("Validation result: \n" . $result);	
		}	
	}

	public function importFilter() {
	    $this->initConfig();
	    $this->readInputArgs();
	    if (!isset($this->params["f"])) {
	        $this->out("Missing -f parameter");
	        return;
	    }
	    $impFile = $this->params['f'];
	    if (!file_exists($impFile) ) {
	        $this->out("Import file not found: " . $impFile);
	        return;
	    }
		if (!isset($this->params["filter"])) {
	        $this->out("Missing -filter parameter");
	        return;
	    }
	
	    $options = array();
	    if (isset($this->params["id"])) {
	        $options["sectionId"] = $this->params["id"];
	    }
	    
	    // pass other options to filter
	    foreach ($this->params as $k => $v) {
	        if(!in_array($k, array("f", "id", "filter", "working", 
	                "app", "root", "webroot"))) {
	             $options[$k] = $v;
	        }
	    }
	    
	    $this->out("Importing file : " . $impFile . (empty($options["sectionId"]) ? 
	            "":  " into section: " . $options["sectionId"])
	            . " using filter: " . $this->params["filter"]);
	
	    $filterClass = Configure::read("filters.import." . $this->params["filter"]);
        if (empty($filterClass)) {
            $this->out("No import filter found for: " . $this->params["filter"]);
            return;
        }
	    
	    $filterModel = ClassRegistry::init($filterClass);
	    $result = $filterModel->import($impFile, $options);
	    $this->out($result["objects"] . " objects created in import ");
	}

	public function filters() {
		$this->initConfig();
		$filters = Configure::read('filters');
		$this->out("\n" . '(import)' . "\n");
		foreach ($filters['import'] as $k => $v) {
			$this->out($k . ': ' . $v);
		}
		$this->out("\n" . '(export)' . "\n");
		foreach ($filters['export'] as $k => $v) {
			$this->out($k . ': ' . $v);
		}
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
        $this->out('    Usage: cleanup [-frontend <frontend path>] [-logs] [-all]');
        $this->out(' ');
        $this->out("    -frontend \t clean files in <frontend path> [use frontend /app path]");
        $this->out("    -logs \t clean log files");
        $this->out("    -all  \t clean all dirs in tmp/cache dir, not just 'models', 'persistent' and 'views' (default no)");
        $this->out(' ');
        $this->out('3. checkMedia: check media files on db and filesystem');
        $this->out(' ');
        $this->out('    Usage: checkMedia [-create] [--remove-files] [--force-remove][-level <max-depth-level>] [-exclude <dirs>]');
        $this->out(' ');
        $this->out("    -create \t create media objects from files in media root not in DB");
        $this->out("    --remove-files \t remove files not referenced in media objects, ask user confirm");
        $this->out("    --force-remove \t remove files not referenced in media objects, don't ask user confirm");
        $this->out("    -level <max-depth-level> \t max depth level checking filesystem, default 2");
        $this->out("    -exclude <dirs> \t exclude from filesystem check list of comma separated dirs in media root, -exclude dir1,dir2");
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
        $this->out('7. modules: simple operations on BEdita modules list/enable/disable');
  		$this->out(' ');
  		$this->out('   Usage: modules [-list] [-enable <module-name>] [-disable <module-name>]');
        $this->out(' ');
        $this->out('8. mimeTypes: update config/mime.types.php from standard mime.types file');
  		$this->out(' ');
  		$this->out('   Usage: mimeTypes -f <mime.types-file>');
        $this->out(' ');
        $this->out('9. updateObjectTypes: update object_types table');
  		$this->out(' ');
		$this->out('10. cleanphp: clean php files from leading and trailing spaces');
  		$this->out(' ');
		$this->out('   Usage: cleanuphp [-f <file/dir-path>] [-r]');
		$this->out(' ');
		$this->out("    -f <file-or-directory-path>");
        $this->out("    -r recusrion on directory");
		$this->out(' ');
        $this->out('11. exportFilter: export object using an export filter');
  		$this->out(' ');
  		$this->out('   Usage: exportFilter -f <filename> -filter <filtername> -id <object-id>');
		$this->out(' ');
		$this->out("    -f <filename>\t actual file name to create");
        $this->out("    -filter <filtername>\t logical name of filter");
        $this->out("    -id <object-id>\t object id to export");
        $this->out(' ');
        $this->out('12. importFilter: import objects using an import filter');
  		$this->out(' ');
  		$this->out('   Usage: importFilter -f <file-to-import-path> -filter <filtername> [-id <dest-section-id>] [....]');
		$this->out(' ');
		$this->out("    -f <file-to-import-path>\t import file path");
        $this->out("    -filter <filtername>\t logical name of filter");
        $this->out("    -id <dest-section-id>\t optional section/area id destination of imported objects");
        $this->out("    - ... - specific filter params may be passed to filter");
        $this->out(' ');
	}
}

?>