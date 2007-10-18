<?php
loadModel('DataSource');
vendor('splitter_sql');

class DataSourceTest extends DataSource {
	var $name = 'DataSourceTest';
	var $useDbConfig = 'test_suite';

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

class DataSourceTestCase extends CakeTestCase {

	function testDelete() {
		// esecuzione script di eliminazione dati
		$db =& ConnectionManager::getDataSource('test');
		$script = SQL_SCRIPT_PATH . "bedita_delete.sql";
		$this->DataSourceTest =& new DataSourceTest();
		//$this->DataSourceTest->executeQuery($db,$script);
	}

 	function testCreate() {
 		// esecuzione script di creazione schema
		$db =& ConnectionManager::getDataSource('test');
		$script = SQL_SCRIPT_PATH . "bedita_schema.sql";
		$this->DataSourceTest =& new DataSourceTest();
		$this->DataSourceTest->executeQuery($db,$script);
	}

	function testTruncate() {
		// esecuzione script di eliminazione dati
		$db =& ConnectionManager::getDataSource('test');
		$script = SQL_SCRIPT_PATH . "bedita_truncate.sql";
		$this->DataSourceTest =& new DataSourceTest();
		$this->DataSourceTest->executeQuery($db,$script);
	}

	function testProcedure() {
		// esecuzione script di creazione procedure
		$db =& ConnectionManager::getDataSource('test');
		$script = SQL_SCRIPT_PATH . "bedita_procedure.sql";
		$this->DataSourceTest =& new DataSourceTest();
		$this->DataSourceTest->executeQuery($db,$script);
	}

	function testPopulate() {
		$db =& ConnectionManager::getDataSource('test');
		$script = SQL_SCRIPT_PATH . "bedita_data.sql";
		$this->DataSourceTest =& new DataSourceTest();
		$this->DataSourceTest->executeQuery($db,$script);
	}

}

?>