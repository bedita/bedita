<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Middleware;

use BEdita\API\Middleware\PrimaryDatabaseMiddleware;
use BEdita\API\Test\Utility\TestRequestHandler;
use BEdita\Core\Database\Connection;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\Middleware\PrimaryDatabaseMiddleware} Test Case
 *
 * @coversDefaultClass \BEdita\API\Middleware\PrimaryDatabaseMiddleware
 */
class PrimaryDatabaseMiddlewareTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Connection::setSticky(false);

        parent::tearDown();
    }

    /**
     * Data provider for `testProcess`.
     *
     * @return array
     */
    public function processProvider(): array
    {
        return [
            'GET' => ['GET', false],
            'HEAD' => ['HEAD', false],
            'POST' => ['POST', true],
            'PATCH' => ['PATCH', true],
            'PUT' => ['PUT', true],
            'DELETE' => ['DELETE', true],
            'OPTIONS' => ['OPTIONS', false],
        ];
    }

    /**
     * Test that only GET, HEAD and OPTIONS requests may use the replica.
     *
     * @param string $method Request method.
     * @param bool $expected Expected sticky flag.
     * @return void
     * @dataProvider processProvider()
     * @covers ::process()
     */
    public function testProcess(string $method, bool $expected): void
    {
        Connection::setSticky(false);
        $request = new ServerRequest(['environment' => ['REQUEST_METHOD' => $method]]);

        $response = (new PrimaryDatabaseMiddleware())->process($request, new TestRequestHandler());

        static::assertSame(200, $response->getStatusCode());
        static::assertSame($expected, Connection::isSticky());
    }
}
