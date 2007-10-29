<?php 
/**
 * BeAuth test data 
 * 
 * @author ste@channelweb.it
 * 
 */

class BeAuthTestData extends BeditaTestData {
	var $data =  array(
		'user1'	=> array('userid' 	=> 'giangi', 'passwd' => 'giangi'),
		'user2'	=> array('userid' 	=> 'giangi', 'passwd' => 'giungggg'),
		'user3'	=> array('userid' 	=> 'nuovoutente', 'passwd' => 'nuovapass'),
		'new.group' => array('Group' => array('name' => 'pinucci')), 
		'new.user'	=> array('User' => array('userid' => 'nuovoutente', 'passwd' => 'nuovapass')), 
		'new.user.groups' =>array('guest','calcettari'),
		'policy'  => array(
			'maxLoginAttempts' => 1,
			'maxNumDaysInactivity' => 30,
			'maxNumDaysValidity' => 4,
			'authorizedGroups' => array('administrator', 'guest'))
		) ;
	}

?> 