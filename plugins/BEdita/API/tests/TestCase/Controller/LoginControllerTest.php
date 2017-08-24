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

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Model\Action\SaveEntityAction;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

/**
 * @coversDefaultClass \BEdita\API\Controller\LoginController
 */
class LoginControllerTest extends IntegrationTestCase
{
    /**
     * Test login method.
     *
     * @return string A valid JWT.
     *
     * @covers ::login()
     * @covers ::reducedUserData()
     * @covers ::jwtTokens()
     */
    public function testLoginOkJson()
    {
        // test using 'Content-Type' => 'application/json'
        $this->configRequestHeaders('POST', [
            'Content-Type' => 'application/json',
        ]);

        $this->post('/auth', json_encode(['username' => 'first user', 'password' => 'password1']));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);

        $lastLogin = TableRegistry::get('Users')->get(1)->get('last_login');
        $this->assertNotNull($lastLogin);
        $this->assertEquals(time(), $lastLogin->timestamp, '', 1);

        return $result['meta'];
    }

    /**
     * Test login method.
     *
     * @return string A valid JWT.
     *
     * @covers ::login()
     * @covers ::reducedUserData()
     * @covers ::jwtTokens()
     */
    public function testLoginOkForm()
    {
        // test using 'Content-Type' => 'application/x-www-form-urlencoded'
        $this->configRequestHeaders('POST', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        $this->post('/auth', ['username' => 'first user', 'password' => 'password1']);
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);

        return $result['meta'];
    }

    /**
     * Test login method with renew token.
     *
     * @param array $meta Login metadata.
     * @return void
     *
     * @depends testLoginOkJson
     * @covers ::login()
     * @covers \BEdita\API\Auth\JwtAuthenticate::authenticate()
     */
    public function testSuccessfulRenew(array $meta)
    {
        sleep(1);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => sprintf('Bearer %s', $meta['renew']),
            ],
        ]);

        $this->post('/auth', []);
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertTextNotEquals($meta['renew'], $result['meta']['renew']);
    }

    /**
     * Test login method with invalid credentials
     *
     * @return void
     *
     * @covers ::login()
     */
    public function testFailedLogin()
    {
        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);

        $this->post('/auth', ['username' => 'first user', 'password' => 'wrongPassword']);

        $this->assertResponseCode(401);
    }

    /**
     * Test login method with wrong content type
     *
     * @return void
     *
     * @covers ::login()
     */
    public function testWrongContentTypeLogin()
    {
        // using default 'application/vnd.api+json' - wrong content type here
        $this->configRequestHeaders('POST');

        $this->post('/auth', json_encode(['username' => 'first user', 'password' => 'wrongPassword']));

        $this->assertResponseCode(400);
    }

    /**
     * Test read logged user data.
     *
     * @param array $meta Login metadata.
     * @return void
     *
     * @depends testLoginOkJson
     * @covers ::whoami()
     * @covers ::userEntity()
     * @covers \BEdita\API\Auth\JwtAuthenticate::authenticate()
     */
    public function testLoggedUser(array $meta)
    {
        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $meta['jwt']),
        ];

        $this->configRequest(compact('headers'));
        $this->get('/auth/user');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(1, $result['data']['id']);
        $this->assertEquals('users', $result['data']['type']);
        $this->assertNotEmpty($result['data']['attributes']);

        // GET /auth *deprecated*
        $this->configRequest(compact('headers'));
        $this->get('/auth');
        $this->assertResponseCode(200);
        $result2 = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals($result['data']['attributes'], $result2['data']['attributes']);
    }

    /**
     * Test read logged user fail.
     *
     * @return void
     *
     * @covers ::whoami()
     * @covers ::userEntity()
     */
    public function testLoggedUserFail()
    {
        $this->configRequestHeaders();

        $this->get('/auth/user');
        $this->assertResponseCode(401);
    }

    /**
     * Remove perms on /auth
     *
     * @return void
     */
    protected function removePermissions()
    {
        TableRegistry::get('EndpointPermissions')->deleteAll(['endpoint_id' => 1]);
        TableRegistry::get('EndpointPermissions')->deleteAll(['endpoint_id IS NULL']);
    }

    /**
     * Test `change` request method
     *
     * @return void
     *
     * @covers ::change()
     */
    public function testChangeRequest()
    {
        $this->removePermissions();
        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);

        $data = [
            'contact' => 'first.user@example.com',
            'change_url' => 'http://users.example.com',
        ];

        $this->post('/auth/change', json_encode($data));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(204);
        $this->assertEmpty($result);
    }

    /**
     * Create job for test
     *
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    protected function createTestJob()
    {
        $action = new SaveEntityAction(['table' => TableRegistry::get('AsyncJobs')]);

        return $action([
            'entity' => TableRegistry::get('AsyncJobs')->newEntity(),
            'data' => [
                'service' => 'credentials_change',
                'payload' => [
                    'user_id' => 1,
                ],
                'scheduled_from' => new Time('1 day'),
                'priority' => 1,
            ]
        ]);
    }

    /**
     * Test perform `change`
     *
     * @return void
     *
     * @covers ::change()
     */
    public function testPerformChange()
    {
        $this->removePermissions();

        $job = $this->createTestJob();
        $data = [
            'uuid' => $job->uuid,
            'password' => 'wewantgustavoforpresident',
        ];
        $this->configRequestHeaders('PATCH', ['Content-Type' => 'application/json']);

        $this->patch('/auth/change', json_encode($data));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(1, $result['data']['id']);
        $this->assertEquals('users', $result['data']['type']);
        $this->assertNotEmpty($result['data']['attributes']);
    }

    /**
     * Test perform `change` with login
     *
     * @return void
     *
     * @covers ::change()
     * @covers ::reducedUserData()
     * @covers ::jwtTokens()
     */
    public function testPerformChangeLogin()
    {
        $this->removePermissions();

        $job = $this->createTestJob();
        $data = [
            'uuid' => $job->uuid,
            'password' => 'wewantgustavoforpresident',
            'login' => true,
        ];
        $this->configRequestHeaders('PATCH', ['Content-Type' => 'application/json']);

        $this->patch('/auth/change', json_encode($data));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(1, $result['data']['id']);
        $this->assertEquals('users', $result['data']['type']);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('jwt', $result['meta']);
        $this->assertArrayHasKey('renew', $result['meta']);
    }

    /**
     * Test update user data
     *
     * @return void
     *
     * @depends testLoginOkJson
     *
     * @covers ::update()
     * @covers ::userEntity()
     */
    public function testUpdate(array $meta)
    {
        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
            'Content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $meta['jwt']),
        ];

        $data = [
            'name' => 'Gustavo',
            'surname' => 'Trump',
        ];
        $this->configRequest(compact('headers'));
        $this->patch('/auth/user', json_encode($data));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertNotEmpty($result['data']);
        static::assertEquals(1, $result['data']['id']);
        static::assertEquals($data['name'], $result['data']['attributes']['name']);
        static::assertEquals($data['surname'], $result['data']['attributes']['surname']);
    }

    /**
     * Test update user data failure
     *
     * @return void
     *
     * @depends testLoginOkJson
     *
     * @covers ::update()
     * @covers ::userEntity()
     */
    public function testUpdateFailure(array $meta)
    {
        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
            'Content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $meta['jwt']),
        ];

        $data = [
           'password' => 'wewantgustavoforpresident',
        ];

        $this->configRequest(compact('headers'));
        $this->patch('/auth/user', json_encode($data));
        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);

        static::assertNotEmpty($result['error']);
        static::assertEquals('Bad input data', $result['error']['title']);
    }
}
