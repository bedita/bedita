<?php
/**
 * @author ste ste@channelweb.it
 * 
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class SchemaTestCase extends BeditaTestCase {
	var $uses		= array('BEObject') ;
 	var $components	= array('Transaction') ;
    var $dataSource	= 'test' ;
	
    public function testDbDifference() {
		$this->requiredData(array("db1","db2"));
		$this->resetDefaultDataSource();
    	$model = new BEObject();
		$db1 = $this->data['db1'];
		$db2 = $this->data['db2'];
		$model->setDataSource($db1);
    	pr("using data source: ". $db1);
    	$tables1 = $this->tableList($model);
    	$tableDetails1 = $this->tableDetails($tables1, $model);
    	
		$model->setDataSource($db2);
	   	pr("using data source: ".$db2);
    	$tables2 = $this->tableList($model);
    	$tableDetails2 = $this->tableDetails($tables2, $model);

    	$this->assertTrue(count($tables1) == count($tables2), "Numero di tabelle differente!");
    	$diff = array_diff($tables1, $tables2);
    	$this->assertTrue(empty($diff), "Tabelle in $db1 non presenti in $db2!");
		if(!empty($diff)) {
			pr($diff);
		}
    	$diff = array_diff($tables2, $tables1);
    	$this->assertTrue(empty($diff), "Tabelle in $db2 non presenti in $db1!");
		if(!empty($diff)) {
			pr($diff);
		}

		// analisi tabella per tabella
		$commonTables = array_intersect($tables1, $tables2);
		foreach ($commonTables as $t) {
			$tabDb1 = $tableDetails1[$t];
			$tabDb2 = $tableDetails2[$t];

			foreach ($tabDb1 as $k=>$v) {
				$diff = array_diff_assoc($v, $tabDb2[$k]);
	    		$this->assertTrue(empty($diff), "Campo $t.$k su $db1 diverso da $db2!");
				if(!empty($diff)) {
					pr($diff);
				}
			}
			foreach ($tabDb2 as $k=>$v) {
				$diff = array_diff_assoc($v, $tabDb1[$k]);
	    		$this->assertTrue(empty($diff), "Campo $t.$k su $db2 diverso da $db1!");
				if(!empty($diff)) {
					pr($diff);
				}
			}
		}
    }

    private function tableList($model) {
   		$tables = $model->execute("show tables");
    	$res = array();
    	foreach ($tables as $k=>$v) {
    		$t1 = array_values($v);
    		$t2 = array_values($t1[0]);
    		$res[]=$t2[0] ;
    	}
    	return $res;
    }
    
    private function tableDetails($tables, $model) {
    	$res = array();
    	foreach ($tables as $t) {
    		$fields = $model->execute("describe $t");
    		$columns = array();
    		foreach($fields as $c) {
    			$columns[$c['COLUMNS']['Field']] = $c['COLUMNS'];
    		}
    		$res[$t] = $columns;
    	}
    	return $res;
    }
    
    public   function __construct () {
		parent::__construct('Schema', dirname(__FILE__)) ;
	}		
    
}

?>