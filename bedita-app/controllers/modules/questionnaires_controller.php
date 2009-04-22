<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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

	var $uses = array('BEObject', 'Question', 'Questionnaire',  'Tree') ;
	protected $moduleName = 'questionnaires';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
    	$filter["object_type_id"] = Configure::read("objectTypes.questionnaire.id");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
	 }
	
	 public function view($id = null) {
		$this->viewObject($this->Questionnaire, $id);
	 }


	public function index_sessions_results() {

	}

	public function view_session_results() {

	}

	public function view_sessions_average() {

	}

	 public function view_question($id = null) {
	 	$this->viewObject($this->Question, $id);

	 }

	public function index_questions($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
    	$filter["object_type_id"] = Configure::read("objectTypes.question.id");
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
		$this->eventInfo("question [id=". $this->data["id"]."] saved");
	 }
	 
	public function delete() {
		$modelName = $this->BEObject->getType($this->data["id"]);	
		$method = "delete" . $modelName;
		if (!method_exists($this, $method)) {
			$this->redirect($this->referer());
		}
		$this->action = $method;
		$this->{$method}();
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
	 
	
	 protected function forward($action, $esito) {
		$REDIRECT = array( 
			"saveQuestion"	=> 	array(
							"OK"	=> "/questionnaires/view_question/".@$this->Question->id,
							"ERROR"	=> "/questionnaires/view_question/".@$this->Question->id 
							),
			"addItemsToAreaSection"	=> 	array(
							"OK"	=> '/questionnaires/index',
							"ERROR"	=> '/questionnaires/index' 
							),
			"changeStatusQuestions"	=> 	array(
							"OK"	=> '/questionnaires/index_questions',
							"ERROR"	=> '/questionnaires/index_questions' 
							),
			"deleteQuestion"	=> 	array(
							"OK"	=> '/questionnaires/index_questions',
							"ERROR"	=> '/questionnaires/index_questions' 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}	

?>