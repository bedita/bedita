<?php
/**
 * 
 * @author giangi@qwerg.com
 * 
 * Verifica il meccanismo di login/logout
 * 
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class AuthenticationControllerTestCase extends BeditaTestCase {

    var $dataSource	= 'test' ;
    var $data		= null ;
	var $components	= array('Session') ;

	////////////////////////////////////////////////////////////////
	function testLoginOk() {
		pr("Esegue un login con successo.") ;
		
		$ret = $this->testAction('/authentications/login',	array('data' => $this->data['login'], 'method' => 'post'));
		pr($ret);
		
		$user 	= $this->Session->read('BEAuthUser') ;
		$allow 	= $this->Session->read('BEAuthAllow') ;
		
		$this->assertEqual($user['userid'], $this->data['login']['login']['userid']);
	} 

	function testLogout() {
		pr("Chiude la sessione") ;
		
		$this->testAction('/authentications/logout');
		$user 	= $this->Session->read('BEAuthUser') ;
		$allow 	= $this->Session->read('BEAuthAllow') ;
		$this->assertEqual($user, null);
	}
	
	public   function __construct () {
		parent::__construct('AuthenticationController', dirname(__FILE__)) ;
	}

}

?>

