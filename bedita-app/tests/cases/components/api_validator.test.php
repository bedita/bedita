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

class ApiValidatorDummyTestController extends BeditaTestController {

    public $uses = array();

    public $components = array('ApiValidator');

    public $requestMethod = 'get';

    public $apiConf = array(
        'validation' => array(
            'writableObjects' => array('image', 'document')
        )
    );

    public function __construct() {
        Configure::write('api', $this->apiConf);
        parent::__construct();
    }

    public function getRequestMethod() {
        return $this->requestMethod;
    }

}

class ApiValidatorComponentTest extends BeditaTestCase {

    public $controller = null;

    protected $customPropCreated = array();

    public function __construct () {
        parent::__construct('ApiValidator', dirname(__FILE__));
        $this->controller = new ApiValidatorDummyTestController();
        $this->controller->constructClasses();
        $this->controller->ApiValidator->initialize($this->controller);
        $this->controller->ApiValidator->startup($this->controller);
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

   public function testCheckUrlParams() {
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
            'endpoint3' => array('common', 'filter[name1]', 'filter[name2]', 'groupname1', 'groupname2')
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

        $this->controller->params['url']['filter'] = array(
            'name1' => 'test',
            'name3' => 'test'
        );
        $this->assertFalse(
            $this->controller->ApiValidator->isUrlParamsValid('endpoint3')
        );
        unset($this->controller->params['url']['filter']['name3']);
        $this->assertTrue(
            $this->controller->ApiValidator->isUrlParamsValid('endpoint3')
        );

        // test check __all
        $this->controller->requestMethod = 'post';
        unset($this->controller->params['url']['filter']);
        $this->assertFalse(
            $this->controller->ApiValidator->isUrlParamsValid('new_endpoint')
        );
        unset($this->controller->params['url']['groupname2']);
        $this->assertTrue(
            $this->controller->ApiValidator->isUrlParamsValid('new_endpoint')
        );
    }

    public function testCheckCustomProperties() {
        $this->requiredData(array('checkCustomProp'));
        $d = $this->data['checkCustomProp'];
        $checkDate = $this->data['checkDate'];
        // clean db test
        $this->assertTrue($this->cleanCustomProperties($d));
        // prepare data on db test
        $property = ClassRegistry::init('Property');
        foreach ($d as $name => $propData) {
            $property->create();
            $property->save($propData);
            $this->customPropCreated[] = $property->id;
            if (!empty($propData['PropertyOption'])) {
                $propOpt = array();
                foreach ($propData['PropertyOption'] as $opt) {
                    $propOpt[] = array(
                        'property_id' => $property->id,
                        'property_option' => trim($opt)
                    );
                }
                $property->PropertyOption->saveAll($propOpt);
            }
        }

        // not existing custom
        $test = array(
            'custom_text' => 'hello',
            'invalid_prop' => 'bye'
        );
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(false);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(true);
        }

        // invalid custom property for object type
        $test = array('custom_text' => 'hello');
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 3);
            $this->assertTrue(false);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(true);
        }

        // invalid number
        $test = array('custom_number' => '12a');
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(false);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(true);
        }

        // valid number
        $test = array('custom_number' => '125');
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(true);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(false);
        }

        // not valid date
        $test = array('custom_date' => $checkDate['errors']['format'][0]);
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(false);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(true);
        }

        // valid date
        $test = array('custom_date' => $checkDate['jsIso']);
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(true);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(false);
        }

        // invalid option (single choice)
        $test = array('custom_option' => array('one', 'two'));
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(false);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(true);
        }

        // invalid option (single choice)
        $test = array('custom_option' => 'four');
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(false);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(true);
        }

        // valid option (single choice)
        $test = array('custom_option' => 'one');
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(true);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(false);
        }

        // invalid option (multiple choice)
        $test = array('custom_multiple_options' => array('one', 'two', 'four', 'five'));
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(false);
        } catch(BeditaBadRequestException $ex) {
            $this->assertTrue(true);
        }

        // valid option (multiple choice)
        $test = array('custom_multiple_options' => array('six', 'five'));
        try {
            $this->controller->ApiValidator->checkCustomProperties($test, 22);
            $this->assertTrue(true);
        } catch(BeditaBadRequestException $ex) {
            echo $ex->getMessage();
            $this->assertTrue(false);
        }
    }

    public function testIsObjectTypeWritable() {
        $this->assertFalse($this->controller->ApiValidator->isObjectTypeWritable('video'));
        $this->assertTrue($this->controller->ApiValidator->isObjectTypeWritable('document'));
        $documentId = Configure::read('objectTypes.document.id');
        $this->assertTrue($this->controller->ApiValidator->isObjectTypeWritable($documentId));
    }

    public function testIsObjectTypeUploadable() {
        $this->assertFalse($this->controller->ApiValidator->isObjectTypeUploadable('video'));
        $this->assertTrue($this->controller->ApiValidator->isObjectTypeUploadable('image'));
        $this->assertFalse($this->controller->ApiValidator->isObjectTypeUploadable('document'));
    }

    public function testIsMimeTypeValid() {
        $this->assertTrue($this->controller->ApiValidator->isMimeTypeValid('image/jpg', 'image'));
        $this->assertTrue($this->controller->ApiValidator->isMimeTypeValid('image/png', 'image'));
        $this->assertTrue($this->controller->ApiValidator->isMimeTypeValid('application/x-shockwave-flash', 'application'));
        $this->assertFalse($this->controller->ApiValidator->isMimeTypeValid('image/jpg', 'video'));
        $this->assertFalse($this->controller->ApiValidator->isMimeTypeValid('image/jpg', 'document'));
    }

    public function endTest($method) {
        if ($method == 'testCheckCustomProperties') {
            $this->assertTrue($this->cleanCustomProperties());
        }
    }

    private function cleanCustomProperties($customProp = null) {
        if ($customProp === null) {
            $this->requiredData(array('checkCustomProp'));
            $customProp = $this->data['checkCustomProp'];
        }
        $conditions = array(
            'name' => Set::extract('/name', $customProp),
        );
        return ClassRegistry::init('Property')->deleteAll($conditions, false);
    }

}
