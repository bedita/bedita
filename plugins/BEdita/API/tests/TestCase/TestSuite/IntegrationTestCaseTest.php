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
namespace BEdita\API\Test\TestCase\TestSuite;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Event\Event;
use Cake\Event\EventManager;

/**
 * {@see \BEdita\API\TestSuite\IntegrationTestCase} Test Case
 *
 * @coversDefaultClass \BEdita\API\TestSuite\IntegrationTestCase
 */
class IntegrationTestCaseTest extends IntegrationTestCase
{
    /**
     * Data provider for addAuthFixtures
     *
     * @return array
     */
    public function authFixturesProvider()
    {
        return [
            'default' => [
                [
                    'plugin.BEdita/Core.Annotations',
                    'plugin.BEdita/Core.Config',
                    'plugin.BEdita/Core.AsyncJobs',
                    'plugin.BEdita/Core.AuthProviders',
                    'plugin.BEdita/Core.ExternalAuth',
                    'plugin.BEdita/Core.ObjectTypes',
                    'plugin.BEdita/Core.Objects',
                    'plugin.BEdita/Core.Locations',
                    'plugin.BEdita/Core.Media',
                    'plugin.BEdita/Core.Profiles',
                    'plugin.BEdita/Core.Users',
                    'plugin.BEdita/Core.Roles',
                    'plugin.BEdita/Core.RolesUsers',
                    'plugin.BEdita/Core.Endpoints',
                    'plugin.BEdita/Core.Applications',
                    'plugin.BEdita/Core.EndpointPermissions',
                    'plugin.BEdita/Core.Relations',
                    'plugin.BEdita/Core.RelationTypes',
                    'plugin.BEdita/Core.Properties',
                    'plugin.BEdita/Core.PropertyTypes',
                    'plugin.BEdita/Core.Trees',
                    'plugin.BEdita/Core.ObjectRelations',
                    'plugin.BEdita/Core.Translations',
                    'plugin.BEdita/Core.UserTokens',
                    'plugin.BEdita/Core.Categories',
                    'plugin.BEdita/Core.ObjectCategories',
                    'plugin.BEdita/Core.Tags',
                    'plugin.BEdita/Core.ObjectTags',
                    'plugin.BEdita/Core.History',
                ],
                [],
            ],
            'fixturesPresent' => [
                [
                    'plugin.BEdita/Core.Annotations',
                    'plugin.BEdita/Core.Config',
                    'plugin.BEdita/Core.AsyncJobs',
                    'plugin.BEdita/Core.AuthProviders',
                    'plugin.BEdita/Core.ExternalAuth',
                    'plugin.BEdita/Core.ObjectTypes',
                    'plugin.BEdita/Core.Objects',
                    'plugin.BEdita/Core.Locations',
                    'plugin.BEdita/Core.Media',
                    'plugin.BEdita/Core.Profiles',
                    'plugin.BEdita/Core.Users',
                    'plugin.BEdita/Core.Roles',
                    'plugin.BEdita/Core.RolesUsers',
                    'plugin.BEdita/Core.Endpoints',
                    'plugin.BEdita/Core.Applications',
                    'plugin.BEdita/Core.EndpointPermissions',
                    'plugin.BEdita/Core.Relations',
                    'plugin.BEdita/Core.RelationTypes',
                    'plugin.BEdita/Core.Properties',
                    'plugin.BEdita/Core.PropertyTypes',
                    'plugin.BEdita/Core.Trees',
                    'plugin.BEdita/Core.ObjectRelations',
                    'plugin.BEdita/Core.Translations',
                    'plugin.BEdita/Core.UserTokens',
                    'plugin.BEdita/Core.Categories',
                    'plugin.BEdita/Core.ObjectCategories',
                    'plugin.BEdita/Core.Tags',
                    'plugin.BEdita/Core.ObjectTags',
                    'plugin.BEdita/Core.History',
                ],
                [
                    'plugin.BEdita/Core.Users',
                ],
            ],
        ];
    }

