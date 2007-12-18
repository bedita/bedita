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
				array('bedita', 		'user')
		),
		'resultDeletePerms1' => array(
				array('administrator', 	'group', MODIFY_READ),
				array('guest', 			'group', BEDITA_PERMS_READ),
		),
		'addPerms1' => array(
				array('administrator', 	'group', MODIFY_READ),
				array('guest', 			'group', BEDITA_PERMS_READ)
			),
		'user.test'	=> array('User' => array('userid' => 'nuovoutente', 'passwd' => 'nuovapass')), 
		'add.perms.user' => array(
				array('nuovoutente', 	'user', BEDITA_PERMS_READ)
		),
		'remove.perms.user' => array(
				array('nuovoutente', 		'user')
		),
		'add.perms.guest' => array(
				array('guest', 			'group', BEDITA_PERMS_READ)
		),
		'remove.perms.guest' => array(
				array('guest', 			'group')
		),
		'updateGroupName' => 'guest',
		'updateGroupModules' => array('areas'=>3, 'documents'=>1, 'galleries'=>15)
	);
}


?> 