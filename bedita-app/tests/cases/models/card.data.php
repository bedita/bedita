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
 class CardTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'name' => 'John',
			'surname' => 'Smith',
			'email' => 'john.smith@bedita.com',
			'ObjectUser' => array(
				'card' => array(
					0 => array(
						"user_id" => 1,
						"switch" => "card"
					)
				)
			)
		),
		'insertError' => array(
			'email' => 'john.smith@bedita'	
		)
	);
}

?>