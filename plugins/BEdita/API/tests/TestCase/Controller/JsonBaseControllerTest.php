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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\Controller\JsonBaseController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * BEdita\API\Controller\JsonBaseController Test Case
 *
 * @coversDefaultClass  \BEdita\API\Controller\JsonBaseController
 */
class JsonBaseControllerTest extends TestCase
{
    /**
     * Test `initialize()` method
     *
     * @covers ::initialize()
     * @return void
     */
    public function testInitialize(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'input' => true,
            ],
        ]);

        $controller = new class ($request) extends JsonBaseController {
        };

        static::assertEquals('Json', $controller->RequestHandler->getConfig('viewClassMap.json'));
        static::assertEquals(['json_decode', true], $controller->RequestHandler->getConfig('inputTypeMap.json'));
        static::assertFalse($controller->components()->has('JsonApi'));
        static::assertEquals('Json', $controller->viewBuilder()->getClassName());
    }
}
