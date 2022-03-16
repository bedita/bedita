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

use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\ORM\Association\HasMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Validation\Validator;

/**
 * {@see \BEdita\Core\Model\Table\PropertyTypesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\PropertyTypesTable
 */
class PropertyTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\PropertyTypesTable
     */
    public $PropertyTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'File']);

        $this->PropertyTypes = TableRegistry::getTableLocator()->get('PropertyTypes');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->PropertyTypes);

        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'Null']);

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
        $this->PropertyTypes->initialize([]);
        static::assertEquals('property_types', $this->PropertyTypes->getTable());
        static::assertEquals('id', $this->PropertyTypes->getPrimaryKey());
        static::assertEquals('name', $this->PropertyTypes->getDisplayField());

        static::assertInstanceOf(HasMany::class, $this->PropertyTypes->Properties);
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
                    'name' => 'propName',
                    'params' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'notValid' => [
                false,
                [
                    'name' => '',
                    'params' => '',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $PropertyTypes = $this->PropertyTypes->newEntity($data);

        $error = (bool)$PropertyTypes->getErrors();
        static::assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->PropertyTypes->save($PropertyTypes);
            static::assertTrue((bool)$success);
        }
    }

    /**
     * Test after save callback.
     *
     * @return void
     * @covers ::afterSave()
     */
    public function testInvalidateCacheAfterSave()
    {
        $this->PropertyTypes->Properties->get(1);

        static::assertNotFalse(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));

        $propertyType = $this->PropertyTypes->get(12);
        $propertyType->name = 'gustavo';
        $this->PropertyTypes->save($propertyType);

        static::assertNull(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Test after delete callback.
     *
     * @return void
     * @covers ::afterDelete()
     */
    public function testInvalidateCacheAfterDelete()
    {
        $this->PropertyTypes->Properties->get(1);

        static::assertNotNull(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));

        $propertyType = $this->PropertyTypes->get(12);
        $this->PropertyTypes->delete($propertyType);

        static::assertNull(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Test that an exception is raised when attempting to delete a property type in use.
     *
     * @return void
     * @covers ::beforeDelete()
     */
    public function testBeforeDeleteInUse()
    {
        $this->expectException(\Cake\Http\Exception\ForbiddenException::class);
        $this->expectExceptionCode('403');
        $this->expectExceptionMessage('Property type with existing properties');
        $propertyType = $this->PropertyTypes->get(1);

        $this->PropertyTypes->delete($propertyType);
    }

    /**
     * Test that no exception is raised when attempting to delete a property type not in use.
     *
     * @return void
     * @covers ::beforeDelete()
     */
    public function testBeforeDeleteOk()
    {
        $propertyType = $this->PropertyTypes->get(12);

        $success = $this->PropertyTypes->delete($propertyType);

        static::assertTrue($success);
    }

    /**
     * Data provider for `testDetect` test case.
     *
     * @return array
     */
    public function detectProvider()
    {
        return [
            'by name' => [
                'status',
                'status',
                'Objects',
            ],
            'by validation rule' => [
                'email',
                'email',
                'Profiles',
            ],
            'by type name' => [
                'string',
                'lang',
                'Objects',
            ],
            'integer' => [
                'integer',
                'duration',
                'Streams',
            ],
            'timestamp' => [
                'datetime',
                'expires',
                'AsyncJobs',
                'timestamp',
            ],
            'float' => [
                'number',
                'duration',
                'Streams',
                'float',
            ],
            'datetime' => [
                'datetime',
                'modified',
                'Objects',
            ],
            'date' => [
                'date',
                'birthdate',
                'Profiles',
            ],
            'fallback' => [
                'string',
                'created',
                'Objects',
                'gustavo',
            ],
        ];
    }

    /**
     * Test automatic detection of field type.
     *
     * @param string $expected Expected property type name.
     * @param string $name Column name.
     * @param string $table Table name.
     * @param string $overrideType Column type to override.
     * @return void
     * @dataProvider detectProvider()
     * @covers ::detect()
     */
    public function testDetect($expected, $name, $table, $overrideType = null)
    {
        $table = TableRegistry::getTableLocator()->get($table);
        if ($overrideType !== null) {
            $table
                ->setValidator('default', new Validator())
                ->getSchema()
                ->setColumnType($name, $overrideType);
        }

        $result = $this->PropertyTypes->detect($name, $table)->name;

        static::assertSame($expected, $result);
    }

    /**
     * Test that an exception is raised when attempting to change a core property type.
     *
     * @return void
     * @covers ::beforeSave()
     */
    public function testBeforeSaveForbidden()
    {
        $this->expectException(\BEdita\Core\Exception\ImmutableResourceException::class);
        $this->expectExceptionCode('403');
        $this->expectExceptionMessage('Could not modify core property');
        $propertyType = $this->PropertyTypes->get(1);
        $propertyType->set('name', 'gustavo');
        $this->PropertyTypes->save($propertyType);
    }

    /**
     * Test that no exception is raised when attempting to change a non core property type.
     *
     * @return void
     * @covers ::beforeSave()
     */
    public function testBeforeSaveOk()
    {
        $propertyType = $this->PropertyTypes->get(12);
        $propertyType->set('name', 'gustavo');
        $success = $this->PropertyTypes->save($propertyType);
        static::assertNotFalse($success);
    }
}
