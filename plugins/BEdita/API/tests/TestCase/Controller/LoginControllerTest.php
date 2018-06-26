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
use BEdita\Core\State\CurrentApplication;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Controller\LoginController
 */
class LoginControllerTest extends IntegrationTestCase
{
    /**
     * Not successful login expected result
     *
     * @var array
     */
    const NOT_SUCCESSFUL_EXPECTED_RESULT = [
        'error' => [
            'status' => '401',
            'title' => 'Login not successful',
        ],
        'links' => [
            'self' => 'http://api.example.com/auth',
            'home' => 'http://api.example.com/home',
        ],
    ];

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
        static::assertNotNull($lastLogin);
        static::assertEquals(Time::now()->timestamp, $lastLogin->timestamp, '', 1);

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
     * Test login ok but authorization denied.
     *
     * @return void
     *
     * @covers ::login()
     */
    public function testLoginAuthorizationDenied()
    {
        // Add role id 2 to user id 5
        $table = TableRegistry::get('RolesUsers');
        $entity = $table->newEntity(['user_id' => 5, 'role_id' => 2]);
        $table->saveOrFail($entity);

        // Permissions on endpoint `/auth` for application id 2 and role 2 is 0b0001 --> write NO, read MINE
        // POST /auth with role id 2 on application id 2 MUST fail
        CurrentApplication::setApplication(TableRegistry::get('Applications')->get(2));

        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);

        $this->post('/auth', json_encode(['username' => 'second user', 'password' => 'password2']));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        static::assertArrayHasKey('error', $result);
        static::assertEquals('Login not authorized', $result['error']['title']);
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
        $this->assertNotEmpty($result['included']);
        $this->assertEquals(1, count($result['included']));
        $this->assertEquals(1, $result['included'][0]['id']);
        $this->assertEquals('roles', $result['included'][0]['type']);

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
     * @param array $meta Previous response metadata.
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
     * Test update user data, ignore not accessible fields
     *
     * @param array $meta Previous response metadata.
     * @return void
     *
     * @depends testLoginOkJson
     *
     * @covers ::update()
     * @covers ::userEntity()
     */
    public function testUpdateIgnore(array $meta)
    {
        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
            'Content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $meta['jwt']),
        ];

        $data = [
            'username' => 'gustavo',
        ];

        $this->configRequest(compact('headers'));
        $this->patch('/auth/user', json_encode($data));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);

        static::assertNotEmpty($result['data']);
        static::assertEquals(1, $result['data']['id']);
        static::assertNotEquals($data['username'], $result['data']['attributes']['username']);
    }

    /**
     * Login with deleted user method.
     *
     * @return void.
     *
     * @coversNothing
     */
    public function testDeletedLogin()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/users/5');

        $this->configRequestHeaders('POST', [
            'Content-Type' => 'application/json',
        ]);
        $this->post('/auth', json_encode(['username' => 'second user', 'password' => 'password2']));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        static::assertEquals(self::NOT_SUCCESSFUL_EXPECTED_RESULT, Hash::remove($result, 'error.meta'));
    }

    /**
     * Login with blocked user method.
     *
     * @return void.
     *
     * @coversNothing
     */
    public function testBlockedLogin()
    {
        $usersTable = TableRegistry::get('Users');
        $user = $usersTable->get(5);
        $user->blocked = true;
        $result = $usersTable->saveOrFail($user);

        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/auth', json_encode(['username' => 'second user', 'password' => 'password2']));
        $this->assertResponseCode(401);

        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals(self::NOT_SUCCESSFUL_EXPECTED_RESULT, Hash::remove($result, 'error.meta'));
    }

    /**
     * Data provider for `testStatus`
     *
     * @return void
     */
    public function statusProvider()
    {
        return [
            'draft' => [
                true,
                'draft',
            ],
            'off' => [
                false,
                'off',
            ],
            'on' => [
                true,
                'on',
            ]
        ];
    }
    /**
     * Test login with some user `status` .
     *
     * @param bool $expected Is login successful?
     * @param string $status User `status`
     *
     * @return void.
     *
     * @coversNothing
     * @dataProvider statusProvider
     */
    public function testStatus($expected, $status)
    {
        $usersTable = TableRegistry::get('Users');
        $user = $usersTable->get(5);
        $user->set('status', $status);
        $usersTable->saveOrFail($user);

        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/auth', json_encode(['username' => 'second user', 'password' => 'password2']));

        if ($expected) {
            $this->assertResponseCode(200);
        } else {
            $this->assertResponseCode(401);
            $result = json_decode((string)$this->_response->getBody(), true);
            static::assertEquals(self::NOT_SUCCESSFUL_EXPECTED_RESULT, Hash::remove($result, 'error.meta'));
        }
    }

    /**
     * Data provider for `testPasswordChange`
     *
     * @return void
     */
    public function passwordChangeProvider()
    {
        return [
            'missing' => [
                400,
                [
                    'password' => 'new password',
                ],
                'Missing current password',
            ],
            'wrong' => [
                400,
                [
                    'password' => 'new password',
                    'old_password' => 'old password',
                ],
                'Wrong password',
            ],
            'no pass' => [
                200,
                [
                    'name' => 'Gustavo',
                ],
                'Wrong password',
            ],
            'ok' => [
                200,
                [
                    'password' => 'password2',
                    'old_password' => 'password1',
                ],
            ],
        ];
    }
    /**
     * Test password change.
     *
     * @param int $expected Expected status code.
     * @param array $data Request body.
     * @param string $error Error title in response, if $expected is >= 400.
     *
     * @return void.
     *
     * @covers ::checkPassword()
     * @dataProvider passwordChangeProvider
     */
    public function testPasswordChange($expected, array $data, $error = null)
    {
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader() + ['Content-Type' => 'application/json']);
        $this->patch('/auth/user', json_encode($data));

        $this->assertResponseCode($expected);
        if ($expected >= 400) {
            $result = json_decode((string)$this->_response->getBody(), true);
            static::assertEquals($error, $result['error']['title']);
        } elseif (!empty($data['password'])) {
            // login with new password
            $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
            $this->post('/auth', json_encode(['username' => 'first user', 'password' => $data['password']]));
            $this->assertResponseCode(200);
        }
    }
}
