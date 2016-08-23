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

namespace BEdita\Core\Test\TestCase\Database\Type;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Database\Type\JsonType} Test Case
 *
 * @covers \BEdita\Core\Database\Type\JsonType
 */
class JsonTypeTest extends TestCase
{

    /**
     * Test mock table.
     *
     * @var \Cake\ORM\Table
     */
    public $JsonSchemaTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.json_schema_table',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->JsonSchemaTable = TableRegistry::get('JsonSchemaTable');

        $this->JsonSchemaTable->table('json_schema_table');
        $this->JsonSchemaTable->primaryKey('id');
        $this->JsonSchemaTable->displayField('name');

        $schema = $this->JsonSchemaTable->schema();
        $schema->columnType('json_field', 'json');
        $this->JsonSchemaTable->schema($schema);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->JsonSchemaTable);

        parent::tearDown();
    }

    /**
     * Test reading from database.
     *
     * @return void
     */
    public function testRead()
    {
        $list = $this->JsonSchemaTable->find('list', ['valueField' => 'json_field'])->toArray();

        $expected = [
            1 => ['json', 'array'],
            2 => null,
        ];

        $this->assertEquals($list, $expected);
    }

    /**
     * Data provider for `testWrite` test case.
     *
     * @return array
     */
    public function someProvider()
    {
        return [
            'null' => [
                null,
                null,
            ],
            'array' => [
                [0, -1.5, true, 'string', null],
                [0, -1.5, true, 'string', null],
            ],
            'object' => [
                [
                    'key' => 'value',
                    'otherKey' => ['complex', 'value', 2],
                ],
                '{"key":"value","otherKey":["complex","value",2]}',
            ],
        ];
    }

    /**
     * Test marshalling data and writing data to database.
     *
     * @param mixed $expectedJsonField Expected value.
     * @param mixed $jsonField Value to be put in JSON field.
     *
     * @return void
     * @dataProvider someProvider()
     */
    public function testWrite($expectedJsonField, $jsonField)
    {
        $name = uniqid();

        $entity = $this->JsonSchemaTable->newEntity();
        $entity = $this->JsonSchemaTable->patchEntity(
            $entity,
            [
                'name' => $name,
                'json_field' => $jsonField,
            ]
        );
        $success = $this->JsonSchemaTable->save($entity);
        $savedEntity = $this->JsonSchemaTable->get($success->get($this->JsonSchemaTable->primaryKey()));

        $this->assertTrue((bool)$success);
        $this->assertArraySubset(
            array_filter([
                'name' => $name,
                'json_field' => $expectedJsonField,
            ]),
            $success->toArray()
        );
        $this->assertEquals($expectedJsonField, $savedEntity->get('json_field'));
    }
}
