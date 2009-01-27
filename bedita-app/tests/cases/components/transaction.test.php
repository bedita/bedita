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
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class TransactionTestCase extends BeditaTestCase {

 	var $uses		= array('BEObject', 'Area', 'Document', 'Event', 'BEFile', 'Image', 
 							'Audio', 'Video', 'ShortNews') ;
 	var $components	= array('Transaction') ;
    var $dataSource	= 'test' ;
    var $data		= null ;

	////////////////////////////////////////////////////////////////////

	function testRollback() {	
		$numRecordBegin = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		
		$this->Transaction->rollback() ;
		pr('Operazione di rollback, il DB torna alla situazione precedente') ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testCommit() {	
		$numRecordBegin = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		
		$this->Transaction->commit() ;
		pr('Operazione di commit, il DB deve risultare modificato') ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->assertNotEqual($numRecordBegin,$numRecordEnd);
	} 

	function testRollbackMultipleObjects() {	
		$numRecordBegin = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		$this->_insert($this->Community, $this->data['minimo']) ;
		$this->_insert($this->Section, $this->data['minimo']) ;
		
		$this->Transaction->rollback() ;
		pr('Operazione di rollback, il DB torna alla situazione precedente') ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testRollbackMakeFileFromData() {
		pr('Crea un oggetto BEFile passando i dati e salvando i dati su file ed esegue il rollback ') ;
		$conf  	= Configure::getInstance() ;
		$data 	= $this->data['makeFileFromData'] ;
		
		// Inizio transazione
		$this->Transaction->init('default', $conf->tmp) ;	
		$this->Transaction->begin() ;
		
		// Inserisce il file in File System
		$path = dirname(__FILE__) . DS . $data['name'];
//		$path = "/tmp" . DS . $data['name'];
		$ret  = $this->Transaction->makeFromData($path, $data['data']) ;
		pr("File creato") ;
		$this->assertEqual(file_exists($path), true);
		
		// Inserisce l'oggetto in DB
		$data['size'] = filesize($path) ;
		$data['path'] = $path ;
		$numRecordBegin = $this->_getNumRecordsTable($this->Area) ; 
		
		$result = $this->BEFile->save($data) ;
		$this->assertEqual($result,true);		
		
		$this->BEFile->bviorHideFields = array('Index', 'ObjectType', 'Permission', 'Version', 'LangText') ;
		
		$obj = $this->BEFile->findById($this->BEFile->id) ;
		pr("Oggetto Creato: {$this->BEFile->id}") ;
		pr($obj) ;
		
		// annulla tutto
		$this->Transaction->rollback() ;		
		
		// test cambianti
		$numRecordEnd = $this->_getNumRecordsTable($this->Area) ; 
		pr('DB invariato') ;
		$this->assertEqual($numRecordBegin,$numRecordEnd);
		
		pr('File assente') ;
		$this->assertEqual(file_exists($path), false);
	} 

	function testRollbackMakeFileFromFile() {
		pr('Crea un oggetto Image passando da un file presente ed esegue il rollback ') ;
		$conf  	= Configure::getInstance() ;
		$data 	= $this->data['makeFileFromFile'] ;
		
		// Inizio transazione
		$this->Transaction->init('default', $conf->tmp) ;	
		$this->Transaction->begin() ;
		
		// Inserisce il file in File System
		$path = dirname(__FILE__) ;
		$ret  = $this->Transaction->makeFromFile(($path . DS . $data['name']), ($path . DS . $data['nameSource'])) ;
		pr("File creato") ;
		$this->assertEqual(file_exists($path . DS . $data['name']), true);
		
		// Inserisce l'oggetto in DB
		$data['size'] = filesize($path . DS . $data['name']) ;
		$data['path'] = $path . DS . $data['name'] ;
		$numRecordBegin = $this->_getNumRecordsTable($this->Image) ; 
		
		$result = $this->Image->save($data) ;
		$this->assertEqual($result,true);		
		
		$this->BEFile->bviorHideFields = array('Index', 'ObjectType', 'Permission', 'Version', 'LangText') ;
		
		$obj = $this->Image->findById($this->Image->id) ;
		pr("Oggetto Creato: {$this->Image->id}") ;
		pr($obj) ;
		
		// annulla tutto
		$this->Transaction->rollback() ;		
		
		// test cambianti
		$numRecordEnd = $this->_getNumRecordsTable($this->Image) ; 
		pr('DB invariato') ;
		$this->assertEqual($numRecordBegin,$numRecordEnd);
		
		pr('File assente') ;
		$this->assertEqual(file_exists($path . DS . $data['name']), false);
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	private function _insert(&$model, &$data) {
		$conf  		= Configure::getInstance() ;
		
		// Crea
		$result = $model->save($data) ;
		$this->assertEqual($result,true);		
		
		// Visualizza
		$obj = $model->findById($model->id) ;
		pr("Oggetto Creato: {$model->id}") ;
//		pr($obj) ;
		
	} 
	
	private function _delete(&$model) {
		$conf  		= Configure::getInstance() ;
		
		// Cancella
		$result = $model->Delete($model->{$model->primaryKey});
		$this->assertEqual($result,true);		
		pr("Oggetto cancellato");
	} 

	/**
	 * Torna un array con il numero di righe per ogni tabella
	 *
	 */
	private function _getNumRecordsTable(&$model) {
		$recs = array() ;
		
		$tables = $model->query("SHOW TABLES") ;
		
		for($i = 0 ; $i < count($tables) ; $i++) {
			$ret = array_values($tables[$i]['TABLE_NAMES']) ;
			
			$nums = $model->query("SELECT count(*) AS num FROM {$ret[0]} ")  ;
			
			$recs[] = array($ret[0], $nums[0][0]['num']) ;
		}
		
		return $recs ;
	}
	
	public   function __construct () {
		parent::__construct('Transaction', dirname(__FILE__)) ;
	}	
}
?> 