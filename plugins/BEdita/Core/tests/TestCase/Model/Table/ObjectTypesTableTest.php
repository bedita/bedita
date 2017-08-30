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
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association\HasMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

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
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Cache::drop('_bedita_object_types_');
        Cache::setConfig('_bedita_object_types_', ['className' => 'File']);

        $this->ObjectTypes = TableRegistry::get('ObjectTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ObjectTypes);

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
        $this->ObjectTypes->initialize([]);

        static::assertEquals('object_types', $this->ObjectTypes->getTable());
        static::assertEquals('id', $this->ObjectTypes->getPrimaryKey());
        static::assertEquals('name', $this->ObjectTypes->getDisplayField());

        static::assertInstanceOf(HasMany::class, $this->ObjectTypes->Objects);
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
                    'singular' => 'foo',
                    'name' => 'foos',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notUnique' => [
                false,
                [
                    'singular' => 'document',
                    'name' => 'many_documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notUnique2' => [
                false,
                [
                    'singular' => 'card',
                    'name' => 'profiles',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notCrossUnique1' => [
                false,
                [
                    'singular' => 'profiles',
                    'name' => 'many_profiles',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notCrossUnique2' => [
                false,
                [
                    'singular' => 'piece_of_profile',
                    'name' => 'profile',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notUniqueNotNew' => [
                false,
                [
                    'id' => 1,
                    'singular' => 'profiles',
                    'name' => 'profile',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Profiles',
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
        if (!empty($data['id'])) {
            $objectType = $this->ObjectTypes->get($data['id']);
        }
        $this->ObjectTypes->patchEntity($objectType, $data);

        $success = $this->ObjectTypes->save($objectType);
        static::assertEquals($expected, (bool)$success);
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
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                ],
                1,
            ],
            'stringId' => [
                [
                    'id' => 1,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                ],
                '1',
            ],
            'singular' => [
                [
                    'id' => 1,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                ],
                'document',
            ],
            'plural' => [
                [
                    'id' => 1,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                ],
                'documents',
            ],
            'notUnderscored' => [
                [
                    'id' => 1,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                ],
                'Documents',
            ],
            'missingId' => [
                new RecordNotFoundException('Record not found in table "object_types"'),
                99,
            ],
            'missingType' => [
                new RecordNotFoundException('Record not found in table "object_types"'),
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
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        $entity = $this->ObjectTypes->get($primaryKey);

        static::assertTrue($entity->has('left_relations'));
        static::assertTrue($entity->has('right_relations'));
        foreach ($entity->left_relations as $relation) {
            static::assertTrue($relation->has('right_object_types'));
        }
        foreach ($entity->right_relations as $relation) {
            static::assertTrue($relation->has('left_object_types'));
        }

        $result = $entity->toArray();
        // remove 'left_relations' and 'right_relations' because contain to many data
        $result = Hash::remove($result, 'left_relations');
        $result = Hash::remove($result, 'right_relations');

        static::assertEquals($expected, $result);
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
        $this->ObjectTypes->get(2);
        $this->ObjectTypes->get(5);

        static::assertNotFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_5_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->ObjectTypes->patchEntity($entity, ['singular' => 'foo', 'name' => 'foos']);
        $this->ObjectTypes->save($entity);

        static::assertFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_5_rel', ObjectTypesTable::CACHE_CONFIG));
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
        $entity = $this->ObjectTypes->get('document');
        $this->ObjectTypes->get(2);
        $this->ObjectTypes->get(5);

        static::assertNotFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_5_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $this->ObjectTypes->delete($entity);

        static::assertFalse(Cache::read('id_1_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_5_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Data provider for `testFindByRelation` test case.
     *
     * @return array
     */
    public function findByRelationProvider()
    {
        return [
            'error' => [
                new \LogicException('Missing required parameter "name"'),
                [],
            ],
            'right' => [
                ['documents', 'profiles'],
                ['name' => 'test'],
            ],
            'left' => [
                ['documents'],
                ['name' => 'test', 'side' => 'left'],
            ],
            'inverse right' => [
                ['documents'],
                ['name' => 'inverse_test'],
            ],
            'inverse left' => [
                ['documents', 'profiles'],
                ['name' => 'inverse_test', 'side' => 'left'],
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
     * @covers ::findByRelation()
     * @dataProvider findByRelationProvider()
     */
    public function testFindByRelation($expected, array $options)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        $result = $this->ObjectTypes
            ->find('byRelation', $options)
            ->find('list')
            ->toArray();

        static::assertEquals($expected, $result, '', 0, 10, true);
    }

    /**
     * Test default finder.
     *
     * @return void
     *
     * @covers ::findAll()
     */
    public function testFindAll()
    {
        $query = $this->ObjectTypes->find();
        $contain = $query->contain();

        static::assertArrayHasKey('LeftRelations', $contain);
        static::assertArrayHasKey('RightRelations', $contain);
    }
}
