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
use Cake\Utility\Text;

/**
 * @coversDefaultClass \BEdita\API\Controller\Admin\AsyncJobsController
 */
class AsyncJobsControllerTest extends IntegrationTestCase
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
        $AsyncJobs = TableRegistry::get('AsyncJobs');
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/async_jobs?sort=uuid',
                'first' => 'http://api.example.com/admin/async_jobs?sort=uuid',
                'last' => 'http://api.example.com/admin/async_jobs?sort=uuid',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 8,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 8,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '0c833458-dff1-4fbb-bbf6-a30818b60616',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'example',
                        'priority' => 1,
                        'payload' => [
                            'key' => 'value',
                        ],
                        'scheduled_from' => null,
                        'expires' => '1992-08-17T19:29:31+00:00',
                        'max_attempts' => 1,
                    ],
                    'meta' => [
                        'locked_until' => null,
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => null,
                        'status' => 'failed',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/0c833458-dff1-4fbb-bbf6-a30818b60616',
                    ],
                ],
                [
                    'id' => '1e2d1c66-c0bb-47d7-be5a-5bc92202333e',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'example',
                        'priority' => 1,
                        'payload' => [
                            'key' => 'value',
                        ],
                        'scheduled_from' => null,
                        'expires' => null,
                        'max_attempts' => 1,
                    ],
                    'meta' => [
                        'locked_until' => null,
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => '2017-04-28T19:29:31+00:00',
                        'status' => 'completed',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/1e2d1c66-c0bb-47d7-be5a-5bc92202333e',
                    ],
                ],
                [
                    'id' => '40e22034-213f-4028-9930-81c0ed79c5a6',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'example',
                        'priority' => 1,
                        'payload' => [
                            'key' => 'value',
                        ],
                        'scheduled_from' => null,
                        'expires' => null,
                        'max_attempts' => 0,
                    ],
                    'meta' => [
                        'locked_until' => null,
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => null,
                        'status' => 'failed',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/40e22034-213f-4028-9930-81c0ed79c5a6',
                    ],
                ],
                [
                    'id' => '427ece75-71fb-4aca-bfab-1214cd98495a',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'signup',
                        'priority' => 20,
                        'payload' => [
                            'user_id' => '99999',
                        ],
                        'scheduled_from' => null,
                        'expires' => null,
                        'max_attempts' => 1,
                    ],
                    'meta' => [
                        'locked_until' => null,
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => null,
                        'status' => 'pending',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/427ece75-71fb-4aca-bfab-1214cd98495a',
                    ],
                ],
                [
                    'id' => '6407afa6-96a3-4aeb-90c1-1541756efdef',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'example',
                        'priority' => 1,
                        'payload' => [
                            'key' => 'value',
                        ],
                        'scheduled_from' => null,
                        'expires' => null,
                        'max_attempts' => 1,
                    ],
                    'meta' => [
                        'locked_until' => json_decode(json_encode($AsyncJobs->get('6407afa6-96a3-4aeb-90c1-1541756efdef')->get('locked_until')), true),
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => null,
                        'status' => 'locked',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/6407afa6-96a3-4aeb-90c1-1541756efdef',
                    ],
                ],
                [
                    'id' => '66594f3c-995f-49d2-9192-382baf1a12b3',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'example',
                        'priority' => 1,
                        'payload' => [
                            'key' => 'value',
                        ],
                        'scheduled_from' => json_decode(json_encode($AsyncJobs->get('66594f3c-995f-49d2-9192-382baf1a12b3')->get('scheduled_from')), true),
                        'expires' => null,
                        'max_attempts' => 1,
                    ],
                    'meta' => [
                        'locked_until' => null,
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => null,
                        'status' => 'planned',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/66594f3c-995f-49d2-9192-382baf1a12b3',
                    ],
                ],
                [
                    'id' => 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'example',
                        'priority' => 1,
                        'payload' => [
                            'key' => 'value',
                        ],
                        'scheduled_from' => null,
                        'expires' => null,
                        'max_attempts' => 1,
                    ],
                    'meta' => [
                        'locked_until' => null,
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => null,
                        'status' => 'pending',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c',
                    ],
                ],
                [
                    'id' => 'e533e1cf-b12c-4dbe-8fb7-b25fafbd2f76',
                    'type' => 'async_jobs',
                    'attributes' => [
                        'service' => 'example2',
                        'priority' => 10,
                        'payload' => [
                            'key' => 'value',
                        ],
                        'scheduled_from' => null,
                        'expires' => null,
                        'max_attempts' => 1,
                    ],
                    'meta' => [
                        'locked_until' => null,
                        'created' => '2017-04-28T19:29:31+00:00',
                        'modified' => '2017-04-28T19:29:31+00:00',
                        'completed' => null,
                        'status' => 'pending',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/async_jobs/e533e1cf-b12c-4dbe-8fb7-b25fafbd2f76',
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/async_jobs?sort=uuid');
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
                'self' => 'http://api.example.com/admin/async_jobs',
                'first' => 'http://api.example.com/admin/async_jobs',
                'last' => 'http://api.example.com/admin/async_jobs',
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

        TableRegistry::get('AsyncJobs')->deleteAll([]);

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/async_jobs');
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
                'self' => 'http://api.example.com/admin/async_jobs/d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c',
                'type' => 'async_jobs',
                'attributes' => [
                    'service' => 'example',
                    'priority' => 1,
                    'payload' => [
                        'key' => 'value',
                    ],
                    'scheduled_from' => null,
                    'expires' => null,
                    'max_attempts' => 1,
                ],
                'meta' => [
                    'completed' => null,
                    'locked_until' => null,
                    'created' => '2017-04-28T19:29:31+00:00',
                    'modified' => '2017-04-28T19:29:31+00:00',
                    'status' => 'pending',
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/async_jobs/d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c');
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
        $uuid = Text::uuid();
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/async_jobs/' . $uuid,
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/async_jobs/' . $uuid);
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
            'type' => 'async_jobs',
            'attributes' => [
                'service' => 'whatever',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/async_jobs', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $asyncJob = TableRegistry::get('AsyncJobs')
            ->find()
            ->order(['created' => 'DESC'])
            ->first();

        $this->assertHeader('Location', 'http://api.example.com/admin/async_jobs/' . $asyncJob->get('uuid'));

        $expected = array_merge(['uuid' => $asyncJob->get('uuid')], $data['attributes']);
        static::assertArraySubset($expected, $asyncJob->toArray());
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
            'type' => 'async_jobs',
            'attributes' => [
                'priority' => 99,
            ],
        ];

        $AsyncJobs = TableRegistry::get('AsyncJobs');
        $count = $AsyncJobs->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/async_jobs', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, $AsyncJobs->find()->count());
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
            'id' => 'e533e1cf-b12c-4dbe-8fb7-b25fafbd2f76',
            'type' => 'async_jobs',
            'attributes' => [
                'priority' => 99,
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/async_jobs/e533e1cf-b12c-4dbe-8fb7-b25fafbd2f76', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $AsyncJobs = TableRegistry::get('AsyncJobs');
        $entity = $AsyncJobs->get('e533e1cf-b12c-4dbe-8fb7-b25fafbd2f76');
        static::assertEquals(99, $entity->get('priority'));
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
            'id' => 'e533e1cf-b12c-4dbe-8fb7-b25fafbd2f76',
            'type' => 'async_jobs',
            'attributes' => [
                'priority' => 99,
            ],
        ];

        $AsyncJobs = TableRegistry::get('AsyncJobs');
        $expected = $AsyncJobs->get('d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c')->get('priority');

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/async_jobs/d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $AsyncJobs->get('d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c')->get('priority'));
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
        $this->delete('/admin/async_jobs/d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        static::assertFalse(TableRegistry::get('AsyncJobs')->exists(['uuid' => 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c']));
    }
}
