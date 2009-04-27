<?php 
/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: 1611 $
 * @modifiedby 		$LastChangedBy: ste $
 * @lastmodified	$LastChangedDate: 2008-12-16 18:02:35 +0100 (mar, 16 dic 2008) $
 * 
 * $Id: document.data.php 1611 2008-12-16 17:02:35Z ste $
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