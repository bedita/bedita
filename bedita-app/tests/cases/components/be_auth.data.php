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

class BeAuthTestData extends BeditaTestData {
	var $data =  array(
		'user1'	=> array('userid' 	=> 'giangi', 'passwd' => 'giangi'),
		'user2'	=> array('userid' 	=> 'giangi', 'passwd' => 'giungggg'),
		'user3'	=> array('userid' 	=> 'nuovoutente', 'passwd' => 'nuovapass'),
	
		'mutable.group' => array('Group' => array('name' => 'chameleon', 'immutable' => 0)), 
		'auth.group' => array('Group' => array('name' => 'noauth', 'immutable' => 0, 'backend_auth' => 0)), 
	
		'new.group' => array('Group' => array('name' => 'frustrated', 'backend_auth' => 1)), 
		'bad.group' => array('Group' => array('name' => null)), 
		'new.user'	=> array('User' => array('userid' => 'nuovoutente', 'passwd' => 'nuovapass')), 
        'new.user.bad.pass' => 'wrongpassword',
	    'new.user.groups' =>array('frustrated'),
		'new.group.name' => 'supergeeks',
		'policy'  => array(
			'maxLoginAttempts' => 5,
			'maxNumDaysInactivity' => 30,
			'maxNumDaysValidity' => 4
			)
		) ;
	}

?> 