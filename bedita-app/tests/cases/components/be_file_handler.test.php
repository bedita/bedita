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

include_once(dirname(__FILE__) . DS . 'be_file_handler.data.php') ;


class BeFileHandlerTestCase extends CakeTestCase {
	
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array() ;
 	var $components	= array('Transaction', 'BeFileHandler') ;
    var $dataSource	= 'test' ;
 	
    var $data		= null ;

	////////////////////////////////////////////////////////////////////

	function testCreateImageFromFile() {	
		$this->Transaction->begin() ;
		
		$data 			= &$this->data['minimo'] ;
		$data['path'] 	= dirname(__FILE__) . DS . $data['nameSource'];
		$data['size'] 	= filesize(dirname(__FILE__) . DS . $data['nameSource']) ;
		try {
			$ret = $this->BeFileHandler->save($data) ;						
		} catch (Exception $e) {
			pr($e->getMessage());
			$this->Transaction->rollback() ;
			
			return ;
		}
		pr("Oggetto creato: {$ret}");
		$this->assertNotEqual($ret , false);
		
		$this->Transaction->rollback() ;
	} 

	function testCreateImageFromURL() {	
		$this->Transaction->begin() ;
		
		$data 			= &$this->data['minimo'] ;
		try {
			// Crea il primo oggetto
			$data['path'] 	= dirname(__FILE__) . DS . $data['nameSource'];
			$data['size'] 	= filesize(dirname(__FILE__) . DS . $data['nameSource']) ;
			
			$ret = $this->BeFileHandler->save($data) ;
			pr("Oggetto Base Creato: {$ret}");
			$this->assertNotEqual($ret , false);
			
			// Ricava l'URL
			$data 			= &$this->data['minimoURL'] ;
			$data['path'] 	= $this->BeFileHandler->url($ret) ;
			
			pr("URL Oggetto Base: {$data['path']}");

			$ret = $this->BeFileHandler->save($data) ;
			pr("Oggetto Creato da URL: {$ret}");
			$this->assertNotEqual($ret, false);
			
		} catch (Exception $e) {
			pr("Eccezione in testCreateImageFromURL");
			pr($e->getMessage());
			$this->Transaction->rollback() ;
			
			return ;
		}
		
		$this->Transaction->rollback() ;
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
		$BeFileHandlerData 	= &new BeFileHandlerData() ;
		$this->data	= $BeFileHandlerData->getData() ;

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
		
		// Carica un controller di test
		loadController('App') ;
		$controller = new AppController() ;
		
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
						if(method_exists($component, 'startup')) {
							$component->startup($controller) ;
						} 
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