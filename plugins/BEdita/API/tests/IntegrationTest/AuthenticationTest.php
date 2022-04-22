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

namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Tests on /auth endpoint.
 *
 * @coversNothing
 */
class AuthenticationTest extends IntegrationTestCase
{
    use ModelAwareTrait;

    /**
     * Data provider for `testAuth` method.
     *
     * @return array
     */
    public function authProvider(): array
    {
        return [
            'client credentials' => [
                [
                    'client_id' => API_KEY,
                    'grant_type' => 'client_credentials',
                ],
            ],
            'password' => [
                [
                    'username' => 'first user',
                    'password' => 'password1',
                    'grant_type' => 'password',
                ],
                [
                    'X-Api-Key' => API_KEY,
                ],
            ],
        ];
    }

    /**
     * Test auth + renew.
     *
     * @param array $data Post data.
     * @param array $headers Additional headers.
     * @return void
     * @dataProvider authProvider
     * @coversNothing
     */
    public function testAuth(array $data, array $headers = []): void
    {
        $headers += [
            'Host' => 'api.example.com',
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.api+json',
        ];
        $this->configRequestHeaders('POST', $headers);
        $this->post('/auth', json_encode($data));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);

        $jwt = Hash::get($body, 'meta.jwt');
        static::assertNotNull($jwt);
        $renew = Hash::get($body, 'meta.renew');
        static::assertNotNull($renew);

        // renew token
        $headers['Authorization'] = 'Bearer ' . $renew;
        $this->configRequest(compact('headers'));
        $this->post('/auth', json_encode(['grant_type' => 'refresh_token']));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);

        $jwt = Hash::get($body, 'meta.jwt');
        static::assertNotNull($jwt);
        $renew = Hash::get($body, 'meta.renew');
        static::assertNotNull($renew);
    }

    /**
     * Test /auth response with wrong formatted token.
     *
     * @return void
     * @coversNothing
     */
    public function testBadToken(): void
    {
        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
            'Authorization' => 'Bearer gustavo',
        ];
        $this->configRequest(compact('headers'));
        $this->post('/auth', json_encode(['grant_type' => 'refresh_token']));

        $this->assertResponseCode(401);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('error', $body);
        static::assertEquals('401', $body['error']['status']);
        static::assertEquals('Wrong number of segments', $body['error']['title']);
    }

    /**
     * Test OAuth2 flow with app credentials, user login and token renew.
     *
     * @return void
     */
    public function testOauth2Flow(): void
    {
        $Applications = TableRegistry::getTableLocator()->get('Applications');
        $app = $Applications->get(2);
        $app->set('enabled', true);
        $Applications->saveOrFail($app);
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll([]);

        $headers = [
            'Host' => 'api.example.com',
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.api+json',
        ];

        // App credentials login
        $data = [
            'client_id' => 'abcdef12345',
            'client_secret' => 'topsecretstring',
            'grant_type' => 'client_credentials',
        ];
        $this->configRequest(compact('headers'));
        $this->post('/auth', json_encode($data));

        $this->assertResponseCode(200);
        $body = json_decode((string)$this->_response->getBody(), true);
        $jwt = Hash::get($body, 'meta.jwt');
        static::assertNotNull($jwt);

        // User login
        $headers['Authorization'] = 'Bearer ' . $jwt;
        $data = [
            'username' => 'second user',
            'password' => 'password2',
            'grant_type' => 'password',
        ];
        $this->configRequest(compact('headers'));
        $this->post('/auth', json_encode($data));

        $this->assertResponseCode(200);
        $body = json_decode((string)$this->_response->getBody(), true);
        $renew = Hash::get($body, 'meta.renew');
        static::assertNotNull($renew);

        // Token renew
        $headers['Authorization'] = 'Bearer ' . $renew;
        $this->configRequest(compact('headers'));
        $this->post('/auth', json_encode(['grant_type' => 'refresh_token']));

        $this->assertResponseCode(200);
        $body = json_decode((string)$this->_response->getBody(), true);

        $jwt = Hash::get($body, 'meta.jwt');
        static::assertNotNull($jwt);
        $renew = Hash::get($body, 'meta.renew');
        static::assertNotNull($renew);
    }
}
