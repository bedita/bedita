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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
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
	    		$this->assertNotNull($tabDb2[$k], "Campo $t.$k mancante su $db2!");
				if(isset($tabDb2[$k])) {
		    		$diff = array_diff_assoc($v, $tabDb2[$k]);
		    		$this->assertTrue(empty($diff), "Campo $t.$k su $db1 diverso da $db2!");
					if(!empty($diff)) {
						pr($diff);
					}
				}
			}
			foreach ($tabDb2 as $k=>$v) {
	    		$this->assertNotNull($tabDb1[$k], "Campo $t.$k mancante su $db1!");
				if(isset($tabDb1[$k])) {
		    		$diff = array_diff_assoc($v, $tabDb1[$k]);
		    		$this->assertTrue(empty($diff), "Campo $t.$k su $db2 diverso da $db1!");
					if(!empty($diff)) {
						pr($diff);
					}
				}
			}
		}
    }

    private function tableList($model) {
   		$tables = $model->query("show tables");
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
    		$fields = $model->query("describe $t");
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