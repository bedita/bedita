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

use BEdita\App\Application;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\App\Application} Test Case
 *
 * @coversDefaultClass \BEdita\App\Application
 */
class BaseApplicationTest extends TestCase
{
    /**
     * Test `middleware` method
     *
     * @return void
     *
     * @covers ::middleware
     */
    public function testMiddleware(): void
    {
        $app = new Application(CONFIG);
        $middleware = new MiddlewareQueue();
        $middleware = $app->middleware($middleware);

        static::assertInstanceOf(ErrorHandlerMiddleware::class, $middleware->get(0));
        static::assertInstanceOf(RoutingMiddleware::class, $middleware->get(1));
    }

    /**
     * Test `bootstrap` method
     *
     * @return void
     *
     * @covers ::bootstrap()
     * @covers ::bootstrapCli()
     */
    public function testBootstrap()
    {
        Configure::write('Plugins', []);
        $app = new Application(CONFIG);
        $app->bootstrap();

        static::assertTrue($app->getPlugins()->has('BEdita/Core'));
        static::assertTrue($app->getPlugins()->has('BEdita/API'));
        static::assertTrue($app->getPlugins()->has('Migrations'));
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
            ]

        ];
    }

    /**
     * Test `addConfigPlugins` method using `Bake` Plugin
     *
     * @return void
     *
     * @covers ::addConfigPlugins()
     * @covers ::addConfigPlugin()
     * @dataProvider configPluginsProvider
     */
    public function testConfigPlugins(bool $expected, array $config, bool $debug = false)
    {
        $currDebug = Configure::read('debug');

        Configure::write('Plugins', $config);
        Configure::write('debug', $debug);

        $app = new Application(CONFIG);
        $app->getPlugins()->remove('Bake');

        $app->addConfigPlugins();

        static::assertEquals($expected, $app->getPlugins()->has('Bake'));

        Configure::write('debug', $currDebug);
    }
}
