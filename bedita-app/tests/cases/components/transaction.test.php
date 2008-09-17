<?php 
/**
 * 
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5

 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @author giangi@qwerg.com
 * 
 * Crea e salva tutti i tipi di oggetti per verificare il settaggio del tipo correttamente
 * 
 */

include_once(dirname(__FILE__) . DS . 'transaction.data.php') ;


class TransactionTestCase extends CakeTestCase {
	
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array(
 							'BEObject',
 							'Area', 'Community', 'Faq', 'Newsletter', 'Questionnaire',
 							'Scroll', 'Section', 'Timeline',
 							
 							'Content', 'BaseDocument', 
 							'Document', 'Event', 'Question', 'Answer',
 							'BEFile', 'Image', 'Audio', 'Video',
 							'Comment', 'Book', 'Author', 'ShortNews',
 							'Bibliography', 'FaqQuestion', 'BiblioItem', 'ObjectUser'
 	) ;
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
		
		$tables = $model->execute("SHOW TABLES") ;
		
		for($i = 0 ; $i < count($tables) ; $i++) {
			$ret = array_values($tables[$i]['TABLE_NAMES']) ;
			
			$nums = $model->execute("SELECT count(*) AS num FROM {$ret[0]} ")  ;
			
			$recs[] = array($ret[0], $nums[0][0]['num']) ;
		}
		
		return $recs ;
	}
	
	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	
	function startCase() {
		echo '<h1>Test Transazioni sul DB e sul File System</h1>';
	}

	function endCase() {
		echo '<h1>Ending Test Case</h1>';
	}

	function startTest($method) {
		echo '<h3>Starting method ' . $method . '</h3>';
	}

	function endTest($method) {
		echo '<hr />';
	}

	/**
 	* Loads and instantiates models required by this controller.
 	* If Controller::persistModel; is true, controller will create cached model instances on first request,
 	* additional request will used cached models
 	*
 	*/
	public   function __construct () {
		parent::__construct() ;
		
		// Carica i dati d'esempio
		$AreaData 	= &new AreaData() ;
		$this->data	= $AreaData->getData() ;

		// Cambia il dataSource di default
		if(isset($this->dataSource)) $this->setDefaultDataSource($this->dataSource) ;

		// Carica i Models
		if ($this->uses) {
			if($this->uses === null || ($this->uses === array())){
				return ;
			}

			$uses = is_array($this->uses) ? $this->uses : array($this->uses);

			foreach($uses as $modelClass) {
				$modelKey = Inflector::underscore($modelClass);

				if(!class_exists($modelClass)){
					loadModel($modelClass);
				}

				if (class_exists($modelClass)) {
						$model =& new $modelClass();
						$this->modelNames[] = $modelClass;
						$this->{$modelClass} =& $model;
				} else {
					echo "Missing Model: $modelClass" ;
					return ;
				}
			}
		}
		
		// carica i components
		if ($this->components) {
			if($this->components === null || ($this->components === array())){
				return ;
			}

			$components = is_array($this->components) ? $this->components : array($this->components);

			foreach($components as $componentClass) {
				loadComponent($componentClass);
				
				$className = $componentClass . 'Component' ;
				if (class_exists($className)) {
						$component =& new $className();
						$this->{$componentClass} =& $component;
				} else {
					echo "Missing Component: $className" ;
					return ;
				}
			}
		}
		
	}

	/**
 	* Cambio il data source di default
 	*/
	protected function setDefaultDataSource($name) {		
		$_this =& ConnectionManager::getInstance();

		if (in_array($name, array_keys($_this->_dataSources))) {
			return $_this->_dataSources[$name];
		}

		$connections = $_this->enumConnectionObjects();
		if (in_array($name, array_keys($connections))) {
			$conn = $connections[$name];
			$class = $conn['classname'];
			$_this->loadDataSource($name);
			
			$this->_originalDefaultDB = &$_this->_dataSources['default'] ;
			
			$_this->_dataSources['default'] =& new $class($_this->config->{$name});
			$_this->_dataSources['default']->configKeyName = $name;
		} else {
			trigger_error(sprintf(__("ConnectionManager::getDataSource - Non-existent data source %s", true), $name), E_USER_ERROR);
			return null;
		}

		return $_this->_dataSources['default'];		
	}

	/**
 	* Resetta data source di default
 	*/
	protected function resetDefaultDataSource($name) {
		
		if(!isset($this->_originalDefaultDB)) return ;
		$_this->_dataSources['default'] = &$this->_originalDefaultDB  ;
		
		unset($this->_originalDefaultDB);
		
		return $_this->_dataSources['default'];
	}
	
}
?> 