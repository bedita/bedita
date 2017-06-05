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

use BEdita\API\Middleware\HtmlMiddleware;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\API\Middleware\HtmlMiddleware
 */
class HtmlMiddlewareTest extends TestCase
{

    /**
     * Test execution with a non-HTML request.
     *
     * @return void
     */
    public function testJsonRequest()
    {
        $body = 'hello';

        $middleware = new HtmlMiddleware();

        $request = (new Request())->withHeader('Accept', 'application/json');
        $response = new Response();
        $next = function (Request $request, Response $response) use ($body) {
            return $response->withStringBody($body);
        };

        $result = $middleware($request, $response, $next);

        static::assertSame($body, (string)$result->getBody());
    }

    /**
     * Test execution with a non-JSON response.
     *
     * @return void
     */
    public function testInnerHtmlResponse()
    {
        $body = 'hello';

        $middleware = new HtmlMiddleware();

        $request = (new Request())->withHeader('Accept', 'text/html');
        $response = new Response();
        $next = function (Request $request, Response $response) use ($body) {
            static::assertSame('application/vnd.api+json', $request->getHeaderLine('Accept'));

            return $response
                ->withType('html')
                ->withStringBody($body);
        };

        $result = $middleware($request, $response, $next);

        static::assertContains('text/html', $result->getHeaderLine('Content-Type'));
        static::assertSame($body, (string)$result->getBody());
    }

    /**
     * Test execution.
     *
     * @return void
     */
    public function testResponse()
    {
        $body = json_encode(['meta' => ['gustavo' => 'supporto']]);

        $middleware = new HtmlMiddleware();

        $request = (new Request())->withHeader('Accept', 'text/html');
        $response = new Response();
        $next = function (Request $request, Response $response) use ($body) {
            static::assertSame('application/vnd.api+json', $request->getHeaderLine('Accept'));

            return $response
                ->withType('jsonapi')
                ->withStringBody($body);
        };

        $result = $middleware($request, $response, $next);

        static::assertContains('text/html', $result->getHeaderLine('Content-Type'));
        static::assertContains('<!DOCTYPE html>', (string)$result->getBody());
        static::assertContains($body, (string)$result->getBody());
    }
}
