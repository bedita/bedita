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

include_once(dirname(__FILE__) . DS . 'permission.data.php') ;


class PermissionTestCase extends CakeTestCase {
	
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array(
 							'BEObject', 'Collection', 
 							'Area', 'Community', 'Faq', 'Newsletter', 'Questionnaire',
 							'Scroll', 'Section', 'Timeline',
 							
 							'ContentBase', 'ViewImage', 'Content', 'BaseDocument', 
 							'Document', 'Event', 'Question', 'Answer',
 							'BEFile', 'Image', 'AudioVideo',
 							'Comment', 'Book', 'Author', 'ShortNews',
 							'Bibliography', 'FaqQuestion', 'BiblioItem', 'ObjectUser'
 	) ;
 	var $components	= array('Transaction', 'Permission') ;
    var $dataSource	= 'test' ;
 	
    var $data		= null ;

	////////////////////////////////////////////////////////////////////

	function testAddSingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;

		// Aggiunge i permessi
		$ret = $this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati
		$perms = $this->Permission->load($this->Area->id) ;
		pr("Verifica permessi aggiunti") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testAddMultipleObject() {	
		$this->Transaction->begin() ;
		
		// Inserisce i diversi oggetti
		$this->_insert($this->Area, $this->data['minimo']) ;
		$this->_insert($this->Section, $this->data['minimo']) ;
		$this->_insert($this->Document, $this->data['minimo']) ;

		// Aggiunge i permessi
		$ret = $this->Permission->add(array($this->Area->id, $this->Section->id, $this->Document->id), $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati e verifica
		$perms = $this->Permission->load($this->Area->id) ;
		pr("Verifica permessi aggiunti Area") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$perms = $this->Permission->load($this->Section->id) ;
		pr("Verifica permessi aggiunti Section") ;
		$this->assertEqual($this->data['addPerms1'], $perms);

		$perms = $this->Permission->load($this->Document->id) ;
		pr("Verifica permessi aggiunti Document") ;
		$this->assertEqual($this->data['addPerms1'], $perms);

		$this->Transaction->rollback() ;
	} 

	function testDefaultPermissionByObject() {	
		$conf  		= Configure::getInstance() ;
		
		$this->Transaction->begin() ;
				
		// Preleva i permessi
		$perms = $this->Permission->getDefaultByType($conf->objectTypes['area']) ; 
		pr("Permessi di default per un oggetto Area") ;
		$this->assertEqual(count($perms) > 0,true);
		
		// Inserisce l'oggetto
		$this->data['minimo']['Permissions'] = $perms ;
		$this->_insert($this->Area, $this->data['minimo']) ;

		// Aggiunge i permessi
		$ret = $this->Permission->add($this->Area->id, $perms) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
	
		// Carica i permessi
		$permsObj = $this->Permission->load($this->Area->id) ;
		pr("Verifica permessi aggiunti Area") ;
		$this->assertEqual($perms, $permsObj);

//$obj = $this->Area->findById($this->Area->id) ;
//pr($obj);		
		$this->Transaction->rollback() ;
	} 

	function testDeleteBySingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;

		// Aggiunge i permessi
		$ret = $this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);

		// cancella i permessi
		$ret = $this->Permission->remove($this->Area->id, $this->data['removePerms1']) ;
		pr("Cancella i permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati
		$perms = $this->Permission->load($this->Area->id) ;
		pr("Verifica permessi cancella") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		$this->Transaction->rollback() ;
	} 
	
	function testDeleteAllBySingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;

