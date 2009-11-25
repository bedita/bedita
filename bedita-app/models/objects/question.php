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
 * Question content, for questionnaire
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Question extends BEAppObjectModel
{
	var $actsAs = array("CompactResult" => array("QuestionAnswer","Answer"));
		
	var $hasMany = array(
		"QuestionAnswer" => array('order' => 'QuestionAnswer.priority ASC'),
		"Answer"
	);
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"ObjectProperty",
															"LangText",
															"RelatedObject",
															"Annotation",
															"Category"
															),
									"QuestionAnswer"),
				"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType", "Annotation",
									"Category", "RelatedObject" ), "QuestionAnswer"),

				"minimum" => array("BEObject" => array("ObjectType"))		
	);
	
	public $searchFields = array("title" => 10 , "description" => 6);	
	
	public function beforeSave() {
		if (!empty($this->data["Question"]["QuestionAnswer"])) {
			$dataAnswer =& $this->data["Question"]["QuestionAnswer"];
			foreach($dataAnswer as $key => $answer) {
				$dataAnswer[$key]["description"] = trim($answer["description"]);
				if (empty($dataAnswer[$key]["description"])) {
					unset($dataAnswer[$key]);
				}
			}
		}
		return true;
	}
	
	function afterSave() {
		return $this->updateHasManyAssoc();
	}
}
?>
