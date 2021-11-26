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
namespace BEdita\API\Test\TestCase\Middleware;

use BEdita\API\Middleware\TokenMiddleware;
use BEdita\Core\State\CurrentApplication;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Response;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * {@see \BEdita\API\Middleware\TokenMiddleware} Test Case
 *
 * @coversDefaultClass \BEdita\API\Middleware\TokenMiddleware
 */
class TokenMiddlewareTest extends TestCase
{

    public function setUp()
    {
        CurrentApplication::setApplication(null);
    }

    /**
     * Data provider for testInvoke() method.
     *
     * @return array
     */
    public function invokeProvider(): array
    {
        return [
            'old style' => [
                [
                    'app' => ['id' => 1],
                    'payload' => ['someData' => 'someValue'],
                ],
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . JWT::encode(['someData' => 'someValue'], Security::getSalt()),
                    'HTTP_X_API_KEY' => API_KEY,
                ],
            ],
            'new style' => [
                [
                    'app' => ['id' => 1],
                    'payload' => ['app' => (object)['id' => 1, 'name' => 'one']],
                ],
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . JWT::encode(['app' => ['id' => 1, 'name' => 'one']], Security::getSalt()),
                ],
            ],
            'token query' => [
                [
                    'payload' => ['someData' => 'someValue'],
                ],
                [],
                [
                    'token' => JWT::encode(['someData' => 'someValue'], Security::getSalt()),
                ],
            ],
            'invalid API key' => [
                new ForbiddenException('Invalid API key'),
                [
                    'HTTP_X_API_KEY' => 'this API key is invalid!',
                ],
            ],
            'invalid query API key' => [
                new ForbiddenException('Invalid API key'),
                [],
                [
                    'api_key' => 'gustavo',
                ],
            ],
            'missing API key' => [
                new ForbiddenException('Missing API key'),
                [],
                [],
                true,
            ],
            'api API query' => [
                [
                    'app' => ['id' => 1],
                    'payload' => null,
                ],
                [],
                [
                    'api_key' => API_KEY,
                ],
            ],
            'no token' => [
                [
                    'payload' => null,
                ],
                [
                ],
            ],
        ];
    }

    /**
     * Test middleware invocation.
     *
     * @return void
     *
     * @dataProvider invokeProvider
     *
     * @covers ::__invoke()
     * @covers ::readApplication()
     * @covers ::getToken()
     * @covers ::applicationFromApiKey()
     */
    public function testInvoke($expected, array $server, ?array $query = null, bool $blockAnonymous = false): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        Configure::write('Security.blockAnonymousApps', $blockAnonymous);

        $request = ServerRequestFactory::fromGlobals($server, $query);
        $response = new Response();
        $next = function ($req, $res) {
            return $req;
        };
        $middleware = new TokenMiddleware();
        /** @var \Zend\Diactoros\ServerRequest $result */
        $result = $middleware($request, $response, $next);

        $app = CurrentApplication::getApplication();
        if (empty($expected['app'])) {
            static::assertNull($app);
        } else {
            $id = Hash::get($expected, 'app.id');
            static::assertEquals($app->id, $id);
        }

        $payload = $result->getAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, null);
        $expectedPayload = Hash::get($expected, 'payload');
        static::assertEquals($expectedPayload, $payload);
    }

    /**
     * Test default behavior on missing 'Security.blockAnonymousApps' key
     *
     * @return void
     * @coversNothing
     */
    public function testGetApplicationDefault()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Missing API key');

        Configure::delete('Security.blockAnonymousApps');

        $middleware = new TokenMiddleware();
        $middleware(
            ServerRequestFactory::fromGlobals(),
            new Response(),
            null
        );
    }
}