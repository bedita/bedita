<?php

App::import('Core', 'String');
App::import('Core', 'Controller');
App::import('Core', 'Model');
App::import('Controller', 'App'); // BeditaException
App::import('Model', 'Document');

class DumpModel extends AppModel {
	var $useTable = "objects";
};

abstract class MigrationBase {
	
	protected $model;
	private $handle;
	
	protected function createInsert($data, $t) {
		$fields = "";
		$values = "";
		$count = 0;
		foreach ($data as $k=>$v) {
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
    	return "INSERT INTO $t (".$fields.") VALUES ($values);\n";
    }
    
    public function setModel(Model $m) {
    	$this->model = $m;
    }

    public function setFile($f) {
    	$this->handle = fopen($f, "w");
		if($this->handle === FALSE) 
			throw new Exception("Error opening file: ".$f);
    }
    
    protected function write($s) {
    	fwrite($this->handle, $s);
    }
    
    protected function close() {
    	fclose($this->handle);
    }
    
    protected function copyTable($t) {
		$res = $this->model->execute("SELECT * from $t");
		foreach ($res as $r) {
			$this->write($this->createInsert($r[$t], $t));
		}
    }

    protected function createExportFromArray(array $arr) {
		foreach ($arr as $method => $q) {
			if($method == "copy") {
				$tables =  explode(" ", $q);
				foreach ($tables as $t) {
					$this->copyTable($t);
				}
			} else {
				$res = $this->model->execute($q);
				if(is_array($res)) {
					foreach ($res as $r) {
						$this->$method($r);
					}
				}
			}
		}
	}
    
    abstract public function createExport();
};

class MigrateShell extends Shell {

	const DEFAULT_MIGRATION_DUMP = 'bedita-migration.sql' ;
	
	
	public function migrate() {
		
        $dbCfg = 'default';
    	if (isset($this->params['db'])) {
            $dbCfg = $this->params['db'];
    	}
		$db = ConnectionManager::getDataSource($dbCfg);
    	$dbName = $db->config['database'];
		$this->out("Importing data from db config: $dbCfg - database=".$dbName);
		$model = new DumpModel();
		$model->setDataSource($dbCfg);
		
		$scr = $this->params['script'];
		if (empty($scr)) {
	        $this->out("script file is mandatory, use -script ");
			return;
		}
		$this->out("Using script: $scr");
		include_once($scr);
		
		$migration = new Migration();
		$migration->setModel($model);
		$expFile = self::DEFAULT_MIGRATION_DUMP;
    	if (isset($this->params['f'])) {
            $expFile = $this->params['f'];
    	}
    	if(file_exists($expFile)) {
			$res = $this->in("$expFile exists, overwrite? [y/n]");
			if($res == "y") {
				if(!unlink($expFile)){
					throw new Exception("Error deleting $expFile");
				}
			} else {
				$this->out("Export aborted. Bye.");
				return;
			}
		}
    	
		$migration->setFile($expFile);
		$migration->createExport();
	}
	
	function help() {
 	
		$this->out('Available functions:');
        $this->out('1. migrate: migrate db data');
  		$this->out(' ');
  		$this->out('   Usage: migrate -script <migration-script.php> [-db <dbname>] [-f <sql-dump-filename>]');
  		$this->out(' ');
	}
	
}

?>