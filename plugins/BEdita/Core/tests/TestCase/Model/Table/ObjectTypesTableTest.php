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
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\ObjectTypesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ObjectTypesTable
 */
class ObjectTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectTypesTable
     */
    public $ObjectTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Cache::clear(false, ObjectTypesTable::CACHE_CONFIG);

        $this->ObjectTypes = TableRegistry::get('ObjectTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ObjectTypes);

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
        $this->ObjectTypes->initialize([]);

        $this->assertEquals('object_types', $this->ObjectTypes->getTable());
        $this->assertEquals('id', $this->ObjectTypes->getPrimaryKey());
        $this->assertEquals('name', $this->ObjectTypes->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\HasMany', $this->ObjectTypes->Objects);
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
                    'name' => 'foo',
                    'pluralized' => 'foos',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notUnique' => [
                false,
                [
                    'name' => 'document',
                    'pluralized' => 'many_documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notUnique2' => [
                false,
                [
                    'name' => 'card',
                    'pluralized' => 'profiles',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notCrossUnique1' => [
                false,
                [
                    'name' => 'profiles',
                    'pluralized' => 'many_profiles',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notCrossUnique2' => [
                false,
                [
                    'name' => 'piece_of_profile',
                    'pluralized' => 'profile',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
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
     * @covers \BEdita\Core\ORM\Rule\IsUniqueAmongst
     */
    public function testValidation($expected, array $data)
    {
        $objectType = $this->ObjectTypes->newEntity();
        $this->ObjectTypes->patchEntity($objectType, $data);

        $success = $this->ObjectTypes->save($objectType);
        $this->assertEquals($expected, (bool)$success);
    }

    /**
     * Data provider for `testGet` test case.
     *
     * @return array
     */
    public function getProvider()
    {
        return [
            'id' => [
                [
                    'id' => 1,
                    'name' => 'document',
                    'pluralized' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                ],
                1,
            ],
            'stringId' => [
                [
                    'id' => 1,
                    'name' => 'document',
                    'pluralized' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                ],
                '1',
            ],
            'singular' => [
                [
                    'id' => 1,
                    'name' => 'document',
                    'pluralized' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                ],
                'document',
            ],
            'plural' => [
                [
                    'id' => 1,
                    'name' => 'document',
                    'pluralized' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                ],
                'documents',
            ],
            'missingId' => [
                false,
                99,
            ],
            'missingType' => [
                false,
                'missing_type',
            ],
        ];
    }

    /**
     * Test get method.
     *
     * @param array|false $expected Expected result.
     * @param string|int $primaryKey Primary key.
     * @return void
     *
     * @dataProvider getProvider
     * @covers ::get()
     */
    public function testGet($expected, $primaryKey)
    {
        if (!$expected) {
            $this->expectException('\Cake\Datasource\Exception\RecordNotFoundException');
        }

        $entity = $this->ObjectTypes->get($primaryKey);

        $this->assertEquals($expected, $entity->toArray());
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
        $entity = $this->ObjectTypes->get('document');

        $this->assertNotFalse(Cache::read('id_1', ObjectTypesTable::CACHE_CONFIG));
        $this->assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        $this->assertNotFalse(Cache::read('map_pluralized', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->ObjectTypes->patchEntity($entity, ['name' => 'foo', 'pluralized' => 'foos']);
        $this->ObjectTypes->save($entity);

        $this->assertFalse(Cache::read('id_1', ObjectTypesTable::CACHE_CONFIG));
        $this->assertFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        $this->assertFalse(Cache::read('map_pluralized', ObjectTypesTable::CACHE_CONFIG));
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
        $entity = $this->ObjectTypes->get('document');

        $this->assertNotFalse(Cache::read('id_1', ObjectTypesTable::CACHE_CONFIG));
        $this->assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        $this->assertNotFalse(Cache::read('map_pluralized', ObjectTypesTable::CACHE_CONFIG));

        $this->ObjectTypes->delete($entity);

        $this->assertFalse(Cache::read('id_1', ObjectTypesTable::CACHE_CONFIG));
        $this->assertFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        $this->assertFalse(Cache::read('map_pluralized', ObjectTypesTable::CACHE_CONFIG));
    }
}
