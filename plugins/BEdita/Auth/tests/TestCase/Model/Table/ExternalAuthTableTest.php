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

namespace BEdita\Auth\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Auth\Model\Table\ExternalAuthTable} Test Case
 *
 * @coversDefaultClass \BEdita\Auth\Model\Table\ExternalAuthTable
 */
class ExternalAuthTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Auth\Model\Table\ExternalAuthTable
     */
    public $ExternalAuth;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Auth.users',
        'plugin.BEdita/Auth.auth_providers',
        'plugin.BEdita/Auth.external_auth',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->ExternalAuth = TableRegistry::get(
            'ExternalAuth',
            TableRegistry::exists('ExternalAuth') ? [] : ['className' => 'BEdita\Auth\Model\Table\ExternalAuthTable']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ExternalAuth);

        TableRegistry::clear();

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::_initializeSchema()
     */
    public function testInitialization()
    {
        $this->ExternalAuth->initialize([]);
        $schema = $this->ExternalAuth->schema();

        $this->assertEquals('external_auth', $this->ExternalAuth->table());
        $this->assertEquals('id', $this->ExternalAuth->primaryKey());
        $this->assertEquals('id', $this->ExternalAuth->displayField());

        $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->ExternalAuth->AuthProviders);
        $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->ExternalAuth->Users);

        $this->assertEquals('json', $schema->columnType('params'));
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
                    'user_id' => 1,
                    'auth_provider_id' => 2,
                    'username' => 'unique_username',
                ],
            ],
            'notUnique' => [
                false,
                [
                    'user_id' => 2,
                    'auth_provider_id' => 1,
                    'username' => 'first_user',
                    'params' => [
                        'someParam' => 'someValue',
                    ],
                ],
            ],
            'notUnique2' => [
                false,
                [
                    'user_id' => 1,
                    'auth_provider_id' => 1,
                    'username' => 'some_username',
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
     * @covers ::validationDefault
     * @covers ::buildRules
     */
    public function testValidation($expected, array $data)
    {
        $externalAuth = $this->ExternalAuth->newEntity();
        $this->ExternalAuth->patchEntity($externalAuth, $data);

        $success = $this->ExternalAuth->save($externalAuth);
        $this->assertEquals($expected, (bool)$success);
    }
}
