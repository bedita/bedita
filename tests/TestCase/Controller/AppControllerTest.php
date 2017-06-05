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

use BEdita\API\Controller\AppController;
use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\NotAcceptableException;

/**
 * @coversDefaultClass \BEdita\API\Controller\AppController
 */
class AppControllerTest extends IntegrationTestCase
{

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
     * @covers ::apiKeyCheck()
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
     *
     * @covers ::initialize()
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

    /**
     * Data provider for `testCheckAccept` test case.
     *
     * @return array
     */
    public function checkAcceptProvider()
    {
        return [
            'ok' => [
                true,
                'application/vnd.api+json',
            ],
            'error (dramatic music)' => [
                new NotAcceptableException('Bad request content type "gustavo/supporto"'),
                'gustavo/supporto',
            ],
        ];
    }

    /**
     * Test accepted content types in `beforeFilter()` method.
     *
     * @param true|\Exception $expected Expected success.
     * @param string $accept Value of "Accept" header.
     * @return void
     *
     * @dataProvider checkAcceptProvider
     * @covers ::beforeFilter()
     */
    public function testCheckAccept($expected, $accept)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $request = new ServerRequest([
            'environment' => [
                'HTTP_ACCEPT' => $accept,
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        $controller = new AppController($request);

        $controller->dispatchEvent('Controller.initialize');

        static::assertTrue($expected);
    }
}
