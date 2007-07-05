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
 * Verifica il componente Permission
 * 
 */

include_once(dirname(__FILE__) . DS . 'permissionModule.data.php') ;


class PermissionModuleTestCase extends CakeTestCase {
	
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array() ;
 	var $components	= array('Transaction', 'BePermissionModule') ;
    var $dataSource	= 'test' ;
 	
    var $data		= null ;

	////////////////////////////////////////////////////////////////////

	function testAddSingleModule() {	
		$this->Transaction->begin() ;
		
		$perms = $this->BePermissionModule->load('areas') ;
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi modulo") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati
		$perms = $this->BePermissionModule->load('areas') ;
		
		pr("Verifica permessi modulo aggiunti") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
	
		$this->Transaction->rollback() ;
	} 

	function testAddMultipleModule() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add(array('admin', 'areas'), $this->data['addPerms1']) ;
		pr("Aggiunta permessi moduli") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati e verifica
		$perms = $this->BePermissionModule->load('admin') ;
		pr("Verifica permessi modulo 'admin'") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$perms = $this->BePermissionModule->load('areas') ;
		pr("Verifica permessi modulo 'areas'") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testDeleteBySingleModule() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);

		// cancella i permessi
		$ret = $this->BePermissionModule->remove('areas', $this->data['removePerms1']) ;
		pr("Cancella i permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati
		$perms = $this->BePermissionModule->load('areas') ;
		pr("Verifica permessi cancellati") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);

		$this->Transaction->rollback() ;
	} 
	
	function testDeleteAllBySingleModule() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);

		// cancella i permessi
		$ret = $this->BePermissionModule->removeAll('areas') ;
		pr("Cancella i permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati
		$perms = $this->BePermissionModule->load('areas') ;
		pr("Verifica permessi cancella") ;
		$this->assertEqual(array(), $perms);

		$this->Transaction->rollback() ;
	} 

	function testPermissionsByUserid() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
		
		// Verifica dei permessi
		$ret = $this->BePermissionModule->verify('areas', 'torto', BEDITA_PERMS_MODIFY) ;
		pr("Verifica permessi di modifica utente 'torto' (true)") ;
		$this->assertEqual((boolean)$ret, true);
		
		$ret = $this->BePermissionModule->verify('areas', 'torto', BEDITA_PERMS_CREATE) ;
		pr("Verifica permessi di creazione utente 'torto' (false)") ;
		$this->assertEqual((boolean)$ret, false);

		$ret = $this->BePermissionModule->verify('areas', '', BEDITA_PERMS_READ) ;
		pr("Verifica permessi di lettura utente anonimo (true)") ;
		$this->assertEqual((boolean)$ret, true);

		$this->Transaction->rollback() ;
	} 
	
	function testPermissionsByGroup() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
		
		// Verifica dei permessi
		$ret = $this->BePermissionModule->verifyGroup('areas', 'guest', BEDITA_PERMS_READ) ;
		pr("Verifica permessi di lettura gruppo 'guest' (true)") ;
		$this->assertEqual((boolean)$ret, true);
		
		$ret = $this->BePermissionModule->verifyGroup('areas', 'guest', BEDITA_PERMS_DELETE) ;
		pr("Verifica permessi di cancellazione gruppo 'guest' (false)") ;
		$this->assertEqual((boolean)$ret, false);

		$this->Transaction->rollback() ;
	} 
	
	function testGetListModuleReadableByUserid() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
		
		// Verifica dei permessi
		$ret = $this->BePermissionModule->getListModules('bedita') ;
pr($ret);		
		$this->Transaction->rollback() ;
	} 

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

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	
	function startCase() {
		echo '<h1>Creazione, gestione, cancellazione permessi sui moduli Case</h1>';
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
		$PermissionData = &new PermissionModuleData() ;
		$this->data		= $PermissionData->getData() ;

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