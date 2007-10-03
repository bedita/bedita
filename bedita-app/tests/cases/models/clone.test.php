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

include_once(dirname(__FILE__) . DS . 'clone.data.php') ;
/*
class AreaTest extends Area {
    var $name 			= 'Area';
//    var $tablePrefix 	= '';
    var $useDbConfig 	= 'test_suite';  
}
*/
class CloneTestCase extends CakeTestCase {
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array(
 							'Tree', 'Permission', 'BEObject', 'ContentBase', 'Content', 
 							'BaseDocument', 'Event', 'Area', 'Section', 'Document',
 							'BEFile', 'Bibliography', 'BiblioItem', 'Book',
 							'Faq', 'FaqQuestion', 'Community', 'ObjectUser',
 							'Questionnaire', 'Question', 'Comment'
 							) ;
 	var $components = array('Transaction') ;
    var $dataSource	= 'test' ;
 	
  	/**
     * Dati utilizzati come esempio
     */
    var $data		= null ;
   
	function testInserimentoMinimo() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		$this->Event->bviorHideFields = array( 'Version', 'Index', 'Permissions', 'UserCreated', 'UserModified') ;
		$result = $this->Event->save($this->data['insert']['area']['minimo']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Event->validationErrors);
			
			return ;
		}
		
		$this->Event->bviorHideFields = array('Version', 'Index', 'Permissions', 'UserCreated', 'UserModified') ;
		$result = $this->Event->findById($this->Event->id);
		pr("Event creato:");
		pr($result);
		
		// clone l'evento
		$newEvent = clone $this->Event ;		
		
		$newEvent->bviorHideFields = array( 'Version', 'Index', 'Permissions', 'UserCreated', 'UserModified') ;
		$result = $newEvent->findById($newEvent->id);
		pr("Event clone:");
		pr($result);
		
		$this->Transaction->rollback() ;
	} 
	
	function testInserimentoTree() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		$id = $this->_insertTree(null, $this->data['insertTree']) ;
				
		// clone area
		$this->Area->id = $id ;		
		$newArea = clone $this->Area ;		
		$result = $this->Tree->cloneTree($id, $newArea->id) ;
		$this->assertEqual($result,true);		
		pr("Start Area ID: {$id}. Clone Area ID: {$newArea->id}") ;
		
		$this->Transaction->rollback() ;
	} 

	function testClonazioneLangObjs() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		$this->Document->bviorHideFields = array( 'Version', 'Index', 'Permissions', 'UserCreated', 'UserModified') ;
		$ret = $this->Document->findById(5);
		pr($ret) ;
		
		// clone l'evento
		$this->Document->id = $this->data['insert']['idDocPresente'] ;
		$newDocument = clone $this->Document ;
		
		$newDocument->bviorHideFields = array( 'Version', 'Index', 'Permissions', 'UserCreated', 'UserModified') ;
		$result = $newDocument->findById($newDocument->id);
		pr("Document clone:");
		pr($result);

		$this->Transaction->rollback() ;
	} 

	function testExceptionClonazione() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		$result = $this->BEFile->save($this->data['insert']['file']) ;
		$this->assertEqual($result,true);		
		
		$this->BEFile->bviorHideFields = array('Version', 'Index', 'Permissions', 'UserCreated', 'UserModified') ;
		$result = $this->BEFile->findById($this->BEFile->id);
		
		pr("eccezione sollevata con tentativo clonazione file") ;
		try {
		$newFile = clone $this->BEFile ;
		} catch (BEditaCloneModelException $e) {
			$this->assertEqual(true,true);
			
			$this->Transaction->rollback() ;
			return ;
		}
		$this->assertEqual(false,true);
		$this->Transaction->rollback() ;
	} 

	function testClonazioneBibliography() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		// Crea la bibliografia
		$biblio = $this->Bibliography->save($this->data['insert']['biblio']) ;
		$this->assertEqual($biblio,true);		
		pr("Bibliografia creata: {$this->Bibliography->id}");
		
		// Inserisco gli item/book
		$this->BiblioItem = new BiblioItem() ;
		$this->data['insert']['item1']['bibliography_id'] = $this->Bibliography->id ;
		$item = $this->BiblioItem->save($this->data['insert']['item1']) ;
		$this->assertEqual($item,true);		
		
		$this->Book = new Book() ;
		$book = $this->Book->save($this->data['insert']['book2']) ;
		$this->assertEqual($book,true);		
		$biblio = $this->Bibliography->appendChild($this->Book->id, $this->Bibliography->id) ;
		
		$this->BiblioItem = new BiblioItem() ;
		$this->data['insert']['item3']['bibliography_id'] = $this->Bibliography->id ;
		$item = $this->BiblioItem->save($this->data['insert']['item3']) ;
		$this->assertEqual($item,true);		
		
		// Visualizza gli items della bibliografia
		$ret = $this->Bibliography->getItems($this->Bibliography->id, $items) ;
		$this->assertEqual($ret,true);		
		pr($items) ;
		
		// Clona la bibliografia
		$newBiblio = clone $this->Bibliography ;
		$result = $newBiblio->findById($newBiblio->id);
		pr("Bibliografia clonata: {$result['id']}") ;
		
		// Visualizza gli items della bibliografia clonata
		$ret = $this->Bibliography->getItems($newBiblio->id, $itemsClone) ;
		$this->assertEqual($ret,true);		
		pr("Items bibliografia clonata") ;
		pr($itemsClone) ;

		$this->Transaction->rollback() ;
	} 

	function testClonazioneFAQ() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		// Crea la FAQ
		$ret = $this->Faq->save($this->data['insert']['FAQ']) ;
		$this->assertEqual($ret,true);
		
		$FAQID = $this->Faq->id ;
		
		// Crea le domande e le inserisce
		$ret = $this->FaqQuestion->save($this->data['insert']['domanda1']) ;
		$ret = $this->Faq->appendChild($this->FaqQuestion->id) ;

		$this->FaqQuestion = new FaqQuestion() ;
		
		$ret = $this->FaqQuestion->save($this->data['insert']['domanda2']) ;
		$ret = $this->Faq->appendChild($this->FaqQuestion->id) ;
		
		$this->Faq->getItems($queries) ;
		pr($queries) ;
		
		// Clona
		$clone = clone $this->Faq ;
