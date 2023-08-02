<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Utility\System;
use Cake\Core\Configure;

/**
 * @coversDefaultClass \BEdita\API\Controller\SysinfoController
 */
class SysinfoControllerTest extends IntegrationTestCase
{
    /**
     * Test index method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndex()
    {
        $fullBaseUrl = Configure::read('App.fullBaseUrl');
        if (empty($fullBaseUrl)) {
            Configure::write('App.fullBaseUrl', 'http://api.example.com');
        }
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/sysinfo',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'info' => System::info(),
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/sysinfo');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test index method with `Accept: * / *` header.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testGenericContentType()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/sysinfo',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'info' => System::info(),
            ],
        ];

        $this->configRequestHeaders('GET', ['Accept' => '*/*']);
        $this->get('/sysinfo');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test `HEAD` request.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testHeadRequest()
    {
        $this->configRequestHeaders('HEAD', ['Accept' => '*/*']);
        $this->_sendRequest('/sysinfo', 'HEAD');

        $this->assertResponseCode(200);
        $this->assertContentType('application/json');
        $this->assertResponseEmpty();
    }
}
