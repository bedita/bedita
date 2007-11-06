<?php 
/**
  * @author giangi@qwerg.com
 * 
 * Verifica il componente Permission
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class PermissionModuleTestCase extends BeditaTestCase {
	
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array('Group') ;
 	var $components	= array('Transaction', 'BePermissionModule') ;
    var $dataSource	= 'test' ;

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

	function testUpdateGroupPermissions() {	
		$this->Transaction->begin();
		
		$groupName = $this->data['updateGroupName'];
		$g = $this->Group->findByName($groupName);
		$groupId = $g['Group']['id'];
		$moduleFlags = $this->data['updateGroupModules'];
		
		$this->BePermissionModule->updateGroupPermission($groupId, $moduleFlags);
		
		pr("Verifica permessi inseriti \n");
		pr($moduleFlags);
		foreach ($moduleFlags as $k=>$v) {
			$this->assertEqual($this->BePermissionModule->verifyGroup($k, $groupName, $v), true);
		}
		
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
	public   function __construct () {
		parent::__construct('PermissionModule', dirname(__FILE__)) ;
	}		
}
?> 