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
    
	function help() {
        $this->out('Available functions:');
        $this->out('1. updateDb: update database with bedita-db sql scripts');
  		$this->out(' ');
        $this->out('    Usage: updateDb [-db <dbname>] [-data <sql>]');
  		$this->out(' ');
  		$this->out("    -db <dbname>\t use db configuration <dbname> specified in config/database.php");
  		$this->out("    -data <sql>     \t use <sql> data dump, use absolute path if not in bedita-db/");
	}
}

?>