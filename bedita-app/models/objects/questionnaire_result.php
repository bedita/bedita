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
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class QuestionnaireResult extends BEAppObjectModel {
 	
	public $actsAs = array("compactResult" => array("Answer","DateItem"));
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
	}
	
	public function afterFind($results) {
		if (!empty($results[0])) {
			foreach ($results as $key => $val) {
				$results[$key]["correct_answers"] = $this->Answer->countCorrectAnswers($val["id"]);		
			}
		}
		return $results;
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