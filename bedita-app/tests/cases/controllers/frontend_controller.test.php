<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
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


require_once ROOT . DS . APP_DIR . DS . 'tests'. DS . 'bedita_base.test.php';
require_once BEDITA_CORE_PATH . DS . 'controllers'. DS . 'frontend_controller.php';

class FrontendDummyTestController extends FrontendController {

    var $uses = array();
    public function __construct() {
        parent::__construct();
    }

    public function _modelBindings($model, $bindings) {
        $this->modelBindings[$model] = $bindings;
    }
}

class FrontendControllerTest extends BeditaTestCase {

    public $controller = null;

    protected $customPropCreated = array();
    var $uses = array('Area', 'BEObject','Document') ;
    var $areaId;
    var $documentId;

    public function __construct () {
        parent::__construct('FrontendController', dirname(__FILE__));
        $this->controller = new FrontendDummyTestController();
        $this->controller->constructClasses();
    }

    public function testLoadObj() {
        $this->saveDataAndConfig();
        $this->objectCache = array(); // reset cache
        try {
            $this->controller->_modelBindings('Document',
                array(
                    'BEObject' => array(
                        'ObjectProperty'
                    )
                )
            );
            // this call save data to cache: OK
            $doc1 = $this->controller->loadObj($this->documentId);
            $this->controller->_modelBindings('Document',
                array(
                    'BEObject' => array(
                        'ObjectProperty',
                        'Category'
                    )
                )
            );
            // this call uses cache, but it should not: BUG!
            $doc2 = $this->controller->loadObj($this->documentId);
            $k1 = array_keys($doc1);
            $k2 = array_keys($doc2);
            $diff = array_diff($k2,$k1);
            echo 'First case: load obj with different bindings: it should not use cache and it should reload data (different response)';
            $expected = !empty($diff);
            $this->assertTrue($expected);
        } catch(Exception $e) {
            pr($e->xdebug_message);
        }
        $this->deleteData();
    }

    /**
     * Test loading object with empty ID.
     */
    public function testLoadObjFailure() {
        $this->saveDataAndConfig();

        // Empty string.
        try {
            $document = $this->controller->loadObj('');
            $this->fail();
        } catch (BeditaInternalErrorException $e) {
            $this->assertEqual(__('Missing object id', true), $e->getMessage());
        } catch (Exception $e) {
            $this->fail($e->xdebug_message);
        }

        // `null`
        try {
            $document = $this->controller->loadObj(null);
            $this->fail();
        } catch (BeditaInternalErrorException $e) {
            $this->assertEqual(__('Missing object id', true), $e->getMessage());
        } catch (Exception $e) {
            $this->fail($e->xdebug_message);
        }

        // `false`
        try {
            $document = $this->controller->loadObj(false);
            $this->fail();
        } catch (BeditaInternalErrorException $e) {
            $this->assertEqual(__('Missing object id', true), $e->getMessage());
        } catch (Exception $e) {
            $this->fail($e->xdebug_message);
        }
        $this->deleteData();
    }

    /**
     * Test loading object by nickname.
     */
    public function testLoadObjByNick()
    {
        $this->saveDataAndConfig();
        $nickname = ClassRegistry::init('BEObject')->getNicknameFromId($this->documentId);
        try {
            $document = $this->controller->loadObjByNick($nickname);
            $this->assertTrue(!empty($document));
        } catch (Exception $e) {
            $this->fail($e->xdebug_message);
        }
        $this->deleteData();
    }

    /**
     * Test loading object by nickname when the nickname does not exist.
     */
    public function testLoadObjByNickFailure()
    {
        $this->saveDataAndConfig();
        $nickname = 'this-nickname-does-not-exist';
        try {
            $document = $this->controller->loadObjByNick($nickname);
            $this->fail();
        } catch (BeditaNotFoundException $e) {
            $this->assertEqual(__('Content not found', true) . ' nick: ' . $nickname, $e->getMessage());
        } catch (Exception $e) {
            $this->fail($e->xdebug_message);
        }
        $this->deleteData();
    }

    private function saveDataAndConfig() {
        $result = $this->Area->save($this->data['area']);
        if (!$result) {
			debug($this->Area->validationErrors);
			return;
		}
        $this->areaId = $this->Area->id;
        Configure::write(
            array(
                'frontendAreaId' => $this->areaId,
                'draft' => true
            )
        );
		$result = $this->Document->save($this->data['document']) ;
		$this->assertEqual($result,true);		
		if (!$result) {
			debug($this->Document->validationErrors);
			return;
		}
        $this->documentId = $this->Document->id;
    }

    private function deleteData() {
        $this->Document->delete($this->documentId);
        $this->Area->delete($this->areaId);
    }
}
