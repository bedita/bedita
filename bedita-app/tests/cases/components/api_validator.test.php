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

class ApiValidatorDummyTestController extends Controller {

    public $uses = array();

    public $components = array('ApiValidator');

    public $requestMethod = 'get';

    public function getRequestMethod() {
        return $this->requestMethod;
    }

}

class ApiValidatorComponentTest extends BeditaTestCase {

    public $controller = null;

    public function __construct () {
        parent::__construct('ApiValidator', dirname(__FILE__));
        $this->controller = new ApiValidatorDummyTestController();
        $this->controller->constructClasses();
        $this->controller->ApiValidator->initialize($this->controller);
    }

    public function testCheckDate() {
        $this->requiredData(array('checkDate'));
        $d = $this->data['checkDate'];
        $this->controller->ApiValidator->checkDate($d['iso8601']);
        $dt = $this->controller->ApiValidator->checkDate($d['iso8601-2']);
        $dt->setTimezone(new DateTimezone('UTC'));
        $this->assertEqual('2015-07-06T16:04:19+00:00', $dt->format(DateTime::ATOM));

        $dt = $this->controller->ApiValidator->checkDate($d['jsIso']);
        $dt->setTimezone(new DateTimezone('UTC'));
        $this->assertEqual('2015-09-25T08:12:51+00:00', $dt->format(DateTime::ATOM));

        // invalid dates
        foreach ($d['errors']['invalid'] as $value) {
            try {
                $dt = $this->controller->ApiValidator->checkDate($value);
                $this->assertTrue(false);
            } catch (BeditaBadRequestException $ex) {
                $this->assertTrue(true);
                $checkMsg = strstr($ex->getMessage(), 'not a valid date');
                $this->assertTrue(!empty($checkMsg));
            }
        }

        // wrong format
        foreach ($d['errors']['format'] as $value) {
            try {
                $dt = $this->controller->ApiValidator->checkDate($value);
                $this->assertTrue(false);
            } catch (BeditaBadRequestException $ex) {
                $this->assertTrue(true);
                $checkMsg = strstr($ex->getMessage(), 'format');
                $this->assertTrue(!empty($checkMsg));
            }
        }
   }

   public function testCheckQueryString() {
        $this->requiredData(array('checkUrlParams'));
        $d = $this->data['checkUrlParams'];

        // test register
        $this->controller->ApiValidator->registerAllowedUrlParams($d);
        $res = $this->controller->ApiValidator->getAllowedUrlParams();
        $expected = array(
            '__all' => array('common'),
            '_group1' => array('groupname1', 'groupname2'),
            'endpoint1' => array('common', 'name1', 'name2'),
            'endpoint2' => array('common', 'groupname1', 'groupname2', 'name3'),
        );
        $this->assertEqual($res, $expected);

        // test check query string
        $this->controller->params['url'] = array(
            'url' => 'http://example.com',
            'common' => 'test',
            'groupname2' => 'test'
        );
        $this->assertFalse(
            $this->controller->ApiValidator->isUrlParamsValid('endpoint1')
        );
        $this->assertTrue(
            $this->controller->ApiValidator->isUrlParamsValid('endpoint2')
        );

        // test check __all
        $this->controller->requestMethod = 'post';
        $this->assertFalse(
            $this->controller->ApiValidator->isUrlParamsValid('new_endpoint')
        );
        unset($this->controller->params['url']['groupname2']);
        $this->assertTrue(
            $this->controller->ApiValidator->isUrlParamsValid('new_endpoint')
        );
   }

}
