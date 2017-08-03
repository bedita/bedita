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
 * @coversDefaultClass \BEdita\Core\Model\Table\RelationsTable
 */
class RelationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\RelationsTable
     */
    public $Relations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.relations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'File']);

        $this->Relations = TableRegistry::get('Relations');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Relations);

        Cache::clear(false, ObjectTypesTable::CACHE_CONFIG);
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
            'valid' => [
                true,
                [
                    'name' => 'my_relation',
                    'label' => 'My Relation',
                    'inverse_name' => 'my_inverse_relation',
                    'inverse_label' => 'My Inverse Relation',
                    'description' => 'null',
                    'params' => [
                        [
                            'name' => 'param1',
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'notUnique' => [
                false,
                [
                    'name' => 'test',
                    'label' => 'Some label',
                    'inverse_name' => 'tset',
                    'inverse_label' => 'Same label :)',
                    'description' => null,
                    'params' => null,
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
     *
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $objectType = $this->Relations->newEntity();
        $this->Relations->patchEntity($objectType, $data);

        $success = $this->Relations->save($objectType);
        static::assertEquals($expected, (bool)$success);
    }

    /**
     * Data provider for `testFindByName` test case.
     *
     * @return array
     */
    public function findByNameProvider()
    {
        return [
            'error' => [
                new \LogicException('Missing required parameter "name"'),
                [],
            ],
            'name' => [
                [1],
                ['name' => 'test'],
            ],
            'inverse name' => [
                [1],
                ['name' => 'InverseTest'],
            ],
            'not found' => [
                [],
                ['name' => 'relation_does_not_exist'],
            ],
        ];
    }

    /**
     * Test finder by relation name.
     *
     * @param array\\Exception $expected Expected results.
     * @param array $options Finder options.
     * @return void
     *
     * @covers ::findByName()
     * @dataProvider findByNameProvider()
     */
    public function testFindByName($expected, array $options)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        $result = $this->Relations
            ->find('byName', $options)
            ->extract('id')
            ->toArray();

        static::assertEquals($expected, $result, '', 0, 10, true);
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
        $this->Relations->LeftObjectTypes->get('document');
        $this->Relations->LeftObjectTypes->get(2);

        static::assertNotFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->Relations->get(1);
        $entity = $this->Relations->patchEntity($entity, ['description' => 'My brand new description']);
        $this->Relations->save($entity);

        static::assertFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));
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
        $this->Relations->LeftObjectTypes->get('document');
        $this->Relations->LeftObjectTypes->get(2);

        static::assertNotFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->Relations->get(1);
        $this->Relations->delete($entity);

        static::assertFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));
    }
}
