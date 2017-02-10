<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Auth;

use BEdita\API\Auth\AnonymousAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\API\Auth\AnonymousAuthenticate
 */
class AnonymousAuthenticateTest extends TestCase
{

    /**
     * Test `authenticate` method.
     *
     * @return void
     */
    public function testAuthenticate()
    {
        $expected = [
            '_anonymous' => true,
        ];

        $request = new Request();
        $response = new Response();

        $auth = new AnonymousAuthenticate(new ComponentRegistry());
        $result = $auth->authenticate($request, $response);

        static::assertEquals($expected, $result);
    }
}
