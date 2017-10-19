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
use Cake\Event\Event;
use Cake\Event\EventManager;
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
     * Data provider for `testCustomData`
     *
     * @return void
     */
    public function customProvider()
    {
        return [
            'empty' => [
                function () {
                    return null;
                },
                []
            ],
            'simple' => [
                function (Event $e, ServerRequestInterface $request, Response $response) {
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
     * @dataProvider customProvider
     * @covers ::readCustomData()
     */
    public function testCustomData($callback, $expected)
    {
        EventManager::instance()->on('Analytics.custom', $callback);

        $request = ServerRequestFactory::fromGlobals();
        $response = new Response();
        $next = function ($req, $res) {
            return $res;
        };
        $middleware = new AnalyticsMiddleware();
        $middleware($request, $response, $next);

        $data = $middleware->getData();
        static::assertNotEmpty($data);
        static::assertArrayHasKey('x', $data);
        static::assertEquals($data['x'], $expected);

        EventManager::instance()->off('Analytics.custom', $callback);
    }

    /**
     * Data provider for `testCallback`
     *
     * @return void
     */
    public function errorCodeProvider()
    {
        return [
            'empty' => [
                '{"error":{"status":"401","title":"Expired token","code":"be_token_expired"}}',
                401,
                'be_token_expired',
            ],
            'simple' => [
                '{"error":{"status":"404","title":"Not Found"}}',
                404,
                null,
            ],
            'data' => [
                '{"data":{}}',
                200,
                null,
            ],
        ];
    }

    /**
     * Test getAppErrorCode() method
     *
     * @return void
     *
     * @dataProvider errorCodeProvider
     * @covers ::getAppErrorCode()
     */
    public function testAppErrorCode($body, $status, $expected)
    {
        $request = ServerRequestFactory::fromGlobals();
        $response = new Response();
        $response = $response->withStatus($status)->withStringBody($body);
        $next = function ($req, $res) {
            return $res;
        };

        $middleware = new AnalyticsMiddleware();
        $middleware($request, $response, $next);

        $data = $middleware->getData();
        static::assertNotEmpty($data);
        static::assertArrayHasKey('c', $data);
        static::assertEquals($data['c'], $expected);
    }

}
