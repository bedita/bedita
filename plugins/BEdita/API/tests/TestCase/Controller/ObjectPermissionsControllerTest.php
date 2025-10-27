<?php
declare(strict_types=1);

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

use BEdita\API\Controller\ObjectPermissionsController;
use BEdita\API\TestSuite\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\API\Controller\ObjectPermissionsController} Test Case
 */
#[CoversClass(ObjectPermissionsController::class)]
class ObjectPermissionsControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectPermissions',
    ];

    /**
     * Test index method.
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->configRequestHeaders();
        $this->get('/object_permissions');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertCount(1, $result['data']);
        // check links
        $expectedLinks = [
            'self' => 'http://api.example.com/object_permissions',
            'first' => 'http://api.example.com/object_permissions',
            'last' => 'http://api.example.com/object_permissions',
            'prev' => null,
            'next' => null,
            'home' => 'http://api.example.com/home',
        ];
        $this->assertEquals($expectedLinks, $result['links']);
        // check meta
        $expectedMeta = [
            'pagination' => [
                'count' => 1,
                'page' => 1,
                'page_count' => 1,
                'page_items' => 1,
                'page_size' => 20,
            ],
        ];
        $this->assertEquals($expectedMeta, $result['meta']);
        // check data
        $this->assertEquals('object_permissions', $result['data'][0]['type']);
        $expectedAttributes = [
            'object_id' => 2,
            'role_id' => 1,
        ];
        $this->assertEquals($expectedAttributes, $result['data'][0]['attributes']);
        $expectedMeta = [
            'created_by' => 1,
            'created' => '2023-03-29T15:08:00+00:00',
        ];
        $this->assertEquals($expectedMeta, $result['data'][0]['meta']);
        $this->assertStringContainsString('http://api.example.com/object_permissions/', $result['data'][0]['links']['self']);
    }

    /**
     * Test view method.
     *
     * @return void
     */
    public function testSingle(): void
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
     */
    public function testAdd(): void
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
     */
    public function testEdit(): void
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
     */
    public function testDelete(): void
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/object_permissions/1');
        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        $this->assertFalse($this->fetchTable('ObjectPermissions')->exists(['id' => 1]));
    }
}
