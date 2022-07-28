<?php
declare(strict_types=1);

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

namespace BEdita\API\Test\TestCase\Authenticator;

use BEdita\API\Authenticator\DynamicFormAuthenticator;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \BEdita\API\Authenticator\DynamicFormAuthenticator} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Authenticator\DynamicFormAuthenticator
 */
class DynamicFormAuthenticatorTest extends TestCase
{
    /**
     * Test `_getData` method
     *
     * @return void
     * @covers ::_getData()
     */
    public function testGetData(): void
    {
        $authenticator = new class extends DynamicFormAuthenticator {
            public function __construct(array $config = [])
            {
                $this->setConfig($config);
            }

            // make method public in
            public function requestData(ServerRequestInterface $request)
            {
                return $this->_getData($request);
            }
        };

        $request = new ServerRequest([
            'post' => [
                'username' => 'gustavo',
                'password' => 'supporto',
                'otherprop' => 'othervalue',
            ],
        ]);

        $result = $authenticator->requestData($request);
        $expected = [
            'username' => 'gustavo',
            'password' => 'supporto',
        ];
        static::assertEquals($expected, $result);
    }
}
