<?php 
/**
  * @author giangi@qwerg.com
 * 
 */

define("MODIFY_READ",	BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ) ;

class PermissionModuleTestData extends BeditaTestData {
	var $data =  array(
		'minimo'	=> array('title' 			=> 'Titolo di test'),
		'removePerms1' => array(
				array('alberto', 		'user'),
				array('torto', 			'user')
			),
		'resultDeletePerms1' => array(
				array('administrator', 	'group', BEDITA_PERMS_MODIFY),
				array('guest', 			'group', BEDITA_PERMS_READ),
		),
		'addPerms1' => array(
				array('administrator', 	'group', MODIFY_READ),
				array('guest', 			'group', BEDITA_PERMS_READ),
				array('torto', 			'user',  MODIFY_READ)
			),
		'updateGroupName' => 'guest',
		'updateGroupModules' => array('areas'=>3, 'documents'=>1, 'galleries'=>15)
	);
}


?> 