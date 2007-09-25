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

include_once(dirname(__FILE__) . DS . 'saveDelete.data.php') ;


class SaveDeleteTestCase extends CakeTestCase {
	
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
    var $dataSource	= 'test' ;
 	
    var $data		= null ;

	////////////////////////////////////////////////////////////////////
	// Contenitori
/*
	function testInsertArea() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->_insertDelete($this->Area, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Area) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertCommunity() {

		$numRecordBegin = $this->_getNumRecordsTable($this->Community) ; 
		
		$this->_insertDelete($this->Community, $this->data['community']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Community) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);

	} 
	
	function testInsertFaq() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Faq) ; 
		
		$this->_insertDelete($this->Faq, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Faq) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
	
	function testInsertNewsletter() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Newsletter) ; 
		
		$this->_insertDelete($this->Newsletter, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Newsletter) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
	
	function testInsertQuestionnaire() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Questionnaire) ; 
		
		$this->_insertDelete($this->Questionnaire, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Questionnaire) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertScroll() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Scroll) ; 
		
		$this->_insertDelete($this->Scroll, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Scroll) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
		
	function testInsertSection() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Section) ; 
		
		$this->data['minimo']['parent_id'] = 2 ;
		$this->_insertDelete($this->Section, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Section) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
	
	function testInsertTimeline() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Timeline) ; 
		
		$this->_insertDelete($this->Timeline, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Timeline) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	////////////////////////////////////////////////////////////////////
	// Contenuti
	function testInsertQuestion() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Question) ; 
		
		$this->_insertDelete($this->Question, $this->data['domanda']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Question) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertQuestionAnswer() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Document) ; 
		
		// Crea la domanda
		$result = $this->Question->save($this->data['domanda']) ;
		$this->assertEqual($result,true);		
		pr("Domanda Creata: {$this->Question->id}") ;
		
		// Crea le risposte
		$this->data['risposta1']['question_id']	= $this->Question->id ;
		
		// Prima risposta
		$result = $this->Answer->save($this->data['risposta1']) ;
		$this->assertEqual($result,true);		
		pr("Prima risposta: {$this->Answer->id}");
		$this->Answer->id = false ;
		
		// Seconda risposta
		$this->data['risposta2']['question_id']	= $this->Question->id ;
		
		$this->Answer = new Answer() ;
		$result = $this->Answer->save($this->data['risposta2']) ;
		$this->assertEqual($result,true);		
		pr("Seconda risposta: {$this->Answer->id}");
		
		// Cancella la domanda
		$result = $this->Question->Delete($this->Question->id);
		$this->assertEqual($result,true);		
		pr("Domanda cancellata");

		// Verifica il numero di record del DB
		pr("Verifica il numero di record del DB");
		$numRecordEnd = $this->_getNumRecordsTable($this->Document) ; 
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
	
	function testInsertStreamWithoutDefaultValue() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Document) ; 
		
		// Errore,  crea lo strem senza valori di default
		$result = $this->BEFile->save($this->data['emptyStream']) ;
		pr('Oggetto Non creato') ;
		pr($this->BEFile->validationErrors);		
		
		$this->assertEqual($result,false);		

		$numRecordEnd = $this->_getNumRecordsTable($this->BEFile) ; 
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
	
	function testInsertBEFile() {
		$numRecordBegin = $this->_getNumRecordsTable($this->BEFile) ; 
		
		$this->_insertDelete($this->BEFile, $this->data['file']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->BEFile) ; 
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
	
	function testInsertImage() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Image) ; 
		
		$this->_insertDelete($this->Image, $this->data['file']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Image) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertAudioVideo() {
		$numRecordBegin = $this->_getNumRecordsTable($this->AudioVideo) ; 
		
		$this->_insertDelete($this->AudioVideo, $this->data['file']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->AudioVideo) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertDocumentAndComments() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Comment) ; 
		
		// Inserisce un documento minimo
		$this->Document->save($this->data['minimo']) ;
		pr("Documento creato: {$this->Document->id}");
		
		// Inserice 2 nuovi commenti
		$data = $this->data['minimo'] ;
		$data['object_id'] = $this->Document->id ;
		$this->Comment = new Comment() ;
		$ret = $this->Comment->save($data) ;
		$this->assertEqual($ret, true);
		$id1 = $this->Comment->id ;
		pr("Nuovo commento: {$this->Comment->id}");
		
		$this->Comment = new Comment() ;
		$ret = $this->Comment->save($data) ;
		$this->assertEqual($ret, true);
		$id2 = $this->Comment->id ;
		pr("Nuovo commento: {$this->Comment->id}");

		// Preleva l'oggetto commentato
//		$this->Document->bviorHideFields = array('Permission', 'Version', 'CustomProperties', 'Index', 'ObjectType', ) ;
//		$obj = $this->Document->findById($this->Document->id) ;
//		pr("Oggetto commentato");
//		pr($obj) ;
		
		// Cancella il secondo commento
		$ret = $this->Comment->delete($id2) ;
		$this->assertEqual($ret, true);
		pr("Cancella il secondo commento: {$id2}");
		
		// Cancella il documento commentato e i commenti
		$ret = $this->Document->delete($this->Document->id) ;
		$this->assertEqual($ret, true);
		pr("Cancella il documento");

		$numRecordEnd = $this->_getNumRecordsTable($this->Comment) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertDocument() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Document) ; 
		
		$this->_insertDelete($this->Document, $this->data['docWithLinks']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Document) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertEvent() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Event) ; 
		
		$this->_insertDelete($this->Event, $this->data['eventWithDate']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Event) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertAuthor() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Author) ; 
		
		$this->_insertDelete($this->Author, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Author) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertBook() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Book) ; 
			
		$this->_insertDelete($this->Book, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Book) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertShortNews() {
		$numRecordBegin = $this->_getNumRecordsTable($this->ShortNews) ; 
		
		$this->_insertDelete($this->ShortNews, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->ShortNews) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertFaqQuestion() {
		$numRecordBegin = $this->_getNumRecordsTable($this->FaqQuestion) ; 
		
		$this->_insertDelete($this->FaqQuestion, $this->data['minimo']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->FaqQuestion) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	
	function testInsertBibliography() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Bibliography) ; 
		
		// Crea la bibliografia
		$biblio = $this->Bibliography->save($this->data['biblio']) ;
		$this->assertEqual($biblio,true);		
		pr("Bibliografia creata: {$this->Bibliography->id}");
		
		// Inserisco gli item/book
		$this->BiblioItem = new BiblioItem() ;
		$this->data['item1']['bibliography_id'] = $this->Bibliography->id ;
		$item = $this->BiblioItem->save($this->data['item1']) ;
		$this->assertEqual($item,true);		
		pr("Item Biblio creato: {$this->BiblioItem->id}");
		
		$this->Book = new Book() ;
		$book = $this->Book->save($this->data['book2']) ;
		$this->assertEqual($book,true);		
		$biblio = $this->Bibliography->appendChild($this->Book->id, $this->Bibliography->id) ;
		pr("Libro creato creato: {$this->Book->id}");
		
		$this->BiblioItem = new BiblioItem() ;
		$this->data['item3']['bibliography_id'] = $this->Bibliography->id ;
		$item = $this->BiblioItem->save($this->data['item3']) ;
		$this->assertEqual($item,true);		
		pr("Item Biblio creato: {$this->Bibliography->id}");
		
		// Visualizza gli items della bibliografia
		$ret = $this->Bibliography->getItems($this->Bibliography->id, $items) ;
		$this->assertEqual($ret,true);		
		pr("Items Bibliografia: {$this->Bibliography->id}") ;
//		pr($items) ;
		
		// Cancella la bilbigrafia e i suoi contenuti (eccetto i Book)
		$result = $this->Bibliography->Delete($this->Bibliography->id);
		$this->assertEqual($result,true);		
		pr("Bibliografia cancellata");
		
		// Cancella il libro aggiunto
		$result = $this->Book->Delete($this->Book->id);
		$this->assertEqual($result,true);		
		pr("Libro cancellato");
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Bibliography) ; 
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 
	

	function testInsertObjectUser() {
		$numRecordBegin = $this->_getNumRecordsTable($this->ObjectUser) ; 
		
		// Torna l'id dell'utente 'bedita'
		if(!class_exists('User')) loadModel('User') ;
		$this->User = new User() ;
		
		$ret = $this->User->findByUserid('bedita') ;
		$this->data['user']['user_id'] = $ret['User']['id'] ;
		
		$this->_insertDelete($this->ObjectUser, $this->data['user']) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->ObjectUser) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertFaqAndFaqQuestion() {
		$numRecordBegin = $this->_getNumRecordsTable($this->FaqQuestion) ; 
		
		// Crea la FAQ
		$ret = $this->Faq->save($this->data['minimo']) ;
		$this->assertEqual($ret,true);
		
		$FAQID = $this->Faq->id ;
		
		// Crea la domanda
		$ret = $this->FaqQuestion->save($this->data['domanda']) ;
		$this->assertEqual($ret,true);
		
		// Inserisce la domanda
		$ret = $this->Faq->appendChild($this->FaqQuestion->id) ;
		$this->assertEqual($ret,true);
		
		// Preleva e stampa il risultato
		$this->Faq->bviorHideFields = array( 'Version', 'Index', 'Permissions', 'UserCreated', 'UserModified') ;
		$FAQ 		= $this->Faq->findById($this->Faq->id) ;
//		$queries 	= $this->Tree->getAll($this->Faq->id) ;
		$this->Faq->getItems($queries) ;

		pr($FAQ) ;
		pr($queries) ;
		
		$this->Faq->delete($this->Faq->id) ;
			
		$numRecordEnd = $this->_getNumRecordsTable($this->FaqQuestion) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	} 

	function testInsertQuestionnaireAndQuestion() {
		$numRecordBegin = $this->_getNumRecordsTable($this->Questionnaire) ; 
		
		// Crea il questionario con domande e risposte
		$result = $this->Questionnaire->save($this->data['questionario']) ;
		$this->assertEqual($result,true);		
		pr("Questionario Creato: {$this->Questionnaire->id}") ;

		$result = $this->Question->save($this->data['domanda']) ;
		$this->assertEqual($result,true);		
		pr("Domanda Creata: {$this->Question->id}") ;
		
		$result = $this->Questionnaire->appendChild($this->Question->id) ;
		$this->assertEqual($result,true);		
		
		$this->data['risposta1']['question_id']	= $this->Question->id ;
		
		$result = $this->Answer->save($this->data['risposta1']) ;
		$this->assertEqual($result,true);		
		pr("Prima risposta: {$this->Answer->id}");
		$this->Answer->id = false ;
		
		$this->Answer = new Answer ;
		$this->data['risposta2']['question_id']	= $this->Question->id ;
		$result = $this->Answer->save($this->data['risposta2']) ;
		$this->assertEqual($result,true);		
		pr("Seconda risposta: {$this->Answer->id}");
		
		$this->Questionnaire->delete($this->Questionnaire->id) ;
		
		$numRecordEnd = $this->_getNumRecordsTable($this->Questionnaire) ; 
		
		$this->assertEqual($numRecordBegin,$numRecordEnd);
	}	
*/	
	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	private function _insertDelete(&$model, &$data) {
		$conf  		= Configure::getInstance() ;
		
		// Crea
		$result = $model->save($data) ;
		$this->assertEqual($result,true);		
		
		// Visualizza
		$obj = $model->findById($model->id) ;
		pr("Oggetto Creato: {$model->id}") ;
//		pr($obj) ;
		
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
		echo '<h1>Crea e salva tutti i tipi di oggetti per verificare il settaggio del tipo correttamente Case</h1>';
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
					echo "Missing Model: $modelClass" ;
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