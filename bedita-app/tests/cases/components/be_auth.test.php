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
    
	function testLogin() {
		$this->requiredData(array("user1","user2"));
		$beAuth	= new BeAuthComponent();
		
		$this->assertTrue($beAuth->login($this->data['user1']['userid'], $this->data['user1']['passwd']));
		$this->assertFalse($beAuth->login($this->data['user2']['userid'], $this->data['user2']['passwd']));
		
	}
	
	function testCreateUser() {
		$this->requiredData(array("new.user"));
		$beAuth	= new BeAuthComponent();
		$beAuth->createUser($this->data['new.user']);
	}

	function testDeleteUser() {
	}

	function testEditUser() {
	}
	
	function testCreateGroup() {
	}
	
	function testDeleteGroup() {
	}

	function testEditGroup() {
	}
	
	public   function __construct () {
		parent::__construct('BeAuth', dirname(__FILE__)) ;
	}
}
?> 