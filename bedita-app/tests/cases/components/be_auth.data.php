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
		'new.userWithEmail'	=> array('User' => array('userid' => 'nuovoutente2', 'passwd' => 'nuovapass', 'email' => 'user@example.com')), 
		'new.userWithEmptyEmail'	=> array('User' => array('userid' => 'nuovoutente3', 'passwd' => 'nuovapass', 'email' => '')), 
		'new.userWithEmailPresent'	=> array('User' => array('userid' => 'nuovoutente4', 'passwd' => 'nuovapass', 'email' => 'user@example.com')),
        'new.user.bad.pass' => 'wrongpassword',
	    'new.user.groups' =>array('frustrated'),
		'new.group.name' => 'supergeeks',
		'policy'  => array(
			'maxLoginAttempts' => 5,
			'maxNumDaysInactivity' => 30,
			'maxNumDaysValidity' => 4,
			"passwordRule" => "/\w{5,}/", // regexp to match for valid passwords (empty => no regexp)
			"passwordMessage" => "Password must contain at least 5 valid alphanumeric characters", // error message for passwrds not matching given regexp
	
			),
		'new.user.good.passwd'	=> array('User' => array('userid' => 'goodpassword', 'passwd' => 'goodpassword')),
		'new.user.bad.passwd'	=> array('User' => array('userid' => 'badpassword', 'passwd' => 'bad')),
	) ;
}

?> 