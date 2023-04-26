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

/**
 * @coversDefaultClass \BEdita\API\Controller\ObjectPermissionsController
 */
class ObjectPermissionsControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectPermissions',
    ];

    /**
     * Test index method.
     *
     * @return void
     * @coversNothing
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/object_permissions',
                'first' => 'http://api.example.com/object_permissions',
                'last' => 'http://api.example.com/object_permissions',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
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
            'data' => [
                [
                    'id' => '1',
                    'type' => 'object_permissions',
                    'attributes' => [
                        'object_id' => 2,
                        'role_id' => 1,
                    ],
                    'meta' => [
                        'created_by' => 1,
                        'created' => '2023-03-29T15:08:00+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_permissions/1',
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/object_permissions');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     * @coversNothing
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/object_permissions/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'object_permissions',
                'attributes' => [
                    'object_id' => 2,
                    'role_id' => 1,
                ],
                'meta' => [
                    'created_by' => 1,
                    'created' => '2023-03-29T15:08:00+00:00',
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/object_permissions/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test add method.
     *
     * @return void
     * @coversNothing
     */
    public function testAdd()
    {
        $data = [
            'type' => 'object_permissions',
            'attributes' => [
                'object_id' => 3,
                'role_id' => 1,
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/object_permissions', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('data', $result);
        $this->assertHeader('Location', 'http://api.example.com/object_permissions/2');
    }

    /**
     * Test edit method.
     *
     * @return void
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'object_permissions',
            'attributes' => [
                'role_id' => 2,
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/object_permissions/1', json_encode(compact('data')));

        $this->assertResponseCode(405);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test delete method.
     *
     * @return void
     * @coversNothing
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/object_permissions/1');
        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        $this->assertFalse($this->fetchTable('ObjectPermissions')->exists(['id' => 1]));
    }
}
