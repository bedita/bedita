<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Controller\Admin;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

/**
 * @coversDefaultClass \BEdita\API\Controller\Admin\ApplicationsController
 */
class ApplicationsControllerTest extends IntegrationTestCase
{

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/applications',
                'first' => 'http://api.example.com/admin/applications',
                'last' => 'http://api.example.com/admin/applications',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 2,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 2,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'applications',
                    'attributes' => [
                        'name' => 'First app',
                        'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat.',
                        'enabled' => true,
                    ],
                    'meta' => [
                        'api_key' => API_KEY,
                        'created' => '2016-10-28T07:10:57+00:00',
                        'modified' => '2016-10-28T07:10:57+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/applications/1',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'applications',
                    'attributes' => [
                        'name' => 'Disabled app',
                        'description' => 'This app has been disabled',
                        'enabled' => false,
                    ],
                    'meta' => [
                        'api_key' => 'abcdef12345',
                        'created' => '2017-02-17T15:51:29+00:00',
                        'modified' => '2017-02-17T15:51:29+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/applications/2',
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/applications');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/applications',
                'first' => 'http://api.example.com/admin/applications',
                'last' => 'http://api.example.com/admin/applications',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 0,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
            'data' => [],
        ];

        TableRegistry::get('EndpointPermissions')->deleteAll([]);
        TableRegistry::get('Applications')->deleteAll([]);

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/applications');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/applications/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'applications',
                'attributes' => [
                    'name' => 'First app',
                    'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat.',
                    'enabled' => true,
                ],
                'meta' => [
                    'api_key' => API_KEY,
                    'created' => '2016-10-28T07:10:57+00:00',
                    'modified' => '2016-10-28T07:10:57+00:00',
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/applications/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     * @covers \BEdita\API\Error\ExceptionRenderer
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/applications/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/applications/99');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayNotHasKey('data', $result);
        static::assertArrayHasKey('links', $result);
        static::assertArrayHasKey('error', $result);
        static::assertEquals($expected['links'], $result['links']);
        static::assertArraySubset($expected['error'], $result['error']);
        static::assertArrayHasKey('title', $result['error']);
        static::assertNotEmpty($result['error']['title']);
    }

    /**
     * Test add method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::resourceUrl()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'applications',
            'attributes' => [
                'name' => 'My brand new application',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/applications', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $application = TableRegistry::get('Applications')
            ->find()
            ->order(['id' => 'DESC'])
            ->first();

        $this->assertHeader('Location', 'http://api.example.com/admin/applications/' . $application->id);

        $expected = array_merge(['id' => $application->id], $data['attributes']);
        static::assertArraySubset($expected, $application->toArray());
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAddInvalid()
    {
        $data = [
            'type' => 'applications',
            'attributes' => [
                'description' => 'Anonymous application.',
            ],
        ];

        $Applications = TableRegistry::get('Applications');
        $count = $Applications->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/applications', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, $Applications->find()->count());
    }

    /**
     * Test edit method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'applications',
            'attributes' => [
                'name' => 'new name',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/applications/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $Applications = TableRegistry::get('Applications');
        $entity = $Applications->get(1);
        static::assertEquals('new name', $entity->get('name'));
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEditConflict()
    {
        $data = [
            'id' => '1',
            'type' => 'applications',
            'attributes' => [
                'name' => 'new name',
            ],
        ];

        $Applications = TableRegistry::get('Applications');
        $expected = $Applications->get(1)->get('name');

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/applications/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $Applications->get(1)->get('name'));
    }

    /**
     * Test delete method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/admin/applications/2');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        static::assertFalse(TableRegistry::get('Applications')->exists(['id' => 2]));
    }
}
