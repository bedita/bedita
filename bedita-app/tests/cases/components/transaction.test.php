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
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class TransactionTestCase extends BeditaTestCase {

 	var $uses		= array('BEObject', 'Area', 'Document', 'Event', 'BEFile', 'Image') ;
 	var $components	= array('Transaction') ;
    var $dataSource	= 'test' ;

    ////////////////////////////////////////////////////////////////////

	function testRollback() {	
		$numRecordBegin = $this->_getNumRecordsTable() ; 
		
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		
		$this->Transaction->rollback() ;
		pr('Rollback, check DB rows') ;
		
		$numRecordEnd = $this->_getNumRecordsTable() ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
		
		$this->Area->containLevel('minimum');
		$obj = $this->Area->findById($this->Area->id) ;
//		$obj = $this->Area->findById(1231) ;
		$this->assertFalse($obj);
		
		if($obj !== false) { // cleanup....
			$this->_delete($this->Area);
		}
	} 

	function testCommit() {	
		$numRecordBegin = $this->_getNumRecordsTable() ; 
		
		$this->Transaction->begin() ;
		
		$this->Area->create();
		$this->_insert($this->Area, $this->data['minimo']) ;
		
		$this->Transaction->commit() ;
		pr('Commit, DB has to be modified') ;
		
		$numRecordEnd = $this->_getNumRecordsTable() ; 
		
		$this->assertNotEqual($numRecordBegin, $numRecordEnd);
		
		$this->_delete($this->Area);
	} 

	function testRollbackMultipleObjects() {	
		$numRecordBegin = $this->_getNumRecordsTable() ; 
		
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		$this->_insert($this->Document, $this->data['minimo']) ;
		$this->_insert($this->Event, $this->data['minimo']) ;
		
		$this->Transaction->rollback() ;
		pr('Rollback, previous Database operations cancelled. Data restored') ;
		
		$numRecordEnd = $this->_getNumRecordsTable() ; 
		
		$this->assertEqual($numRecordBegin, $numRecordEnd);
	} 

	function testRollbackMakeFileFromData() {
		pr('Create a BEFile object, save on file and rollback ') ;
		$data 	= $this->data['makeFileFromData'] ;
		
		// start transaction
		$this->Transaction->init('default', Configure::read("tmp")) ;	
		$this->Transaction->begin() ;
		
		// create file on filesystem
		$path = TMP. DS . $data['name'];
		$ret  = $this->Transaction->makeFromData($path, $data['data']) ;
		pr("File creato") ;
		$this->assertEqual(file_exists($path), true);
		
		// Insert object into the DB
		$data['file_size'] = filesize($path) ;
		$data['uri'] = $path ;
		$numRecordBegin = $this->_getNumRecordsTable() ; 
		
		$result = $this->BEFile->save($data) ;
		$this->assertEqual($result,true);		
		
		$this->BEFile->containLevel('minimum');
		$obj = $this->BEFile->findById($this->BEFile->id) ;
		pr("Oggetto Creato: {$this->BEFile->id}") ;
		pr($obj) ;
		
		// rollback
		$this->Transaction->rollback() ;		
		
		$numRecordEnd = $this->_getNumRecordsTable() ; 
		pr('DB not changed') ;
		$this->assertEqual($numRecordBegin,$numRecordEnd);
		
		pr('File not found') ;
		$this->assertEqual(file_exists($path), false);
	} 

	function testRollbackMakeFileFromFile() {
		pr('Create an Image object from file and rollback ') ;
		$data 	= $this->data['makeFileFromFile'] ;
		
		// Start transaction
		$this->Transaction->init('default', Configure::read("tmp")) ;	
		$this->Transaction->begin() ;
		
		// Insert file on the File System
		$srcPath = dirname(__FILE__) ;
		$path = TMP;
		$ret  = $this->Transaction->makeFromFile(($path . DS . $data['name']), ($srcPath . DS . $data['nameSource'])) ;
		pr("File creato") ;
		$this->assertEqual(file_exists($path . DS . $data['name']), true);
		
		// Insert object into the DB
		$data['size'] = filesize($path . DS . $data['name']) ;
		$data['path'] = $path . DS . $data['name'] ;
		$numRecordBegin = $this->_getNumRecordsTable() ; 
		
		$result = $this->Image->save($data) ;
		$this->assertEqual($result,true);		
		
		$this->Image->containLevel('minimum');
		$obj = $this->Image->findById($this->Image->id) ;
		pr("Oggetto Creato: {$this->Image->id}") ;
		pr($obj) ;
		
		// rollback
		$this->Transaction->rollback() ;		
		
		$numRecordEnd = $this->_getNumRecordsTable() ; 
		pr('DB not changed') ;
		$this->assertEqual($numRecordBegin,$numRecordEnd);
		
		pr('File not found') ;
		$this->assertEqual(file_exists($path . DS . $data['name']), false);
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	private function _insert($model, &$data) {
		$result = $model->save($data) ;
		$this->assertEqual($result,true);		
		$obj = $model->findById($model->id) ;
		pr("Object created: {$model->id}") ;
	} 
	
	private function _delete($model) {
		$result = $model->delete($model->{$model->primaryKey});
		$this->assertEqual($result,true);		
		pr("Object removed");
	} 

	/**
	 * Returns an array with row count for every table
	 */
	private function _getNumRecordsTable() {
		$recs = array() ;
		
		$model = ClassRegistry::init("BEObject");
		$tables = $model->query("SHOW TABLES") ;
		
		$count = 0;
		for($i = 0 ; $i < count($tables) ; $i++) {
			$ret = array_values($tables[$i]['TABLE_NAMES']) ;
			
			$q = "SELECT count(*) AS num FROM {$ret[0]} ";
			$nums = $model->query($q, false);
			
			$count += $nums[0][0]['num'] ;
		}
		
		return $count ;
	}
	
	public   function __construct () {
		parent::__construct('Transaction', dirname(__FILE__)) ;
	}	
}
?> 