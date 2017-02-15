<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\Core\Utility\System;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\StatusController
 */
class StatusControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/API.applications',
        'plugin.BEdita/API.endpoint_permissions',
    ];

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     */
    public function testIndex()
    {
        $status = System::status();
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/status',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'status' => $status
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'X-Api-Key' => API_KEY,
            ],
        ]);
        $this->get('/status');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
