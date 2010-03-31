<?php 
/**
 * 
 *
 * @version			$Revision: 2487 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2009-11-25 17:56:37 +0100 (mer, 25 nov 2009) $
 * 
 * $Id: permission_module.data.php 2487 2009-11-25 16:56:37Z ste $
 */
class PermissionModuleTestData extends BeditaTestData {
	var $data =  array(
		'addPerms1' => array(
				array(
					"name" => 'reader',
					"switch" => 'group',
					"flag" => BEDITA_PERMS_READ_MODIFY
				),
				array(
					"name" => 'translator',
					"switch" => 'group',
					"flag" => BEDITA_PERMS_READ
				)
			),
		'user.test'	=> array('User' => array('userid' => 'nuovoutente', 'passwd' => 'nuovapass')), 
		'add.perms.user' => array(
				array(
					"name" => 'nuovoutente',
					"switch" => 'user',
					"flag" => BEDITA_PERMS_READ
				)
		),
		'updateGroupName' => 'translator',
		'updateGroupModules' => array('areas' => 3, 'documents' => 1, 'galleries' => 15)
	);
}


?> 