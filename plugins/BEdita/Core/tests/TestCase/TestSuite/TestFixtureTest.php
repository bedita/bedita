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
namespace BEdita\Core\Test\TestCase\TestSuite;

use BEdita\Core\TestSuite\Fixture\TestFixture;
use BEdita\Core\Test\Fixture\ObjectsFixture;
use BEdita\Core\Test\Fixture\RolesFixture;
use Cake\Database\Schema\TableSchema;
use Cake\TestSuite\TestCase;

/**
 * TestMissingSchemaFileFixture class
 */
class TestMissingSchemaFileFixture extends TestFixture
{
    /**
     * {@inheritDoc}
     */
    protected $schemaPlugin = 'BEdita/API';
}

/**
 * TestMissingSchemaPluginFixture class
 */
class TestMissingSchemaPluginFixture extends TestFixture
{
    /**
     * {@inheritDoc}
     */
    protected $schemaPlugin = 'NotAPluginLoaded';
}

/**
 * TestMissingTableFromSchemaFixture class
 */
class TestMissingTableFromSchemaFixture extends TestFixture
{
}

/**
 * {@see \BEdita\Core\TestSuite\Fixture\TestFixture} Test Case
 *
 * @coversDefaultClass \BEdita\Core\TestSuite\Fixture\TestFixture
 */
class TestFixtureTest extends TestCase
{
    /**
     * test init()
     *
     * @return void
     *
     * @covers ::init()
     */
    public function testInit()
    {
        $rolesFixture = new RolesFixture();
        $this->assertEquals('roles', $rolesFixture->table);
        $this->assertNotEmpty($rolesFixture->fields);

        $customRolesFixture = new RolesFixture();
        $customRolesFixture->table = 'test_custom_roles_table';
        $customRolesFixture->fields = [
            'id' => ['type' => 'integer', 'length' => 10],
            'name' => ['type' => 'string', 'length' => 255],
        ];
        $customRolesFixture->init();
        $this->assertEquals('test_custom_roles_table', $customRolesFixture->table);
        $this->assertNotEquals($rolesFixture->fields, $customRolesFixture->fields);
    }

    /**
     * test implementedEvents()
     *
     * @return void
     *
     * @covers ::implementedEvents()
     */
    public function testImplementedEvents()
    {
        $rolesFixture = new RolesFixture();
        $this->assertCount(0, $rolesFixture->eventManager()->listeners('TestFixture.beforeBuildSchema'));

        $objectsFixture = new ObjectsFixture();
        $this->assertCount(1, $objectsFixture->eventManager()->listeners('TestFixture.beforeBuildSchema'));
    }

    /**
     * data provider for `testFieldsFromConf()` test case
     *
     * @return array
     */
    public function fieldsFromConfigProvider()
    {
        $schemaErrorMsg = 'Cannot describe schema for table `%s` for fixture `%s` : the table does not exist.';

        return [
            'missingSchemaFile' => [
                new \Cake\Core\Exception\Exception(
                    sprintf(
                        $schemaErrorMsg,
                        'test_missing_schema_files',
                        TestMissingSchemaFileFixture::class
                    )
                ),
                TestMissingSchemaFileFixture::class
            ],
            'missingSchemaPlugin' => [
                new \Cake\Core\Exception\Exception(
                    sprintf(
                        $schemaErrorMsg,
                        'test_missing_schema_plugins',
                        TestMissingSchemaPluginFixture::class
                    )
                ),
                TestMissingSchemaPluginFixture::class
            ],
            'missingTableFromSchema' => [
                new \Cake\Core\Exception\Exception(
                    sprintf(
                        $schemaErrorMsg,
                        'test_missing_table_from_schemas',
                        TestMissingTableFromSchemaFixture::class
                    )
                ),
                TestMissingTableFromSchemaFixture::class
            ],
            'ok' => [
                true,
                RolesFixture::class
            ],
        ];
    }

    /**
     * test fieldsFromConf()
     *
     * @param mixed $expected The expected result
     * @param string $fixtureClass The fixture class to use
     * @return void
     *
     * @dataProvider fieldsFromConfigProvider
     * @covers ::fieldsFromConf()
     * @covers ::getTableSchemaFromConf()
     */
    public function testFieldsFromConf($expected, $fixtureClass)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $fixture = new $fixtureClass();
        $this->assertEquals($expected, (bool)$fixture->fields);
    }

    /**
     * data provider for `testSchemaToFields()` test case
     *
     * @return array
     */
    public function schemaToFieldsProvider()
    {
        return [
            'customSchema' => [
                [
                    'id' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'autoIncrement' => true,
                        'precision' => null,
                        'null' => null,
                        'default' => null,
                        'comment' => null
                    ],
                    'name' => [
                        'type' => 'string',
                        'length' => 100,
                        'precision' => null,
                        'null' => null,
                        'default' => null,
                        'comment' => null,
                        'fixed' => null
                    ],
                    '_constraints' => [
                        'primary' => [
                            'type' => 'primary',
                            'columns' => ['id'],
                            'length' => []
                        ],
                    ],
                    '_indexes' => [
                        'test_name_idx' => [
                            'type' => 'index',
                            'columns' => ['name'],
                            'length' => []
                        ],
                    ],
                    '_options' => [
                        'collation' => 'utf8_general_ci'
                    ],
                ],
                [
                    'columns' => [
                        'id' => [
                            'type' => 'integer',
                            'length' => 10,
                            'unsigned' => true,
                            'autoIncrement' => true
                        ],
                        'name' => [
                            'type' => 'string',
                            'length' => 100,
                            'collate' => 'utf8_general_ci',
                        ],
                    ],
                    'constraints' => [
                        'primary' => [
                            'type' => 'primary',
                            'columns' => ['id']
                        ],
                    ],
                    'indexes' => [
                        'test_name_idx' => [
                            'type' => 'index',
                            'columns' => ['name']
                        ],
                    ],
                    'options' => ['collation' => 'utf8_general_ci']
                ]
            ]
        ];
    }

    /**
     * test schemaToFields()
     *
     * @param array $expected The expected `Fixture::$fields`
     * @param array $data The schema data used to build `TableSchema`
     * @return void
     *
     * @dataProvider schemaToFieldsProvider
     * @covers ::fieldsFromConf()
     */
    public function testSchemaToFields($expected, $data)
    {
        $schema = new TableSchema('test_schema', $data['columns']);
        if (!empty($data['constraints'])) {
            foreach ($data['constraints'] as $name => $options) {
                $schema->addConstraint($name, $options);
            }
        }

        if (!empty($data['indexes'])) {
            foreach ($data['indexes'] as $name => $options) {
                $schema->addIndex($name, $options);
            }
        }

        if (!empty($data['options'])) {
            $schema->setOptions($data['options']);
        }

        $fixtureMock = $this->getMockBuilder(RolesFixture::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTableSchemaFromConf'])
            ->getMock();

        $fixtureMock->method('getTableSchemaFromConf')
            ->willReturn($schema);

        $fixtureMock->fields = [];
        $fixtureMock->init();

        $this->assertEquals($expected, $fixtureMock->fields);
    }
}