		// Aggiunge i permessi
		$ret = $this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);

		// cancella i permessi
		$ret = $this->Permission->removeAll($this->Area->id) ;
		pr("Cancella i permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati
		$perms = $this->Permission->load($this->Area->id) ;
		pr("Verifica permessi cancellati") ;
		$this->assertEqual(array(), $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testReplaceByRootTree() {	
		$this->Transaction->begin() ;
		
		// aggiunge/sosstituisce per una ramificazione (3, 6, 5, 8, 12)
		$ret = $this->Permission->addTree(3, $this->data['addPerms1']) ;
		pr("aggiunge/sostituisce per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati e verifica
		$perms = $this->Permission->load(3) ;
		pr("Verifica permessi creati (3)") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$perms = $this->Permission->load(5) ;
		pr("Verifica permessi creati (5)") ;
		$this->assertEqual($this->data['addPerms1'], $perms);

		$perms = $this->Permission->load(12) ;
		pr("Verifica permessi creati (12)") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testDeleteByRootTree() {	
		$this->Transaction->begin() ;
		
		// aggiunge/sosstituisce per una ramificazione (3, 6, 5, 8, 12)
		$ret = $this->Permission->addTree(3, $this->data['addPerms1']) ;
		pr("aggiunge/sostituisce per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);

		// Cancella per una ramificazione (3, 6, 5, 8, 12)
		$ret = $this->Permission->removeTree(3, $this->data['removePerms1']) ;
		pr("Cancella i permessi per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati e verifica
		$perms = $this->Permission->load(3) ;
		pr("Verifica permessi cancellati (3)") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		$perms = $this->Permission->load(5) ;
		pr("Verifica permessi cancellati (5)") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);

		$perms = $this->Permission->load(12) ;
		pr("Verifica permessi cancellati (12)") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testDeleteAllByRootTree() {	
		$this->Transaction->begin() ;
		
		// aggiunge/sosstituisce per una ramificazione (3, 6, 5, 8, 12)
		$ret = $this->Permission->addTree(3, $this->data['addPerms1']) ;
		pr("aggiunge/sostituisce per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);

		// cancella i permessi
		$ret = $this->Permission->removeAllTree(3) ;
		pr("Cancella tutti i permessi per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati e verifica
		$perms = $this->Permission->load(3) ;
		pr("Verifica permessi cancellati (3)") ;
		
		$this->assertEqual(array(), $perms);
		
		$perms = $this->Permission->load(5) ;
		pr("Verifica permessi cancellati (5)") ;
		$this->assertEqual(array(), $perms);

		$perms = $this->Permission->load(12) ;
		pr("Verifica permessi cancellati (12)") ;
		$this->assertEqual(array(), $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testPermissionsByUserid() {	
		$this->Transaction->begin() ;
		
		// aggiunge/sosstituisce per una ramificazione (3, 6, 5, 8, 12)
		$ret = $this->Permission->addTree(3, $this->data['addPerms1']) ;
		pr("aggiunge/sostituisce per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);
		
		// Verifica dei permessi
		$ret = $this->Permission->verify(3, 'torto', BEDITA_PERMS_MODIFY) ;
		pr("Verifica permessi di modifica utente 'torto' (true)") ;
		$this->assertEqual((boolean)$ret, true);
		
		$ret = $this->Permission->verify(3, 'torto', BEDITA_PERMS_CREATE) ;
		pr("Verifica permessi di creazione utente 'torto' (false)") ;
		$this->assertEqual((boolean)$ret, false);

		$ret = $this->Permission->verify(3, '', BEDITA_PERMS_READ) ;
		pr("Verifica permessi di lettura utente anonimo (true)") ;
		$this->assertEqual((boolean)$ret, true);

		$this->Transaction->rollback() ;
	} 
	
	function testPermissionsByGroup() {	
		$this->Transaction->begin() ;
		
		// aggiunge/sosstituisce per una ramificazione (3, 6, 5, 8, 12)
		$ret = $this->Permission->addTree(3, $this->data['addPerms1']) ;
		pr("aggiunge/sostituisce per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);
		
		// Verifica dei permessi
		$ret = $this->Permission->verifyGroup(3, 'guest', BEDITA_PERMS_READ) ;
		pr("Verifica permessi di lettura gruppo 'guest' (true)") ;
		$this->assertEqual((boolean)$ret, true);
		
		$ret = $this->Permission->verifyGroup(3, 'guest', BEDITA_PERMS_DELETE) ;
		pr("Verifica permessi di cancellazione gruppo 'guest' (false)") ;
		$this->assertEqual((boolean)$ret, false);

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
		echo '<h1>Creazione, gestione, cancellazione permessi Case</h1>';
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
		$PermissionData = &new PermissionData() ;
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