<?php 
/**
 * @author giangi@qwerg.com
 */
define("ALL_PERMS",	BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ) ;
define("CREATE_MODIFY_READ",	 BEDITA_PERMS_CREATE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ);
define("DELETE_MODIFY_READ",	 BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ);
 
class PermissionTestData extends BeditaTestData {

	var $data =  array(
		'minimo'	=> array('title' 			=> 'Titolo di test'),
		'addPerms1' => array(
				array('administrator', 	'group', ALL_PERMS),
				array('guest', 			'group', BEDITA_PERMS_READ),
				array('alberto', 		'user',  CREATE_MODIFY_READ),
				array('torto', 			'user',  DELETE_MODIFY_READ)
		),
		'removePerms1' => array(
				array('alberto', 		'user'),
				array('torto', 			'user')
		),
		'resultDeletePerms1' => array(
				array('administrator', 	'group', ALL_PERMS),
				array('guest', 			'group', BEDITA_PERMS_READ)
		)
	);
}

?> 