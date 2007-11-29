<?php 
/**
 * Areas, sections test cases...
 * 
 * @author giangi@qwerg.com ste@channelweb.it
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class AreaTestCase extends BeditaTestCase {

    var $fixtures 	= array( 'area_test' );
 	var $uses		= array('BEObject', 'Collection', 'Area', 'Tree', 'Section') ;
    var $dataSource	= 'test' ;	
 	var $components	= array('Transaction', 'Permission') ;


 	function testInserimentoMinimo() {
		$conf  		= Configure::getInstance() ;
		
		$this->requiredData(array("insert"));
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
		
		// Cancella l'area creata
		$result = $this->Area->Delete($this->Area->{$this->Area->primaryKey});
		$this->assertEqual($result,true);		
		pr("Area cancellata");
		
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
		// Cancella l'area creata
		$result = $this->Area->Delete($this->Area->{$this->Area->primaryKey});
		$this->assertEqual($result,true);		
		pr("Area cancellata");
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
		
		// Cancella l'area creata
		$result = $this->Area->Delete($this->Area->{$this->Area->primaryKey});
		$this->assertEqual($result,true);		
		pr("Area cancellata");
		
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
		
		// Cancella l'area creata
		$result = $this->Area->Delete($this->Area->{$this->Area->primaryKey});
		$this->assertEqual($result,true);		
		pr("Area cancellata");
		
	} 

	function testInserimentoInTreeCancellazione() {
		$conf  		= Configure::getInstance() ;
		
		$this->Transaction->begin() ;

		// Inserisce
		$result = $this->Area->save($this->data['insert']['area']['minimo']) ;
		$this->assertEqual($result,true);		

		$this->data['insert']['sezione']['minimo1']['parent_id'] = $this->Area->id;
		$result = $this->Section->save($this->data['insert']['sezione']['minimo1']) ;
		$this->assertEqual($result,true);

		$id1 = $this->Section->id ;
		$section = $this->Section->findById($id1);
		$this->assertEqual($section['id'] ,$id1);
		
		$this->Section = new Section() ;
		$this->data['insert']['sezione']['minimo2']['parent_id'] = $this->Area->id;
		$result = $this->Section->save($this->data['insert']['sezione']['minimo2']) ;
		$this->assertEqual($result,true);
		$id2 = $this->Section->id ;

		$this->Section = new Section() ;
		$this->data['insert']['sezione']['minimo3']['parent_id'] = $this->Area->id;
		$result = $this->Section->save($this->data['insert']['sezione']['minimo3']) ;
		$this->assertEqual($result,true);
		$id3 = $this->Section->id ;

		// Preleva l'abero inserito
		$tree = $this->Tree->getAll($this->Area->id) ;
		
		// Cancella l'area creata
		$result = $this->Area->Delete($this->Area->{$this->Area->primaryKey});
		$this->assertEqual($result,true);		
		pr("Area cancellata");

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

	
	protected function cleanUp() {
		$this->Transaction->rollback() ;
	}
	
	public   function __construct () {
		parent::__construct('Area', dirname(__FILE__)) ;
	}	
}

?> 