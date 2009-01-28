<?php 
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class PermissionTestCase extends BeditaTestCase {
	
    var $fixtures 	= array( 'area_test' );
 	var $uses		= array('BEObject', 
 							'Area', 'Section',
 							'Document', 'Event'	) ;
 	var $components	= array('Transaction', 'Permission') ;
    var $dataSource	= 'test' ;

	////////////////////////////////////////////////////////////////////

	function testAddSingleObject() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;

		sort($this->data['addPerms1']);
		// Aggiunge i permessi
		$ret = $this->Permission->add($this->Area->id, $this->data['addPerms1']) ;
		pr("Aggiunta permessi");
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
		$perms = $this->Permission->getDefaultByType($conf->objectTypes['area']["id"]) ; 
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

		$this->_insert($this->Area, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['minimo']) ;
		
		sort($this->data['addPerms1']);
		$ret = $this->Permission->addTree($this->Area->id, $this->data['addPerms1']) ;
		$this->assertEqual($ret,true);
		
		// area permissions
		$perms = $this->Permission->load($this->Area->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Area->id) ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		// section permissions
		$perms = $this->Permission->load($this->Section->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Section->id) ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		// document permissions
		$perms = $this->Permission->load($this->Document->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Document->id) ;
		$this->assertEqual($this->data['addPerms1'], $perms);
		
		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 

	function testDeleteByRootTree() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['minimo']) ;
		
		sort($this->data['addPerms1']);
		$ret = $this->Permission->addTree($this->Area->id, $this->data['addPerms1']) ;
		$this->assertEqual($ret,true);
		
		// remove permissions
		$ret = $this->Permission->removeTree($this->Area->id, $this->data['removePerms1']) ;
		$this->assertEqual($ret,true);
		
		// area permissions
		$perms = $this->Permission->load($this->Area->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Area->id) ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		// section permissions
		$perms = $this->Permission->load($this->Section->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Section->id) ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		// document permissions
		$perms = $this->Permission->load($this->Document->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Document->id) ;
		$this->assertEqual($this->data['resultDeletePerms1'], $perms);
		
		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 

	function testDeleteAllByRootTree() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Area->id;
		$this->_insert($this->Section, $this->data['minimo']) ;
		$this->data['minimo']['parent_id'] = $this->Section->id;
		$this->_insert($this->Document, $this->data['minimo']) ;
		
		sort($this->data['addPerms1']);
		$ret = $this->Permission->addTree($this->Area->id, $this->data['addPerms1']) ;
		$this->assertEqual($ret,true);

		// remove all permissions
		$ret = $this->Permission->removeAllTree($this->Area->id);
		$this->assertEqual($ret,true);
		
		// area permissions
		$perms = $this->Permission->load($this->Area->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Area->id) ;
		$this->assertEqual(array(), $perms);
		
		// section permissions
		$perms = $this->Permission->load($this->Section->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Section->id) ;
		$this->assertEqual(array(), $perms);
		
		// document permissions
		$perms = $this->Permission->load($this->Document->id);
		sort($perms);
		pr("Verify permission on id:" . $this->Document->id) ;
		$this->assertEqual(array(), $perms);
		
		$this->_delete($this->Document) ;
		$this->_delete($this->Section) ;
		$this->_delete($this->Area) ;
		$this->Transaction->rollback() ;
	} 

	function testPermissionsByUserOrGroup() {	
		$this->Transaction->begin() ;
		
		$this->_insert($this->Area, $this->data['minimo']) ;
		
		sort($this->data['addPerms1']);
		$ret = $this->Permission->addTree($this->Area->id, $this->data['addPerms1']) ;
		$this->assertEqual($ret,true);
				
		// Verify user/group permissions
		foreach ($this->data['addPerms1'] as $permsData) {
			$who = $permsData[0];
			$type = $permsData[1];
			$p = $permsData[2];
			if($type === 'user') {
				$ret = $this->Permission->verify($this->Area->id, $who, $p) ;
				$this->assertEqual($ret, true);
			} else {
				$ret = $this->Permission->verifyGroup($this->Area->id, $who, $p) ;
				$this->assertEqual($ret, true);
			}
		}
		
		// guest perms
		$ret = $this->Permission->verify($this->Area->id, '', BEDITA_PERMS_READ) ;
		$this->assertEqual((boolean)$ret, true);
		
		$this->_delete($this->Area) ;
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
		$result = $model->delete($model->{$model->primaryKey});
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