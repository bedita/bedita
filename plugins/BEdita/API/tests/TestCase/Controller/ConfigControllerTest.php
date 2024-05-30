<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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

/**
 * @coversDefaultClass \BEdita\API\Controller\ConfigController
 */
class ConfigControllerTest extends IntegrationTestCase
{
    /**
     * Test index method.
     *
     * @return void
     * @coversNothing
     */
    public function testIndex()
    {
        $expected = [
            'data' => [
                [
                    'id' => 11,
                    'type' => 'config',
                    'attributes' => [
                        'content' => '{"val": 42}',
                        'name' => 'appVal',
                    ],
                    'meta' => [
                        'created' => '2018-05-16T12:34:56+00:00',
                        'modified' => '2018-05-16T12:38:02+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/config/11',
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/config',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/config',
                'last' => 'http://api.example.com/config',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'pagination' => [
                    'count' => 1,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 1,
                    'page_size' => 20,
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/config');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method with name as argument.
     *
     * @return void
     * @covers ::getResourceId()
     */
    public function testSingleName()
    {
        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/config/appVal');
        $this->assertResponseCode(200);
    }
}
