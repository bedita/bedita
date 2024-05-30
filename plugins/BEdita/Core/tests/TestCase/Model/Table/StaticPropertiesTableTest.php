<?php
declare(strict_types=1);

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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Entity\StaticProperty;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * {@see \BEdita\Core\Model\Table\StaticPropertiesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\StaticPropertiesTable
 */
class StaticPropertiesTableTest extends TestCase
{
    use ArraySubsetAsserts;

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\StaticPropertiesTable
     */
    public $StaticProperties;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Cache::delete('static_properties', ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->StaticProperties);

        parent::tearDown();
    }

    /**
     * Test table initialization.
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $this->StaticProperties = TableRegistry::getTableLocator()->get('StaticProperties');

        static::assertSame(StaticProperty::class, $this->StaticProperties->getEntityClass());
        static::assertMatchesRegularExpression('/^(?:[\w_]+\.)?static_properties_[a-f0-9]{16}$/', $this->StaticProperties->getTable());

        $otherInstance = TableRegistry::getTableLocator()->get('BEdita/Core.StaticProperties');

        static::assertNotSame($otherInstance->getTable(), $this->StaticProperties->getTable());
    }

    /**
     * Test creation of temporary table.
     *
     * @return void
     * @covers ::createTable()
     */
    public function testCreateTable()
    {
        $this->StaticProperties = TableRegistry::getTableLocator()->get('StaticProperties');

        $Properties = TableRegistry::getTableLocator()->get('Properties');

        $staticPropSchema = $this->StaticProperties->getSchema();
        $propSchema = $Properties->getSchema();

        static::assertSame($Properties->getConnection(), $this->StaticProperties->getConnection());
        //static::assertTrue($staticPropSchema->isTemporary()); // Does not work as expected.

        $tableName = $this->StaticProperties->getTable();
        if (strpos($tableName, '.')) {
            [, $tableName] = explode('.', $tableName);
        }
        $prefix = sprintf('%s_', str_replace('_', '', $tableName));

        // Check that columns have the same definition, except ID.
        foreach ($staticPropSchema->columns() as $column) {
            $expectedSchema = $propSchema->getColumn($column);
            if ($column === $this->StaticProperties->getPrimaryKey()) {
                // Primary key has a different definition.
                $expectedSchema = [
                    'type' => 'uuid',
                    'length' => null,
                    'null' => false,
                    'default' => null,
                    'comment' => '',
                    'precision' => null,
                ];
            } elseif (in_array($column, ['created', 'modified'])) {
                $expectedSchema['null'] = true;
            }

            static::assertEquals($expectedSchema, $staticPropSchema->getColumn($column));
        }

        // Check that indexes have the same definition, but different name.
        foreach ($staticPropSchema->indexes() as $index) {
            $correspondingIndex = sprintf('properties_%s', substr($index, strlen($prefix)));
            $definition = $staticPropSchema->getIndex($index);

            static::assertStringStartsWith($prefix, $index);
            static::assertEquals($propSchema->getIndex($correspondingIndex), $definition);
        }

        // Check that there are no foreign keys.
        foreach ($staticPropSchema->constraints() as $constraint) {
            $definition = $staticPropSchema->getConstraint($constraint);
            static::assertNotSame(TableSchema::CONSTRAINT_FOREIGN, $definition['type']);
        }
    }

    /**
     * Data provider for `testAddSchemaDetails` test case.
     *
     * @return array
     */
    public function addSchemaDetailsProvider()
    {
        return [
            'objects.status' => [
                [
                    'object_type_id' => 1,
                    'property_type_id' => 3,
                    'name' => 'status',
                ],
                [
                    'object_type_id' => 1,
                    'name' => 'status',
                ],
            ],
            '*.id' => [
                [
                    'name' => 'id',
                    'object_type_id' => 1,
                ],
                [
                    'name' => 'id',
                ],
            ],
            'profiles.email' => [
                [
                    'object_type_id' => 3,
                    'property_type_id' => 4,
                    'name' => 'email',
                ],
                [
                    'object_type_id' => 3,
                    'name' => 'email',
                ],
            ],
            'documents.*' => [
                null, // All fields are inherited from `objects`.
                [
                    'object_type_id' => 2,
                ],
            ],
            'objects.locked' => [
                [
                    'object_type_id' => 1,
                    'property_type_id' => 10,
                    'name' => 'locked',
                ],
                [
                    'object_type_id' => 1,
                    'name' => 'locked',
                ],
            ],
            'objects.created' => [
                [
                    'object_type_id' => 1,
                    'property_type_id' => 7,
                    'name' => 'created',
                ],
                [
                    'object_type_id' => 1,
                    'name' => 'created',
                ],
            ],
            'objects.extra' => [
                [
                    'object_type_id' => 1,
                    'property_type_id' => 11,
                    'name' => 'extra',
                ],
                [
                    'object_type_id' => 1,
                    'name' => 'extra',
                ],
            ],
            'media.provider_thumbnail' => [
                [
                    'object_type_id' => 8,
                    'property_type_id' => 5,
                    'name' => 'provider_thumbnail',
                ],
                [
                    'object_type_id' => 8,
                    'name' => 'provider_thumbnail',
                ],
            ],
        ];
    }

    /**
     * Test data insertion into temporary table.
     *
     * @param array|null $expected Expected result.
     * @param array $conditions Conditions
     * @return void
     * @dataProvider addSchemaDetailsProvider()
     * @covers ::addSchemaDetails()
     * @covers ::listOwnTables()
     * @covers ::prepareTableFields()
     */
    public function testAddSchemaDetails(?array $expected, array $conditions)
    {
        $result = TableRegistry::getTableLocator()->get('StaticProperties')->find()
            ->where($conditions)
            ->enableHydration(false)
            ->first();

        if ($expected === null) {
            static::assertNull($result);

            return;
        }

        static::assertNotNull($result);
        static::assertArraySubset($expected, $result);

        $secondResult = TableRegistry::getTableLocator()->get('BEdita/Core.StaticProperties')->find()
            ->where($conditions)
            ->enableHydration(false)
            ->first();

        static::assertSame($result['id'], $secondResult['id']);
    }
}
