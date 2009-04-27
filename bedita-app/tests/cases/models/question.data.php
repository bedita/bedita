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
 class QuestionTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'multiple' => array(
				'title' => 'Multiple question',
				'question_type' => 'multiple',
				'QuestionAnswer' => array(
					0 => array(
						"description" => "first answer"
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