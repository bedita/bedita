<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
	var $uses		= array('BeSchema') ;
 	var $components	= array('Transaction') ;
    var $dataSource	= 'test' ;
	
    public function testDbDifference() {
		$this->requiredData(array("db1","db2"));
		$this->resetDefaultDataSource();
    	$beSchema = new BeSchema();
		$db1 = $this->data['db1'];
		$db2 = $this->data['db2'];
    	pr("compare data source: ". $this->dbDetails($db1));
    	pr("with data source: ". $this->dbDetails($db2));
    	clearCache(null, 'models');
		clearCache(null, 'persistent');
    	
    	$tableDetails1 = $beSchema->readTables(array("connection" => $db1));
    	$tables1 = array_keys($tableDetails1);
    	
    	clearCache(null, 'models');
		clearCache(null, 'persistent');
    	$tableDetails2 = $beSchema->readTables(array("connection" => $db2));
    	$tables2 = array_keys($tableDetails2);

    	$this->assertTrue(count($tables1) == count($tables2), "Number of tables is different!");
    	$diff = array_diff($tables1, $tables2);
    	$this->assertTrue(empty($diff), "Tables in $db1 not present in $db2!");
		if(!empty($diff)) {
			pr($diff);
		}
    	$diff = array_diff($tables2, $tables1);
    	$this->assertTrue(empty($diff), "Tables in $db2 not present in $db1!");
		if(!empty($diff)) {
			pr($diff);
		}

		// analysis table by table
		$commonTables = array_intersect($tables1, $tables2);
		foreach ($commonTables as $t) {
			$tabDb1 = $tableDetails1[$t];
			$tabDb2 = $tableDetails2[$t];

			foreach ($tabDb1 as $k=>$v) {
	    		$this->assertNotNull($tabDb2[$k], "Field $t.$k missing on $db2!");
				if(isset($tabDb2[$k])) {
		    		$diff = array_diff_assoc($v, $tabDb2[$k]);
		    		$this->assertTrue(empty($diff), "Field $t.$k on $db1 is different than in $db2!");
					if(!empty($diff)) {
						pr($diff);
					}
				}
			}
			foreach ($tabDb2 as $k=>$v) {
	    		$this->assertNotNull($tabDb1[$k], "Field $t.$k missing on $db1!");
				if(isset($tabDb1[$k])) {
		    		$diff = array_diff_assoc($v, $tabDb1[$k]);
		    		$this->assertTrue(empty($diff), "Field $t.$k on $db2 is different than in $db1!");
					if(!empty($diff)) {
						pr($diff);
					}
				}
			}
		}
    }

    private function dbDetails($dbCfg) {
    	$db = ConnectionManager::getDataSource($dbCfg);
    	$hostName = $db->config['host'];
    	$dbName = $db->config['database'];
    	$driver = $db->config['driver'];
    	return "$dbCfg - $driver [host=".$hostName.", database=".$dbName."]";
    }
    
    public   function __construct () {
		parent::__construct('Schema', dirname(__FILE__)) ;
	}		
    
}

?>