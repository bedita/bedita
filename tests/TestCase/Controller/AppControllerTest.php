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
use Cake\Datasource\ConnectionManager;
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
        'plugin.BEdita/Core.roles',
    ];

    /**
     * The configuration to restore at the end of every unit test
     *
     * @var array
     */
    protected $backupConf = [];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->backupConf = [
            'debug' => Configure::read('debug'),
            'Accept.html' => Configure::read('Accept.html'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        foreach ($this->backupConf as $key => $val) {
            Configure::write($key, $val);
        }

        parent::tearDown();
    }

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
                'application/json; charset=UTF-8',
                'application/json',
                '/roles',
            ],
            'jsonapi' => [
                200,
                'application/vnd.api+json',
                'application/vnd.api+json',
                '/roles',
            ],
            'jsonapiWrongMediaType' => [
                415,
                'application/vnd.api+json',
                'application/vnd.api+json; m=test',
                '/roles',
            ],
            'htmlNotAllowed' => [
                406,
                null,
                'text/html,application/xhtml+xml',
                '/roles',
                [
                    'debug' => 0,
                    'Accept.html' => 0,
                ],
            ],
            'htmlDebugMode' => [
                200,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/roles',
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'htmlAccepted' => [
                200,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/roles',
                [
                    'debug' => 0,
                    'Accept.html' => 1,
                ],
            ],
            'notFoundRouteJson' => [
                404,
                'application/json; charset=UTF-8',
                'application/json',
                '/find_me_if_you_can',
            ],
            'notFoundRouteJsonapi' => [
                404,
                'application/vnd.api+json',
                'application/vnd.api+json',
                '/find_me_if_you_can',
            ],
            'notFoundRouteHtmlDebug' => [
                404,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/find_me_if_you_can',
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'notFoundRouteHtmlAccepted' => [
                404,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/find_me_if_you_can',
                [
                    'debug' => 0,
                    'Accept.html' => 1,
                ],
            ],
            // Should it be 406?
            'notFoundRouteHtmlNotAccepted' => [
                404,
                'application/vnd.api+json',
                'text/html,application/xhtml+xml',
                '/find_me_if_you_can',
                [
                    'debug' => 0,
                    'Accept.html' => 0,
                ],
            ],
            'notFoundRecordJson' => [
                404,
                'application/json; charset=UTF-8',
                'application/json',
                '/roles/99999999',
            ],
            'notFoundRecordJsonapi' => [
                404,
                'application/vnd.api+json',
                'application/vnd.api+json',
                '/roles/99999999',
            ],
            'notFoundRecordHtmlDebug' => [
                404,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/roles/99999999',
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'notFoundRecordHtmlAccepted' => [
                404,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/roles/99999999',
                [
                    'debug' => 0,
                    'Accept.html' => 1,
                ],
            ],
            'notFoundRecordHtmlNotAccepted' => [
                406,
                'application/vnd.api+json',
                'text/html,application/xhtml+xml',
                '/roles/99999999',
                [
                    'debug' => 0,
                    'Accept.html' => 0,
                ],
            ],
            'internalErrorJson' => [
                500,
                'application/json; charset=UTF-8',
                'application/json',
                '/roles',
            ],
            'internalErrorJsonapi' => [
                500,
                'application/vnd.api+json',
                'application/vnd.api+json',
                '/roles',
            ],
            'internalErrorHtmlDebug' => [
                500,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/roles',
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'internalErrorHtmlAccepted' => [
                500,
                'text/html; charset=UTF-8',
                'text/html,application/xhtml+xml',
                '/roles',
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
     * @covers \BEdita\API\Error\ExceptionRenderer::render()
     */
    public function testContentType($expectedCode, $expectedContentType, $accept, $endpoint, array $config = null)
    {
        Configure::write($config);

        // change db connection to simulate db connection fails
        if ($expectedCode == 500) {
            $connection = ConnectionManager::get('default');
            $dbConf = $connection->config();
            $dbConf['database'] = '__fail_db_connection';
            unset($dbConf['name']);
            ConnectionManager::config('__fail_db_connection', $dbConf);
            ConnectionManager::alias('__fail_db_connection', 'default');
        }

        // use $_SERVER array to assure using the right HTTP_ACCEPT header also if request
        // is recreated from globals as in \Cake\Error\ExceptionRenderer::_getController()
        $_SERVER['HTTP_ACCEPT'] = $accept;

        $this->get($endpoint);

        $this->assertResponseCode($expectedCode);
        if ($expectedContentType) {
            $this->assertContentType($expectedContentType);

            if (strpos($expectedContentType, 'text/html') !== false) {
                $this->assertLayout('html');
                $this->assertResponseContains('<!DOCTYPE html>');
                if ($expectedCode < 400) {
                    $this->assertTemplate('html');
                } else {
                    $this->assertTemplate('error');
                }
            } else {
                $this->assertResponseNotContains('<!DOCTYPE html>');
            }
        }

        // restore db connection
        if ($expectedCode == 500) {
            ConnectionManager::alias('test', 'default');
            ConnectionManager::drop('__fail_db_connection');
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

    /**
     * Test API meta info header.
     *
     * @return void
     */
    public function testMetaInfo()
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->_sendRequest('/home', 'HEAD');

        $this->assertHeader('X-BEdita-Version', Configure::read('BEdita.version'));
    }
}
