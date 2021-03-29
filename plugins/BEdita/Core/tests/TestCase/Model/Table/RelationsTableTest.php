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

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;
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
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Relations',
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

        $this->Relations = TableRegistry::getTableLocator()->get('Relations');
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
                        'type' => 'object',
                        'properties' => [
                            'param1' => [
                                'type' => 'string',
                            ],
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
            'simple' => [
                true,
                [
                    'name' => 'some_relation',
                    'inverse_name' => 'some_inverse_relation',
                ],
            ],
            'empty label' => [
                false,
                [
                    'id' => 1,
                    'inverse_name' => 'reverse_test',
                    'inverse_label' => '',
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
        if (empty($data['id'])) {
            $objectType = $this->Relations->newEntity();
        } else {
            $objectType = $this->Relations->get($data['id']);
        }
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
                new BadFilterException('Missing required parameter "name"'),
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
     * @param array|\Exception $expected Expected results.
     * @param array $options Finder options.
     * @return void
     *
     * @covers ::findByName()
     * @dataProvider findByNameProvider()
     */
    public function testFindByName($expected, array $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
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
        $this->Relations->LeftObjectTypes->get(3);

        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->Relations->get(1);
        $entity = $this->Relations->patchEntity($entity, ['description' => 'My brand new description']);
        $this->Relations->save($entity);

        static::assertFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Test before save callback on an existing entity.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSaveExisting()
    {
        $entity = $this->Relations->get(1);
        $originalLabel = $entity->label;
        $originalInverseLabel = $entity->inverse_label;

        $entity = $this->Relations->patchEntity($entity, [
            'name' => 'foo_bar',
            'inverse_name' => 'bar_foo',
        ]);
        $this->Relations->saveOrFail($entity);

        $entity = $this->Relations->get(1);
        static::assertSame('foo_bar', $entity->name);
        static::assertSame($originalLabel, $entity->label);
        static::assertSame('bar_foo', $entity->inverse_name);
        static::assertSame($originalInverseLabel, $entity->inverse_label);
    }

    /**
     * Test before save callback with a new entity.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSave()
    {
        $entity = $this->Relations->newEntity([
            'name' => 'some_name',
            'inverse_name' => 'some_inverse_name',
        ]);
        $entity = $this->Relations->saveOrFail($entity);

        static::assertSame('Some Name', $entity->get('label'));
        static::assertSame('Some Inverse Name', $entity->get('inverse_label'));
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
        $this->Relations->LeftObjectTypes->get(3);

        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->Relations->get(1);
        $this->Relations->delete($entity);

        static::assertFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Data provider for testGet()
     *
     * @return array
     */
    public function getProvider()
    {
        return [
            'int' => [
                1,
                1,
            ],
            'numeric' => [
                1,
                '1',
            ],
            'name' => [
                1,
                'test',
            ],
            'inverse_name' => [
                1,
                'inverse_test',
            ],
            'notFoundString' => [
                new RecordNotFoundException('Record not found in table "relations"'),
                'not_exists',
            ],
        ];
    }

    /**
     * Test get() method.
     * If it was success just the entity id is tested
     *
     * @param int|\Exception $expected The expected result
     * @param int|string $search The search value
     * @return void
     *
     * @dataProvider getProvider
     * @covers ::get()
     */
    public function testGet($expected, $search)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $relation = $this->Relations->get($search);
        static::assertEquals($expected, $relation->id);
    }
}
