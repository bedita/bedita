<?php
declare(strict_types=1);

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
use Cake\Utility\Hash;

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
    protected $fixtures = [
        'plugin.BEdita/Core.AuthProviders',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->AuthProviders = TableRegistry::getTableLocator()->get('AuthProviders');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
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
                [],
                [
                    'name' => 'some_unique_value',
                    'url' => 'https://example.com/oauth2',
                    'params' => [],
                ],
            ],
            'notUnique' => [
                [
                    'name._required',
                    'name.unique',
                ],
                [
                    'name' => 'example',
                    'url' => 'https://example.com/oauth2',
                    'params' => [
                        'someParam' => 'someValue',
                    ],
                ],
            ],
            'invalidUrl' => [
                [
                    'url.url',
                ],
                [
                    'name' => 'some_unique_value',
                    'url' => 'this is not a URL',
                ],
            ],
            'URL without protocol' => [
                [
                    'url.url',
                ],
                [
                    'name' => 'some_unique_value',
                    'url' => 'www.example.com/without/protocol.json?shouldBeValid=no',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param string[] $expected Expected validation errors.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation(array $expected, array $data): void
    {
        $authProvider = $this->AuthProviders->newEntity([]);
        $this->AuthProviders->patchEntity($authProvider, $data);

        $errors = $authProvider->getErrors();
        $errors = Hash::flatten($errors);
        static::assertEquals($expected, array_keys($errors));

        if (empty($expected)) {
            $success = $this->AuthProviders->save($authProvider);
            static::assertTrue((bool)$success);
        }
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

        $result = $this->AuthProviders->find('enabled')->where(['name' => 'example'])->toArray();
        static::assertNotEmpty($result);
        static::assertEquals(1, count($result));

        $result = $this->AuthProviders->find('enabled')->where(['name' => 'linkedout'])->toArray();
        static::assertEmpty($result);
    }
}
