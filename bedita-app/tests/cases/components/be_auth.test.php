<?php 
/**
 * 
 * @author ste@channelweb.it
 * 
 * BeAuthComponent tests
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class BeAuthTestCase extends BeditaTestCase {
	var $components = array('BeAuth');
	var $uses = array('User', 'Group');
    var $dataSource = 'default' ;

	////////////////////////////////////////////////////////////////////

    private function removeIfPresent($userData, $groupData) {
		$user = new User() ;
		$user->setSimpleMode();
		$u = $user->findByUserid($userData['User']['userid']);
		if(!empty($u["User"])) {
			$beAuth	= new BeAuthComponent();
			$this->assertTrue($beAuth->removeUser($userData['User']['userid']));
		}
		$group = new Group() ;
		$g = $group->findByName($groupData['Group']['name']);
		if(!empty($g["Group"])) {
			$beAuth = new BeAuthComponent();
			$this->assertTrue($beAuth->removeGroup($groupData['Group']['name']));
		}
    }
    
	function testLogin() {
		$this->requiredData(array("new.user","policy","new.user.groups","new.group"));
		$beAuth = new BeAuthComponent();
		$this->removeIfPresent($this->data['new.user'], $this->data['new.group']);
		$group = new Group();
		$this->assertTrue($group->save($this->data['new.group']));
		$this->assertTrue($beAuth->createUser($this->data['new.user'], $this->data['new.user.groups']));
		$this->assertTrue($beAuth->login($this->data['new.user']['User']['userid'], $this->data['new.user']['User']['passwd'], $this->data['policy']));
		$this->assertTrue($beAuth->removeUser($this->data['new.user']['User']['userid']));
		$this->assertTrue($beAuth->removeGroup($this->data['new.group']['Group']['name']));
	}
	
	function testGroup() {
//		$this->requiredData(array("new.group","policy"));
//		$beAuth	= new BeAuthComponent();
//		$group = new 
//		$this->assertTrue($beAuth->createUser($this->data['new.user'], $this->data['new.user.groups']));
		
	}
	
	
	public   function __construct () {
		parent::__construct('BeAuth', dirname(__FILE__)) ;
	}
}
?> 