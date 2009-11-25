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
		pr("Added permits for module") ;
		$this->assertEqual($ret,true);
		
		// load new perms and calc expected
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
		pr("Added permits for module") ;
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
		
		// Add permits
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Added permits") ;
		$this->assertEqual($ret,true);

		$newPerms = $this->BePermissionModule->load('areas') ;
		sort($newPerms);
		$ret = $this->BePermissionModule->remove('areas', $this->data['removePerms1']) ;
		pr("Removed permits") ;
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
		
		// Load created permits
		$perms = $this->BePermissionModule->load('areas') ;
		sort($perms);
		pr("Verify removed permits") ;
		$this->assertEqual($expectedPerms, $perms);

		$this->Transaction->rollback() ;
	} 
	
	function testDeleteAllBySingleModule() {	
		$this->Transaction->begin() ;
		
		// Add permits
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Added permits") ;
		$this->assertEqual($ret,true);

		// Remove permits
		$ret = $this->BePermissionModule->removeAll('areas') ;
		pr("Removed permits") ;
		$this->assertEqual($ret,true);
		
		// Load created permits
		$perms = $this->BePermissionModule->load('areas') ;
		pr("Verify removed permits") ;
		$this->assertEqual(array(), $perms);

		$this->Transaction->rollback() ;
	} 

	
	function testPermissionsByUserid() {	
		$this->Transaction->begin() ;
		
		// create user
		pr("Create user") ;
		$this->assertTrue($this->BeAuth->createUser($this->data['user.test']));
		
		// Add permits
		$ret = $this->BePermissionModule->add('areas', $this->data['add.perms.user']) ;
		pr("Added permits - $ret") ;
		$this->assertEqual($ret,true);
		
		// Verify permits
		$userid = $this->data['user.test']['User']['userid'];
		$ret = $this->BePermissionModule->verify('areas', $userid, BEDITA_PERMS_READ) ;
		pr("Verify write permits - $ret");
		$this->assertEqual($ret, true);
		$ret = $this->BePermissionModule->verify('areas', $userid, BEDITA_PERMS_MODIFY) ;
		$this->assertEqual($ret, false);

		// remove user
		pr("Remove user") ;
		$this->assertTrue($this->BeAuth->removeUser($userid));

		// remove perms
		$ret = $this->BePermissionModule->remove('areas', $this->data['remove.perms.user']) ;
		pr("Removed permits") ;
		$this->assertEqual($ret, true);
		// @todo not working...
//		$ret = $this->BePermissionModule->verify('areas', '', BEDITA_PERMS_READ) ;
//		pr("Verifica permessi di lettura utente anonimo (true) - $ret") ;
//		$this->assertEqual($ret, true);

		$this->Transaction->rollback() ;
	} 
	
	function testPermissionsByGroup() {	
		$this->Transaction->begin() ;
		
		// Add permits
		$ret = $this->BePermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Added permits - $ret") ;
		$this->assertEqual($ret,true);
		
		// Verify permits
		$ret = $this->BePermissionModule->verifyGroup('areas', 'guest', BEDITA_PERMS_READ) ;
		pr("Verify read permits for group 'guest' (true) - $ret") ;
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
		
		pr("Verify inserted permits \n");
		pr($moduleFlags);
		foreach ($moduleFlags as $k=>$v) {
			$this->assertEqual($this->BePermissionModule->verifyGroup($k, $groupName, $v), true);
		}
		
		$this->Transaction->rollback() ;
	} 
		
	function testGetListModuleReadableByUserid() {	
		$this->Transaction->begin() ;
		
		// Verify permits
		$ret = $this->BePermissionModule->getListModules('bedita') ;
		pr("Permits for user bedita");	
		pr($ret);	
		
		// Add permits
//		$ret = $this->BePermissionModule->add('areas', $this->data['add.perms.guest']) ;
//		pr("Added permits") ;
//		$this->assertEqual($ret, true);
//		
//		// Verify permits
//		$ret = $this->BePermissionModule->getListModules('bedita') ;
//		pr("Permits for user bedita");	
//		pr($ret);	
//
//		$ret = $this->BePermissionModule->remove('areas', $this->data['remove.perms.guest']) ;
//		pr("Removed permits") ;
//		$this->assertEqual($ret,true);
//		
//		// Verify permits
//		$ret = $this->BePermissionModule->getListModules('bedita') ;
//		pr("Permits for user bedita");	
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