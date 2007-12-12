<?php 
/**
 * 
 * @author giangi@qwerg.com
 * 
 * Verifica il componente Permission
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class PermissionTestCase extends BeditaTestCase {
	
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array(
 							'BEObject', 'Collection', 
 							'Area', 'Community', 'Faq', 'Newsletter', 'Questionnaire',
 							'Scroll', 'Section', 'Timeline',
 							
 							'ContentBase', 'ViewImage', 'Content', 'BaseDocument', 
 							'Document', 'Event', 'Question', 'Answer',
 							'BEFile', 'Image', 'Audio', 'Video',
 							'Comment', 'Book', 'Author', 'ShortNews',
 							'Bibliography', 'FaqQuestion', 'BiblioItem', 'ObjectUser'
 	) ;
 	var $components	= array('Transaction', 'Permission') ;
    var $dataSource	= 'test' ;

	////////////////////////////////////////////////////////////////////

	function testAddSingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;

		sort($this->data['addPerms1']);
		// Aggiunge i permessi
		$ret = $this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati
		$perms = $this->Permission->load($this->Area->id) ;
		pr("Verifica permessi aggiunti") ;
		sort($perms);
		$this->assertEqual($this->data['addPerms1'], $perms);

		$this->_delete($this->Area) ;
		
		$this->Transaction->rollback() ;
	} 


	function testAddMultipleObject() {	
		$this->Transaction->begin() ;
		
		// Inserisce i diversi oggetti
		$this->_insert($this->Area, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['minimo']) ;

		sort($this->data['addPerms1']);
		// Aggiunge i permessi
		$ret = $this->Permission->add(array($this->Area->id, $this->Section->id, $this->Document->id), $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati e verifica
		$perms = $this->Permission->load($this->Area->id) ;
		sort($perms);
		pr("Verifica permessi aggiunti Area") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$perms = $this->Permission->load($this->Section->id) ;
		sort($perms);
		pr("Verifica permessi aggiunti Section") ;
		$this->assertEqual($this->data['addPerms1'], $perms);

		$perms = $this->Permission->load($this->Document->id) ;
		sort($perms);
		pr("Verifica permessi aggiunti Document") ;
		$this->assertEqual($this->data['addPerms1'], $perms);

		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 


	function testDefaultPermissionByObject() {	
		$conf  		= Configure::getInstance() ;
		
		$this->Transaction->begin() ;
				
		// Preleva i permessi
		$perms = $this->Permission->getDefaultByType($conf->objectTypes['area']) ; 
		sort($perms);
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
		sort($permsObj);
		pr("Verifica permessi aggiunti Area") ;
		$this->assertEqual($perms, $permsObj);

		$this->_delete($this->Area) ;
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
		sort($perms);
		
		pr("Verifica permessi cancella") ;
		$this->assertEqual(sort($this->data['resultDeletePerms1']), $perms);
		$this->_delete($this->Area) ;
		
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
		
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 

	function testReplaceByRootTree() {	
		$this->Transaction->begin() ;
		
		sort($this->data['addPerms1']);
		// aggiunge/sosstituisce per una ramificazione (3, 6, 5, 8, 12)
		$ret = $this->Permission->addTree(3, $this->data['addPerms1']) ;
		pr("aggiunge/sostituisce per una ramificazione (3, 6, 5, 8, 12)") ;
		$this->assertEqual($ret,true);
		
		// Carica i permessi creati e verifica
		$perms = $this->Permission->load(3) ;
		sort($perms);
		pr("Verifica permessi creati (3)") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$perms = $this->Permission->load(5) ;
		sort($perms);
		pr("Verifica permessi creati (5)") ;
		$this->assertEqual($this->data['addPerms1'], $perms);

		$perms = $this->Permission->load(12) ;
		sort($perms);
		pr("Verifica permessi creati (12)") ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testDeleteByRootTree() {	
		$this->Transaction->begin() ;
		
		sort($this->data['addPerms1']);
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
		sort($perms);
		pr("Verifica permessi cancellati (3)") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		$perms = $this->Permission->load(5) ;
		sort($perms);
		pr("Verifica permessi cancellati (5)") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);

		$perms = $this->Permission->load(12) ;
		sort($perms);
		pr("Verifica permessi cancellati (12)") ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testDeleteAllByRootTree() {	
		$this->Transaction->begin() ;
		
		sort($this->data['addPerms1']);
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
		sort($perms);
		pr("Verifica permessi cancellati (3)") ;
		
		$this->assertEqual(array(), $perms);
		
		$perms = $this->Permission->load(5) ;
		sort($perms);
		pr("Verifica permessi cancellati (5)") ;
		$this->assertEqual(array(), $perms);

		$perms = $this->Permission->load(12) ;
		sort($perms);
		pr("Verifica permessi cancellati (12)") ;
		$this->assertEqual(array(), $perms);
		
		$this->Transaction->rollback() ;
	} 

	function testPermissionsByUserid() {	
		$this->Transaction->begin() ;
		
		sort($this->data['addPerms1']);
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
		
		sort($this->data['addPerms1']);
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
	private function _insert($model, $data) {
		// Crea
		$result = $model->save($data) ;
		$this->assertEqual($result,true);		
		
		// Visualizza
		$obj = $model->findById($model->id) ;
		pr("Oggetto Creato: {$model->id}") ;
		
	} 
	
	private function _delete($model) {
		$id = $model->id;
		$result = $model->Delete($model->{$model->primaryKey});
		$this->assertEqual($result,true);		
		pr("Oggetto cancellato: $id");
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	public   function __construct () {
		parent::__construct('Permission', dirname(__FILE__)) ;
	}
}
?> 