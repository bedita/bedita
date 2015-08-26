<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
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


require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

App::import('Controller', 'Controller', false);

class ApiDummyTestController extends Controller {

    public $uses = array();

    public $components = array('ApiFormatter');

}

class ApiFormatterComponentTest extends BeditaTestCase {

    public $controller = null;

    public $defaultFieldsToRemove = array();

    public function __construct () {
        parent::__construct('ApiFormatter', dirname(__FILE__)) ;
    }

    public function  testRemoveFields() {
        $this->requiredData(array('removeObjectFields'));
        $confApi = Configure::write('api', $this->data['removeObjectFields']);
        $this->controller = new ApiDummyTestController();
        $this->controller->constructClasses();
        $expected = $this->defaultFieldsToRemove = $this->controller->ApiFormatter->objectFieldsToRemove();
        $expected[] = 'description';
        $expected[] = 'title';
        $expected['Category'][] = 'name';
        $expected['GeoTag'] = array('title');
        $expected[] = 'Tag';
        unset($expected['Tag']);
        $this->controller->ApiFormatter->initialize($this->controller);
        $result = $this->controller->ApiFormatter->objectFieldsToRemove();
        $this->assertEqual($result, $expected);

        foreach ($this->data['removeObjectFields']['formatting']['fields']['remove'] as $key => $field) {
            if (!is_array($field)) {
                $isIn = in_array($field, $result);
                $this->assertTrue($isIn);
            } elseif (isset($result[$key])) {
                foreach ($field as $f) {
                    $isIn = in_array($f, $result[$key]);
                    $this->assertTrue($isIn);
                }
            }
        }
    }

    public function testKeepFields() {
        $this->requiredData(array('keepObjectFields'));
        // reset to default
        $this->controller->ApiFormatter->objectFieldsToRemove($this->defaultFieldsToRemove, true);
        $result = $this->controller->ApiFormatter->objectFieldsToRemove($this->data['keepObjectFields']['formatting']['fields'], false);

        foreach ($this->data['keepObjectFields']['formatting']['fields']['keep'] as $key => $field) {
            if (!is_array($field)) {
                $isIn = in_array($field, $result);
                $this->assertFalse($isIn);
            } elseif (isset($result[$key])) {
                foreach ($field as $f) {
                    $isIn = in_array($f, $result[$key]);
                    $this->assertFalse($isIn);
                }
            }
        }
    }

}
