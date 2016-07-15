<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @covers \BEdita\API\Controller\AppController
 */
class AppControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
    ];

    /**
     * Data provider for `testContentType` test case.
     *
     * @return array
     */
    public function contentTypeProvider()
    {
        return [
            'json' => [
                200,
                'application/json',
                'application/json',
            ],
            'jsonapi' => [
                200,
                'application/vnd.api+json',
                'application/vnd.api+json',
            ],
            'jsonapiWrongMediaType' => [
                415,
                'application/vnd.api+json',
                'application/vnd.api+json; m=test',
            ],
            'htmlNotAllowed' => [
                406,
                null,
                'text/html,application/xhtml+xml',
                [
                    'debug' => 0,
                    'Accept.html' => 0,
                ],
            ],
            'htmlDebugMode' => [
                200,
                'text/html',
                'text/html,application/xhtml+xml',
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'htmlAccepted' => [
                200,
                'text/html',
                'text/html,application/xhtml+xml',
                [
                    'debug' => 0,
                    'Accept.html' => 1,
                ],
            ],
        ];
    }

    /**
     * Test content type negotiation rules.
     *
     * @param int $expectedCode Expected response code.
     * @param string|null $expectedContentType Expected content type.
     * @param string $accept Request's "Accept" header.
     * @param array|null $config Configuration to be written.
     * @return void
     *
     * @dataProvider contentTypeProvider
     * @covers \BEdita\API\Controller\Component\JsonApiComponent::startup()
     * @covers \BEdita\API\Controller\Component\JsonApiComponent::beforeRender()
     */
    public function testContentType($expectedCode, $expectedContentType, $accept, array $config = null)
    {
        Configure::write($config);

        $this->configRequest([
            'headers' => ['Accept' => $accept],
        ]);

        $this->get('/users');

        $this->assertResponseCode($expectedCode);
        if ($expectedContentType) {
            $this->assertContentType($expectedContentType);
        }
    }
}
