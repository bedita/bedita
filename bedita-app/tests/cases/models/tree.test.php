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
include_once(dirname(__FILE__) . DS . 'tree.data.php') ;

class TreeTestCase extends CakeTestCase {
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array(
 							'BEObject', 'Collection', 
 							'Area', 'Community', 'Faq', 'Newsletter', 'Questionnaire',
 							'Scroll', 'Section', 'Timeline',
 							
 							'ContentBase', 'ViewImage', 'Content', 'BaseDocument', 
 							'Document', 'Event', 'Question', 'Answer',
 							'BEFile', 'Image', 'AudioVideo',
 							'Comment', 'Book', 'Author', 'ShortNews',
 							'Bibliography', 'FaqQuestion', 'BiblioItem', 'ObjectUser',
 							'Tree'
 	) ;
 	var $components	= array('Transaction', 'Permission') ;
    var $dataSource	= 'test' ;
 	
    var $data		= null ;
    
	function testAllTree() {
		$this->Transaction->begin() ;
		
		$tree = $this->Tree->getAll() ;
		pr("Carica l'albero completo") ;
		
		$this->assertEqual($tree,unserialize($this->data['resultAllTree1']));
		
		$this->Transaction->rollback() ;		
	} 

	function testAreeSezioniTree() {
		$this->Transaction->begin() ;

		$conf  = Configure::getInstance() ;
		
		$tree = $this->Tree->getAll(null, null, null, array($conf->objectTypes['area'], $conf->objectTypes['section'])) ;
		
		pr("Carica l'albero con solo aree e sezioni") ;
		$this->assertEqual($tree,unserialize($this->data['resultTree2']));
		 
		pr(serialize($tree));
		
		$this->Transaction->rollback() ;		
	} 

	function testUserTree() {
		$this->Transaction->begin() ;

		$conf  = Configure::getInstance() ;
		
		// Inserisce i dati
		$this->_insert(null, $this->data['inserimento']);
		$tree = $this->Tree->getAll(null, 'torto') ;
		
		pr("Carica l'albero a cui puo' accede l'utente 'torto'") ;
		for($i=0 ; $i < count($tree) ; $i++) {
			$this->_eliminaFields($tree[$i], $fields = array('id', 'parent_id', 'path', 'pathParent', 'priority', 'object_type_id', 'status', 'lang')) ;
		}
		$this->assertEqual($tree, unserialize($this->data['resultTree3']));

		$this->Transaction->rollback() ;		
	} 
	
	function testBranchTree() {
		$this->Transaction->begin() ;
		
		pr("Carica solo una ramificazione dell'albero con radice la sezione id == 3") ;
		
		$tree = $this->Tree->getAll(3) ;
		for($i=0 ; $i < count($tree) ; $i++) {
			$this->_eliminaFields($tree[$i], $fields = array('priority', 'object_type_id', 'status', 'lang')) ;
		}
		$this->assertEqual($tree,unserialize($this->data['resultTree4']));
		
		$this->Transaction->rollback() ;		
	} 

	function testStatusTree() {
		$this->Transaction->begin() ;
		
		pr("Carica gli oggetti con status 'on' ") ;
		
		$tree = $this->Tree->getAll(null, null, 'on') ;
		$compare = $this->Tree->getAll() ;
		$this->assertEqual($tree, $compare);
		
		pr("Carica gli oggetti con status 'off' (insieme vuoto) ") ;
		
		$tree = $this->Tree->getAll(null, null, 'off') ;
		
		$this->assertEqual($tree, array());

		$this->Transaction->rollback() ;		
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	private function _insert($idParent, &$data) {
		foreach ($data as $key => $item) {
			foreach ($item as $key => $value) {
				$obj = new $key() ;
				
				$datiNewObj = array(
						'title' 	=> $value['title'],
						'parent_id' => (isset($idParent))?$idParent:null,
				) ;
				
				$obj->save($datiNewObj) ;
				$id = $obj->id ;
				unset($obj) ;
				 
				if(count($value['children'])) $this->_insert($id, $value['children']);
				
				// Aggiunge eventuali permessi
				if(isset($this->data[@$value['perms']])) {
					$perms 	= &$this->data[$value['perms']] ;
					$ret 	= $this->Permission->add($id, $perms) ;
				}
			}
		}
	}
	
	/**
	 * Elimina da un albero dati campi, per potre effettuare un confronto,
	 * elimina i dti relativi agli id progressivi anche con le transaction.
	 *
	 * @param unknown_type $tree
	 * @param unknown_type $fields
	 */
	function _eliminaFields(&$tree, &$fields) {
		foreach($fields as $field) {
			unset($tree[$field]) ;
		}
		
		for($i=0 ; $i < count($tree['children']) ; $i++) {
			$this->_eliminaFields($tree['children'][$i], $fields) ;
		}
	}
	
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