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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class PermissionModuleTestCase extends BeditaTestCase {
	
  	var $uses		= array('Group') ;
 	var $components	= array('Transaction', 'BePermissionModule','BeAuth') ;
    var $dataSource	= 'test' ;

    ////////////////////////////////////////////////////////////////////

	function testAddSingleModule() {	
		$this->Transaction->begin() ;
		
		$prevPerms = $this->BePermissionModule->load('areas') ;
		// add perms
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi modulo") ;
		$this->assertEqual($ret,true);
		
		// laod new perms and calc expected
		$newPerms = $this->BePermissionModule->load('areas') ;
		sort($newPerms);
		
		$expectedPerms = $this->mergePerms($prevPerms, $this->data['addPerms1']);		
		pr("Verify module perms") ;
		$this->assertEqual($expectedPerms, $newPerms);

		// restore previous perms
		$ret = $this->BePermissionModule->removeAll('areas') ;
		$this->assertEqual($ret, true);
		$ret = $this->BePermissionModule->add('areas', $prevPerms) ;
		$this->assertEqual($ret, true);
		
		$this->Transaction->rollback() ;
	} 

	private function mergePerms(array &$oldPerms, array &$addedPerms) {
		$expectedPerms = array();
		foreach ($oldPerms as $p) {
			$found = false;
			foreach ($this->data['addPerms1'] as $added) {
				if($found === false && $added[0] === $p[0] && $added[1] === $p[1]) {
					$expectedPerms[] = $added;
					$found = true;
				}
			}
			if($found === false)
				$expectedPerms[] = $p;
		}
		foreach ($this->data['addPerms1'] as $added) {
			$found = false;
			foreach ($oldPerms as $p) {
				if($found === false && $added[0] === $p[0] && $added[1] === $p[1]) {
					$found = true;
				}
			}
			if($found === false)
				$expectedPerms[] = $added;
		}
		
		sort($expectedPerms);
		return $expectedPerms;
	}
	
	function testAddMultipleModule() {	
		$this->Transaction->begin() ;
		
		$prevPermsArea = $this->BePermissionModule->load('areas') ;
		$prevPermsAdmin = $this->BePermissionModule->load('admin') ;
		
		$ret = $this->BePermissionModule->add(array('admin', 'areas'), $this->data['addPerms1']) ;
		pr("Aggiunta permessi moduli") ;
		$this->assertEqual($ret,true);
		
		$perms = $this->BePermissionModule->load('admin') ;
		sort($perms);
		$expectedPerms = $this->mergePerms($prevPermsAdmin, $this->data['addPerms1']);		
		pr("Verify 'admin' module perms") ;
		$this->assertEqual($expectedPerms, $perms);
		
		$perms = $this->BePermissionModule->load('areas') ;
		sort($perms);
		$expectedPerms = $this->mergePerms($prevPermsArea, $this->data['addPerms1']);		
		pr("Verify 'areas' module perms") ;
		$this->assertEqual($expectedPerms, $perms);
				
		// restore previous perms
		$ret = $this->BePermissionModule->removeAll('areas') ;
		$this->assertEqual($ret, true);
		$ret = $this->BePermissionModule->add('areas', $prevPermsArea) ;
		$this->assertEqual($ret, true);
		$ret = $this->BePermissionModule->removeAll('admin') ;
		$this->assertEqual($ret, true);
		$ret = $this->BePermissionModule->add('admin', $prevPermsAdmin) ;
		$this->assertEqual($ret, true);
		
		$this->Transaction->rollback() ;
	} 

	function testDeleteBySingleModule() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi") ;
		$this->assertEqual($ret,true);

		$newPerms = $this->BePermissionModule->load('areas') ;
		sort($newPerms);
		$ret = $this->BePermissionModule->remove('areas', $this->data['removePerms1']) ;
		pr("Cancella i permessi") ;
		$this->assertEqual($ret,true);

		$expectedPerms = $newPerms;
		foreach ($this->data['removePerms1'] as $r) {
			$found = false;
			for ($i = 0; $i < count($expectedPerms) && !$found; $i++) {
				if($r[0] === $expectedPerms[$i][0] && $r[1] === $expectedPerms[$i][1]) {
					$found = true;
					unset($expectedPerms[$i]);
				}
			}
		}
		
		// Carica i permessi creati
		$perms = $this->BePermissionModule->load('areas') ;
		sort($perms);
		pr("Verifica permessi cancellati") ;
		$this->assertEqual($expectedPerms, $perms);

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
		
		// create user
		pr("Create user") ;
		$this->assertTrue($this->BeAuth->createUser($this->data['user.test']));
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['add.perms.user']) ;
		pr("Aggiunta permessi - $ret") ;
		$this->assertEqual($ret,true);
		
		// Verifica dei permessi
		$userid = $this->data['user.test']['User']['userid'];
		$ret = $this->BePermissionModule->verify('areas', $userid, BEDITA_PERMS_READ) ;
		pr("Verifica permessi di modifica - $ret");
		$this->assertEqual($ret, true);
		$ret = $this->BePermissionModule->verify('areas', $userid, BEDITA_PERMS_MODIFY) ;
		$this->assertEqual($ret, false);

		// remove user
		pr("Remove user") ;
		$this->assertTrue($this->BeAuth->removeUser($userid));

		// remove perms
		$ret = $this->BePermissionModule->remove('areas', $this->data['remove.perms.user']) ;
		pr("Cancella i permessi") ;
		$this->assertEqual($ret, true);
		// @todo non va...
//		$ret = $this->BePermissionModule->verify('areas', '', BEDITA_PERMS_READ) ;
//		pr("Verifica permessi di lettura utente anonimo (true) - $ret") ;
//		$this->assertEqual($ret, true);

		$this->Transaction->rollback() ;
	} 
	
	function testPermissionsByGroup() {	
		$this->Transaction->begin() ;
		
		// Aggiunge i permessi
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Aggiunta permessi - $ret") ;
		$this->assertEqual($ret,true);
		
		// Verifica dei permessi
		$ret = $this->BePermissionModule->verifyGroup('areas', 'guest', BEDITA_PERMS_READ) ;
		pr("Verifica permessi di lettura gruppo 'guest' (true) - $ret") ;
		$this->assertEqual((boolean)$ret, true);
		
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
		
		// Verifica dei permessi
		$ret = $this->BePermissionModule->getListModules('bedita') ;
		pr("Permessi utente bedita");	
		pr($ret);	
		
		// Aggiunge i permessi
//		$ret = $this->BePermissionModule->add('areas', $this->data['add.perms.guest']) ;
//		pr("Aggiunta permessi") ;
//		$this->assertEqual($ret, true);
//		
//		// Verifica dei permessi
//		$ret = $this->BePermissionModule->getListModules('bedita') ;
//		pr("Permessi utente bedita");	
//		pr($ret);	
//
//		$ret = $this->BePermissionModule->remove('areas', $this->data['remove.perms.guest']) ;
//		pr("Cancella i permessi") ;
//		$this->assertEqual($ret,true);
//		
//		// Verifica dei permessi
//		$ret = $this->BePermissionModule->getListModules('bedita') ;
//		pr("Permessi utente bedita");	
//		pr($ret);	
		
		$this->Transaction->rollback() ;
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	public   function __construct () {
		parent::__construct('PermissionModule', dirname(__FILE__)) ;
	}		
}
?> 