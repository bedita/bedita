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

 */
class QuestionnairesController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeLangText');

	var $uses = array('BEObject', 'Question', 'Questionnaire', 'QuestionnaireResult', 'Tree') ;
	protected $moduleName = 'questionnaires';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
    	$filter["object_type_id"] = Configure::read("objectTypes.questionnaire.id");
    	$filter["count_annotation"] = array("EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
	}
	
	public function viewQuestionnaire($id = null) {
		$this->viewObject($this->Questionnaire, $id);
	}

	public function saveQuestionnaire() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Questionnaire);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Questionnaire saved", true)." - ".$this->data["title"]);
		$this->eventInfo("questionnaire [id=". $this->Questionnaire->id ."] saved");	
	}

	 
	public function index_sessions_results($questionnaire_id, $order = "", $dir = true, $page = 1, $dim = 20) {
		$filter["object_type_id"] = Configure::read("objectTypes.questionnaireresult.id");
		$filter["QuestionnaireResult.object_id"] = $questionnaire_id;
		$this->paginatedList(null, $filter, $order, $dir, $page, $dim);
		foreach ($this->viewVars["objects"] as $key => $objs) {
			$user = ClassRegistry::init("User")->find("first", array(
					"conditions" => array("id" => $objs["user_created"]),
					"contain" => array()
				)
			);
			$this->viewVars["objects"][$key]["UserCreated"] = $user["User"]; 
		}
	}

	public function view_session_results($id) {
		$this->viewObject($this->QuestionnaireResult, $id);
		$this->Questionnaire->containLevel("default");
		$questionnaire = $this->Questionnaire->find("first", array("conditions" => array("Questionnaire.id" => $this->viewVars["object"]["object_id"])));
		if (!empty($questionnaire['RelatedObject'])) {
			$questionnaire["relations"] = $this->objectRelationArray($questionnaire['RelatedObject']);
			unset($questionnaire['RelatedObject']);
		}
		pr($questionnaire);
		pr($this->viewVars["object"]);exit;
	}

	public function view_sessions_average() {

	}

	 public function viewQuestion($id = null) {
	 	$this->viewObject($this->Question, $id);
	 }

	public function indexQuestions($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		if (!empty($this->passedArgs["question_type"]))
			$filter["question_type"] = $this->passedArgs["question_type"];
		if (!empty($this->passedArgs["question_difficulty"]))
			$filter["question_difficulty"] = $this->passedArgs["question_difficulty"];
		if (!empty($this->passedArgs["edu_level"]))
			$filter["edu_level"] = $this->passedArgs["edu_level"];
    	$filter["object_type_id"] = Configure::read("objectTypes.question.id");
    	$filter["Question.*"] = "";
    	$filter["count_annotation"] = array("EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
	 }
	 
	 public function saveQuestion() {
	 	$this->checkWriteModulePermission();
	 	if (empty($this->data["QuestionAnswer"])) {
	 		$this->data["QuestionAnswer"] = array();
	 	}
		$this->Transaction->begin();
		$this->saveObject($this->Question);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Question saved", true)." - ".$this->data["title"]);
		$this->eventInfo("question [id=". $this->Question->id."] saved");
	 }
	 
	public function changeStatusQuestions() {
		$this->changeStatusObjects();
	}
	
	public function deleteQuestion() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Question");
		$this->userInfoMessage(__("Questions deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Questions " . $objectsListDeleted . " deleted");
	}
	
	public function deleteQuestionnaire() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Questionnaire");
		$this->userInfoMessage(__("Questionnaires deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Questionnaires " . $objectsListDeleted . " deleted");
	}
	
	public function cloneObject() {
		$object_type_id = ClassRegistry::init("BEObject")->findObjectTypeId($this->data['id']);
		unset($this->data['id']);
		$this->data['status']='draft';
		$this->data['fixed'] = 0;
		if ($object_type_id == Configure::read('objectTypes.question.id')) {
			$this->saveQuestion();
			$this->action = "saveQuestion";
		} else {
			$this->save();
			$this->action = "save";
		}
	}
	
	
	public function loadQuestionAjax() {
		$conditions = array("Question.id" => explode( ",", trim($this->params["form"]["object_selected"],",") ));
		$this->Question->containLevel("minimum");
		$questions = $this->Question->find("all", array("conditions" => $conditions) ) ;
		$this->set("objsRelated", $questions);
		$this->set("rel", $this->params["form"]["relation"]);
		$this->layout = "ajax";
		$this->render(null, null, VIEWS . "questionnaires/inc/form_question_ajax.tpl");
	}
	
	protected function forward($action, $esito) {
		$REDIRECT = array( 
			"saveQuestion"	=> 	array(
							"OK"	=> "/questionnaires/view/".@$this->Question->id,
							"ERROR"	=> "/questionnaires/view/".@$this->Question->id 
							),
			"saveQuestionnaire"	=> 	array(
							"OK"	=> "/questionnaires/view/".@$this->Questionnaire->id,
							"ERROR"	=> "/questionnaires/view/".@$this->Questionnaire->id 
							),
			"addItemsToAreaSection"	=> 	array(
							"OK"	=> '/questionnaires/index',
							"ERROR"	=> '/questionnaires/index' 
							),
			"changeStatusObjects" => 	array(
							"OK"	=> '/questionnaires',
							"ERROR"	=> '/questionnaires' 
							),
			"changeStatusQuestions"	=> 	array(
							"OK"	=> '/questionnaires/index_questions',
							"ERROR"	=> '/questionnaires/index_questions' 
							),
			"deleteQuestion"	=> 	array(
							"OK"	=> '/questionnaires/index_questions',
							"ERROR"	=> '/questionnaires/index_questions' 
							),
			"deleteQuestionnaire"	=> 	array(
							"OK"	=> '/questionnaires/index',
							"ERROR"	=> '/questionnaires/index' 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}	

?>