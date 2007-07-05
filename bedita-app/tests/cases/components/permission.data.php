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
class PermissionData extends Object {
	var $data =  array(
		'minimo'	=> array('title' 			=> 'Titolo di test'),
		
	) ;
	
	function __construct() {
		$this->data['addPerms1'] = array(
				array('administrator', 	'group', (BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ)),
				array('guest', 			'group', BEDITA_PERMS_READ),
				array('alberto', 		'user',  ( BEDITA_PERMS_CREATE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ)),
				array('torto', 			'user',  ( BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ))
		) ;
		$this->data['removePerms1'] = array(
				array('alberto', 		'user'),
				array('torto', 			'user')
		) ;
		$this->data['resultDeletePerms1'] = array(
				array('administrator', 	'group', (BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ)),
				array('guest', 			'group', BEDITA_PERMS_READ),
		) ;
	}
		
	function &getData() { return $this->data ;  }

}

?> 