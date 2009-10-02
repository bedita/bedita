<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class QuestionnaireResult extends BEAppObjectModel {
 	
	public $actsAs = array("CompactResult" => array("Answer","DateItem"));
	public $searchFields = array();
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"ObjectProperty",
															"LangText",
															"Annotation"
															),
									 "DateItem", 
									 "Answer" => array("QuestionAnswer")
									),
				"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType"),
									"DateItem", "Answer"),

				"minimum" => array("BEObject" => array("ObjectType"))		
	);
	
	public $hasMany = array(
		"Answer", 
		"DateItem" =>
				array(
					'className'		=> 'DateItem',
					'foreignKey'	=> 'object_id',
					'dependent'		=> true
				)
	);
	
	
	function afterSave($created) {
		if (!empty($this->data["QuestionnaireResult"]["Answer"])) {
			
			if (!$created)
				$this->updateOldAnswers($this->data["QuestionnaireResult"]["Answer"]);
			
			$question_id = false;
			foreach ($this->data["QuestionnaireResult"]["Answer"] as $key => $answer) {
				if ($question_id != $answer["question_id"]) { 
					$question_type = $this->Answer->Question->field("question_type", array("id" => $answer["question_id"]));
				}
								
				if ( ($question_type == "freetext" && !empty($answer["answer"])) || !empty($answer["question_answer_id"]) ) {
					$answer["questionnaire_result_id"] = $this->id;
					$this->Answer->create();
					if (!$this->Answer->save($answer))
						throw new BeditaException(__("error saving answers",true));
				}
				
				$question_id = $answer["question_id"];
			}
		}
		
		if (!empty($this->data["QuestionnaireResult"]["DateItem"])) {
			foreach($this->data["QuestionnaireResult"]["DateItem"] as $dateitem) {
				$this->DateItem->create();
				$dateitem["object_id"] = $this->id;
				if (!$this->DateItem->save($dateitem))
					throw new BeditaException(__("error saving compiling time",true));
			}
		}
		
		if (!empty($this->data["QuestionnaireResult"]["completed"])) {
			$rating = $this->estimatedRating($this->id, $this->data["QuestionnaireResult"]["object_id"]);
			if ($rating === false)
				throw new BeditaException(__("error calculating rating", true));
			if (!$this->saveField("rating",$rating))
				throw new BeditaException(__("error saving rating",true));
		}
	}
	
	public function afterFind($results) {
		if (!empty($results[0]) && empty($results[0][0]["count"])) {
			foreach ($results as $key => $val) {
				if (!empty($val["id"])) {
					$results[$key]["correct_answers"] = $this->Answer->countCorrectAnswers($val["id"]);
				}
				if (!empty($val["DateItem"])) {
					$totalTime = 0;
					foreach ($val["DateItem"] as $dateitem) {
						$totalTime += strtotime($dateitem["end"]) - strtotime($dateitem["start"]);
					}
					$results[$key]["total_time"] = $totalTime;
				}		
			}
		}
		return $results;
	}
	
	/**
	 * calculate questionnaire result rating
	 * 
	 * @param $questionnaire_result_id
	 * @param $questionnaire_id if null get it from questionnaire_result
	 * @return integer rating
	 */
	public function estimatedRating($questionnaire_result_id, $questionnaire_id=null) {
		if (empty($questionnaire_id)) {
			$questionnaire_id = $this->field("object_id", array("id" => $questionnaire_result_id, "completed" => 1));
		}
		$relModel = ClassRegistry::init("RelatedObject");
		$rel = $relModel->find("all", array(
							"conditions" => array("id" => $questionnaire_id, "switch" => "question")
						)
					);
		$countQuestions = count($rel);
		if ($countQuestions == 0) {
			return false;
		}
		$questionModel = ClassRegistry::init("Question");		
		$correctedAnswers = 0;
		foreach ($rel as $r) {
			$question = $questionModel->find("first", array(
					"conditions" => array("Question.id" => $r["RelatedObject"]["object_id"]),
					"contain" => array(
						"BEObject",
						"QuestionAnswer" => array("Answer.questionnaire_result_id=".$questionnaire_result_id),
						"Answer" => array("questionnaire_result_id=".$questionnaire_result_id." AND final = 1")
					)
				)
			);
			
			// multiple question type => correct if all correct possible answers have been checked
			if ($question["question_type"] == "multiple") {
				$correct = true;
				foreach ($question["QuestionAnswer"] as $qa) {
					if ( ($qa["correct"] && empty($qa["Answer"])) || (!$qa["correct"] && !empty($qa["Answer"])) ) {
						$correct = false;
						break;
					}
				}
			// single radio/pulldown queston type => correct if the only correct answer is checked
			} elseif ($question["question_type"] == "single_radio" || $question["question_type"] == "single_pulldown") {
				$correct = false;
				foreach ($question["QuestionAnswer"] as $qa) {
					if ($qa["correct"] && !empty($qa["Answer"])) {
						$correct = true;
						break;
					}
				}
			// freetext question type => correct if it's not left blank
			} elseif ($question["question_type"] == "freetext") {
				$correct = (!empty($question["Answer"]))? true : false;
			// checkopen/degree question type => correct like multiple and if a specified value for possible answers is equal at the answer
			} elseif ($question["question_type"] == "checkopen" || $question["question_type"] == "degree") {
				$correct = true;
				foreach ($question["QuestionAnswer"] as $qa) {
					if ( ($qa["correct"] && empty($qa["Answer"])) || (!$qa["correct"] && !empty($qa["Answer"])) ) {
						$correct = false;
						break;
					} elseif ($qa["correct"] && !empty($qa["Answer"])) {
						if ( !empty($qa["correct_value"]) && $qa["correct_value"] != $qa["Answer"]["answer"]) {
							$correct = false;
							break;
						}
					}
				}
			}
			
			if ($correct) {
				$correctedAnswers++;	
			}
		}

		return round(($correctedAnswers/$countQuestions)*100);
		
	}
	
	/**
	 * called before answers are saved 
	 * if exists old answers relative to the questions set final=0 at the old answers
	 * 
	 * @param $answers
	 */
	private function updateOldAnswers(&$answers) {
		foreach ($answers as $answer) { 
			$oldAnswers = $this->Answer->find("all", array(
									"conditions" => array(
										"questionnaire_result_id" => $this->id,
										"question_id" => $answer["question_id"],
										"final" => 1
									)
								)
							) ;
			
			if (!empty($oldAnswers)) {
				foreach ($oldAnswers as $oa) {
					$this->Answer->id = $oa["id"];
					if (!$this->Answer->saveField("final",0))
						throw new BeditaException(__("error updating old answers",true));
				}
			}
		}
	}
	
}
 
?>