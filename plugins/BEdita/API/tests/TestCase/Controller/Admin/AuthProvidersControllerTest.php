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
 * @coversDefaultClass \BEdita\API\Controller\Admin\AuthProvidersController
 */
class AuthProvidersControllerTest extends IntegrationTestCase
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
        $this->get('/admin/auth_providers');
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
                    'type' => 'auth_providers',
                    'attributes' => [
                        'name' => 'example',
                        'auth_class' => 'BEdita/API.OAuth2',
                        'url' => 'https://example.com/oauth2',
                        'params' => ['provider_username_field' => 'owner_id'],
                        'enabled' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/auth_providers/1',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'modified' => '2018-04-07T12:51:27+00:00',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'auth_providers',
                    'attributes' => [
                        'name' => 'uuid',
                        'auth_class' => 'BEdita/API.Uuid',
                        'url' => null,
                        'params' => ['status' => 'on'],
                        'enabled' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/auth_providers/2',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'modified' => '2018-04-07T12:51:27+00:00',
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'auth_providers',
                    'attributes' => [
                        'name' => 'linkedout',
                        'auth_class' => 'BEdita/API.OAuth2',
                        'url' => 'https://out.example.com/oauth2',
                        'params' => ['provider_username_field' => 'owner_id'],
                        'enabled' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/auth_providers/3',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'modified' => '2018-04-07T12:51:27+00:00',
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'auth_providers',
                    'attributes' => [
                        'name' => 'otp',
                        'auth_class' => 'BEdita/API.OTP',
                        'url' => null,
                        'params' => ['expiry' => '+5 minutes'],
                        'enabled' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/auth_providers/4',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'modified' => '2018-04-07T12:51:27+00:00',
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/admin/auth_providers',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/admin/auth_providers',
                'last' => 'http://api.example.com/admin/auth_providers',
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
        $this->get('/admin/auth_providers');
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
                'self' => 'http://api.example.com/admin/auth_providers',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/admin/auth_providers',
                'last' => 'http://api.example.com/admin/auth_providers',
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
        TableRegistry::getTableLocator()->get('AuthProviders')->deleteAll([]);

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/auth_providers');
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
                'self' => 'http://api.example.com/admin/auth_providers/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'auth_providers',
                'attributes' => [
                    'name' => 'example',
                    'auth_class' => 'BEdita/API.OAuth2',
                    'url' => 'https://example.com/oauth2',
                    'params' => ['provider_username_field' => 'owner_id'],
                    'enabled' => true,
                ],
                'meta' => [
                    'created' => '2018-04-07T12:51:27+00:00',
                    'modified' => '2018-04-07T12:51:27+00:00',
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/auth_providers/1');
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
                'self' => 'http://api.example.com/admin/auth_providers/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/auth_providers/99');
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
            'type' => 'auth_providers',
            'attributes' => [
                'name' => 'oauth2',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/auth_providers', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $application = TableRegistry::getTableLocator()->get('auth_providers')
            ->find()
            ->orderBy(['id' => 'DESC'])
            ->first();

        $this->assertHeader('Location', 'http://api.example.com/admin/auth_providers/' . $application->id);

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
            'type' => 'auth_providers',
            'attributes' => [
                'name' => 'example',
            ],
        ];

        $auth_providers = TableRegistry::getTableLocator()->get('auth_providers');
        $count = $auth_providers->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/auth_providers', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, $auth_providers->find()->count());
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
            'type' => 'auth_providers',
            'attributes' => [
                'name' => 'otp2',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/auth_providers/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $auth_providers = TableRegistry::getTableLocator()->get('auth_providers');
        $entity = $auth_providers->get(1);
        static::assertEquals('otp2', $entity->get('name'));
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
            'type' => 'auth_providers',
            'attributes' => [
                'name' => 'otp',
            ],
        ];

        $auth_providers = TableRegistry::getTableLocator()->get('auth_providers');
        $expected = $auth_providers->get(1)->get('name');

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/auth_providers/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $auth_providers->get(1)->get('name'));
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
        $this->delete('/admin/auth_providers/2');

        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        static::assertFalse(TableRegistry::getTableLocator()->get('auth_providers')->exists(['id' => 2]));
    }
}
