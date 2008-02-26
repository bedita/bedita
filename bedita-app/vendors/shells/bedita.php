<?php 

App::import('Model', 'DataSource');
vendor('splitter_sql');

class DataSourceTest extends DataSource {
	
	function executeQuery($db,$script) {
		$sql = file_get_contents($script);
		$queries = array();
		
		$SplitterSql = new SplitterSql() ;
		$SplitterSql->parse($queries, $sql) ;
		
		foreach($queries as $q) {	
			if(strlen($q)>1) {
				$db->execute(stripSlashes($q)) ;	
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
    	
    	$this->out("Updating bedita db config: $dbCfg");
        $this->hr();
	
		$db =& ConnectionManager::getDataSource($dbCfg);
		$this->DataSourceTest =& new DataSourceTest();
		
		$script = SQL_SCRIPT_PATH . "bedita_schema.sql";
		$this->out("Update schema from $script");
		$this->DataSourceTest->executeQuery($db,$script);

		$script = SQL_SCRIPT_PATH . "bedita_procedure.sql";
		$this->out("Create procedures from $script");
        $this->DataSourceTest->executeQuery($db,$script);
        
		$this->out("Load data from $sqlDataDump");
		$this->DataSourceTest->executeQuery($db, $sqlDataDump);
		
       $this->out("$dbCfg database updated, bye!");
    }

    function test() {
		pr($this->params);
		pr($this->args);
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
    }
    
    function cleanup() {
        if (!isset($this->params['nologs'])) {
    	   $this->__clean(TMP . 'logs');
            $this->out('Logs cleaned.');
        }
        $this->__clean(TMP . 'cache' . DS . 'models');
        $this->__clean(TMP . 'cache' . DS . 'persistent');        
        $this->__clean(TMP . 'cache' . DS . 'views');        
        $this->out('Cache cleaned.');
        $this->__clean(TMP . 'smarty' . DS . 'compile');
        $this->__clean(TMP . 'smarty' . DS . 'cache');
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
        $this->out('    Usage: updateDb [-db <dbname>] [-data <sql>]');
  		$this->out(' ');
  		$this->out("    -db <dbname>\t use db configuration <dbname> specified in config/database.php");
  		$this->out("    -data <sql>     \t use <sql> data dump, use absolute path if not in bedita-db/");
        $this->out(' ');
  		$this->out('2. cleanup: cleanup cahe, compile, log files');
        $this->out(' ');
        $this->out('    Usage: cleanup [-nologs] [-media]');
        $this->out(' ');
        $this->out("    -nologs \t don't clean log files");
        $this->out("    -media  \t clean media files in MEDIA_ROOT");
  		$this->out('3. checkIni: check difference between bedita.ini.php and .sample');
        $this->out(' ');
	}
}

?>