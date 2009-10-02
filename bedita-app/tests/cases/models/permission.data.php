<?php 
/**
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
 
class PermissionTestData extends BeditaTestData {

	var $data =  array(
		'min'	=> array('title' => 'Test title'),
		'addPerms1' => array(
				array('switch' => 'user', 'flag' => OBJ_PERMS_WRITE, 'name' => 'bedita'),
				array('switch' => 'group', 'flag' => OBJ_PERMS_READ_FRONT, 'name' => 'reader'),
		),
		'removePerms1' => array(
				array('switch' => 'group', 'flag' => OBJ_PERMS_READ_FRONT, 'name' => 'reader'),
		),
		'resultDeletePerms1' => array(
				array('switch' => 'user', 'flag' => OBJ_PERMS_WRITE, 'name' => 'bedita'),
		)
	);

}

?> 