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

use Cake\ORM\TableRegistry;
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
     */
    public function testSuccessfulLogin()
    {
        // Force event listener on `Auth.afterIdentify` to be re-attached.
        TableRegistry::remove('Users');
        TableRegistry::config('Users', ['className' => 'BEdita/Core.Users']);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->post('/auth', ['username' => 'first user', 'password_hash' => 'password1']);
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);

        $lastLogin = TableRegistry::get('Users')->get(1)->get('last_login');
        $this->assertNotNull($lastLogin);
        $this->assertEquals(time(), $lastLogin->timestamp, '', 1);

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
     * @covers \BEdita\API\Auth\JwtAuthenticate::authenticate()
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

    /**
     * Test login method with invalid credentials.
     *
     * @return void
     *
     * @covers ::login()
     */
    public function testFailedLogin()
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->post('/auth', ['username' => 'first user', 'password_hash' => 'wrongPassword']);

        $this->assertResponseCode(401);
    }
}
