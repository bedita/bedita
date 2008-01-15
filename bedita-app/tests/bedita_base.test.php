<?php
/**
 * @author ste@channelweb.it
 *
 * Base Class for bedita unit tests
 *
 */

class BeditaTestData extends Object {
	var $data =  array() ;
	function &getData() { return $this->data ;  }
}

App::import('Controller', 'App'); // base controller, beditaexcepion... 

class BeditaTestCase extends CakeTestCase {

	var $dataSource	= 'test' ;
	var $data		= NULL ;
	var $components = array();
	var $uses = array();
	var $testName = NULL;
	var $dataFile	  = NULL ;
	
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	function startCase() {
		if(isset($this->testName)) {
			echo '<h1>Starting '.$this->testName.' Case</h1>';
			if(isset($this->dataFile)) {
				echo '<h2>Data file: '.$this->dataFile.'</h2>';
			}
		}
	}

	function endCase() {
		if(isset($this->testName)) {
			echo '<h1>Ending '.$this->testName.' Case</h1>';
		}
		$this->cleanUp();
	}

	function startTest($method) {
		echo '<h3>Starting method ' . $method . '</h3>';
	}

	function endTest($method) {
		echo '<hr />';
	}

	function requiredData($names) {
		foreach ($names as $n) {
			if(!isset($this->data[$n]))
				throw new BeditaException("Missing required data: $n");
		}
	}
	
	/**
	 * Default constructor, loads models, components, data....
	 */
	public   function __construct ($t=NULL, $phpDataDir=NULL) {
		parent::__construct() ;

		$this->testName = $t;
		if(!isset($t))
			return;
		
		// example data
		if($phpDataDir != NULL) {
			$basePath= $phpDataDir . DS . Inflector::underscore($t);
			if(file_exists($basePath.".localdata.php"))  // local data test, not versioned
				$this->dataFile = $basePath.".localdata.php";
			else
				$this->dataFile = $basePath.".data.php";
			require_once($this->dataFile);
		}	
		
		$dataClass = $this->testName."TestData";
		if (!class_exists($dataClass)) {
			echo "Missing Data: $dataClass" ;
			return ;
		}

		$testData = &new $dataClass() ;
		$r = new ReflectionClass($dataClass);
		$dataParent = $r->getParentClass()->getName();
		if($dataParent != "BeditaTestData") {
			echo "Parent Data class $dataParent" ;
			echo "Data class $dataClass should extend BeditaTestData" ;
			return ;
		}
		$this->data = $testData->getData() ;

		// Carica i Models
		if (isset($this->uses)) {
			if($this->uses !== null && $this->uses !== array()){

				$uses = is_array($this->uses) ? $this->uses : array($this->uses);

				foreach($uses as $modelClass) {
					$modelKey = Inflector::underscore($modelClass);
					
					if(!class_exists($modelClass)){
						App::import('Model',$modelClass);
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
		}

		// carica i components
		if (isset($this->components)) {
			if($this->components !== null && ($this->components !== array())){

				$components = is_array($this->components) ? $this->components : array($this->components);

				foreach($components as $componentClass) {
					App::import('Component',$componentClass);

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
		// Cambia il dataSource di default
		if(isset($this->dataSource))
			$this->setDefaultDataSource($this->dataSource) ;
		
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

	protected function cleanUp() {}
}

?>

