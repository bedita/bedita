<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase;

use BEdita\API\APIPlugin;
use BEdita\API\App\BaseApplication;
use BEdita\API\Error\ExceptionRenderer;
use BEdita\API\Middleware\AnalyticsMiddleware;
use BEdita\API\Middleware\CorsMiddleware;
use BEdita\API\Middleware\TokenMiddleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Error\Renderer\WebExceptionRenderer;
use Cake\Http\MiddlewareQueue;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\APIPlugin} Test Case.
 *
 * @coversDefaultClass \BEdita\API\API\APIPlugin
 */
class APIPluginTest extends TestCase
{
    /**
     * Data provider for `testBootstrap()`.
     *
     * @return array
     */
    public function bootstrapProvider(): array
    {
        return [
            'conf ok' => [ExceptionRenderer::class],
            'overrider conf' => [WebExceptionRenderer::class],
        ];
    }

    /**
     * Test bootstrap.
     *
     * @param string $exceptionRendererClass The exception renderer class
     * @return void
     * @covers ::bootstrap()
     * @covers ::ensureExceptionRenderer()
     * @dataProvider bootstrapProvider()
     */
    public function testBootstrap(string $exceptionRendererClass): void
    {
        Configure::write('Error.exceptionRenderer', $exceptionRendererClass);
        $app = new class (CONFIG) extends BaseApplication {
        };

        $plugin = new APIPlugin();
        $plugin->bootstrap($app);

        static::assertEquals(ExceptionRenderer::class, Configure::read('Error.exceptionRenderer'));
    }

    /**
     * Test `middleware` method
     *
     * @return void
     * @covers ::middleware()
     */
    public function testMiddleware(): void
    {
        $plugin = new APIPlugin();
        $middlewareQueue = (new MiddlewareQueue())
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')));

        $middlewareQueue = $plugin->middleware($middlewareQueue);
        $middlewareQueue->rewind();

        $expected = [AnalyticsMiddleware::class, CorsMiddleware::class, ErrorHandlerMiddleware::class, TokenMiddleware::class];

        foreach ($middlewareQueue as $actual) {
            static::assertInstanceOf(current($expected), $actual);
            next($expected);
        }
    }
}
