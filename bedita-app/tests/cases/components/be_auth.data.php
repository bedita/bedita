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

class BeAuthTestData extends BeditaTestData {
	var $data =  array(
		'user1'	=> array('userid' 	=> 'giangi', 'passwd' => 'giangi'),
		'user2'	=> array('userid' 	=> 'giangi', 'passwd' => 'giungggg'),
		'user3'	=> array('userid' 	=> 'nuovoutente', 'passwd' => 'nuovapass'),
		'new.group' => array('Group' => array('name' => 'frustrated')), 
		'new.user'	=> array('User' => array('userid' => 'nuovoutente', 'passwd' => 'nuovapass')), 
        'new.user.bad.pass' => 'wrongpassword',
	    'new.user.groups' =>array('guest','frustrated'),
		'new.group.name' => 'supergeeks',
		'policy'  => array(
			'maxLoginAttempts' => 5,
			'maxNumDaysInactivity' => 30,
			'maxNumDaysValidity' => 4,
			'authorizedGroups' => array('administrator', 'guest'))
		) ;
	}

?> 