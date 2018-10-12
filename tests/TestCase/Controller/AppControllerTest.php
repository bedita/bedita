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
use BEdita\Core\State\CurrentApplication;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotAcceptableException;

/**
 * @coversDefaultClass \BEdita\API\Controller\AppController
 */
class AppControllerTest extends IntegrationTestCase
{

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

    /**
     * Data provider for `testGetApplication` test case.
     *
     * @return array
     */
    public function getApplicationProvider()
    {
        return [
            'standard' => [
                1,
                [
                    'HTTP_X_API_KEY' => API_KEY,
                ],
            ],
            'invalid API key' => [
                new ForbiddenException('Invalid API key'),
                [
                    'HTTP_X_API_KEY' => 'this API key is invalid!',
                ],
            ],
            'missing API key' => [
                new ForbiddenException('Missing API key'),
                [],
                [],
                true,
            ],
            'anonymous application' => [
                null,
                [],
            ],
            'query string api key' => [
                1,
                [],
                [
                    'api_key' => API_KEY,
                ],
            ],
            'query string failure' => [
                new ForbiddenException('Invalid API key'),
                [],
                [
                    'api_key' => 'this API key is invalid!',
                ]
            ],
        ];
    }

    /**
     * Test getting application from request headers.
     *
     * @param int|\Exception $expected Expected application ID.
     * @param array $environment Request headers.
     * @param array $query Request query strings.
     * @param bool $blockAnonymous Block anonymous apps flag.
     * @return void
     *
     * @dataProvider getApplicationProvider()
     * @covers ::getApplication()
     */
    public function testGetApplication($expected, array $environment, array $query = [], $blockAnonymous = false)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        Configure::write('Security.blockAnonymousApps', $blockAnonymous);
        CurrentApplication::getInstance()->set(null);
        $environment += ['HTTP_ACCEPT' => 'application/json'];
        $request = new ServerRequest(compact('environment', 'query'));

        $controller = new AppController($request);
        $controller->dispatchEvent('Controller.initialize');

        static::assertEquals($expected, CurrentApplication::getApplicationId());
    }
}
