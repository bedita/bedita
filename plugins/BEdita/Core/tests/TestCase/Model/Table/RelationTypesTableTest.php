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

use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Table\RelationTypesTable
 */
class RelationTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\RelationTypesTable
     */
    public $RelationTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Relations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'File']);

        $this->RelationTypes = TableRegistry::getTableLocator()->get('RelationTypes');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->RelationTypes);

        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'Null']);

        parent::tearDown();
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'validLeft' => [
                true,
                [
                    'object_type_id' => 4,
                    'relation_id' => 1,
                    'side' => 'left',
                ],
            ],
            'validRight' => [
                true,
                [
                    'object_type_id' => 4,
                    'relation_id' => 1,
                    'side' => 'right',
                ],
            ],
            'invalidSide' => [
                false,
                [
                    'object_type_id' => 4,
                    'relation_id' => 1,
                    'side' => 'Dark side of the Moon',
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
        $objectType = $this->RelationTypes->newEntity([]);
        $this->RelationTypes->patchEntity($objectType, $data);

        $success = $this->RelationTypes->save($objectType);
        static::assertEquals($expected, (bool)$success);
    }

    /**
     * Test after save callback.
     *
     * @return void
     * @covers ::afterSave()
     */
    public function testInvalidateCacheAfterSave()
    {
        $this->RelationTypes->ObjectTypes->get('document');
        $this->RelationTypes->ObjectTypes->get(3);
        $this->RelationTypes->ObjectTypes->get(6);

        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->RelationTypes->get([1, 2, 'left']);
        $entity->object_type = $this->RelationTypes->ObjectTypes->get(5);
        $this->RelationTypes->save($entity);

        static::assertNull(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Test after delete callback.
     *
     * @return void
     * @covers ::afterDelete()
     */
    public function testInvalidateCacheAfterDelete()
    {
        $this->RelationTypes->ObjectTypes->get('document');
        $this->RelationTypes->ObjectTypes->get(3);
        $this->RelationTypes->ObjectTypes->get(6);

        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->RelationTypes->get([1, 2, 'left']);
        $this->RelationTypes->delete($entity);

        static::assertNull(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNull(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));
    }
}
