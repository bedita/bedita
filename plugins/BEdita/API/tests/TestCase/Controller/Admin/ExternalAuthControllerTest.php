<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
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
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @coversDefaultClass \BEdita\API\Controller\Admin\ExternalAuthController
 */
class ExternalAuthControllerTest extends IntegrationTestCase
{
    use ArraySubsetAsserts;

    /**
     * Test `index` method with user not admin.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndexNoAdmin()
    {
        $this->configRequestHeaders();
        $this->get('/admin/external_auth');
        $this->assertResponseCode(401);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test `index` method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndex()
    {
        // auth as admin
        $fullBaseUrl = Configure::read('App.fullBaseUrl');
        if (empty($fullBaseUrl)) {
            Configure::write('App.fullBaseUrl', 'http://api.example.com');
        }
        $expected = [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'external_auth',
                    'attributes' => [
                        'user_id' => 1,
                        'auth_provider_id' => 1,
                        'params' => null,
                        'provider_username' => 'first_user',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/external_auth/1',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'modified' => '2018-04-07T12:51:27+00:00',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'external_auth',
                    'attributes' => [
                        'user_id' => 5,
                        'auth_provider_id' => 2,
                        'params' => null,
                        'provider_username' => '17fec0fa-068a-4d7c-8283-da91d47cef7d',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/external_auth/2',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'modified' => '2018-04-07T12:51:27+00:00',
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'external_auth',
                    'attributes' => [
                        'user_id' => 5,
                        'auth_provider_id' => 3,
                        'params' => null,
                        'provider_username' => 'disabled_auth_provider',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/external_auth/3',
                    ],
                    'meta' => [
                        'created' => '2020-10-26T17:16:27+00:00',
                        'modified' => '2020-10-26T17:16:27+00:00',
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'external_auth',
                    'attributes' => [
                        'user_id' => 20,
                        'auth_provider_id' => 1,
                        'params' => null,
                        'provider_username' => 'third_user',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/external_auth/4',
                    ],
                    'meta' => [
                        'created' => '2018-04-10T12:51:27+00:00',
                        'modified' => '2018-04-10T12:51:27+00:00',
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/admin/external_auth',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/admin/external_auth',
                'last' => 'http://api.example.com/admin/external_auth',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'pagination' => [
                    'count' => 4,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 4,
                    'page_size' => 20,
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/external_auth');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test index method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'data' => [],
            'links' => [
                'self' => 'http://api.example.com/admin/external_auth',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/admin/external_auth',
                'last' => 'http://api.example.com/admin/external_auth',
                'prev' => null,
                'next' => null,
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
        ];

        TableRegistry::getTableLocator()->get('ExternalAuth')->deleteAll([]);

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/external_auth');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/external_auth/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'external_auth',
                'attributes' => [
                    'user_id' => 1,
                    'auth_provider_id' => 1,
                    'params' => null,
                    'provider_username' => 'first_user',
                ],
                'meta' => [
                    'created' => '2018-04-07T12:51:27+00:00',
                    'modified' => '2018-04-07T12:51:27+00:00',
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/external_auth/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     * @covers \BEdita\API\Error\ExceptionRenderer
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/external_auth/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/external_auth/99');
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
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::resourceUrl()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'external_auth',
            'attributes' => [
                'user_id' => 1,
                'auth_provider_id' => 2,
                'provider_username' => 'new_user',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/external_auth', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $application = TableRegistry::getTableLocator()->get('external_auth')
            ->find()
            ->order(['id' => 'DESC'])
            ->first();

        $this->assertHeader('Location', 'http://api.example.com/admin/external_auth/' . $application->id);

        $expected = array_merge(['id' => $application->id], $data['attributes']);
        static::assertArraySubset($expected, $application->toArray());
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAddAlreadyExist()
    {
        $data = [
            'type' => 'external_auth',
            'attributes' => [
                'name' => 'example',
            ],
        ];

        $external_auth = TableRegistry::getTableLocator()->get('external_auth');
        $count = $external_auth->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/external_auth', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, $external_auth->find()->count());
    }

    /**
     * Test edit method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'external_auth',
            'attributes' => [
                'provider_username' => 'new name',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/external_auth/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $external_auth = TableRegistry::getTableLocator()->get('external_auth');
        $entity = $external_auth->get(1);
        static::assertEquals('new name', $entity->get('provider_username'));
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEditConflict()
    {
        $data = [
            'id' => '1',
            'type' => 'external_auth',
            'attributes' => [
                'name' => 'new name',
            ],
        ];

        $external_auth = TableRegistry::getTableLocator()->get('external_auth');
        $expected = $external_auth->get(1)->get('name');

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/external_auth/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $external_auth->get(1)->get('name'));
    }

    /**
     * Test delete method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/admin/external_auth/2');

        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        static::assertFalse(TableRegistry::getTableLocator()->get('external_auth')->exists(['id' => 2]));
    }
}
