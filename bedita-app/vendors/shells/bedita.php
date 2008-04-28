<?php 

App::import('Model', 'DataSource');
App::import('Model', 'Stream');
vendor('splitter_sql');

class DataSourceTest extends DataSource {
	
	function executeQuery($db,$script) {
		$sql = file_get_contents($script);
		$queries = array();
		
		$SplitterSql = new SplitterSql() ;
		$SplitterSql->parse($queries, $sql) ;
		
		foreach($queries as $q) {	
			if(strlen($q)>1) {
				$res = $db->execute(stripSlashes($q));
				if($res === false) {
					throw new Exception("Error executing query: ".$q."\n");
				}
			}
		}
	}
}

class BeditaShell extends Shell {

    function updateDb() {
        $dbCfg = 'default';
    	if (isset($this->params['db'])) {
            $dbCfg = $this->params['db'];
    	}
		
		if (!defined('SQL_SCRIPT_PATH')) { // cambiare opportunamente questo path
	        $this->out("SQL_SCRIPT_PATH has to be defined in ".APP_DIR."/config/database.php");
			return;
		}
    	$sqlDataDump = SQL_SCRIPT_PATH . 'bedita_init_data.sql';
    	if (isset($this->params['data'])) {
            if(file_exists(SQL_SCRIPT_PATH . $this->params['data'])) {
    			$sqlDataDump = SQL_SCRIPT_PATH .$this->params['data'];
            } else {
    			$sqlDataDump = $this->params['data'];
            	if(!file_exists($sqlDataDump)) {
	        		$this->out("data file $sqlDataDump not found");
					return;
            	}
            }
    	}
    	
		$db =& ConnectionManager::getDataSource($dbCfg);
    	$hostName = $db->config['host'];
    	$dbName = $db->config['database'];
		$this->out("Updating bedita db config: $dbCfg - [host=".$hostName.", database=".$dbName."]");
        $this->hr();
	
        $this->DataSourceTest =& new DataSourceTest();
		$script = SQL_SCRIPT_PATH . "bedita_schema.sql";
		$this->out("Update schema from $script");
		$this->DataSourceTest->executeQuery($db,$script);

		$script = SQL_SCRIPT_PATH . "bedita_procedure.sql";
		$this->out("Create procedures from $script");
        $this->DataSourceTest->executeQuery($db,$script);
        
		if (isset($this->params['nodata'])) {
			$this->out("No data inserted");
		} else {
	        $this->out("Load data from $sqlDataDump");
			$this->DataSourceTest->executeQuery($db, $sqlDataDump);
		}
    	
		if (isset($this->params['media'])) {
            $this->extractMediaZip($this->params['media']);
    	}
       $this->out("$dbCfg database updated");
       
       $this->out("checking media files");
       $this->checkMedia();
       $this->out("bye");
       
    }

    private function extractMediaZip($zipFile) {
		$zip = new ZipArchive;
		if ($zip->open($zipFile) === TRUE) {
			$zip->extractTo(MEDIA_ROOT);
			$zip->close();
  			$this->out("Media files extracted");
		} else {
  			$this->out("Error media file $zipFile not found!!");
		}
    }
    
    function test() {
		pr($this->params);
		pr($this->args);
    }

