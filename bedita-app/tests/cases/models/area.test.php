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
 */

include_once(dirname(__FILE__) . DS . 'area.data.php') ;
/*
class AreaTest extends Area {
    var $name 			= 'Area';
//    var $tablePrefix 	= '';
    var $useDbConfig 	= 'test_suite';  
}
*/
class AreaTestCase extends CakeTestCase {
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array('BEObject', 'Collection', 'Area', 'Tree', 'Section') ;
    var $dataSource	= 'test' ;
 	
 	var $components	= array('Transaction', 'Permission') ;

 	/**
     * Dati utilizzati come esempio
     */
    var $data		= null ;
    
	function testInserimentoMinimo() {
		$conf  		= Configure::getInstance() ;
	
		$result = $this->Area->save($this->data['insert']['area']['minimo']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Area->validationErrors);
			
			return ;
		}
		
		$result = $this->Area->findById($this->Area->id);
		pr("Area creata:");
		pr($result);
		
		// I campi devono essere nella tabella Index
		pr("Proprieta' Indicizzate presenti in DB:");
		$SQL = "SELECT * FROM `indexs`  WHERE object_id IN ({$this->Area->id})" ;
		$result = $this->Area->execute($SQL) ;
		pr($result) ;
	} 
	
	function testInserimentoConCustomProperties() {
		$conf  		= Configure::getInstance() ;
		
		$result = $this->Area->save($this->data['insert']['area']['customProperties']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Area->validationErrors);
			
			return ;
		}
		$result = $this->Area->findById($this->Area->id);
		pr("Area creata:");
		pr($result);
		
		// I campi devono essere nella tabella CustomProperties
		pr("Proprieta' Custom prensenti in DB:");
		$SQL = "SELECT * FROM `custom_properties` AS `CustomProperties` WHERE object_id IN ({$this->Area->id})" ;
		$result = $this->Area->execute($SQL) ;
		pr($result) ;
	} 
	
	function testInserimentoConCustomPropertiesIndicizzate() {
		$conf  		= Configure::getInstance() ;
		
		$result = $this->Area->save($this->data['insert']['area']['customProperties']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Area->validationErrors);
			
			return ;
		}
		
		$result = $this->Area->findById($this->Area->id);
		pr("Area creata:");
		pr($result);
		
		// I campi devono essere nella tabella CustomProperties
		pr("Proprieta' Custom prensenti in DB:");
		$SQL = "SELECT * FROM `custom_properties` AS `CustomProperties` WHERE object_id IN ({$this->Area->id})" ;
		$result = $this->Area->execute($SQL) ;
		pr($result) ;
		
		// I campi devono essere nella tabella Index
		pr("Proprieta' Indicizzate presenti in DB:");
		$SQL = "SELECT * FROM `indexs`  WHERE object_id IN ({$this->Area->id})" ;
		$result = $this->Area->execute($SQL) ;
		pr($result) ;
	} 

	function testInserimentoConTitoloMultiLingua() {
		$conf  		= Configure::getInstance() ;
		
		$result = $this->Area->save($this->data['insert']['area']['traduzioni']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Area->validationErrors);
			
			return ;
		}
		
		$result = $this->Area->findById($this->Area->id);
		pr("Area creata:");
		pr($result);
		
		// Il titolo tradotto deve essere nella tabella lang_texts
		pr("Titolo tradotto presente in DB:");
		$SQL = "SELECT * FROM lang_texts WHERE object_id IN ({$this->Area->id})" ;
		$result = $this->Area->execute($SQL) ;
		pr($result) ;
	} 

	function testInserimentoInTreeCancellazione() {
		$conf  		= Configure::getInstance() ;
		
		$this->Transaction->begin() ;

		// Inserisce
		$result = $this->Area->save($this->data['insert']['area']['minimo']) ;
		$this->assertEqual($result,true);		
		$this->Tree->appendChild($this->Area->id, null) ;
		
		$result = $this->Section->save($this->data['insert']['sezione']['minimo1']) ;
		$this->assertEqual($result,true);
		$this->Tree->appendChild($this->Section->id, $this->Area->id) ;
		$id1 = $this->Section->id ;

		$this->Section = new Section() ;
		$result = $this->Section->save($this->data['insert']['sezione']['minimo2']) ;
		$this->assertEqual($result,true);
		$id2 = $this->Section->id ;
		$this->Tree->appendChild($this->Section->id, $this->Area->id) ;

		$this->Section = new Section() ;
		$result = $this->Section->save($this->data['insert']['sezione']['minimo3']) ;
		$this->assertEqual($result,true);
		$id3 = $this->Section->id ;
		$this->Tree->appendChild($this->Section->id, $id1) ;

		// Preleva l'abero inserito
		$tree = $this->Tree->getAll($this->Area->id) ;
		
		// Cancella l'area creata
		$result = $this->Area->Delete($this->Area->{$this->Area->primaryKey});
		$this->assertEqual($result,true);		
		pr("Oggetto cancellato");

		// Devono essere cancellate anche le sezioni
		$result = $this->Section->findById($id1) ;
		$this->assertEqual($result, false);
		
		$result = $this->Section->findById($id2) ;
		$this->assertEqual($result, false);

		$result = $this->Section->findById($id3) ;
		$this->assertEqual($result, false);
		
		$this->Transaction->rollback() ;
	} 
	
	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	
	function startCase() {
		echo '<h1>Starting Test Case</h1>';
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
		if($this->uses === null || ($this->uses === array())){
			return ;
		}

		if ($this->uses) {
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
					$this->cakeError('missingModel', array(array('className' => $modelClass, 'webroot' => '', 'base' => $this->base)));
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