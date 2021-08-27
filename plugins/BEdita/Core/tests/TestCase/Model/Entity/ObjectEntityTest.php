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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Table\ObjectsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Entity\ObjectEntity} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\ObjectEntity
 */
class ObjectEntityTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ObjectsTable
     */
    public $Objects;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.AuthProviders',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Objects = TableRegistry::getTableLocator()->get('Objects');

        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Objects);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $object = $this->Objects->get(1);

        $created = $object->created;
        $modified = $object->modified;
        $published = $object->published;

        $data = [
            'id' => 42,
            'locked' => false,
            'created' => '2016-01-01 12:00:00',
            'modified' => '2016-01-01 12:00:00',
            'published' => '2016-01-01 12:00:00',
            'created_by' => 2,
            'modified_by' => 2
        ];
        $object = $this->Objects->patchEntity($object, $data);
        if (!($object instanceof ObjectEntity)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $object->id);
        $this->assertTrue($object->locked);
        $this->assertEquals(1, $object->created_by);
        $this->assertEquals(1, $object->modified_by);
        $this->assertEquals($created, $object->created);
        $this->assertEquals($modified, $object->modified);
        $this->assertEquals($published, $object->published);
    }

    /**
     * Data provider for `testGetType` test case.
     *
     * @return array
     */
    public function getTypeProvider()
    {
        return [
            'document' => [
                'documents',
                2,
            ],
            'non existent' => [
                null,
                -1,
            ],
            'invalid' => [
                null,
                null,
            ],
            'typeFromSource' => [
                'documents',
                null,
                ['source' => 'Documents'],
            ],
            'invalidTypeFromSource' => [
                null,
                null,
                ['source' => 'NotValidObjectTable'],
            ],
        ];
    }

    /**
     * Test magic getter for type property.
     *
     * @param string|null $expected Expected type.
     * @param mixed $objectTypeId Object type ID.
     * @param array $options Configuration options for entity.
     * @return void
     *
     * @covers ::_getType()
     * @covers ::loadObjectType()
     * @dataProvider getTypeProvider()
     */
    public function testGetType($expected, $objectTypeId, $options = [])
    {
        $entity = new ObjectEntity([], $options);
        $entity->object_type_id = $objectTypeId;

        $type = $entity->type;

        static::assertSame($expected, $type);
    }

    /**
     * Data provider for `testVisibleProperties` test case.
     *
     * @return array
     */
    public function visiblePropertiesProvider()
    {
        return [
            'document' => [
                [
                    'title',
                    'description'
                ],
                [
                    'title',
                    'description',
                    'type',
                ],
                2,
            ],
            'non existent' => [
                ['body'],
                [
                    'body',
                    'type',
                ],
                -1,
            ],
            'news' => [
                [
                    'body',
                    'description'
                ],
                [
                    'description',
                    'type',
                ],
                5,
            ],
        ];
    }

    /**
     * Test `visibleProperties` method.
     *
     * @param array $properties Properties to set.
     * @param array $expectedVisible Expected result.
     * @param int $objectTypeId Object type ID.
     * @return void
     *
     * @covers ::getVisible()
     * @covers ::loadObjectType()
     * @dataProvider visiblePropertiesProvider()
     */
    public function testVisibleProperties($properties, array $expectedVisible, $objectTypeId)
    {
        $entity = new ObjectEntity();
        $entity->object_type_id = $objectTypeId;

        foreach ($properties as $prop) {
            $entity->set($prop, $prop);
        }
        $visible = $entity->getVisible();

        static::assertSame($expectedVisible, array_values($visible));
    }

    /**
     * Data provider for `testSetType` test case.
     *
     * @return array
     */
    public function setTypeProvider()
    {
        return [
            'document' => [
                2,
                'documents',
            ],
            'non existent' => [
                null,
                'this type does not exist',
            ],
        ];
    }

    /**
     * Test magic setter for type property.
     *
     * @param string|null $expected Expected object type ID.
     * @param mixed $type Type.
     * @return void
     *
     * @covers ::_setType()
     * @dataProvider setTypeProvider()
     */
    public function testSetType($expected, $type)
    {
        $entity = new ObjectEntity();
        $entity->type = $type;

        $objectTypeId = $entity->object_type_id;

        static::assertSame($expected, $objectTypeId);
    }

    /**
     * Test getter for table.
     *
     * @return void
     *
     * @covers ::getTable()
     */
    public function testGetTable()
    {
        $entity = new ObjectEntity();
        $entity->type = 'documents';

        $table = $entity->getTable();

        static::assertInstanceOf(ObjectsTable::class, $table);
        static::assertSame('documents', $table->getRegistryAlias());
    }

    /**
     * Test getter for JSON API type.
     *
     * @return void
     *
     * @covers ::getType()
     */
    public function testGetTypeJsonApi()
    {
        $entity = new ObjectEntity();
        $entity->type = 'documents';
        $entity = $entity->jsonApiSerialize();

        $type = $entity['type'];

        static::assertSame('documents', $type);
    }

    /**
     * Test getter for JSON API meta fields.
     *
     * @return void
     *
     * @covers ::getMeta()
     */
    public function testGetMeta()
    {
        $entity = new ObjectEntity();
        $entity->type = 'documents';
        $entity->created_by = 1;
        $entity = $entity->jsonApiSerialize();

        $meta = $entity['meta'];

        static::assertArrayNotHasKey('type', $meta);
    }

    /**
     * Test magic getter for JSON API links.
     *
     * @return void
     *
     * @covers ::getLinks()
     */
    public function testGetLinks()
    {
        $expected = [
            'self' => '/documents/99',
        ];

        $entity = new ObjectEntity();
        $entity->id = 99;
        $entity->type = 'documents';
        $entity = $entity->jsonApiSerialize();

        $links = $entity['links'];

        static::assertSame($expected, $links);
    }

    /**
     * Test magic getter for JSON API links.
     *
     * @return void
     *
     * @covers ::getLinks()
     */
    public function testGetLinksDeleted()
    {
        $expected = [
            'self' => '/trash/99',
        ];

        $entity = new ObjectEntity();
        $entity->id = 99;
        $entity->type = 'documents';
        $entity->deleted = true;
        $entity = $entity->jsonApiSerialize();

        $links = $entity['links'];

        static::assertSame($expected, $links);
    }

    /**
     * Test magic getter for JSON API relations.
     *
     * @return void
     *
     * @covers ::listAssociations()
     * @covers ::getRelationships()
     */
    public function testGetRelationships()
    {
        $expected = [
            'inverse_test',
            'parents',
            'test',
            'translations',
        ];

        $entity = TableRegistry::getTableLocator()->get('Documents')->newEntity();
        $entity->set('type', 'documents');
        $entity = $entity->jsonApiSerialize();

        $relations = array_keys($entity['relationships']);
        sort($relations);

        static::assertSame($expected, $relations);
    }

    /**
     * Test magic getter for JSON API relations for relation roles
     *
     * @return void
     *
     * @covers ::listAssociations()
     * @covers ::getRelationships()
     */
    public function testGetRelationshipsUsersRoles()
    {
        $expected = [
            'another_test' => [
                'links' => [
                    'related' => '/users/1/another_test',
                    'self' => '/users/1/relationships/another_test',
                ],
            ],
            'parents' => [
                'links' => [
                    'related' => '/users/1/parents',
                    'self' => '/users/1/relationships/parents',
                ],
            ],
            'roles' => [
                'links' => [
                    'related' => '/users/1/roles',
                    'self' => '/users/1/relationships/roles',
                ],
            ],
            'translations' => [
                'links' => [
                    'related' => '/users/1/translations',
                    'self' => '/users/1/relationships/translations',
                ],
            ],
        ];

        $entity = TableRegistry::getTableLocator()->get('Users')->newEntity();
        $entity->set('id', 1);
        $entity->set('type', 'users');
        $entity = $entity->jsonApiSerialize();

        ksort($entity['relationships']);

        static::assertSame($expected, $entity['relationships']);
    }

    /**
     * Test magic getter for JSON API relations.
     *
     * @return void
     *
     * @covers ::listAssociations()
     * @covers ::getRelationships()
     */
    public function testGetRelationshipsOfAssociated()
    {
        $expected = [
            'inverse_test',
            'parents',
            'translations',
        ];

        $entity = TableRegistry::getTableLocator()->get('Documents')->getAssociation('Test')->newEntity();
        $entity->set('type', 'profile');
        $entity = $entity->jsonApiSerialize();

        $relations = array_keys($entity['relationships']);
        sort($relations);

        static::assertSame($expected, $relations);
    }

    /**
     * Test that starting from ObjectEntity and set a specific type
     * the relationships returned will be those of specific type.
     *
     * @return void
     *
     * @covers ::listAssociations()
     * @covers ::getRelationships()
     */
    public function testGetRelationshipsFromObjects()
    {
        $expected = [
            'children',
            'parent',
            'translations',
        ];

        $entity = TableRegistry::getTableLocator()->get('Objects')->newEntity();
        $entity->set('type', 'folders');
        $entity = $entity->jsonApiSerialize();

        $relations = array_keys($entity['relationships']);
        sort($relations);

        static::assertSame($expected, $relations);
    }

    /**
     * Test magic getter for JSON API relations.
     *
     * @return void
     *
     * @covers ::listAssociations()
     * @covers ::getRelationships()
     */
    public function testGetRelationshipsDeleted()
    {
        $entity = TableRegistry::getTableLocator()->get('Documents')->newEntity();
        $entity->set('type', 'documents');
        $entity->set('deleted', true);
        $entity = $entity->jsonApiSerialize();

        static::assertArrayNotHasKey('relationships', $entity);
    }

    /**
     * Test magic getter for JSON API relations.
     *
     * @return void
     *
     * @covers ::getRelationships()
     */
    public function testGetRelationshipsIncluded()
    {
        $entity = TableRegistry::getTableLocator()->get('Documents')->get(2, ['contain' => ['Test']]);
        $entity = $entity->jsonApiSerialize();

        static::assertArrayHasKey('relationships', $entity);
        static::assertArrayHasKey('test', $entity['relationships']);
        static::assertArrayHasKey('data', $entity['relationships']['test']);

        static::assertArrayHasKey('included', $entity);
        static::assertSameSize($entity['relationships']['test']['data'], $entity['included']);
    }

    /**
     * Test magic getter for JSON API relations with single entity `included`
     *
     * @return void
     *
     * @covers ::getRelationships()
     */
    public function testGetRelationshipsSingleIncluded()
    {
        $entity = TableRegistry::getTableLocator()->get('Folders')->get(12, ['contain' => ['Parents']]);
        $entity = $entity->jsonApiSerialize();

        static::assertArrayHasKey('relationships', $entity);
        static::assertArrayHasKey('parent', $entity['relationships']);
        static::assertArrayHasKey('data', $entity['relationships']['parent']);

        static::assertArrayHasKey('included', $entity);
        static::assertEquals(1, count($entity['included']));
    }

    /**
     * Test relationship count.
     *
     * @return void
     *
     * @covers ::getRelationships()
     */
    public function testGetRelationshipsCount(): void
    {
        $count = ['test' => 12];
        $entity = TableRegistry::getTableLocator()->get('Documents')->newEntity();
        $entity->set('type', 'documents');
        $entity->set('_countData', $count);
        $entity = $entity->jsonApiSerialize();

        $testCount = Hash::get($entity, 'relationships.test.meta.count');
        static::assertEquals($count['test'], $testCount);
    }

    /**
     * Test `hasProperty` method
     *
     * @covers ::hasProperty()
     * @return bool
     */
    public function testHasProperty()
    {
        $entity = TableRegistry::getTableLocator()->get('Documents')->get(2);

        // core property
        static::assertTrue($entity->hasProperty('title'));
        // custom property
        static::assertTrue($entity->hasProperty('another_title'));
        // missing property
        static::assertFalse($entity->hasProperty('some_property'));
        // hidden property
        static::assertTrue($entity->hasProperty('deleted'));
        static::assertFalse($entity->hasProperty('deleted', false));
        // virtual property
        static::assertTrue($entity->hasProperty('type', false, true));
    }
}
