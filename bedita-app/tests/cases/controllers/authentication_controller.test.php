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
 * Verifica il meccanismo di login/logout
 * 
 */

include_once(dirname(__FILE__) . DS . 'authentication_controller.data.php') ;

class TestsControllerTest extends CakeTestCase { 
	
    var $dataSource	= 'test' ;
 	
    var $data		= null ;
	var $components	= array('Session') ;

	////////////////////////////////////////////////////////////////
	function testLoginOk() {
		pr("Esegue un login con successo.") ;
		pr("NOTA:\n2 Warning, sono un tentativo di redirect del controller.") ;
		
		$this->testAction('/authentications/login',	array('data' => $this->data['login'], 'method' => 'post'));
		$user 	= $this->Session->read('BEAuthUser') ;
		$allow 	= $this->Session->read('BEAuthAllow') ;
		$this->assertEqual(serialize($user), $this->data['loginOKResult']);
	} 

	function testLogout() {
		pr("Chiude la sessione") ;
		
		$this->testAction('/authentications/logout');
		$user 	= $this->Session->read('BEAuthUser') ;
		$allow 	= $this->Session->read('BEAuthAllow') ;
		$this->assertEqual($user, null);
	}
	
	
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	function startCase() {
		echo '<h1>Starting Authentication Case</h1>';
	}

	function endCase() {
		echo '<h1>Ending Authentication Case</h1>';
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
		$AuthenticationData = &new AuthenticationData() ;
		$this->data			= $AuthenticationData->getData() ;

		// Cambia il dataSource di default
		if(isset($this->dataSource)) $this->setDefaultDataSource($this->dataSource) ;

		// Carica i Models
		if (isset($this->uses)) {
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
		if (isset($this->components)) {
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

