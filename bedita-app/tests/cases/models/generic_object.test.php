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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class GenericObjectTestCase extends BeditaTestCase  {
 	
	public $uses = array("Area", "Section");
	function testActsAs() {
		foreach(Configure::read("objectTypes") as $key => $object) {
			if (is_numeric($key)) {
				pr("<h4>Object Model: " . $object["model"] . "</h4>");
				$model = ClassRegistry::init($object["model"]);
				$this->checkDuplicateBehavior($model);
				pr("<hr/>");
			}
		}
	}
	
	public function testUntitled() {
		$this->requiredData(array("no-title", "empty-title", "with-title"));
		$document = ClassRegistry::init("Document");
		$this->insertAndCheck($document, $this->data["no-title"]);
		$this->insertAndCheck($document, $this->data["empty-title"]);
		$this->insertAndCheck($document, $this->data["with-title"]);

		$event = ClassRegistry::init("Event");
		$this->insertAndCheck($event, $this->data["no-title"]);
		$this->insertAndCheck($event, $this->data["empty-title"]);
		$this->insertAndCheck($event, $this->data["with-title"]);

		$section = ClassRegistry::init("Section");
		$this->insertAndCheck($event, $this->data["with-title"]);
		$section->create();
		$res = $section->save($this->data["empty-title"]);
		pr($res);
		$this->assertEqual($res,false);
		$section->create();
		$res = $section->save($this->data["no-title"]);
		pr($res);
		$this->assertEqual($res,false);		
	}

	private function insertAndCheck(Model $model, array &$d) {
		$model->create();
		$res = $model->save($d);
		$this->assertEqual($res,true);		
		$id = $model->id;
		$model->create();
		$result = $model->findById($id);
		$this->assertNotNull($result);		
		pr("Title: ".$result['title']);
		pr("Nick: ".$result['nickname']);
		$result = $model->delete($id);
		$this->assertEqual($result,true);		
	}

	public   function __construct () {
		parent::__construct('GenericObject', dirname(__FILE__)) ;
	}	
	
}
 
?>