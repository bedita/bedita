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
use BEdita\API\Middleware\TokenMiddleware;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

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
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
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

        Router::fullBaseUrl('http://example.org');
        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

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
     * @covers ::getUser()
     */
    public function testHandleRefreshToken(): void
    {
        $auth = new JwtAuthenticate(new ComponentRegistry(), []);

        $data = ['sub' => 1, 'aud' => 'http://example.org/auth'];
        $request = new ServerRequest([
            'params' => [
                'plugin' => 'BEdita/API',
                'controller' => 'Login',
                'action' => 'login',
                '_method' => 'POST',
            ],
        ]);
        $request = $request->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, $data)
            ->withData('grant_type', 'refresh_token');
        $user = $auth->authenticate($request, new Response());

        static::assertNotEmpty($user);
        static::assertArrayHasKey('username', $user);
        static::assertEquals(1, $user['id']);
    }

    /**
     * Test `handleRefreshToken()` method on renew client credentials case
     *
     * @return void
     *
     * @covers ::handleRefreshToken()
     */
    public function testRenewClientCredentials(): void
    {
        $data = ['sub' => null, 'aud' => 'http://example.org/auth', 'app' => ['id' => 1]];
        $request = new ServerRequest([
            'params' => [
                'plugin' => 'BEdita/API',
                'controller' => 'Login',
                'action' => 'login',
                '_method' => 'POST',
            ],
        ]);
        $controller = new Controller($request);
        $controller->loadComponent('Auth');
        $auth = new JwtAuthenticate(new ComponentRegistry($controller), []);
        $request = $request->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, $data)
            ->withData('grant_type', 'refresh_token');
        $result = $auth->authenticate($request, new Response());

        static::assertFalse($result);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function checkAudienceProvider(): array
    {
        return [
            'ok' => [
                true,
                [
                    'sub' => 1,
                    'aud' => 'http://example.org/auth',
                ],
            ],
            'bad aud' => [
                new UnauthorizedException('Invalid audience http://gustavo.net'),
                [
                    'aud' => 'http://gustavo.net',
                ],
            ],
            'no aud' => [
                new \DomainException('Missing audience'),
                [
                    'some' => 'value',
                ],
            ],
        ];
    }

    /**
     * Test `checkAudience()` method
     *
     * @return void
     *
     * @dataProvider checkAudienceProvider
     * @covers ::checkAudience()
     */
    public function testCheckAudience($expected, array $data): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $request = new ServerRequest([
            'params' => [
                'plugin' => 'BEdita/API',
                'controller' => 'Login',
                'action' => 'login',
                '_method' => 'POST',
            ],
        ]);
        $controller = new Controller($request);
        $controller->loadComponent('Auth');
        $auth = new JwtAuthenticate(new ComponentRegistry($controller), []);
        $request = $request->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, $data)
            ->withData('grant_type', 'refresh_token');

        $result = $auth->authenticate($request, new Response());
        if ($expected) {
            static::assertNotEmpty($result);
        } else {
            static::assertFalse($result);
        }
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
    public function testUnauthenticatedWithInternalErrorMessage()
    {
        $request = new ServerRequest([
            'params' => [
                'plugin' => 'BEdita/API',
                'controller' => 'Login',
                'action' => 'login',
                '_method' => 'POST',
            ],
            'environment' => [
                'HTTP_HOST' => 'api.example.com',
            ],
        ]);

        $request = $request->withAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, ['aud' => 'http://example.org'])
            ->withData('grant_type', 'refresh_token');

        $controller = new Controller($request);
        $controller->loadComponent('Auth', [
            'authError' => 'MyExceptionMessage',
        ]);

        $auth = new JwtAuthenticate($controller->components(), []);

        $result = $auth->authenticate($controller->request, $controller->response);

        static::assertFalse($result);

        $auth->unauthenticated($controller->request, $controller->response);
    }
}