	public function checkMedia() {

		$stream = new Stream();
        // check filesystem
		$this->out("checkMedia - checking filesystem");
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
        	$p = $v['Stream']['path'];
        	if(!file_exists(MEDIA_ROOT.$p)) {
					$this->out("File $p not found on filesystem!!");
					$mediaOk = false;
        	}
        }
        if($mediaOk) {
			$this->out("checkMedia - database OK");
        }
	}    
    
    
    private function __clean($path) {
        
        $folder=& new Folder($path);
        $tree=$folder->tree($path, false);
        foreach ($tree as $files) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
                    $file=& new File($file);
                    if(!$file->delete()) {
                        $this->out("Error deleting file: ".$file->pwd());
                    }
                }
                
            }
        }
        return ;
    }    
    
    function checkIni() {
        @include APP. DS . 'config' . DS . 'bedita.ini.php.sample';
        $cfgSample = $config;
        @include APP. DS . 'config' . DS . 'bedita.ini.php';
        $sampleDiff = array_diff_key($cfgSample, $config);
        if(!empty($sampleDiff)) {
        	$this->out("Config to add [not in bedita.ini.php]: \n");
        	foreach ($sampleDiff as $k=>$v) {
                if(is_array($v)) {
                    $this->out("\$confg['$k']=");
                    print_r($v);
                } else {
                    $this->out("\$config['$k']=$v");
                }
        	}
        }
        
        $iniDiff = array_diff_key($config, $cfgSample);
        if(!empty($iniDiff)) {
            $this->out("\nConfig to remove [no more bedita.ini.php.sample]: \n");
            foreach ($iniDiff as $k=>$v) {
                if(is_array($v)) {
                    $this->out("\$confg['$k']=");
                    print_r($v);
                } else {
                    $this->out("\$config['$k']=$v");
                }
            }
        }
        
        if(empty($iniDiff) && empty($sampleDiff)) {
            $this->out("\nNo config key difference.");
        }

        $valDiff = array_diff($config, $cfgSample);
        if(empty($valDiff)) {
            $this->out("\nNo config values difference.");
        } else {
            $this->out("\nConfig values that are different in bedita.ini.php:\n");
            foreach ($valDiff as $k=>$v) {
                if(is_array($v)) {
                    $this->out("\$confg['$k']=");
                    print_r($v);
                } else {
                    $this->out("\$config['$k']=$v");
                }
            }
        }        
    }
    
    function cleanup() {
		$basePath = TMP;
    	if (isset($this->params['frontend'])) {
    		$basePath = $this->params['frontend'].DS."tmp".DS;
            $this->out('Cleaning dir: '.$basePath);
    		
    	}
        if (!isset($this->params['nologs'])) {
    	   $this->__clean($basePath . 'logs');
            $this->out('Logs cleaned.');
        }
        $this->__clean($basePath . 'cache' . DS . 'models');
        $this->__clean($basePath . 'cache' . DS . 'persistent');        
        $this->__clean($basePath . 'cache' . DS . 'views');        
        $this->out('Cache cleaned.');
        $this->__clean($basePath . 'smarty' . DS . 'compile');
        $this->__clean($basePath . 'smarty' . DS . 'cache');
        $this->out('Smarty compiled/cache cleaned.');

        if (isset($this->params['media'])) {
       
           $this->__clean(MEDIA_ROOT . DS. 'imgcache');
           $folder= new Folder(MEDIA_ROOT);
           $dirs = $folder->ls();
           foreach ($dirs[0] as $d) {
           	    if($d !== 'imgcache') {
           	    	$folder->delete(MEDIA_ROOT . DS. $d);
           	    }
           }
           $this->out('Media files cleaned.');
        }
    }    

	function help() {
        $this->out('Available functions:');
        $this->out('1. updateDb: update database with bedita-db sql scripts');
  		$this->out(' ');
        $this->out('    Usage: updateDb [-db <dbname>] [-data <sql>] [-nodata] [-media <zipfile>]');
  		$this->out(' ');
  		$this->out("    -db <dbname>\t use db configuration <dbname> specified in config/database.php");
  		$this->out("    -nodata <sql>   \t don't insert data");
  		$this->out("    -data <sql>     \t use <sql> data dump, use absolute path if not in bedita-db/");
  		$this->out("    -media <zipfile> \t restore media files in <zipfile>");
  		$this->out(' ');
  		$this->out('2. cleanup: cleanup cahe, compile, log files');
        $this->out(' ');
        $this->out('    Usage: cleanup [-frontend <frontend path>] [-nologs] [-media]');
        $this->out(' ');
        $this->out("    -frontend \t clean files in <frontend path> [use frontend /app path]");
        $this->out("    -nologs \t don't clean log files");
        $this->out("    -media  \t clean media files in MEDIA_ROOT (default no)");
        $this->out(' ');
        $this->out('3. checkIni: check difference between bedita.ini.php and .sample');
        $this->out(' ');
        $this->out('4. checkMedia: check media files on db and filesystem');
        $this->out(' ');
	}
}

?>