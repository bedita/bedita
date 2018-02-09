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

App::import('Controller', 'App'); // base controller, beditaexcepion...
App::import('Lib', 'BeLib');

/**
 * BeditaTestData base class
 */
class BeditaTestData extends Object {
	var $data =  array() ;
	function &getData() { return $this->data ;  }
}

class BeditaTestController extends AppController{}

/**
 * BeditaTestCase base class
 */
class BeditaTestCase extends CakeTestCase {

	var $dataSource	= 'test' ;
	var $data		= NULL ;
	var $components = array();
	var $uses = array();
	var $testName = NULL;
	var $dataFile	  = NULL ;
	var $testController = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	function startCase() {
		if(isset($this->testName)) {
			echo '<h1>Starting '.$this->testName.' Case</h1>';
			if(isset($this->dataFile)) {
				echo '<h2>Data file: '.$this->dataFile.'</h2>';
			}
		}
		$db = ConnectionManager::getDataSource($this->dataSource);
		echo '<h2>Using database: <b>'. $db->config['database'] .'</b></h2>';
		echo '<hr/>';
	}

	function endCase() {
		echo '<hr/>';
		if(isset($this->testName)) {
			echo '<h1>Ending '.$this->testName.' Case</h1>';
		}
		$this->cleanUp();
	}

	function startTest($method) {
		echo '<hr/>';
		echo '<h3>Starting method ' . $method . '</h3>';
		echo '<hr/>';
	}

	function endTest($method) {
		echo '<hr/>';
	}

	function requiredData(array $names) {
		foreach ($names as $n) {
			if(!isset($this->data[$n]))
				throw new BeditaException("Missing required data: $n");
		}
	}

	/**
	 * Default constructor, loads models, components, data....
	 */
	public   function __construct ($t=NULL, $phpDataDir=NULL) {
		parent::__construct();

		BeLib::getObject("BeConfigure")->initConfig();

		// setup unit test user id
		$conf = Configure::getInstance() ;
		$conf->write("beditaTestUserId", $conf->unitTestUserId);

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

		$testData = new $dataClass() ;
		$r = new ReflectionClass($dataClass);
		$dataParent = $r->getParentClass()->getName();
		if($dataParent != "BeditaTestData") {
			echo "Parent Data class $dataParent" ;
			echo "Data class $dataClass should extend BeditaTestData" ;
			return ;
		}
		$this->data = $testData->getData() ;

		// Set dataSource
		if(isset($this->dataSource))
			$this->setDefaultDataSource($this->dataSource) ;

		// load Models
		if (isset($this->uses)) {
			if($this->uses !== null && $this->uses !== array()){

				$uses = is_array($this->uses) ? $this->uses : array($this->uses);

				foreach($uses as $modelClass) {
					$this->{$modelClass} = ClassRegistry::init($modelClass);
					if($this->{$modelClass} === false) {
						echo "Missing Model: $modelClass" ;
						return ;
					}
				}
			}
		}

		// load components
		if (isset($this->components)) {
			if($this->components !== null && ($this->components !== array())){

				$components = is_array($this->components) ? $this->components : array($this->components);

				$this->testController = new BeditaTestController();

				foreach($components as $componentClass) {
					App::import('Component',$componentClass);

					$className = $componentClass . 'Component' ;
					if (class_exists($className)) {
						$component = new $className();
						$this->{$componentClass} = $component;
						if(method_exists($component, "startup")) {
							$component->startup($this->testController);
						}
					} else {
						echo "Missing Component: $className" ;
						return ;
					}
				}
			}
		}

	}

	/**
	 * Set datasource
	 */
	protected function setDefaultDataSource($name) {
		$_this =& ConnectionManager::getInstance();

		$connections = $_this->enumConnectionObjects();
		if (in_array($name, array_keys($connections))) {
			$conn = $connections[$name];
			$class = $conn['classname'];
			$_this->loadDataSource($name);

			$this->_originalDefaultDB = &$_this->_dataSources['default'] ;

			$_this->_dataSources['default'] = new $class($_this->config->{$name});
			$_this->_dataSources['default']->configKeyName = $name;
		} else {
			trigger_error(sprintf(__("ConnectionManager::getDataSource - Non-existent data source %s", true), $name), E_USER_ERROR);
			return null;
		}

		return $_this->_dataSources['default'];
	}

	/**
	 * Reset data source to default
	 */
	protected function resetDefaultDataSource() {
		$_this =& ConnectionManager::getInstance();

		if(!isset($this->_originalDefaultDB)) return ;
		$_this->_dataSources['default'] = &$this->_originalDefaultDB  ;

		unset($this->_originalDefaultDB);

		return $_this->_dataSources['default'];
	}

	protected function cleanUp() {}

	/**
	 * Check for duplicate entry in actsAs
	 *
	 * @param unknown_type $model
	 */
	protected function checkDuplicateBehavior($model) {
		if (empty($model->actsAs)) {
			pr("actsAs attribute not defined for " . $model->name);
			return;
		}

		pr("actsAs attribute, check for duplicate entry:");
 		pr($model->actsAs);

 		// check in numeric array
 		foreach ($model->actsAs as $key => $value) {
 			if (is_numeric($key))
 				$numericActAs[] = $value;
 		}
 		$this->assertEqual($numericActAs, array_unique($numericActAs));

 		// specific associative array
 		if (!empty($model->actsAs["CompactResult"])) {
	 		$this->assertEqual($model->actsAs["CompactResult"],
	 						   array_unique($model->actsAs["CompactResult"]));
 		}

 		if (!empty($model->actsAs["ForeignDependenceSave"])) {
	 		$this->assertEqual($model->actsAs["ForeignDependenceSave"],
	 						   array_unique($model->actsAs["ForeignDependenceSave"]));
 		}

 		if (!empty($model->actsAs["DeleteDependentObject"])) {
 			$this->assertEqual($model->actsAs["DeleteDependentObject"],
	 						   array_unique($model->actsAs["DeleteDependentObject"]));
 		}
	}
}

?>