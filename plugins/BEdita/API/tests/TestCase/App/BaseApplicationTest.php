<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\App\Test\TestCase;

use Authentication\AuthenticationService;
use Authorization\AuthorizationServiceInterface;
use BEdita\API\App\BaseApplication;
use BEdita\API\Middleware\BodyParserMiddleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\API\App\Application} Test Case
 *
 * @coversDefaultClass \BEdita\API\App\BaseApplication
 */
class BaseApplicationTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.AuthProviders',
    ];

    /**
     * Test `middleware` method
     *
     * @return void
     * @covers ::middleware()
     */
    public function testMiddleware(): void
    {
        $app = new class (CONFIG) extends BaseApplication {
        };
        $middleware = new MiddlewareQueue();
        $middleware = $app->middleware($middleware);
        $middleware->rewind();

        static::assertInstanceOf(ErrorHandlerMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(RoutingMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(BodyParserMiddleware::class, $middleware->current());
    }

    /**
     * Test `bootstrap` method
     *
     * @return void
     * @covers ::bootstrap()
     * @covers ::bootstrapCli()
     */
    public function testBootstrap(): void
    {
        Configure::write('Plugins', []);
        Configure::write('Queue', ['default' => ['url' => 'null']]);

        $app = new class (CONFIG) extends BaseApplication {
        };
        $app->bootstrap();

        static::assertTrue($app->getPlugins()->has('Migrations'));
        static::assertTrue($app->getPlugins()->has('Authentication'));
        static::assertTrue($app->getPlugins()->has('Authorization'));
        static::assertTrue($app->getPlugins()->has('Cake/Queue'));
        Configure::delete('Queue');
    }

    /**
     * `testConfigPlugins` data provider
     *
     * @return array
     */
    public function configPluginsProvider(): array
    {
        return [
            'simple' => [
                true,
                [
                    'Bake',
                ],
            ],
            'empty' => [
                false,
                [],
            ],
            'options' => [
                true,
                [
                    'Bake' => ['bootstrap' => true, 'ignoreMissing' => true],
                ],
            ],
            'debug no' => [
                false,
                [
                    'Bake' => ['debugOnly' => true],
                ],
                false,
            ],
            'debug yes' => [
                true,
                [
                    'Bake' => ['debugOnly' => true],
                ],
                true,
            ],

        ];
    }

    /**
     * Test `addConfigPlugins` method using `Bake` Plugin
     *
     * @return void
     * @covers ::addConfigPlugins()
     * @covers ::addConfigPlugin()
     * @dataProvider configPluginsProvider
     */
    public function testConfigPlugins(bool $expected, array $config, bool $debug = false): void
    {
        $currDebug = Configure::read('debug');

        Configure::write('Plugins', $config);
        Configure::write('debug', $debug);

        $app = new class (CONFIG) extends BaseApplication {
        };
        $app->getPlugins()->remove('Bake');

        $app->addConfigPlugins();

        static::assertEquals($expected, $app->getPlugins()->has('Bake'));

        Configure::write('debug', $currDebug);
    }

    /**
     * Test `getAuthorizationService` method
     *
     * @return void
     * @covers ::getAuthorizationService()
     */
    public function testGetAuthorizationService(): void
    {
        $app = new class (CONFIG) extends BaseApplication {
        };

        $service = $app->getAuthorizationService(new ServerRequest());
        static::assertInstanceOf(AuthorizationServiceInterface::class, $service);
        static::assertNotNull($service);
    }

    /**
     * Data provider for `testGetAuthenticationService` method.
     *
     * @return array
     */
    public function authenticationServiceProvider(): array
    {
        return [
            'default' => [
                [
                    'Jwt',
                ],
                [
                    'url' => 'home',
                ],
            ],
            'password' => [
                [
                    'Form',
                    'Password',
                ],
                [
                    'post' => [
                        'grant_type' => 'password',
                    ],
                ],
            ],
            'refresh 1' => [
                [
                    'Jwt',
                    'JwtSubject',
                ],
                [
                    'post' => [
                        'grant_type' => 'refresh_token',
                    ],
                ],
            ],
            'refresh 2' => [
                [
                    'RenewClientCredentials',
                    'RenewClientCredentialsJwtSubject',
                ],
                [
                    'post' => [
                        'grant_type' => 'refresh_token',
                    ],
                ],
            ],
            'client credentials' => [
                [
                    'Application',
                    'Application',
                ],
                [
                    'post' => [
                        'grant_type' => 'client_credentials',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `getAuthenticationService` method
     *
     * @param array $expected Expected result.
     * @param array $config Request configuration.
     * @return void
     * @dataProvider authenticationServiceProvider
     * @covers ::getAuthenticationService()
     * @covers ::loginAuthentication()
     * @covers ::loadAuthProviders()
     * @covers ::passwordGrantType()
     * @covers ::refreshTokenGrantType()
     * @covers ::clientCredentialsGrantType()
     */
    public function testGetAuthenticationService(array $expected, array $config): void
    {
        $app = new class (CONFIG) extends BaseApplication {
        };
        $defaultConf = [
            'base' => '/',
            'url' => 'auth',
        ];
        $request = new ServerRequest(array_merge($defaultConf, $config));
        $service = $app->getAuthenticationService($request);

        static::assertInstanceOf(AuthenticationService::class, $service);
        static::assertNotNull($service);

        static::assertTrue($service->authenticators()->has($expected[0]));
        if (!empty($expected[1])) {
            static::assertTrue($service->identifiers()->has($expected[1]));
        }
    }

    /**
     * Data provider for `testLoadAuthProviders` method.
     *
     * @return array
     */
    public function loadAuthProvidersProvider(): array
    {
        return [
            'ok' => [
                [
                    'OAuth2',
                    'OAuth2',
                ],
                'example',
            ],
            'disabled' => [
                new UnauthorizedException(),
                'linkedout',
            ],
        ];
    }

    /**
     * Test `loadAuthProviders` method
     *
     * @param array|\Exception $expected Expected result.
     * @param string $authProvider Auth provider name.
     * @return void
     * @dataProvider loadAuthProvidersProvider
     * @covers ::loadAuthProviders()
     * @covers ::loginAuthentication()
     */
    public function testLoadAuthProviders($expected, string $authProvider): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $app = new class (CONFIG) extends BaseApplication {
        };
        $config = [
            'base' => '/',
            'url' => 'auth',
            'post' => [
                'auth_provider' => $authProvider,
            ],
        ];
        $request = new ServerRequest($config);
        $service = $app->getAuthenticationService($request);

        static::assertInstanceOf(AuthenticationService::class, $service);
        static::assertNotNull($service);
        static::assertTrue($service->authenticators()->has($expected[0]));
        static::assertTrue($service->identifiers()->has($expected[1]));
    }
}
