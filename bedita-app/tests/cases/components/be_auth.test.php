<?php 
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
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

class BeAuthTestCase extends BeditaTestCase {
	var $components = array('BeAuth');
	var $uses = array('User', 'Group');
    var $dataSource = 'default' ;

	////////////////////////////////////////////////////////////////////

    private function removeIfPresent($userData, $groupData) {
		$user = ClassRegistry::init('User');
		$user->recursive=1;
		$user->unbindModel(array('hasMany' => array('Permission')));
		$u = $user->findByUserid($userData['User']['userid']);
		if(!empty($u["User"])) {
			$beAuth	= new BeAuthComponent();
			$this->assertTrue($beAuth->removeUser($userData['User']['userid']));
		}
		$group = ClassRegistry::init('Group');
		$g = $group->findByName($groupData['Group']['name']);
		if(!empty($g["Group"])) {
			$beAuth = new BeAuthComponent();
			$this->assertTrue($beAuth->removeGroup($groupData['Group']['name']));
		}
    }
    
	function testLogin() {
		$this->requiredData(array("new.user", "new.userWithEmail","new.userWithEmptyEmail", "new.userWithEmailPresent", "policy","new.user.groups","new.group"));
		$beAuth = new BeAuthComponent();
		$this->removeIfPresent($this->data['new.user'], $this->data['new.group']);
		$this->removeIfPresent($this->data['new.userWithEmail'], $this->data['new.group']);
		$this->removeIfPresent($this->data['new.userWithEmptyEmail'], $this->data['new.group']);
		$id = $beAuth->saveGroup($this->data['new.group']);
		$this->assertTrue(!empty($id));
		$this->assertTrue($beAuth->createUser($this->data['new.user'], $this->data['new.user.groups']));
		

		$group = ClassRegistry::init('Group');
		$group->id = $id;
		$group->saveField("backend_auth", 0);
		$this->assertFalse($beAuth->login($this->data['new.user']['User']['userid'], $this->data['new.user']['User']['passwd']));
		$group->saveField("backend_auth", 1);
		$this->assertTrue($beAuth->login($this->data['new.user']['User']['userid'], $this->data['new.user']['User']['passwd']));
		
		$this->assertTrue($beAuth->removeUser($this->data['new.user']['User']['userid']));
		$this->assertTrue($beAuth->removeGroup($this->data['new.group']['Group']['name']));
		
		// create user with empty email field
		$this->assertTrue($beAuth->createUser($this->data['new.userWithEmptyEmail'], $this->data['new.user.groups']));
		// create user with email (disable notification)
		$this->assertTrue($beAuth->createUser($this->data['new.userWithEmail'], $this->data['new.user.groups'], false));
		
		$this->expectException("BeditaException");
		$beAuth->createUser($this->data['new.userWithEmailPresent'], $this->data['new.user.groups'], false);
		
		$this->assertTrue($beAuth->removeUser($this->data['new.userWithEmptyEmail']['User']['userid']));
		$this->assertTrue($beAuth->removeUser($this->data['new.userWithEmail']['User']['userid']));
		
	}

	
	function testPassword() {
		$this->requiredData(array("new.user.good.passwd","policy","new.user.bad.passwd"));
		$beAuth = new BeAuthComponent();
		$this->removeIfPresent($this->data['new.user.good.passwd'], $this->data['new.group']);
		$id = $beAuth->saveGroup($this->data['new.group']);
		$this->assertTrue(!empty($id));
		// write test policy
		Configure::write("loginPolicy", $this->data["policy"]);
		$this->assertTrue($beAuth->createUser($this->data['new.user.good.passwd'], $this->data['new.group']));
		$this->assertTrue($beAuth->removeUser($this->data['new.user.good.passwd']['User']['userid']));
		try {
			$beAuth->createUser($this->data['new.user.bad.passwd'], $this->data['new.group']);
			$this->fail("Failed: bad password accepted");
		} catch(BeditaException $be) {
			$this->pass("Ok: bad password not accepted");
		}
		$this->assertTrue($beAuth->removeGroup($this->data['new.group']['Group']['name']));
	}
	
	
	
	function testGroup() {
		$this->requiredData(array("new.group","new.user","new.group.name"));
		$beAuth	= new BeAuthComponent();
		$this->removeIfPresent($this->data['new.user'], $this->data['new.group']);
		$id = $beAuth->saveGroup($this->data['new.group']);
		$this->assertTrue(!empty($id));
		$groupModel = ClassRegistry::init('Group');
		$g = $groupModel->findById($id);
		$g['Group']['name'] = $this->data['new.group.name'];
		$id2 = $beAuth->saveGroup($g);
		$this->assertTrue($id2 === $id);

		try {
			unset($g['Group']['id']);
			$beAuth->saveGroup($g);
			$this->fail("Failed: existing group");
		} catch(BeditaException $be) {
			$this->pass("Ok: existing group");
		}
		
		$this->assertTrue($beAuth->removeGroup($this->data['new.group.name']));
		try {
			$beAuth->saveGroup($this->data['bad.group']);
			$this->fail("Failed: saving group");
		} catch(BeditaException $be) {
			$this->pass("Ok: bad group, exception raised");
		}
	}
	
	function testImmutable() {
		$this->requiredData(array("mutable.group"));
		$beAuth	= new BeAuthComponent();
		$id = $beAuth->saveGroup($this->data['mutable.group']);
		$this->assertTrue(!empty($id));
		$groupModel = ClassRegistry::init('Group');
		$g = $groupModel->findById($id);
		$g['Group']['name'] = str_shuffle($g['Group']['name']);
		$id2 = $beAuth->saveGroup($g);
		$this->assertTrue($id2 === $id);
		$groupModel->saveField("immutable", 1);
		try {
			$g['Group']['backend_auth'] = 1;
			$id3 = $beAuth->saveGroup($g);
			$this->fail("Failed: immutable group");
		} catch(BeditaException $be) {
			$this->pass("Ok: immutable group");
		}
		$groupModel->id = $id2;
		$res = $groupModel->saveField("immutable", 0);
		$this->assertTrue($beAuth->removeGroup($g['Group']['name']));
	}
	
	public   function __construct () {
		parent::__construct('BeAuth', dirname(__FILE__)) ;
	}
}
?>