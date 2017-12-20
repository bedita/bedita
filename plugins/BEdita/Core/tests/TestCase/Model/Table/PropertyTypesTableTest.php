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
        'plugin.BEdita/Core.streams',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'File']);

        $this->PropertyTypes = TableRegistry::get('PropertyTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->PropertyTypes);

        Cache::clear(false, ObjectTypesTable::CACHE_CONFIG);
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
     *
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
     *
     * @covers ::afterSave()
     */
    public function testInvalidateCacheAfterSave()
    {
        $this->PropertyTypes->Properties->get(1);

        static::assertNotFalse(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));

        $propertyType = $this->PropertyTypes->get(9);
        $propertyType->name = 'gustavo';
        $this->PropertyTypes->save($propertyType);

        static::assertFalse(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Test after delete callback.
     *
     * @return void
     *
     * @covers ::afterDelete()
     */
    public function testInvalidateCacheAfterDelete()
    {
        $this->PropertyTypes->Properties->get(1);

        static::assertNotFalse(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));

        $propertyType = $this->PropertyTypes->get(10);
        $this->PropertyTypes->delete($propertyType);

        static::assertFalse(Cache::read('property_types', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Test that an exception is raised when attempting to delete a property type in use.
     *
     * @return void
     *
     * @covers ::beforeDelete()
     * @expectedException \Cake\Network\Exception\ForbiddenException
     * @expectedExceptionCode 403
     * @expectedExceptionMessage Property type with existing properties
     */
    public function testBeforeDeleteInUse()
    {
        $propertyType = $this->PropertyTypes->get(1);

        $this->PropertyTypes->delete($propertyType);
    }

    /**
     * Test that no exception is raised when attempting to delete a property type not in use.
     *
     * @return void
     *
     * @covers ::beforeDelete()
     */
    public function testBeforeDeleteOk()
    {
        $propertyType = $this->PropertyTypes->get(10);

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
                'number',
                'duration',
                'Streams',
            ],
            'float' => [
                'number',
                'duration',
                'Streams',
                'float',
            ],
            'date' => [
                'date',
                'created',
                'Streams',
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
     *
     * @dataProvider detectProvider()
     * @covers ::detect()
     */
    public function testDetect($expected, $name, $table, $overrideType = null)
    {
        $table = TableRegistry::get($table);
        if ($overrideType !== null) {
            $table
                ->setValidator('default', new Validator())
                ->getSchema()
                ->setColumnType($name, $overrideType);
        }

        $result = $this->PropertyTypes->detect($name, $table)->name;

        static::assertSame($expected, $result);
    }
}
