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

use BEdita\Core\State\CurrentApplication;
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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        CurrentApplication::setFromApiKey(API_KEY);
    }

    /**
     * Test login method.
     *
     * @return string A valid JWT.
     *
     * @covers ::login()
     */
    public function testSuccessfulLogin()
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->post('/auth', ['username' => 'first user', 'password' => 'password1']);
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);

        $lastLogin = TableRegistry::get('Users')->get(1)->get('last_login');
        $this->assertNotNull($lastLogin);
        $this->assertEquals(time(), $lastLogin->timestamp, '', 1);

        return $result['meta'];
    }

    /**
     * Test login method with renew token.
     *
     * @param array $meta Login metadata.
     * @return void
     *
     * @depends testSuccessfulLogin
     * @covers ::login()
     * @covers \BEdita\API\Auth\JwtAuthenticate::authenticate()
     */
    public function testSuccessfulRenew(array $meta)
    {
        sleep(1);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => sprintf('Bearer %s', $meta['renew']),
            ],
        ]);

        $this->post('/auth', []);
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertTextNotEquals($meta['renew'], $result['meta']['renew']);
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

        $this->post('/auth', ['username' => 'first user', 'password' => 'wrongPassword']);

        $this->assertResponseCode(401);
    }

    /**
     * Test read logged user data.
     *
     * @param array $meta Login metadata.
     * @return void
     *
     * @depends testSuccessfulLogin
     * @covers ::whoami()
     * @covers \BEdita\API\Auth\JwtAuthenticate::authenticate()
     */
    public function testLoggedUser(array $meta)
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Authorization' => sprintf('Bearer %s', $meta['jwt']),
            ],
        ]);

        $this->get('/auth');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotEmpty($result);
    }

    /**
     * Test read logged user fail.
     *
     * @return void
     *
     * @covers ::whoami()
     * @covers \BEdita\API\Auth\JwtAuthenticate::authenticate()
     */
    public function testLoggedUserFail()
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->get('/auth');
        $this->assertResponseCode(401);
    }
}
