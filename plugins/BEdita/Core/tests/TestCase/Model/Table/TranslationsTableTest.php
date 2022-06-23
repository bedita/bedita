<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Model\Table\TranslationsTable
 */
class TranslationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\TranslationsTable
     */
    public $Translations;

    /**
     * Fixtures.
     *
     * @var string[]
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Translations',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Translations = TableRegistry::getTableLocator()->get('Translations');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Translations);

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialize()
    {
        static::assertInstanceOf(BelongsTo::class, $this->Translations->Objects);
        static::assertInstanceOf(BelongsTo::class, $this->Translations->CreatedByUsers);
        static::assertInstanceOf(BelongsTo::class, $this->Translations->ModifiedByUsers);
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
                    'object_id' => 2,
                    'lang' => 'fr-FR',
                    'status' => 'draft',
                    'translated_fields' => [
                        'title' => 'ma première traduction',
                    ],
                ],
            ],
            'invalid 1' => [
                [
                    'object_id.integer',
                    'lang.scalar',
                    'status.inList',
                    'translated_fields.isArray',
                ],
                [
                    'object_id' => 'definitely not a number',
                    'lang' => ['definitely', 'not', 'a', 'scalar'],
                    'status' => 'definitely not a valid status',
                    'translated_fields' => 'definitely not an array',
                ],
            ],
            'invalid 2' => [
                [
                    'object_id._required',
                    'lang.maxLength',
                    'status._empty',
                ],
                [
                    'lang' => str_repeat('too long', 128),
                    'status' => null,
                    'translated_fields' => [],
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
     * @dataProvider validationProvider()
     * @coversNothing
     */
    public function testValidation(array $expected, array $data)
    {
        $entity = $this->Translations->newEntity();
        $entity = $this->Translations->patchEntity($entity, $data);
        $errors = array_keys(Hash::flatten($entity->getErrors()));

        static::assertEquals($expected, $errors);
    }
}
