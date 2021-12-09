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

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\OAuth2;
use Cake\Http\Client\Adapter\Stream;
use Cake\Http\Client\Response;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\OAuth2} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\OAuth2
 */
class OAuth2Test extends TestCase
{

    /**
     * Data provider method for `testResponse()`
     *
     * @return array
     */
    public function responseProvider(): array
    {
        return [
            'default' => [
                ['id' => 'gustavo'],
                '{"id":"gustavo"}',
            ],
            'query' => [
                ['id' => 'gustavo'],
                '{"id":"gustavo"}',
                [
                    'mode' => 'query',
                ],
            ],
        ];
    }

    /**
     * Test `response` method
     *
     * @return void
     *
     * @dataProvider responseProvider
     * @covers ::response()
     * @covers ::getQuery()
     * @covers ::getHeaders()
     */
    public function testResponse(array $expected, string $body, array $options = []): void
    {
        $response = new Response([], $body);
        $mock = $this->getMockBuilder(Stream::class)
            ->getMock();
        $mock->method('send')
            ->willReturn([$response]);

        $oauth2 = new OAuth2();
        $oauth2->setConfig('client', ['adapter' => $mock]);
        $data = $oauth2->response('https://oauth2.example.com', 'token-example', $options);

        static::assertEquals($expected, $data);
    }
}
