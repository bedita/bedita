<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\UserTokensTable;
use Cake\Core\Configure;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\UserTokensTable} Test Case
 *
 * @coversDefaultClass BEdita\Core\Model\Table\UserTokensTable
 */
class UserTokensTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\UserTokensTable
     */
    public $UserTokens;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.UserTokens',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserTokens = TableRegistry::getTableLocator()->get('UserTokens');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserTokens);

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
        $this->UserTokens->associations()->removeAll();
        $this->UserTokens->initialize([]);
        $this->assertEquals('user_tokens', $this->UserTokens->getTable());
        $this->assertEquals('id', $this->UserTokens->getPrimaryKey());
        $this->assertEquals('id', $this->UserTokens->getDisplayField());

        $this->assertInstanceOf(BelongsTo::class, $this->UserTokens->Users);
        $this->assertInstanceOf(BelongsTo::class, $this->UserTokens->Applications);
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'ok' => [
                [],
                [
                    'user_id' => 1,
                    'client_token' => 'token token token',
                    'token_type' => 'refresh',
                ],
            ],
            'invalid 1' => [
                [
                    'client_token._required',
                    'token_type._required',
                ],
                [
                    'application_id' => 2,
                    'secret_token' => 'super secret token',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param string[] $expected Expected errors.
     * @param array $data Data.
     * @return void
     * @dataProvider validationProvider
     * @covers ::validationDefault()
     */
    public function testValidation(array $expected, array $data)
    {
        $entity = $this->UserTokens->newEntity([]);
        $entity = $this->UserTokens->patchEntity($entity, $data);
        $errors = array_keys(Hash::flatten($entity->getErrors()));

        static::assertEquals($expected, $errors);
    }

    /**
     * Test 'valid' finder.
     *
     * @return void
     * @covers ::findValid()
     */
    public function testValidFinder()
    {
        $entity = $this->UserTokens->find('valid')->order(['id' => 'ASC'])->first();

        static::assertNotEmpty($entity);
        static::assertEquals(1, $entity->get('id'));
    }

    /**
     * Data provider for `testGetTokenTypes()`
     *
     * @return array
     */
    public function getTokenTypesProvider()
    {
        return [
            'default' => [
                UserTokensTable::DEFAULT_TOKEN_TYPES,
                null,
            ],
            'conf' => [
                array_merge(UserTokensTable::DEFAULT_TOKEN_TYPES, ['token_one', 'token_two']),
                ['token_one', 'token_two'],
            ],
            'confWithDuplicates' => [
                array_merge(UserTokensTable::DEFAULT_TOKEN_TYPES, ['token_one', 'token_two']),
                ['token_one', 'token_two', 'access', 'otp', 'token_one'],
            ],
        ];
    }

    /**
     * Test for getTokenTypes()
     *
     * @return void
     * @dataProvider getTokenTypesProvider
     * @covers ::getTokenTypes()
     */
    public function testGetTokenTypes($expected, $conf)
    {
        Configure::write('UserTokens.types', $conf);
        static::assertEquals($expected, $this->UserTokens->getTokenTypes());
    }
}
