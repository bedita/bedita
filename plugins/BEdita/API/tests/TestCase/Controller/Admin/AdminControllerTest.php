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

namespace BEdita\API\Test\TestCase\Controller\Admin;

use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\Admin\AdminController
 */
class AdminControllerTest extends IntegrationTestCase
{

    /**
     * Test unauthorized response for unauthenticated calls.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testUnauthenticated()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/applications',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '401',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/admin/applications');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(401);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayNotHasKey('data', $result);
        static::assertArrayHasKey('links', $result);
        static::assertArrayHasKey('error', $result);
        static::assertEquals($expected['links'], $result['links']);
        static::assertArraySubset($expected['error'], $result['error']);
        static::assertArrayHasKey('title', $result['error']);
        static::assertNotEmpty($result['error']['title']);
    }

    /**
     * Test unauthorized response for non-admin users.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testUnauthorized()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/applications',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '403',
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader('second user', 'password2'));
        $this->get('/admin/applications');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayNotHasKey('data', $result);
        static::assertArrayHasKey('links', $result);
        static::assertArrayHasKey('error', $result);
        static::assertEquals($expected['links'], $result['links']);
        static::assertArraySubset($expected['error'], $result['error']);
        static::assertArrayHasKey('title', $result['error']);
        static::assertNotEmpty($result['error']['title']);
    }
}
