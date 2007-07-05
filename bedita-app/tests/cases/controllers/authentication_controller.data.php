<?php 
/**
 * 
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5

 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @author giangi@qwerg.com
 * 
 */
class AuthenticationData extends Object {
	var $data =  array(
		'login' => array(
			'login'	=> array(
					'userid' 			=> 'bedita',
					'passwd' 			=> 'bedita',
			),
		),
		'loginOKResult'	=> 'a:5:{s:2:"id";s:1:"1";s:6:"userid";s:6:"bedita";s:8:"realname";s:6:"BEdita";s:6:"passwd";s:32:"0350986918ea1ddfcb9fdd21affc6ccc";s:6:"groups";a:1:{i:0;s:13:"administrator";}}',
		
	) ;
	
	function &getData() { return $this->data ;  }

}

?> 