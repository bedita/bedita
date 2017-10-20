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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Entity\Property;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\StaticPropertiesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\StaticPropertiesTable
 */
class StaticPropertiesTableTest extends TestCase
{

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
    public $fixtures = [
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.media',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Cache::delete('static_properties', ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->StaticProperties);

        parent::tearDown();
    }

    /**
     * Test table initialization.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $this->StaticProperties = TableRegistry::get('StaticProperties');

        static::assertSame(Property::class, $this->StaticProperties->getEntityClass());
        static::assertRegExp('/^static_properties_[a-f0-9]{16}$/', $this->StaticProperties->getTable());

        $otherInstance = TableRegistry::get('BEdita/Core.StaticProperties');

        static::assertNotSame($otherInstance->getTable(), $this->StaticProperties->getTable());
    }

    /**
     * Test creation of temporary table.
     *
     * @return void
     *
     * @covers ::createTable()
     */
    public function testCreateTable()
    {
        $this->StaticProperties = TableRegistry::get('StaticProperties');

        $Properties = TableRegistry::get('Properties');

        $staticPropSchema = $this->StaticProperties->getSchema();
        $propSchema = $Properties->getSchema();

        static::assertSame($Properties->getConnection(), $this->StaticProperties->getConnection());
        //static::assertTrue($staticPropSchema->isTemporary()); // Does not work as expected.

        $prefix = sprintf('%s_', str_replace('_', '', $this->StaticProperties->getTable()));

        // Check that columns have the same definition, except ID.
        foreach ($staticPropSchema->columns() as $column) {
            if ($column === $this->StaticProperties->getPrimaryKey()) {
                // Primary key has a different definition.
                static::assertEquals(
                    [
                        'type' => 'uuid',
                        'length' => null,
                        'null' => false,
                        'default' => null,
                        'comment' => '',
                        'precision' => null,
                    ],
                    $staticPropSchema->getColumn($column)
                );

                continue;
            }
            static::assertEquals($propSchema->getColumn($column), $staticPropSchema->getColumn($column));
        }

        // Check that indexes have the same definition, but different name.
        foreach ($staticPropSchema->indexes() as $index) {
            $correspondingIndex = sprintf('properties_%s', substr($index, strlen($prefix)));
            $definition = $staticPropSchema->getIndex($index);

            static::assertStringStartsWith($prefix, $index);
            static::assertEquals($propSchema->getIndex($correspondingIndex), $definition);
        }

        // Check that constraints have the same definition, but different name, and there are no foreign keys.
        foreach ($staticPropSchema->constraints() as $constraint) {
            $correspondingConstraint = sprintf('properties_%s', substr($constraint, strlen($prefix)));
            $definition = $staticPropSchema->getConstraint($constraint);

            if ($definition['type'] === TableSchema::CONSTRAINT_FOREIGN) {
                static::fail('Temporary table should not have foreign keys');
            }
            if ($constraint !== TableSchema::CONSTRAINT_PRIMARY) {
                static::assertStringStartsWith($prefix, $constraint);
            } else {
                $correspondingConstraint = TableSchema::CONSTRAINT_PRIMARY;
            }
            static::assertEquals($propSchema->getConstraint($correspondingConstraint), $definition);
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
                    'property_type_id' => 1,
                    'name' => 'status',
                    'description' => 'object status: on, draft, off',
                ],
                [
                    'object_type_id' => 1,
                    'name' => 'status',
                ],
            ],
            'objects.id' => [
                null, // ID should never be present.
                [
                    'object_type_id' => 1,
                    'name' => 'id',
                ],
            ],
            'profiles.email' => [
                [
                    'object_type_id' => 3,
                    'property_type_id' => 1,
                    'name' => 'email',
                    'description' => 'first email, can be NULL',
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
        ];
    }

    /**
     * Test data insertion into temporary table.
     *
     * @param array|null $expected Expected result.
     * @param array $conditions Conditions
     * @return void
     *
     * @dataProvider addSchemaDetailsProvider()
     * @covers ::addSchemaDetails()
     * @covers ::prepareTableFields()
     */
    public function testAddSchemaDetails(array $expected = null, array $conditions)
    {
        $result = TableRegistry::get('StaticProperties')->find()
            ->where($conditions)
            ->enableHydration(false)
            ->first();

        if ($expected === null) {
            static::assertNull($result);

            return;
        }

        static::assertArraySubset($expected, $result);

        $secondResult = TableRegistry::get('BEdita/Core.StaticProperties')->find()
            ->where($conditions)
            ->enableHydration(false)
            ->first();

        static::assertSame($result['id'], $secondResult['id']);
    }
}
