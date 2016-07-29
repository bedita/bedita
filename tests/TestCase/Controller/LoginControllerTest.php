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

use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\LoginController
 */
class LoginControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * Test login method.
     *
     * @return string A valid JWT.
     *
     * @covers ::login()
     * @covers \BEdita\API\Auth\JwtAuthenticate
     */
    public function testSuccessfulLogin()
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->post('/auth', ['username' => 'first user', 'password_hash' => 'password1']);
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);

        return $result['meta']['renew'];
    }

    /**
     * Test login method with renew token.
     *
     * @param string $renewToken Renew token.
     * @return void
     *
     * @depends testSuccessfulLogin
     * @covers ::login()
     */
    public function testSuccessfulRenew($renewToken)
    {
        sleep(1);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => sprintf('Bearer %s', $renewToken),
            ],
        ]);

        $this->post('/auth', []);
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertTextNotEquals($renewToken, $result['meta']['renew']);
    }
}
