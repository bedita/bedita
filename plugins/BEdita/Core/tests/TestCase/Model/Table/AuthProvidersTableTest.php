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

namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\AuthProvidersTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\AuthProvidersTable
 */
class AuthProvidersTableTest extends TestCase
{

    /**
     * Test subject
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
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->AuthProviders->initialize([]);
        $schema = $this->AuthProviders->getSchema();

        $this->assertEquals('auth_providers', $this->AuthProviders->getTable());
        $this->assertEquals('id', $this->AuthProviders->getPrimaryKey());
        $this->assertEquals('name', $this->AuthProviders->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\HasMany', $this->AuthProviders->ExternalAuth);

        $this->assertEquals('json', $schema->getColumnType('params'));
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'name' => 'some_unique_value',
                    'url' => 'https://example.com/oauth2',
                    'params' => [],
                ],
            ],
            'notUnique' => [
                false,
                [
                    'name' => 'example',
                    'url' => 'https://example.com/oauth2',
                    'params' => [
                        'someParam' => 'someValue',
                    ],
                ],
            ],
            'invalidUrl' => [
                false,
                [
                    'name' => 'some_unique_value',
                    'url' => 'this is not a URL',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $authProvider = $this->AuthProviders->newEntity();
        $this->AuthProviders->patchEntity($authProvider, $data);

        $error = (bool)$authProvider->getErrors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->AuthProviders->save($authProvider);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Test `findAuthenticate` method.
     *
     * @return void

     * @covers ::findAuthenticate()
     */
    public function testFindAuthenticate()
    {
        $result = $this->AuthProviders->find('authenticate')->toArray();

        static::assertNotEmpty($result);
        static::assertEquals(['BEdita/API.OAuth2', 'BEdita/API.Uuid'], array_keys($result));
        static::assertEquals(['uuid'], array_keys($result['BEdita/API.Uuid']['authProviders']));
        static::assertEquals(['example'], array_keys($result['BEdita/API.OAuth2']['authProviders']));
    }

    /**
     * Test `findEnabled` method.
     *
     * @return void

     * @covers ::findEnabled()
     */
    public function testFindEnabled()
    {
        $result = $this->AuthProviders->find('enabled')->toArray();
        static::assertNotEmpty($result);
        static::assertEquals(3, count($result));

        $result = $this->AuthProviders->find('enabled', ['name' => 'example'])->toArray();
        static::assertNotEmpty($result);
        static::assertEquals(1, count($result));

        $result = $this->AuthProviders->find('enabled', ['name' => 'linkedout'])->toArray();
        static::assertEmpty($result);
    }
}