//		pr($clone);
		
		$clone->getItems($queries) ;
		pr($queries) ;

		$this->Transaction->rollback() ;
	} 

	function testClonazioneCommunity() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		// Crea la community
		$ret = $this->Community->save($this->data['insert']['community']) ;
		$this->assertEqual($ret,true);
		pr("Community creata: {$this->Community->id}");
		
		// Inserisce gli utenti
		$users = $this->data['insert']['community']['user_id'] ;
		$this->User = new User() ;
		
		if(!class_exists('User')) loadModel('User') ;
		for($i=0; $i < count($users) ; $i++) {
			// Torna l'id dell'utente
			$ret = $this->User->findByUserid($users[$i]) ;
			$users[$i] = array(
				'title'		=> $users[$i],
				'user_id'	=> $ret['User']['id']
			) ;
		
			$this->ObjectUser = new ObjectUser() ;
			$ret = $this->ObjectUser->save($users[$i]) ;
			$this->assertEqual($ret,true);
			
			// Aggiunge l'user
			$this->Community->appendChild($this->ObjectUser->id) ;
		}
		
		// Preleva gli oggetti user della community
		$recs = $this->Community->getChildren(null, null,  null, false, 1, 100) ;
		pr($recs) ;
		
		// Clona la community e il suo contenuto
		$clone = clone $this->Community ;
		pr("Community clonata: {$clone->id}") ;
		
		// Oggetti figli del clone
		$recsClone = $clone->getChildren(null, null,  null, false, 1, 100) ;
		pr($recsClone) ;
		
		$this->Transaction->rollback() ;
	}		
	
	function testClonazioneQuestionnaire() {
		$conf  	= Configure::getInstance() ;
	
		$this->Transaction->begin() ;
		
		// Crea il questionario con domande e risposte
		$result = $this->Questionnaire->save($this->data['insert']['questionario']) ;
		$this->assertEqual($result,true);		
		pr("Questionario Creato: {$this->Questionnaire->id}") ;

		$result = $this->Question->save($this->data['insert']['domanda']) ;
		$this->assertEqual($result,true);		
		pr("Domanda Creata: {$this->Question->id}") ;
		
		$result = $this->Questionnaire->appendChild($this->Question->id) ;
		$this->assertEqual($result,true);		
		
		// Torna l'elenco delle domande con le risposte
		$questions = $this->Questionnaire->getQuestions() ;
		pr($questions) ;
		
		// Clona
		$clone = clone $this->Questionnaire ;
		pr("Questionnaire clonato: {$clone->id}") ;
		
		// Oggetti figli del clone
		$recsClone = $clone->getQuestions() ;
		pr($recsClone) ; 
		
		$this->Transaction->rollback() ;
	}		

	function testClonazioneComment() {
		$conf  	= Configure::getInstance() ;

		$this->Transaction->begin() ;

		// Inserisce un documento minimo
		$this->Document->save($this->data['insert']['documento']) ;
		pr("Documento creato: {$this->Document->id}");
		
		// Inserice 2 nuovi commenti
		$data = $this->data['insert']['commento'] ;
		$data['object_id'] = $this->Document->id ;
		$this->Comment = new Comment() ;
		$ret = $this->Comment->save($data) ;
		$this->assertEqual($ret, true);
		$id1 = $this->Comment->id ;
		pr("Nuovo commento: {$this->Comment->id}");
		
		// Clona il commento
		$clone = clone $this->Comment ;
		
		// Stampa il documento con i commenti
		// Preleva l'oggetto commentato
		$this->Document->bviorHideFields = array('Permission', 'Version', 'CustomProperties', 'Index', 'ObjectType', ) ;
		$obj = $this->Document->findById($this->Document->id) ;
		pr("Oggetto commentato");
		pr($obj) ;
		
		$this->Transaction->rollback() ;
	}		

	function testClonazioneArea() {
		$conf  	= Configure::getInstance() ;

//		$this->Transaction->begin() ;

		$this->Area->id = 2 ;
		
		// Preleva l'area e l'albero da clonare
		$children = $this->Tree->getAll($this->Area->id) ;
		pr($children);
		
		// Clona il commento
		$clone = clone $this->Area ;
		
		// Preleva l'area e l'albero da clonata
		$this->Area->bviorHideFields = array('Permission', 'Version', 'CustomProperties', 'Index', 'ObjectType', ) ;
		$children = $this->Tree->getAll($clone->id) ;
		pr($children);
		
//		$this->Transaction->rollback() ;
	}		
		
	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	private function _insertTree($idParent, &$data) {
		$id = false ;
		foreach ($data as $key => $item) {
			foreach ($item as $key => $value) {
				$obj = new $key() ;
				
				$obj->save(array('title' => $value['title'], 'parent_id' => $idParent)) ;
				$id = $obj->id ;
				unset($obj) ;
				
				if(count($value['children'])) $this->_insertTree($id, $value['children']);
			}
		}
		return $id ;
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