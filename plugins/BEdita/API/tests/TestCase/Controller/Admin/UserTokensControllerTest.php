<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Controller\Admin;

use BEdita\API\Controller\Admin\UserTokensController;
use BEdita\API\Error\ExceptionRenderer;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Test\Utility\TestArraySubsetTrait;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserTokensController::class)]
#[CoversClass(ExceptionRenderer::class)]
class UserTokensControllerTest extends IntegrationTestCase
{
    use TestArraySubsetTrait;

    /**
     * Test `index` method with user not admin.
     *
     * @return void
     */
    public function testIndexNoAdmin()
    {
        $this->configRequestHeaders();
        $this->get('/admin/user_tokens');
        $this->assertResponseCode(401);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test `index` method.
     *
     * @return void
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
                    'type' => 'user_tokens',
                    'attributes' => [
                        'user_id' => 5,
                        'application_id' => 1,
                        'client_token' => 'toktoktoktoktok',
                        'secret_token' => 'secretsecretsecret',
                        'token_type' => 'otp',
                        'expires' => '',
                        'params' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/user_tokens/1',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'used' => null,
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'user_tokens',
                    'attributes' => [
                        'user_id' => 1,
                        'application_id' => null,
                        'client_token' => 'abcdefghilkmnop',
                        'secret_token' => null,
                        'token_type' => 'refresh',
                        'expires' => '',
                        'params' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/user_tokens/2',
                    ],
                    'meta' => [
                        'created' => '2018-04-07T12:51:27+00:00',
                        'used' => null,
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/admin/user_tokens',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/admin/user_tokens',
                'last' => 'http://api.example.com/admin/user_tokens',
                'prev' => null,
                'next' => null,
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
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/user_tokens');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $expected['data'][0]['attributes']['expires'] = $result['data'][0]['attributes']['expires'];
        $expected['data'][1]['attributes']['expires'] = $result['data'][1]['attributes']['expires'];
        $expected['data'][0]['meta']['created'] = $result['data'][0]['meta']['created'];
        $expected['data'][1]['meta']['created'] = $result['data'][1]['meta']['created'];
        static::assertEquals($expected, $result);
    }

    /**
     * Test index method.
     *
     * @return void
     */
    public function testEmpty()
    {
        $expected = [
            'data' => [],
            'links' => [
                'self' => 'http://api.example.com/admin/user_tokens',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/admin/user_tokens',
                'last' => 'http://api.example.com/admin/user_tokens',
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

        TableRegistry::getTableLocator()->get('UserTokens')->deleteAll([]);

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/user_tokens');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/user_tokens/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'user_tokens',
                'attributes' => [
                    'user_id' => 5,
                    'application_id' => 1,
                    'client_token' => 'toktoktoktoktok',
                    'secret_token' => 'secretsecretsecret',
                    'token_type' => 'otp',
                    'params' => null,
                ],
                'meta' => [
                    'created' => '2018-04-07T12:51:27+00:00',
                    'used' => null,
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/user_tokens/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $expected['data']['attributes']['expires'] = $result['data']['attributes']['expires'];
        $expected['data']['meta']['created'] = $result['data']['meta']['created'];
        $expected['data']['meta']['used'] = $result['data']['meta']['used'];
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/user_tokens/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/user_tokens/99');
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
     */
    public function testAdd()
    {
        $data = [
            'type' => 'user_tokens',
            'attributes' => [
                'user_id' => 1,
                'application_id' => null,
                'client_token' => 'token',
                'secret_token' => 'secret',
                'token_type' => 'otp',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/user_tokens', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $entity = TableRegistry::getTableLocator()->get('user_tokens')
            ->find()
            ->orderBy(['id' => 'DESC'])
            ->first();

        $this->assertHeader('Location', 'http://api.example.com/admin/user_tokens/' . $entity->id);

        $expected = array_merge(['id' => $entity->id], $data['attributes']);
        static::assertArraySubset($expected, $entity->toArray());
    }

    /**
     * Test edit method.
     *
     * @return void
     */
    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'user_tokens',
            'attributes' => [
                'user_id' => 5,
                'application_id' => 1,
                'client_token' => 'token',
                'secret_token' => 'secret',
                'token_type' => 'otp',
                'params' => null,
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/user_tokens/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $query = TableRegistry::getTableLocator()->get('user_tokens');
        $entity = $query->get(1);
        static::assertEquals('token', $entity->get('client_token'));
    }

    /**
     * Test delete method.
     *
     * @return void
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/admin/user_tokens/2');

        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        static::assertFalse(TableRegistry::getTableLocator()->get('user_tokens')->exists(['id' => 2]));
    }
}
