<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
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

				// check 'Callback' behavior presence
				$isIn = in_array('Callback', $model->actsAs);
				$this->assertTrue($isIn);
				$this->assertTrue($model->Behaviors->attached('Callback'));
				$this->assertTrue($model->Behaviors->enabled('Callback'));
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

	public function testSaveGeoTag() {
		$this->requiredData(array('geotag'));
		$document = ClassRegistry::init("Document");
		$res = $document->save($this->data['geotag']);
		$this->assertEqual($res, true);
		$geotag = ClassRegistry::init('GeoTag');
		$res = $geotag->find('first', array(
			'conditions' => array('object_id' => $document->id)
		));
		$this->assertNotEqual($res, false);
		$this->assertEqual(round($res['GeoTag']['latitude']), 40);
		$this->assertEqual(round($res['GeoTag']['longitude']), 10);
		$this->assertEqual($res['GeoTag']['title'], 'geo tag title');
		$this->assertEqual($res['GeoTag']['address'], 'via Rismondo 2, Bologna');

		// remove geotag
		$this->data['geotag']['id'] = $document->id;
		$this->data['geotag']['GeoTag'][0] = array(
			'title' => '  ',
			'address' => '',
			'latitude' => '',
			'longitude' => ''
		);
		$document->create();
		$res = $document->save($this->data['geotag']);
		$this->assertEqual($res, true);
		$res = $geotag->find('first', array(
			'conditions' => array('object_id' => $document->id)
		));
		$this->assertFalse($res);
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