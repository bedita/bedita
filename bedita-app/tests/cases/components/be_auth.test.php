<?php 
/**
 * 
 * @author ste@channelweb.it
 * 
 * BeAuthComponent tests
 * 
 */

include_once(dirname(__FILE__) . DS . 'be_auth.data.php') ;
loadComponent('BeAuth');
loadComponent('Session');
loadController('Authentications');

class BeAuthTestCase extends CakeTestCase {
	
    var $fixtures 	= array();
 	var $user		= null;
    var $dataSource = 'default' ;
 	
    var $data = null ;

	////////////////////////////////////////////////////////////////////

	function testLogin() {	
		$beAuth	= new BeAuthComponent();
		
		$this->assertTrue($beAuth->login($this->data['user1']['userid'], $this->data['user1']['passwd']));
		$this->assertFalse($beAuth->login($this->data['user2']['userid'], $this->data['user2']['passwd']));
		
	} 

	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	
	function startCase() {
		echo '<h1>Bedita Authorization Test</h1>';
	}

	function endCase() {
		echo '<h1>Ending Test Case</h1>';
	}

	function startTest($method) {
		echo '<h3>Starting method ' . $method . '</h3>';
	}

	function endTest($method) {
		echo '<hr />';
	}
	
	public   function __construct () {
		parent::__construct() ;
		
		$beAuthData 	= &new BeAuthData() ;
		$this->data	= $beAuthData->getData() ;
 
	}	
}
?> 