<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\Controller\LoginController;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\State\CurrentApplication;
use Cake\Cache\Cache;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\I18n\FrozenTime;
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
    public const NOT_SUCCESSFUL_EXPECTED_RESULT = [
        'error' => [
            'status' => '401',
            'title' => 'Login request not successful',
        ],
        'links' => [
            'self' => 'http://api.example.com/auth',
            'home' => 'http://api.example.com/home',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        Cache::clear('_bedita_core_');
    }

    /**
     * Test login method.
     *
     * @return string A valid JWT.
     * @covers ::login()
     * @covers ::identify()
     * @covers ::reducedUserData()
     * @covers ::initialize()
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

        $lastLogin = TableRegistry::getTableLocator()->get('Users')->get(1)->get('last_login');
        static::assertNotNull($lastLogin);
        static::assertEquals(FrozenTime::now()->timestamp, $lastLogin->timestamp, '');
        static::assertEqualsWithDelta(FrozenTime::now()->timestamp, $lastLogin->timestamp, 1, '');

        return $result['meta'];
    }

    /**
     * Test login method.
     *
     * @return string A valid JWT.
     * @covers ::login()
     * @covers ::reducedUserData()
     * @covers ::jwtTokens()
     * @covers ::initialize()
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
     * @depends testLoginOkJson
     * @covers ::login()
     * @covers ::identify()
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
     * Data provider for `testSetGrantType`
     *
     * @return array
     */
    public function setGrantTypeProvider(): array
    {
        return [
            'password' => [
                'password',
                ['username' => 'first user', 'password' => 'password1'],
            ],
            'refresh_token' => [
                'refresh_token',
                [],
            ],
            'client_credentials' => [
                'client_credentials',
                ['client_id' => API_KEY],
            ],
        ];
    }

    /**
     * Test `setGrantType()` method
     *
     * @param string $expected Expected result.
     * @param array $post POST data.
     * @return void
     * @dataProvider setGrantTypeProvider
     * @covers ::setGrantType()
     */
    public function testSetGrantType(string $expected, array $post): void
    {
        $request = new ServerRequest(compact('post') + [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
        ]);
        $controller = new LoginController($request);
        $controller->Auth->getAuthorize('BEdita/API.Endpoint')->setConfig('defaultAuthorized', true);
        try {
            $controller->login();
        } catch (\Cake\Routing\Exception\MissingRouteException $e) {
        }

        static::assertEquals($expected, $controller->getRequest()->getData('grant_type'));
    }

    /**
     * Data provider for `testCheckClientCredentials`
     *
     * @return array
     */
    public function checkClientCredentialsProvider(): array
    {
        return [
            'null' => [
                null,
                ['username' => 'first user', 'password' => 'password1'],
            ],
            'client_credentials' => [
                1,
                [
                    'grant_type' => 'client_credentials',
                    'client_id' => API_KEY,
                ],
            ],
            'fail' => [
                new UnauthorizedException('App authentication failed'),
                ['client_id' => 'gustavo'],
            ],
        ];
    }

    /**
     * Test `checkClientCredentials()` method
     *
     * @param mixed $expected Expected result.
     * @param array $post POST data.
     * @return void
     * @dataProvider checkClientCredentialsProvider
     * @covers ::checkClientCredentials()
     * @covers ::clientCredentialsOnly()
     */
    public function testCheckClientCredentials($expected, array $post): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        CurrentApplication::setApplication(null);
        $request = new ServerRequest(compact('post') + [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
        ]);
        $controller = new LoginController($request);
        $controller->Auth->getAuthorize('BEdita/API.Endpoint')->setConfig('defaultAuthorized', true);
        try {
            $controller->login();
        } catch (\Cake\Routing\Exception\MissingRouteException $e) {
        }

        static::assertEquals($expected, CurrentApplication::getApplicationId());
    }

    /**
     * Test login with client credentials
     *
     * @return void
     * @covers ::checkClientCredentials()
     * @covers ::identify()
     */
    public function testClientCredentials(): void
    {
        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/auth', json_encode(['client_id' => API_KEY, 'grant_type' => 'client_credentials']));

        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('meta', $result);
        static::assertArrayHasKey('jwt', $result['meta']);
        static::assertArrayHasKey('renew', $result['meta']);
    }

    /**
     * Test renew token failure.
     *
     * @param array $meta Login metadata.
     * @return void
     * @depends testLoginOkJson
     * @covers ::login()
     * @covers ::identify()
     * @covers \BEdita\API\Auth\JwtAuthenticate::authenticate()
     */
    public function testFailedRenew(array $meta)
    {
        sleep(1);

        // block user
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get(1);
        $user->blocked = true;
        $usersTable->saveOrFail($user);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => sprintf('Bearer %s', $meta['renew']),
            ],
        ]);

        $this->post('/auth', []);
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        static::assertArrayHasKey('error', $result);
        static::assertEquals('Login request not successful', $result['error']['title']);
        static::assertEquals('401', $result['error']['status']);
    }

    /**
     * Test login method with invalid credentials
     *
     * @return void
     * @covers ::login()
     * @covers ::identify()
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
     * @covers ::login()
     */
    public function testWrongContentTypeLogin()
    {
        // using default 'application/vnd.api+json' - wrong content type here
        $this->configRequestHeaders('POST');

        $this->post('/auth', json_encode(['username' => 'first user', 'password' => 'wrongPassword']));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        static::assertArrayHasKey('error', $result);
        static::assertEquals('Login request not successful', $result['error']['title']);
    }

    /**
     * Test login ok but authorization denied.
     *
     * @return void
     * @covers ::login()
     * @covers ::identify()
     */
    public function testLoginAuthorizationDenied()
    {
        // Permissions on endpoint `/auth` for application id 2 and role 2 is 0b0001 --> write NO, read MINE
        // POST /auth with role id 2 on application id 2 MUST fail
        $table = TableRegistry::getTableLocator()->get('Applications');
        $app = $table->get(2);
        $app->set('enabled', true);
        $app->set('client_secret', null);
        $table->saveOrFail($app);

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Api-Key' => 'abcdef12345',
            ],
        ]);
        $data = [
            'username' => 'second user',
            'password' => 'password2',
            'grant_type' => 'password',
        ];
        $this->post('/auth', json_encode($data));
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
     * Test read logged user blocked failure.
     *
     * @return void
     * @depends testLoginOkJson
     * @covers ::whoami()
     * @covers ::userEntity()
     */
    public function testLoggedUserBlocked(array $meta)
    {
        sleep(1);

        // block user
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get(1);
        $user->blocked = true;
        $usersTable->saveOrFail($user);

        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $meta['jwt']),
        ];

        $this->configRequest(compact('headers'));
        $this->get('/auth/user');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        $expected = self::NOT_SUCCESSFUL_EXPECTED_RESULT;
        $expected['error']['title'] = 'Request not authorized';
        static::assertEquals($expected, Hash::remove($result, 'error.meta'));
    }

    /**
     * Test read logged user fail.
     *
     * @return void
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
     * Test `findAssociations` with `include` query string.
     *
     * @param array $meta Login metadata.
     * @return void
     * @depends testLoginOkJson
     * @covers ::findAssociation()
     */
    public function testFindAssociation(array $meta)
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => sprintf('Bearer %s', $meta['jwt']),
            ],
        ]);

        $this->get('/auth/user?include=another_test');
        $this->assertResponseCode(200);

        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertNotEmpty($result);
        static::assertNotEmpty($result['included']);
        $related = Hash::combine($result, 'included.{n}.type', 'included.{n}.id');
        static::assertEquals($related['locations'], '8');
    }

    /**
     * Test `findAssociation()` error with `include` query string.
     *
     * @param array $meta Login metadata.
     * @return void
     * @depends testLoginOkJson
     * @covers ::findAssociation()
     */
    public function testFindAssociationError(array $meta)
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => sprintf('Bearer %s', $meta['jwt']),
            ],
        ]);

        $this->get('/auth/user?include=gustavo');
        $this->assertResponseCode(400);

        $result = json_decode((string)$this->_response->getBody(), true);
        unset($result['error']['meta'], $result['links']);
        $error = [
            'status' => '400',
            'title' => 'Invalid "include" query parameter (Relationship "gustavo" does not exist)',
        ];

        static::assertEquals(compact('error'), $result);
    }

    /**
     * Remove perms on /auth
     *
     * @return void
     */
    protected function removePermissions()
    {
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll(['endpoint_id' => 1]);
        TableRegistry::getTableLocator()->get('EndpointPermissions')->deleteAll(['endpoint_id IS NULL']);
    }

    /**
     * Test `change` request method
     *
     * @return void
     * @covers ::change()
     * @covers ::initialize()
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

        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
    }

    /**
     * Create job for test
     *
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    protected function createTestJob()
    {
        $action = new SaveEntityAction(['table' => TableRegistry::getTableLocator()->get('AsyncJobs')]);

        return $action([
            'entity' => TableRegistry::getTableLocator()->get('AsyncJobs')->newEntity([]),
            'data' => [
                'service' => 'credentials_change',
                'payload' => [
                    'user_id' => 1,
                ],
                'scheduled_from' => new FrozenTime('1 day'),
                'priority' => 1,
            ],
        ]);
    }

    /**
     * Test perform `change`
     *
     * @return void
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
     * @depends testLoginOkJson
     * @covers ::update()
     * @covers ::userEntity()
     */
    public function testUpdate(array $meta)
    {
        // set email to NULL to be able to change it
        $table = TableRegistry::getTableLocator()->get('Users');
        $user = $table->get(1);
        $user->set('email', null);
        $table->saveOrFail($user);

        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
            'Content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $meta['jwt']),
        ];

        $data = [
            'name' => 'Gustavo',
            'surname' => 'Trump',
            'email' => 'gustavotrump@example.com',
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
        static::assertEquals($data['email'], $result['data']['attributes']['email']);
        static::assertEquals('on', $result['data']['attributes']['status']);
    }

    /**
     * Test update user data, ignore not accessible fields
     *
     * @param array $meta Previous response metadata.
     * @return void
     * @depends testLoginOkJson
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
            'email' => 'another@email.com',
        ];

        $this->configRequest(compact('headers'));
        $this->patch('/auth/user', json_encode($data));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);

        static::assertNotEmpty($result['data']);
        static::assertEquals(1, $result['data']['id']);
        static::assertNotEquals($data['username'], $result['data']['attributes']['username']);
        static::assertNotEquals($data['email'], $result['data']['attributes']['email']);
    }

    /**
     * Login with deleted user method.
     *
     * @return void.
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
     * @coversNothing
     */
    public function testBlockedLogin()
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get(5);
        $user->blocked = true;
        $usersTable->saveOrFail($user);

        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/auth', json_encode(['username' => 'second user', 'password' => 'password2']));
        $this->assertResponseCode(401);

        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals(self::NOT_SUCCESSFUL_EXPECTED_RESULT, Hash::remove($result, 'error.meta'));
    }

    /**
     * Data provider for `testStatus`
     *
     * @return array
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
            ],
        ];
    }

    /**
     * Test login with some user `status` .
     *
     * @param bool $expected Is login successful?
     * @param string $status User `status`
     * @return void.
     * @coversNothing
     * @dataProvider statusProvider
     */
    public function testStatus($expected, $status)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
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
     * @return array
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
            'empty pass' => [
                200,
                [
                    'password' => '',
                    'old_password' => '',
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
     * @return void.
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

    /**
     * Test `otp_request` grant.
     *
     * @return void
     * @covers ::login()
     * @covers ::setGrantType()
     * @covers ::identify()
     */
    public function testOTPRequestLogin()
    {
        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);

        $this->post('/auth', json_encode([
            'username' => 'first user',
            'grant_type' => 'otp_request',
        ]));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        static::assertNotEmpty($result);

        static::assertNotEmpty($result['meta']['authorization_code']);
        $code = $result['meta']['authorization_code'];

        $expected = [
            'links' => [
                'self' => 'http://api.example.com/auth',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'authorization_code' => $code,
            ],
        ];
        static::assertSame($expected, $result);
    }

    /**
     * Test `otp_request` failure.
     *
     * @return void
     * @covers ::login()
     * @covers ::identify()
     */
    public function testOTPRequestFail()
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get(5);
        $user->deleted = true;
        $usersTable->saveOrFail($user);

        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/auth', json_encode([
            'username' => 'second user',
            'grant_type' => 'otp_request',
        ]));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        static::assertNotEmpty($result);
        static::assertEquals(self::NOT_SUCCESSFUL_EXPECTED_RESULT, Hash::remove($result, 'error.meta'));
    }

    /**
     * Test actual `otp` (One Time Password) login.
     *
     * @return void
     * @covers ::login()
     * @covers ::identify()
     */
    public function testOTPLogin()
    {
        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);

        $this->post('/auth', json_encode([
            'username' => 'second user',
            'authorization_code' => 'toktoktoktoktok',
            'token' => 'secretsecretsecret',
            'grant_type' => 'otp',
        ]));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        static::assertNotEmpty($result['meta']['jwt']);
        static::assertNotEmpty($result['meta']['renew']);
    }

    /**
     * Test `otp` login failure.
     *
     * @return void
     * @covers ::login()
     */
    public function testOTPFail()
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get(5);
        $user->status = 'off';
        $usersTable->saveOrFail($user);

        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/auth', json_encode([
            'username' => 'second user',
            'authorization_code' => 'toktoktoktoktok',
            'token' => 'secretsecretsecret',
            'grant_type' => 'otp',
        ]));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        static::assertNotEmpty($result);
        static::assertEquals(self::NOT_SUCCESSFUL_EXPECTED_RESULT, Hash::remove($result, 'error.meta'));
    }

    /**
     * Data provider for `testOptout` test case.
     *
     * @return array
     */
    public function optoutProvider()
    {
        return [
            'ok' => [
                204,
                [
                    'username' => 'second user',
                    'password' => 'password2',
                ],
            ],
            'auth code' => [
                [
                    'meta' => [
                        'authorization_code' => 1,
                    ],
                ],
                [
                    'username' => 'second user',
                    'grant_type' => 'otp_request',
                ],
            ],
            'unauth' => [
                self::NOT_SUCCESSFUL_EXPECTED_RESULT,
                [
                    'username' => 'second user',
                    'password' => 'wrongPassword',
                ],
            ],
        ];
    }

    /**
     * Test `optout` method
     *
     * @param mixed $expected Expected result
     * @param array $data POST data
     * @return void
     * @dataProvider optoutProvider()
     * @covers ::optout()
     * @covers ::initialize()
     * @covers ::identify()
     */
    public function testOptout($expected, $data)
    {
        $this->configRequestHeaders('POST', ['Content-Type' => 'application/json']);
        $this->post('/auth/optout', json_encode($data));
        $result = json_decode((string)$this->_response->getBody(), true);

        if (is_int($expected)) {
            $this->assertResponseCode($expected);
            static::assertEmpty($result);
        } elseif (!empty($expected['meta']['authorization_code'])) {
            $this->assertResponseCode(200);
            unset($result['links']);
            static::assertNotEmpty($result['meta']['authorization_code']);
            $expected['meta']['authorization_code'] = $result['meta']['authorization_code'];
            static::assertEquals($expected, $result);
        } else {
            unset($result['links'], $result['error']['meta']);
            $this->assertResponseCode((int)$result['error']['status']);
            unset($expected['links']);
            static::assertEquals($expected, $result);
        }
    }
}
