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

    /**
     * Data provider for `testApiKey` test case.
     *
     * @return array
     */
    public function apiKeysProvider()
    {
        return [
            'apiKeyMissing' => [
                403,
                [
                    'eqe12131231231231412414' => [
                        'origin' => '*',
                    ],
                ],
            ],
            'apiKeyOk' => [
                200,
                [
                    'eqe12131231231231412414' => [
                        'origin' => '*',
                    ],
                ],
                'eqe12131231231231412414',
            ],
            'noApiKey' => [
                200,
                [],
            ],
            'originOk' => [
                200,
                [
                    'eqe12131231231231412414' => [
                        'origin' => 'example.com',
                    ],
                ],
                'eqe12131231231231412414',
                'example.com'
            ],
            'originKo' => [
                403,
                [
                    'eqe12131231231231412414' => [
                        'origin' => 'example.com',
                    ],
                ],
                'eqe12131231231231412414',
                'otherdomain.com'
            ],
        ];
    }

    /**
     * Test API KEY check rules.
     *
     * @param int $expectedCode Expected response code.
     * @param array $apiKeyCfg API KEY configuration.
     * @param string|null $apiKeyReq API KEY in request header.
     * @param string|null $origin Request's "Origin" header.
     * @return void
     *
     * @dataProvider apiKeysProvider
     */
    public function testApiKeys($expectedCode, $apiKeyCfg, $apiKeyReq = null, $origin = null)
    {
        Configure::write('ApiKeys', $apiKeyCfg);

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/vnd.api+json',
                'Origin' => $origin,
                'X-Api-Key' => $apiKeyReq,
            ]
        ]);

        $this->get('/home');

        $this->assertResponseCode($expectedCode);
    }
}
