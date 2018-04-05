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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\AuthProvider;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Entity\AuthProvider} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\AuthProvider
 */
class AuthProviderTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\AuthProvidersTable
     */
    public $AuthProviders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.auth_providers',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->AuthProviders = TableRegistry::get('AuthProviders');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->AuthProviders);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $authProvider = $this->AuthProviders->get(1);

        $data = [
            'id' => 42,
            'name' => 'patched_name',
        ];
        $authProvider = $this->AuthProviders->patchEntity($authProvider, $data);
        if (!($authProvider instanceof AuthProvider)) {
            throw new \InvalidArgumentException();
        }

        static::assertEquals(1, $authProvider->id);
        static::assertEquals('patched_name', $authProvider->name);
    }

    /**
     * Test getter for `slug` property.
     *
     * @return void
     *
     * @covers ::_getSlug()
     */
    public function testGetSlug()
    {
        $authProvider = $this->AuthProviders->get(2);

        static::assertSame('uuid', $authProvider->slug);
    }

    /**
     * Data provider for `testGetRoles` test case.
     *
     * @return array
     */
    public function getRolesProvider()
    {
        return [
            'empty' => [
                [],
                [],
            ],
            'found' => [
                [
                    1 => 'first role',
                ],
                [
                    'first role',
                    'this role does not exist',
                ],
            ],
        ];
    }

    /**
     * Test getter of roles to be associated to users authenticated via auth provider.
     *
     * @param array $expected Expected result.
     * @param array $configuration Initial configuration.
     * @return void
     *
     * @covers ::getRoles()
     * @dataProvider getRolesProvider()
     */
    public function testGetRoles(array $expected, array $configuration)
    {
        Configure::write('Roles.BEdita/API.Uuid', $configuration);
        $authProvider = $this->AuthProviders->get(2);

        $roles = Hash::combine(
            $authProvider->getRoles(),
            '{n}.id',
            '{n}.name'
        );

        static::assertEquals($expected, $roles);
    }

    /**
     * Data provider for `testCheckAuthorization` test case.
     *
     * @return array
     */
    public function checkAuthorizationProvider()
    {
        return [
            'ok' => [
                true,
                [
                    'owner_id' => 'test',
                ],
                'test',
            ],
            'ko' => [
                false,
                [
                    'some_id' => 'gustavo',
                ],
                'test',
            ],
        ];
    }

    /**
     * Test getter of roles to be associated to users authenticated via auth provider.
     *
     * @param bool $expected Expected result.
     * @param array $configuration Initial configuration.
     * @param string $username Initial configuration.
     * @return void
     *
     * @covers ::checkAuthorization()
     * @dataProvider checkAuthorizationProvider()
     */
    public function testCheckAuthorization(bool $expected, array $response, $username)
    {
        $authProvider = $this->AuthProviders->get(1);
        $result = $authProvider->checkAuthorization($response, $username);

        static::assertEquals($expected, $result);
    }
}
