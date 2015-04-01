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

class DummyTestController extends Controller {

    public $uses = array();

    public $components = array('Session', 'SessionFilter');

}

class SessionFilterComponentTest extends BeditaTestCase {

    public $controller = null;

    public $SessionFilter = null;

    public function __construct () {
        parent::__construct('SessionFilter', dirname(__FILE__)) ;
    }

    public function  testSetup() {
        $this->controller = new DummyTestController();
        $this->controller->action = 'testAction';
        $this->controller->constructClasses();

        // startup session
        $this->controller->Session->startup($this->controller);
        $this->assertTrue(isset($_SESSION));

        // setup session filter
        $this->SessionFilter =& $this->controller->SessionFilter;
        $this->SessionFilter->initialize($this->controller);
        $this->SessionFilter->startup($this->controller);
        $sessionKeyExpected = 'beditaFilter.DummyTest.testAction';
        $this->assertEqual($this->SessionFilter->which(), $sessionKeyExpected);
    }

    public function testAdd() {
        // add
        $this->requiredData(array('add'));
        foreach ($this->data['add'] as $key => $val) {
            $this->assertTrue($this->SessionFilter->add($key, $val));
        }
        // read
        foreach ($this->data['add'] as $key => $val) {
            $sessionValue = $this->SessionFilter->read($key);
            $val = Sanitize::clean($val, array('encode' => true, 'remove_html' => true));
            $this->assertEqual($sessionValue, $val);
        }
        // delete
        $this->assertEqual($this->SessionFilter->read(), $this->data['add']);
        foreach ($this->data['add'] as $key => $val) {
            $this->assertTrue($this->SessionFilter->delete($key));
        }

        $this->assertEqual($this->SessionFilter->read(), array());

        // addBulk
        $this->SessionFilter->addBulk($this->data['add']);
        $this->assertEqual($this->SessionFilter->read(), $this->data['add']);
        foreach ($this->data['add'] as $key => $val) {
            $this->assertTrue($this->SessionFilter->delete($key));
        }

        // addSanitized
        $this->requiredData(array('addSanitized'));
        $this->SessionFilter->addBulk($this->data['addSanitized']);
        $filterValue = $this->SessionFilter->read();
        $expected = array(
            'query' => 'Title my text',
            'BEObject.title' => "alert('hello') object title"
        );
        $this->assertEqual($filterValue, $expected);

        // clean
        $this->assertTrue($this->SessionFilter->clean());
    }

    public function testSetFromUrl() {
        $argsAccepted = array(
            'id' => 1,
            'category' => 'cat-name',
            'relation' => 'attach',
            'rel_object_id' => 12,
            'rel_detail' => true,
            'comment_object_id' => 28,
            'mail_group' => 10,
            'tag' => 'tag-name',
            'query' => 'looking for...',
            'substring' => true
        );

        $argsForbidden = array(
            'custom_key_1' => 'custom_val_1',
            'custom_key_2' => 'custom_val_2'
        );

        $this->controller->params['named'] = array_merge($argsAccepted, $argsForbidden);
        $argsAccepted['parent_id'] = $argsAccepted['id'];
        unset($argsAccepted['id']);
        $this->assertEqual($this->SessionFilter->setFromUrl(), $argsAccepted);
    }

    public function testCleanAll() {
        $this->assertTrue($this->SessionFilter->cleanAll());
        $this->assertEqual($this->SessionFilter->read(), array());
        $this->assertNull($this->controller->Session->read('beditaFilter'));
    }
}
