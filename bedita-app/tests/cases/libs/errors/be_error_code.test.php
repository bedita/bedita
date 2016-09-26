<?php

App::import(
    'File',
    'BeErrorCode',
    array('file' => BEDITA_CORE_PATH . DS . 'libs' . DS . 'errors' . DS . 'codes' . DS .'be_error_code.php')
);

class BeErrorCodeTestCase extends CakeTestCase {

    protected $configuredCodes = array();

    public function start() {
        $this->configuredCodes = include BEDITA_CORE_PATH . DS . 'config' . DS . 'error.codes.php';
    }

    public function testValidCodes() {
        $errorCode = new BeErrorCode();
        $this->assertEqual($this->configuredCodes, $errorCode->validCodes());
    }

    public function testNotValidErrorCode() {
        $code = 'FAKE_ERROR_CODE';
        $errorCode = new BeErrorCode($code, array('fake' => 'test'));

        $this->assertEqual('GENERIC_ERROR', $errorCode->code());
        $this->assertEqual(array(), $errorCode->info());
    }

    public function testValidErrorCode() {
        // simple
        $errorCode = new BeErrorCode('UPLOAD_QUOTA_EXCEEDED');
        $this->assertEqual('UPLOAD_QUOTA_EXCEEDED', $errorCode->code());
        $this->assertEqual($this->configuredCodes['UPLOAD_QUOTA_EXCEEDED'], $errorCode->info());

        // array options
        $errorOptions = array(
            'info1' => 'one',
            'info2' => 'two'
        );
        $errorCode = new BeErrorCode('UPLOAD_QUOTA_EXCEEDED', $errorOptions);

        $expected = $errorOptions + $this->configuredCodes['UPLOAD_QUOTA_EXCEEDED'];
        $this->assertEqual($expected, $errorCode->info());
    }
}
