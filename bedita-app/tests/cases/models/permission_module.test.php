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
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (mer, 25 nov 2009) $
 * 
 * $Id: permission_module.test.php 2487 2009-11-25 16:56:37Z ste $
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class PermissionModuleTestCase extends BeditaTestCase {
	
  	var $uses		= array('Group', 'PermissionModule') ;
 	var $components	= array('Transaction','BeAuth') ;
    var $dataSource	= 'test' ;

    ////////////////////////////////////////////////////////////////////

	function testAddSingleModule() {	
		// add perms
		$ret = $this->PermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Added permits for module") ;
		$this->assertEqual($ret,true);

		$module_id = $this->PermissionModule->Module->field("id", array("name" => "areas"));
		foreach ($this->data["addPerms1"] as $p) {
			$ugid = $this->PermissionModule->Group->field("id", array("name" => $p["name"]));
			$dbPerm = $this->PermissionModule->find("first", array(
				"conditions" => array(
					"ugid" => $ugid,
					"switch" => "group",
					"module_id" => $module_id
				)
			));
			$this->assertEqual($p["flag"], $dbPerm["PermissionModule"]["flag"]);
		}
	} 
	
	function testAddMultipleModule() {
		$modules = array('admin', 'documents');
		$ret = $this->PermissionModule->add($modules, $this->data['addPerms1']) ;
		pr("Added permits for module") ;
		$this->assertEqual($ret,true);
		$gName = array($this->data['addPerms1'][0]['name'], $this->data['addPerms1'][1]['name']);
		$flags = array(
			$this->data['addPerms1'][0]['name'] => $this->data['addPerms1'][0]['flag'],
			$this->data['addPerms1'][1]['name'] => $this->data['addPerms1'][1]['flag']
		);
		$ugid = $this->PermissionModule->Group->find("list", array(
			"fields" => "id",
			"conditions" => array("name" => $gName)
		));
		$res = $this->PermissionModule->find("all", array(
			"conditions" => array(
				"switch" => "group",
				"Module.name" => $modules,
				"ugid" => $ugid
			),
			"contain" => array("Module", "Group")
		));

		foreach ($res as $r) {
			$this->assertEqual($r["PermissionModule"]["flag"], $flags[$r["Group"]["name"]]);
		}
	} 

	function testDeletePermit() {
		$ret = $this->PermissionModule->add('documents', $this->data['addPerms1']) ;
		pr("Added permits for module documents") ;
		$this->assertEqual($ret,true);
		$ret = $this->PermissionModule->remove('documents', $this->data['addPerms1'][1]["name"], "group");

		if ($this->assertEqual($ret,true)) {
			pr("Permission on module documents removed for group " . $this->data['addPerms1'][1]["name"]);
		}
		$module_id = $this->PermissionModule->Module->field("id", array("name" => "documents"));
		$ugid = $this->PermissionModule->Group->field("id", array("name" => $this->data['addPerms1'][1]["name"]));
		$count = $this->PermissionModule->find("count", array(
			"conditions" => array(
				"module_id" => "documents",
				"ugid" => $ugid,
				"switch" => "group"
			)
		));

		$this->assertIdentical($count, 0);

	} 
	
	function testDeleteAllModulePermission() {
		$ret = $this->PermissionModule->add('translations', $this->data['addPerms1']) ;
		pr("Added permits for module translations") ;
		$this->assertEqual($ret,true);
		$res = $this->PermissionModule->removeAll("translations");
		$this->assertIdentical($res, true);
		$module_id = $this->PermissionModule->Module->field("id", array("name" => "translations"));
		$count = $this->PermissionModule->find("count", array(
			"conditions" => array("module_id" => "translations")
		));
		if ($this->assertIdentical($count, 0)) {
			pr("All translations module permissions deleted");
		}
	} 

	
	function testPermissionsByUserid() {
		$userid = $this->data['user.test']['User']['userid'];
		$countUser = $this->PermissionModule->User->find("count", array(
			"conditions" => array("userid" => $userid)
		));
		if ($countUser > 0) {
			$this->assertTrue($this->BeAuth->removeUser($userid));
		}
		// create user
		pr("Create user") ;
		$this->assertTrue($this->BeAuth->createUser($this->data['user.test']));
		
		// Add permits
		$ret = $this->PermissionModule->add('areas', $this->data['add.perms.user']) ;
		pr("Added permits - $ret on areas module") ;
		$this->assertEqual($ret,true);
		
		// Verify permits
		$ret = $this->PermissionModule->permsByUserid($userid, "areas", BEDITA_PERMS_READ);
		if ($this->assertEqual($ret, true)) {
			pr("Read permits are setted on areas module - $ret");
		}
		$ret = $this->PermissionModule->permsByUserid($userid, 'areas', BEDITA_PERMS_MODIFY) ;
		if ($this->assertEqual($ret, false)) {
			pr("User hasn't write permissions on areas module - $ret");
		}

		// remove user
		pr("Remove user") ;
		$this->assertTrue($this->BeAuth->removeUser($userid));

	} 
	
	function testPermissionsByGroup() {	
		// Add permits
		$ret = $this->PermissionModule->add('areas', $this->data['addPerms1']) ;
		pr("Added permits - $ret") ;
		$this->assertEqual($ret,true);
		
		// Verify permits
		$ret = $this->PermissionModule->permsByGroup('translator', 'areas', BEDITA_PERMS_READ) ;
		if ($this->assertEqual($ret, true)) {
			pr("Verify read permits for group 'translator' on areas module - $ret") ;
		}

		$ret = $this->PermissionModule->permsByGroup('translator', 'areas', BEDITA_PERMS_MODIFY) ;
		if ($this->assertEqual($ret, false)) {
			pr("Group 'translator' hasn't write permissions on areas module - $ret") ;
		}
		
	} 

	function testUpdateGroupPermissions() {	
		$groupName = $this->data['updateGroupName'];
		$g = $this->Group->findByName($groupName);
		$groupId = $g['Group']['id'];
		$moduleFlags = $this->data['updateGroupModules'];
		
		$this->PermissionModule->updateGroupPermission($groupId, $moduleFlags);
		
		pr("Verify inserted permits");
		pr($moduleFlags);
		foreach ($moduleFlags as $k=>$v) {
			$this->assertEqual($this->PermissionModule->permsByGroup($groupName, $k, $v), true);
		}
	} 
		
	function testGetListModuleReadableByUserid() {
		$userid = $this->data['user.test']['User']['userid'];
		$countUser = $this->PermissionModule->User->find("count", array(
			"conditions" => array("userid" => $userid)
		));
		if ($countUser > 0) {
			$this->assertTrue($this->BeAuth->removeUser($userid));
		}
		// create user
		pr("Create user") ;
		$this->assertTrue($this->BeAuth->createUser($this->data['user.test'], array("translator")));

		// Verify permits
		$ret = $this->PermissionModule->getListModules($userid) ;
		pr("Permits for user $userid with group translator");
		pr($ret);

		$ret = $this->PermissionModule->remove('documents', "translator", "group");
		if ($this->assertEqual($ret,true)) {
			pr("Permission on module documents removed for group translator");
		}

		$ret = $this->PermissionModule->getListModules($userid) ;
		pr($ret);
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	public   function __construct () {
		parent::__construct('PermissionModule', dirname(__FILE__)) ;
	}		
}
?> 