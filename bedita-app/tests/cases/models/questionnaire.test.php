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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: 1627 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2009-01-02 20:21:19 +0100 (ven, 02 gen 2009) $
 * 
 * $Id: document.test.php 1627 2009-01-02 19:21:19Z bato $
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class QuestionnaireTestCase extends BeditaTestCase {

 	var $uses		= array('Questionnaire', 'Question') ;
    var $dataSource	= 'test' ;	
 	var $components	= array('Transaction') ;

 	protected $inserted = array();
 	
    /////////////////////////////////////////////////
    //      TEST METHODS
    /////////////////////////////////////////////////
 	function testActsAs() {
 		$this->checkDuplicateBehavior($this->Questionnaire);
 	}
 	
 	function testInsert() {
		$this->requiredData(array("insert"));
		
		foreach ($this->data['insert']['questions'] as $key => $question) {
			$this->Question->create();
			$result = $this->Question->save($question);
			if(!$result) {
				debug($this->Question->validationErrors);
				return ;
			}
			$question_id = $this->Question->id;
			$this->data['insert']['questionnaire']['RelatedObject']['question'][$question_id] = array(
				'id' => $question_id,
				'priority' => $key+1
			);
		}
		
		$result = $this->Questionnaire->save($this->data['insert']['questionnaire']) ;
		$this->assertEqual($result,true);		
		if(!$result) {
			debug($this->Questionnaire->validationErrors);
			return ;
		}
		
		$result = $this->Questionnaire->findById($this->Questionnaire->id);
		pr("Questionnaire created:");
		pr($result);
		$this->inserted[] = $this->Questionnaire->id;
	} 
	
	
 	function testDelete() {
        pr("Removinge inserted Questionnaires:");
        foreach ($this->inserted as $ins) {
        	$result = $this->Questionnaire->delete($ins);
			$this->assertEqual($result, true);		
			pr("Questionnaires deleted");
        }        
 	}
 	
    /////////////////////////////////////////////////
	//     END TEST METHODS
	/////////////////////////////////////////////////

	protected function cleanUp() {
		$this->Transaction->rollback() ;
	}
	
	public   function __construct () {
		parent::__construct('Questionnaire', dirname(__FILE__)) ;
	}	
}

?> 