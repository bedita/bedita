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
namespace BEdita\API\Test\TestCase\Middleware;

use BEdita\API\Middleware\AnalyticsMiddleware;
use Cake\Http\Response;
use Cake\Http\ServerRequestFactory;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \BEdita\API\Middleware\AnalyticsMiddleware} Test Case
 *
 * @coversDefaultClass \BEdita\API\Middleware\AnalyticsMiddleware
 */
class AnalyticsMiddlewareTest extends TestCase
{
    /**
     * Test __invoke method response
     *
     * @return void
     *
     * @covers ::__invoke()
     * @covers ::__construct()
     */
    public function testAnalytics()
    {
        $server = [
            'REQUEST_URI' => '/home',
            'REQUEST_METHOD' => 'GET',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_ORIGIN' => 'http://api.example.com',
        ];

        $request = ServerRequestFactory::fromGlobals($server);
        $response = new Response();
        $next = function ($req, $res) {
            return $res;
        };
        Log::drop('analytics');
        $middleware = new AnalyticsMiddleware();
        $middleware($request, $response, $next);

        static::assertNotEmpty($middleware->getData());
    }

    /**
     * Data provider for `testCallback`
     *
     * @return void
     */
    public function callbackProvider()
    {
        return [
            'empty' => [
                'undefined',
                [],
            ],
            'simple' => [
                function (ServerRequestInterface $request, Response $response) {
                    return 'result';
                },
                ['result'],
            ],
        ];
    }

    /**
     * Test callback methods
     *
     * @return void
     *
     * @dataProvider callbackProvider
     * @covers ::registerCallback()
     * @covers ::readCallbackData()
     */
    public function testCallback($callback, $expected)
    {
        $server = [
            'REQUEST_URI' => '/home',
            'REQUEST_METHOD' => 'GET',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_ORIGIN' => 'http://api.example.com',
        ];

        $request = ServerRequestFactory::fromGlobals($server);
        $response = new Response();
        $next = function ($req, $res) {
            return $res;
        };

        AnalyticsMiddleware::registerCallback($callback);
        $middleware = new AnalyticsMiddleware();

        $middleware($request, $response, $next);

        $data = $middleware->getData();
        static::assertNotEmpty($data);
        static::assertArrayHasKey('x', $data);
        static::assertEquals($data['x'], $expected);
    }
}
