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
 * Authentication test
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class AuthenticationControllerTestCase extends BeditaTestCase {

    var $dataSource	= 'test' ;
    var $data		= null ;
	var $components	= array('Session') ;

	////////////////////////////////////////////////////////////////
	function testLoginOk() {
		pr("Logging in....") ;
		
		$ret = $this->testAction('/authentications/login',	array('data' => $this->data['login'], 'method' => 'post'));
		pr($ret);
		
		$user 	= $this->Session->read('BEAuthUser') ;
		$allow 	= $this->Session->read('BEAuthAllow') ;
		
		$this->assertEqual($user['userid'], $this->data['login']['login']['userid']);
	} 

	function testLogout() {
		pr("Closing session....") ;
		
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