    /**
     * Test addAuthFixtures
     *
     * @param array $expected Expected results.
     * @param array $fixtures Class fixtures.
     * @return void
     * @dataProvider authFixturesProvider
     * @covers ::__construct()
     * @covers ::addAuthFixtures()
     */
    public function testAuthFixtures(array $expected, array $fixtures)
    {
        $mock = $this->getMockBuilder(IntegrationTestCase::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->fixtures = $fixtures;
        $mock->__construct();
        static::assertEquals($expected, $mock->fixtures);
    }

    /**
     * Test setUp
     *
     * @return void
     * @covers ::setUp()
     */
    public function testSetUp()
    {
        CurrentApplication::getInstance()->set(null);
        static::assertEquals([], LoggedUser::getUser());
        static::assertNull(CurrentApplication::getApplication());
        LoggedUser::setUserAdmin();

        $this->setUp();
        static::assertEquals([], LoggedUser::getUser());
        static::assertCount(1, EventManager::instance()->listeners('Auth.afterIdentify'));
        static::assertCount(1, EventManager::instance()->listeners('Server.buildMiddleware'));
        static::assertInstanceOf('\BEdita\Core\Model\Entity\Application', CurrentApplication::getApplication());

        $expected = [
            'id' => 9999,
            'username' => 'gustavo',
        ];
        $event = new Event('Auth.afterIdentify', null, [$expected]);
        EventManager::instance()->dispatch($event);
        static::assertEquals($expected, LoggedUser::getUser());
    }

    /**
     * Test tearDown
     *
     * @return void
     * @covers ::tearDown()
     */
    public function testTearDown()
    {
        $user = [
            'id' => 9999,
            'username' => 'gustavo',
        ];
        $event = new Event('Auth.afterIdentify', null, [$user]);
        EventManager::instance()->dispatch($event);
        static::assertEquals($user, LoggedUser::getUser());
        static::assertInstanceOf('\BEdita\Core\Model\Entity\Application', CurrentApplication::getApplication());

        parent::tearDown();
        static::assertEquals([], LoggedUser::getUser());
        static::assertNull(CurrentApplication::getApplication());
    }

    /**
     * Test getUserAuthHeader
     *
     * @return void
     * @covers ::getUserAuthHeader()
     */
    public function testGetUserAuthHeader()
    {
        $authHeader = $this->getUserAuthHeader();
        static::assertArrayHasKey('Authorization', $authHeader);
        static::assertContains('Bearer ', $authHeader['Authorization']);
    }

    /**
     * Test authUser
     *
     * @return void
     * @covers ::authUser()
     */
    public function testAuthUser()
    {
        $tokens = $this->authUser();
        static::assertArrayHasKey('jwt', $tokens);
        static::assertArrayHasKey('renew', $tokens);

        $this->expectException('Cake\Http\Exception\UnauthorizedException');
        $this->expectExceptionMessageRegExp('/^User is not authorized. Status: 401/');
        $this->authUser('gustavo', 'supporto');
    }

    /**
     * Data provider for testConfigRequestHeaders()
     *
     * @return array
     */
    public function headersProvider()
    {
        return [
            'getNoOptions' => [
                [
                    'Host' => 'api.example.com',
                    'Accept' => 'application/vnd.api+json',
                    'X-Api-Key' => 'API_KEY',
                ],
                'GET',
            ],
            'postNoOptions' => [
                [
                    'Host' => 'api.example.com',
                    'Accept' => 'application/vnd.api+json',
                    'Content-Type' => 'application/vnd.api+json',
                    'X-Api-Key' => 'API_KEY',
                ],
                'POST',
            ],
            'overrideOptions' => [
                [
                    'Host' => 'api.example.com',
                    'Accept' => 'application/json',
                    'X-Api-Key' => 'API_KEY',
                ],
                'GET',
                [
                    'Accept' => 'application/json',
                ],
            ],
            'overrideOptions2' => [
                [
                    'Host' => 'myapi.example.com',
                    'Accept' => 'application/vnd.api+json',
                    'Content-Type' => 'application/json',
                    'X-Api-Key' => 'API_KEY',
                ],
                'PATCH',
                [
                    'Host' => 'myapi.example.com',
                    'Content-Type' => 'application/json',
                ],
            ],
        ];
    }

    /**
     * Test configRequestHeaders
     *
     * @param array $expected The expected headers
     * @param string $method The request method
     * @param array $options The optional headers
     * @return void
     * @dataProvider headersProvider
     * @covers ::configRequestHeaders()
     */
    public function testConfigRequestHeaders($expected, $method, array $options = [])
    {
        $this->configRequestHeaders($method, $options);
        $this->assertEquals($expected, $this->_request['headers']);
    }
}
