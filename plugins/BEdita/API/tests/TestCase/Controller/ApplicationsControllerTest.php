<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
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
 * @coversDefaultClass \BEdita\API\Controller\ApplicationsController
 */
class ApplicationsControllerTest extends IntegrationTestCase
{

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     */
    public function testIndex()
    {
        $expected = [
            'data' => [
                [
                    'id' => 1,
                    'type' => 'applications',
                    'attributes' => [
                        'name' => 'First app',
                        'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat.',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/applications/1'
                    ]
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/applications',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/applications',
                'last' => 'http://api.example.com/applications',
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
        $this->get('/applications');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }
}
