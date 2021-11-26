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

namespace BEdita\API\Test\TestCase\Auth;

use BEdita\API\Auth\JwtAuthenticate;
use BEdita\API\Exception\ExpiredTokenException;
use BEdita\API\Middleware\TokenMiddleware;
use Cake\Auth\WeakPasswordHasher;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\I18n\Time;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * @coversDefaultClass \BEdita\API\Auth\JwtAuthenticate
 */
class JwtAuthenticateTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

    // /**
    //  * Data provider for `testGetToken` test case.
    //  *
    //  * @return array
    //  */
    // public function getTokenProvider()
    // {
    //     return [
    //         'header' => [
    //             'myToken',
    //             [],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'Bearer myToken'],
    //             ]),
    //         ],
    //         'headerCustom' => [
    //             'myToken',
    //             [
    //                 'header' => 'X-Api-Jwt',
    //             ],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_X_API_JWT' => 'Bearer myToken'],
    //             ]),
    //         ],
    //         'headerCustomPrefix' => [
    //             'myToken',
    //             [
    //                 'headerPrefix' => 'MyBearer',
    //             ],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'MyBearer myToken'],
    //             ]),
    //         ],
    //         'headerWrongPrefix' => [
    //             null,
    //             [],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'WrongBearer myToken'],
    //             ]),
    //         ],
    //         'query' => [
    //             'myToken',
    //             [],
    //             new ServerRequest([
    //                 'query' => ['token' => 'myToken'],
    //             ]),
    //         ],
    //         'queryCustom' => [
    //             'myToken',
    //             [
    //                 'queryParam' => 'token_jwt',
    //             ],
    //             new ServerRequest([
    //                 'query' => ['token_jwt' => 'myToken'],
    //             ]),
    //         ],
    //         'queryDisallowed' => [
    //             null,
    //             [
    //                 'queryParam' => null,
    //             ],
    //             new ServerRequest([
    //                 'query' => ['token' => 'myToken'],
    //             ]),
    //         ],
    //         'both' => [
    //             'myToken',
    //             [],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'Bearer myToken'],
    //                 'query' => ['token' => 'myOtherToken'],
    //             ]),
    //         ],
    //         'missing' => [
    //             null,
    //             [],
    //             new ServerRequest(),
    //         ],
    //     ];
    // }

    // /**
    //  * Test `getToken` method.
    //  *
    //  * @param string|null $expected Expected result.
    //  * @param array $config Configuration.
    //  * @param \Cake\Http\ServerRequest $request Request.
    //  * @return void
    //  *
    //  * @dataProvider getTokenProvider
    //  * @covers ::getToken()
    //  */
    // public function testGetToken($expected, array $config, ServerRequest $request)
    // {
    //     $auth = new JwtAuthenticate(new ComponentRegistry(), $config);

    //     $result = $auth->getToken($request);

    //     static::assertEquals($expected, $result);
    // }

    /**
     * Data provider for `testAuthenticate` test case.
     *
     * @return array
     */
    // public function authenticateProvider()
    // {
    //     $payload = ['someData' => 'someValue'];

    //     $token = JWT::encode($payload, Security::getSalt());
    //     $renewToken = JWT::encode(['sub' => 1], Security::getSalt());

    //     $invalidToken = JWT::encode(['aud' => 'http://example.org'], Security::getSalt());
    //     $expiredToken = JWT::encode(['exp' => time() - 10], Security::getSalt());

    //     return [
    //         'default' => [
    //             $payload,
    //             [],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
    //             ]),
    //         ],
    //         'queryDatasource' => [
    //             [
    //                 'id' => 1,
    //                 'username' => 'first user',
    //                 'password_hash' => (new WeakPasswordHasher(['hashType' => 'md5']))->hash('password1'),
    //                 'blocked' => false,
    //                 'last_login' => null,
    //                 'last_login_err' => null,
    //                 'num_login_err' => 1,
    //                 'verified' => new Time('2017-05-29 11:36:00'),
    //             ],
    //             [
    //                 'userModel' => 'BEdita/API.Users',
    //                 'finder' => 'all',
    //                 'queryDatasource' => true,
    //             ],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'Bearer ' . $renewToken],
    //             ]),
    //         ],
    //         'queryDatasourceNoSub' => [
    //             false,
    //             [
    //                 'userModel' => 'BEdita/API.Users',
    //                 'queryDatasource' => true,
    //             ],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
    //             ]),
    //         ],
    //         'missingToken' => [
    //             false,
    //             [],
    //             new ServerRequest(),
    //         ],
    //         'invalidToken' => [
    //             new UnauthorizedException('Invalid audience'),
    //             [],
    //             new ServerRequest([
    //                 'params' => [
    //                     'plugin' => 'BEdita/API',
    //                     'controller' => 'Login',
    //                     'action' => 'login',
    //                     '_method' => 'POST',
    //                 ],
    //                 'environment' => [
    //                     'HTTP_AUTHORIZATION' => 'Bearer ' . $invalidToken,
    //                     'HTTP_HOST' => 'api.example.com',
    //                 ],
    //             ]),
    //         ],
    //         'expiredToken' => [
    //             new ExpiredTokenException([
    //                 'title' => __d('bedita', 'Expired token'),
    //                 'detail' => __d('bedita', 'Provided token has expired'),
    //                 'code' => 'be_token_expired',
    //             ]),
    //             [],
    //             new ServerRequest([
    //                 'environment' => ['HTTP_AUTHORIZATION' => 'Bearer ' . $expiredToken],
    //             ]),
    //         ],
    //     ];
    // }

    /**
     * Test `getUser` method.
     *
     * @param array|false|\Exception $expected Expected result.
     * @param array $config Configuration.
     * @param \Cake\Http\ServerRequest $request Request.
     * @return void
     *
     * @dataProvider authenticateProvider
     * @covers ::authenticate()
     * @covers ::getUser()
     * @covers ::getPayload()
     * @covers \BEdita\API\Exception\ExpiredTokenException::__construct()
     */
    // public function testAuthenticate($expected, array $config, ServerRequest $request)
    // {
    //     if ($expected instanceof \Exception) {
    //         $this->expectException(get_class($expected));
    //         $this->expectExceptionMessage($expected->getMessage());
    //         $this->expectExceptionCode($expected->getCode());
    //         // static::assertEquals($expected->getAttributes(), $e->getAttributes());
    //     }

    //     $auth = new JwtAuthenticate(new ComponentRegistry(), $config);

    //     $result = $auth->authenticate($request, new Response());
    //     // } catch (\Exception $e) {
    //     //     $result = $e;
    //     //     static::assertInstanceOf('Exception', $expected);
    //     //     static::assertEquals($expected->getAttributes(), $e->getAttributes());
    //     //     static::assertEquals($expected->getCode(), $e->getCode());
    //     // }
    //     static::assertEquals($expected, $result);
    // }

    /**
     * Test `authenticate()` method
     *
     * @return void
     *
     * @covers ::authenticate()
     */
    public function testAuthenticate(): void
    {
        $auth = new JwtAuthenticate(new ComponentRegistry(), []);
        $data = ['id' => 999];
        $request = ServerRequestFactory::fromGlobals()
            ->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, $data);
        $user = $auth->authenticate($request, new Response());
        static::assertEquals($data, $user);
        static::assertTrue($auth->getConfig('authenticate'));
    }

    /**
     * Test `getPayload()` method
     *
     * @return void
     *
     * @covers ::getPayload()
     */
    public function testGetPayload(): void
    {
        $auth = new JwtAuthenticate(new ComponentRegistry(), []);
        $data = ['gustavo' => 'the best'];
        $request = ServerRequestFactory::fromGlobals()
            ->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, $data);

        $payload = $auth->getPayload($request);
        static::assertEquals($data, $payload);

        // check payload is unchanged
        $request = $request->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, ['key' => 'value']);
        $payload = $auth->getPayload($request);
        static::assertEquals($data, $payload);
    }

    /**
     * Test `getUser()` method
     *
     * @return void
     *
     * @covers ::getUser()
     */
    public function testGetUser(): void
    {
        $auth = new JwtAuthenticate(new ComponentRegistry(), []);
        $data = ['id' => 999];
        $request = new ServerRequest();
        $request = $request->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, $data);
        $user = $auth->getUser($request);
        static::assertEquals($data, $user);
    }

    /**
     * Test `getUser()` method
     *
     * @return void
     *
     * @covers ::getUser()
     */
    public function testGetUserFalse(): void
    {
        $auth = new JwtAuthenticate(new ComponentRegistry(), []);
        $res = $auth->getUser(new ServerRequest());
        static::assertFalse($res);
    }

    /**
     * Test `handleRefreshToken()` method
     *
     * @return void
     *
     * @covers ::handleRefreshToken()
     */
    public function testHandleRefreshToken(): void
    {
        $auth = new JwtAuthenticate(new ComponentRegistry(), []);

        $data = ['id' => 1, 'aud' => 'http://api.example.org', 'sub' => 1];
        $request = new ServerRequest();
        $request = $request->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, $data)
            ->withData('grant_type', 'refresh_token');
        $user = $auth->getUser($request);

        static::assertNotEmpty($user);
    }

    /**
     * Test `checkAudience()` method
     *
     * @return void
     *
     * @covers ::checkAudience()
     */
    public function testCheckAudience(): void
    {
        $auth = new JwtAuthenticate(new ComponentRegistry(), []);
        $request = ServerRequestFactory::fromGlobals();
        $res = $auth->getUser($request);
        static::assertFalse($res);
    }

    /**
     * Test `unauthenticated` method.
     *
     * @return void
     *
     * @expectedException \Cake\Http\Exception\UnauthorizedException
     * @expectedExceptionMessage MyExceptionMessage
     * @covers ::unauthenticated()
     */
    public function testUnauthenticated()
    {
        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authError' => 'MyExceptionMessage',
        ]);

        $auth = new JwtAuthenticate($controller->components(), []);

        $auth->unauthenticated($controller->request, $controller->response);
    }

    /**
     * Test `unauthenticated` method.
     *
     * @return void
     *
     * @expectedException \Cake\Http\Exception\UnauthorizedException
     * @expectedExceptionMessage Invalid audience
     * @covers ::unauthenticated()
     */
    // public function testUnauthenticatedWithInternalErrorMessage()
    // {
    //     $request = new ServerRequest([
    //         'params' => [
    //             'plugin' => 'BEdita/API',
    //             'controller' => 'Login',
    //             'action' => 'login',
    //             '_method' => 'POST',
    //         ],
    //         'environment' => [
    //             'HTTP_AUTHORIZATION' => 'Bearer ' . JWT::encode(['aud' => 'http://example.org'], Security::getSalt()),
    //             'HTTP_HOST' => 'api.example.com',
    //         ],
    //     ]);

    //     $controller = new Controller($request);
    //     $controller->loadComponent('Auth', [
    //         'authError' => 'MyExceptionMessage',
    //     ]);

    //     $auth = new JwtAuthenticate($controller->components(), []);

    //     $result = $auth->authenticate($controller->request, $controller->response);

    //     static::assertFalse($result);

    //     $auth->unauthenticated($controller->request, $controller->response);
    // }
}
