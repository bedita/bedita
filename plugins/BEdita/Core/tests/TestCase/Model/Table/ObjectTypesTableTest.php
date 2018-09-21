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

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ForbiddenException;
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
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.object_relations',
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
            'notNumericName' => [
                false,
                [
                    'singular' => '123_item',
                    'name' => '123',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'notNumericSingular' => [
                false,
                [
                    'singular' => '123',
                    'name' => '123s',
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
            'sameNameSingular' => [
                false,
                [
                    'singular' => 'gustavo',
                    'name' => 'gustavo',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'reservedName' => [
                false,
                [
                    'singular' => 'application_item',
                    'name' => 'applications',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ],
            'reservedSingular' => [
                false,
                [
                    'singular' => 'role',
                    'name' => 'role_list',
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
                    'id' => 2,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                    'is_abstract' => false,
                    'parent_name' => 'objects',
                    'created' => '2017-11-10T09:27:23+00:00',
                    'modified' => '2017-11-10T09:27:23+00:00',
                    'core_type' => true,
                    'enabled' => true,
                ],
                2,
            ],
            'stringId' => [
                [
                    'id' => 2,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                    'is_abstract' => false,
                    'parent_name' => 'objects',
                    'created' => '2017-11-10T09:27:23+00:00',
                    'modified' => '2017-11-10T09:27:23+00:00',
                    'core_type' => true,
                    'enabled' => true,
                ],
                '2',
            ],
            'singular' => [
                [
                    'id' => 2,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                    'is_abstract' => false,
                    'parent_name' => 'objects',
                    'created' => '2017-11-10T09:27:23+00:00',
                    'modified' => '2017-11-10T09:27:23+00:00',
                    'core_type' => true,
                    'enabled' => true,
                ],
                'document',
            ],
            'plural' => [
                [
                    'id' => 2,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                    'is_abstract' => false,
                    'parent_name' => 'objects',
                    'created' => '2017-11-10T09:27:23+00:00',
                    'modified' => '2017-11-10T09:27:23+00:00',
                    'core_type' => true,
                    'enabled' => true,
                ],
                'documents',
            ],
            'notUnderscored' => [
                [
                    'id' => 2,
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'alias' => 'Documents',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                    'is_abstract' => false,
                    'parent_name' => 'objects',
                    'created' => '2017-11-10T09:27:23+00:00',
                    'modified' => '2017-11-10T09:27:23+00:00',
                    'core_type' => true,
                    'enabled' => true,
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
        $result['created'] = $result['created']->jsonSerialize();
        $result['modified'] = $result['modified']->jsonSerialize();

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
        $this->ObjectTypes->get(3);
        $this->ObjectTypes->get(6);

        static::assertNotFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $entity = $this->ObjectTypes->patchEntity($entity, ['singular' => 'foo', 'name' => 'foos']);
        $this->ObjectTypes->save($entity);

        static::assertFalse(Cache::read('id_2_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
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
        // there are no 'news` in objects fixture, safe to delete it
        $entity = $this->ObjectTypes->get('news_item');
        $this->ObjectTypes->get(3);
        $this->ObjectTypes->get(6);

        static::assertNotFalse(Cache::read('id_5_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map', ObjectTypesTable::CACHE_CONFIG));
        static::assertNotFalse(Cache::read('map_singular', ObjectTypesTable::CACHE_CONFIG));

        $this->ObjectTypes->delete($entity);

        static::assertFalse(Cache::read('id_5_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_3_rel', ObjectTypesTable::CACHE_CONFIG));
        static::assertFalse(Cache::read('id_6_rel', ObjectTypesTable::CACHE_CONFIG));
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
            'with descendants' => [
                ['media', 'files'],
                ['name' => 'test_abstract', 'descendants' => true],
            ],
            'relation not found' => [
                [],
                ['name' => 'this_relation_does_not_exist'],
            ],
            'relation not found, with descendants' => [
                [],
                ['name' => 'this_relation_does_not_exist', 'descendants' => true],
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

    /**
     * Provider for `testModelRules`
     *
     * @return array
     */
    public function modelRulesProvider()
    {
        return [
            'foo' => [
                [
                    'singular' => 'foo',
                    'name' => 'foos',
                ]
            ],
            'cats' => [
                [
                    'singular' => 'foo',
                    'name' => 'foos',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'parent_name' => 'objects',
                ]
            ],
        ];
    }

    /**
     * Test default parent, plugin and model.
     *
     * @param array $data Entity data.
     * @return void
     *
     * @dataProvider modelRulesProvider
     * @covers ::beforeRules()
     */
    public function testDefaultModelRules(array $data)
    {
        $objectType = $this->ObjectTypes->newEntity();
        $this->ObjectTypes->patchEntity($objectType, $data);

        $success = $this->ObjectTypes->save($objectType);
        static::assertNotEmpty($success);

        $parentId = empty($data['parent_name']) ?
            ObjectTypesTable::DEFAULT_PARENT_ID : $this->ObjectTypes->get($data['parent_name'])->id;

        static::assertSame($parentId, $success->parent_id);
        static::assertTrue((bool)$success);
    }

    /**
     * Data provider for `testBeforeDelete`
     *
     * @return array
     */
    public function beforeDeleteProvider()
    {
        return [
            'objects' => [
                'objects',
                new ForbiddenException('Abstract type with existing subtypes'),
            ],
            'documents' => [
                'documents',
                new ForbiddenException('Core types are not removable'),
            ],
            'news' => [
                'news',
                true,
            ],
        ];
    }

    /**
     * Test `beforeDelete`
     *
     * @param string $typeName Object type name to delete
     * @param mixed $expected Expected result: exception or boolean
     * @return void
     * @dataProvider beforeDeleteProvider
     * @covers ::beforeDelete()
     * @covers ::beforeRules()
     */
    public function testBeforeDelete($typeName, $expected)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $entity = $this->ObjectTypes->get($typeName);
        $result = $this->ObjectTypes->delete($entity);
        static::assertEquals($expected, $result);
    }

    /**
     * Test delete failure when `Objects of this type exist`
     *
     * @return void
     * @covers ::beforeDelete()
     */
    public function testDeleteWithObjects()
    {
        $expected = new ForbiddenException('Objects of this type exist');
        $this->expectException(get_class($expected));
        $this->expectExceptionMessage($expected->getMessage());

        $data = [
            'singular' => 'foo',
            'name' => 'foos',
        ];
        $entity = $this->ObjectTypes->newEntity();
        $entity = $this->ObjectTypes->patchEntity($entity, $data);
        $this->ObjectTypes->save($entity);

        $data = [
            'title' => 'Foo',
        ];
        $table = TableRegistry::get('Foos');
        $entity = $table->newEntity();
        $entity = $table->patchEntity($entity, $data);
        $entity->created_by = 1;
        $entity->modified_by = 1;
        $success = $table->save($entity);
        static::assertTrue((bool)$success);

        $objectType = $this->ObjectTypes->get('foos');
        $this->ObjectTypes->delete($objectType);
    }

    /**
     * Test `parent_name` change with existing objects
     *
     * @return void
     * @covers ::beforeRules()
     *
     * @expectedException \Cake\Network\Exception\ForbiddenException
     * @expectedExceptionMessage Parent type change forbidden: objects of this type exist
     */
    public function testChangeParent()
    {
        $objectType = $this->ObjectTypes->get('users');
        $objectType->set('parent_name', 'media');
        $this->ObjectTypes->save($objectType);
    }

    /**
     * Data provider for `testBeforeSave`
     *
     * @return array
     */
    public function beforeSaveProvider()
    {
        return [
            'objects' => [
                [
                    'id' => 1,
                    'is_abstract' => false,
                ],
                new ForbiddenException('Setting as not abstract forbidden: subtypes exist'),
            ],
            // there are no 'news` in objects fixture, safe to set as abstract
            'news' => [
                [
                    'id' => 5,
                    'is_abstract' => true,
                ],
                true,
            ],
            'documents' => [
                [
                    'id' => 2,
                    'is_abstract' => true,
                ],
                new ForbiddenException('Setting as abstract forbidden: objects of this type exist'),
            ],
            'documentsDisable' => [
                [
                    'id' => 2,
                    'enabled' => false,
                ],
                new ForbiddenException('Type disable forbidden: objects of this type exist'),
            ],
            'mediaDisable' => [
                [
                    'id' => 8,
                    'enabled' => false,
                ],
                new ForbiddenException('Type disable forbidden: subtypes exist'),
            ],
            'tableNotFound' => [
                [
                    'id' => 2,
                    'table' => 'Missing/Plugin.NotFound',
                ],
                new BadRequestException('"Missing/Plugin.NotFound" is not a valid model table name'),
            ],
        ];
    }

    /**
     * Test `beforeSave`
     *
     * @param array $data Data to save
     * @param mixed $expected Expected result: exception or boolean
     * @return void
     * @dataProvider beforeSaveProvider
     * @covers ::beforeSave()
     * @covers ::objectsExist()
     */
    public function testBeforeSave($data, $expected)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $objectType = $this->ObjectTypes->newEntity();
        if (!empty($data['id'])) {
            $objectType = $this->ObjectTypes->get($data['id']);
        }
        $this->ObjectTypes->patchEntity($objectType, $data);
        $success = $this->ObjectTypes->save($objectType);
        static::assertEquals($expected, (bool)$success);
    }

    /**
     * Data provider for `testFindObjectId()`
     *
     * @return array
     */
    public function findObjectIdProvider()
    {
        return [
            'missingId' => [
                new BadFilterException('Missing required parameter "id"'),
                [],
            ],
            'emptyId' => [
                new BadFilterException('Missing required parameter "id"'),
                ['id' => ''],
            ],
            'findById' => [
                'documents',
                ['id' => 2],
            ],
            'findByUname' => [
                'users',
                ['id' => 'first-user'],
            ],
        ];
    }

    /**
     * Test custom finder `findObjectId()`
     *
     * @param mixed $expected The expected result.
     * @param array $options The option passed to finder.
     * @return void
     *
     * @covers ::findObjectId
     * @dataProvider findObjectIdProvider
     */
    public function testFindObjectId($expected, array $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $type = $this->ObjectTypes->find('objectId', $options)->firstOrFail();
        static::assertEquals($expected, $type->name);
    }

    /**
     * Data provider for `findParent()`
     *
     * @return array
     */
    public function findParentProvider()
    {
        return [
            'missing' => [
                new BadFilterException('Missing required parameter "parent"'),
                [],
            ],
            'find id' => [
                'documents',
                ['id' => 1],
            ],
            'find by name' => [
                'files',
                ['id' => 'media'],
            ],
        ];
    }

    /**
     * Test `findParent()` finder method
     *
     * @param mixed $expected The expected result.
     * @param array $options The option passed to finder.
     * @return void
     *
     * @covers ::findParent()
     * @dataProvider findParentProvider
     */
    public function testFindParent($expected, array $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $type = $this->ObjectTypes->find('parent', $options)->order(['name' => 'ASC'])->firstOrFail();
        static::assertEquals($expected, $type->name);
    }
}
