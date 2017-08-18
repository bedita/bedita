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
