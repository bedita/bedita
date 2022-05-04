<?php
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

namespace BEdita\Core\Test\TestCase\Middleware;

use BEdita\Core\Middleware\BodyParserMiddleware;
use BEdita\Core\Test\Utility\TestRequestHandler;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Middleware\BodyParserMiddleware} Test Case.
 *
 * @covers \BEdita\Core\Middleware\BodyParserMiddleware
 */
class BodyParserMiddlewareTest extends TestCase
{
    /**
     * Test constructor.
     *
     * @return void
     * @covers ::__construct()
     */
    public function testConstruct()
    {
        $middleware = new BodyParserMiddleware();
        $parsers = $middleware->getParsers();
        static::assertArrayHasKey('application/vnd.api+json', $parsers);
        static::assertArrayHasKey('application/x-www-form-urlencoded', $parsers);

        $middleware = new BodyParserMiddleware(['json' => false, 'form' => false]);
        $parsers = $middleware->getParsers();
        static::assertArrayNotHasKey('application/vnd.api+json', $parsers);
        static::assertArrayNotHasKey('application/x-www-form-urlencoded', $parsers);
    }

    /**
     * Data provider for `testDecodeForm` method.
     *
     * @return array
     */
    public function decodeFormProvider(): array
    {
        return [
            'empty' => [
                [],
                '',
            ],
            'simple' => [
                ['name' => 'Gustavo', 'surname' => 'Supporto'],
                'name=Gustavo&surname=Supporto',
            ],
        ];
    }

    /**
     * Test `decodeForm` method
     *
     * @param array $expected Expected request data array
     * @param string $input Request body
     * @return void
     * @covers ::decodeForm()
     * @dataProvider decodeFormProvider
     */
    public function testDecodeForm(array $expected, string $input): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
                'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            ],
            'input' => $input,
        ]);
        $handler = new TestRequestHandler(function ($req) use ($expected) {
            $this->assertEquals($expected, $req->getParsedBody());

            return new Response();
        });
        $parser = new BodyParserMiddleware();
        $parser->process($request, $handler);
    }
}
