<?php 

loadModel('DataSource');
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
    	if (count($this->args) > 0) {
            $dbCfg = $this->args[0];
    	}
       	$this->out("Updating bedita db config: $dbCfg");
        $this->hr();
	
		if (!defined('SQL_SCRIPT_PATH')) { // cambiare opportunamente questo path
	        	$this->out("SQL_SCRIPT_PATH has to be defined in ".APP_DIR."/config/database.php");
			return;
		}
		$db =& ConnectionManager::getDataSource($dbCfg);
		$this->DataSourceTest =& new DataSourceTest();
		
		$script = SQL_SCRIPT_PATH . "bedita_schema.sql";
		$this->out("Update schema from $script");
		$this->DataSourceTest->executeQuery($db,$script);

		$script = SQL_SCRIPT_PATH . "bedita_procedure.sql";
		$this->out("Create procedures from $script");
        $this->DataSourceTest->executeQuery($db,$script);
        
		$script = SQL_SCRIPT_PATH . "bedita_data.sql";
		$this->out("Load data from $script");
		$this->DataSourceTest->executeQuery($db,$script);
		
       $this->out("$dbCfg database updated, bye!");
 		
    }

	function help() {
        $this->out('Available functions:');
        $this->out('updateDb: updatede database with bedita-db sql scripts');
	}
}

?>