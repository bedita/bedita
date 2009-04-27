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
 class QuestionnaireTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'questions' => array(
				0 => array(
					'title' => 'Multiple question'
				),
				1 => array(
					'title' => 'Open answer question'
				)
			),
			'questionnaire' => array(
				'title' => 'This is my first questionnaire',
				'description' => 'questionnaire description'
			)
		)
	);
}

?> 