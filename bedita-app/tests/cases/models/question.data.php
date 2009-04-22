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
 class QuestionTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'multiple' => array(
				'title' => 'Multiple question',
				'question_type' => 'multiple',
				'QuestionAnswer' => array(
					0 => array(
						"description" => "first answer",
						"correct" => 0
					),
					1 => array(
						"description" => "second answer",
						"correct" => 1
					)
				)
			)
		)
	);
}

?> 