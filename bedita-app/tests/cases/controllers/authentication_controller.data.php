<?php 
/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AuthenticationControllerTestData extends BeditaTestData {
	var $data =  array(
		'login' => array(
			'login'	=> array(
					'userid' 			=> 'bedita',
					'passwd' 			=> 'bedita',
			),
		),
		'loginOKResult'	=> 'a:5:{s:2:"id";s:1:"1";s:6:"userid";s:6:"bedita";s:8:"realname";s:6:"BEdita";s:6:"passwd";s:32:"0350986918ea1ddfcb9fdd21affc6ccc";s:6:"groups";a:1:{i:0;s:13:"administrator";}}',
		
	) ;
}

?> 