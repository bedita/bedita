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
define("ALL_PERMS",	BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ) ;
define("CREATE_MODIFY_READ",	 BEDITA_PERMS_CREATE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ);
define("DELETE_MODIFY_READ",	 BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ);
 
class PermissionTestData extends BeditaTestData {

	var $data =  array(
		'minimo'	=> array('title' => 'Test title'),
		'addPerms1' => array(
				array('administrator', 	'group', ALL_PERMS),
				array('bedita', 		'user', BEDITA_PERMS_READ),
				array('guest', 			'group', BEDITA_PERMS_READ),
		),
		'removePerms1' => array(
				array('bedita',  'user'),
				array('guest', 	'group'),
		),
		'resultDeletePerms1' => array(
				array('administrator', 	'group', ALL_PERMS)
		)
	);
}

?> 