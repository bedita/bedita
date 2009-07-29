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
 class UserTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'User' => array(
				'userid' => 'beditauser',
				'realname' => 'My name and surname',
				'email' => 'beditauser@bedita.com',
				'passwd' => 'mysecret',
				'valid' => 1
			)
		)
	);
}

?> 