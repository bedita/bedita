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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Model\Entity\JsonApiTrait
 */
class JsonApiTraitTest extends TestCase
{
    /**
     * Helper table.
     *
     * @var \BEdita\Core\Model\Table\RolesTable
     */
    public $Roles;

    /**
     * Helper table.
     *
     * @var \BEdita\Core\Model\Table\ObjectTypesTable
     */
    public $ObjectTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
        'plugin.BEdita/Core.Annotations',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Roles = TableRegistry::getTableLocator()->get('Roles');
        $this->ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');

        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Roles);
        unset($this->ObjectTypes);

        parent::tearDown();
    }

    /**
     * Tet getter for table.
     *
     * @return void
     * @covers ::getTable()
     */
    public function testGetTable()
    {
        $role = $this->Roles->newEntity([]);
        $table = $role->getTable();

        static::assertInstanceOf(get_class($this->Roles), $table);
    }

    /**
     * Test getter for ID.
     *
     * @return void
     * @covers ::getId()
     */
    public function testGetId()
    {
        $role = $this->Roles->get(1)->jsonApiSerialize();

        $id = $role['id'];

        static::assertSame('1', $id);
    }

    /**
     * Test getter for type.
     *
     * @return void
     * @covers ::getType()
     */
    public function testGetType()
    {
        $role = $this->Roles->newEntity([])->jsonApiSerialize();

        $type = $role['type'];

        static::assertSame($this->Roles->getTable(), $type);
    }

    /**
     * Test getter for attributes.
     *
     * @return void
     * @covers ::getAttributes()
     * @covers ::filterFields()
     */
    public function testGetAttributes()
    {
        $expected = [
            'name',
            'description',
        ];

        $role = $this->Roles->get(1)->jsonApiSerialize();
        $attributes = array_keys($role['attributes']);
        static::assertEquals($expected, $attributes);

        // test with `fields`
        $role = $this->Roles->get(1)->jsonApiSerialize(0, ['roles' => ['name', 'description']]);
        $attributes = array_keys($role['attributes']);
        static::assertEquals($expected, $attributes);
    }

    /**
     * Test getter for meta.
     *
     * @return void
     * @covers ::getLinks()
     * @covers ::routeNamePrefix()
     */
    public function testGetLinks()
    {
        $expected = [
            'self' => '/roles/1',
        ];

        $role = $this->Roles->get(1)->jsonApiSerialize();

        $links = $role['links'];

        static::assertEquals($expected, $links);
    }

    /**
     * Test getter for relationships.
     *
     * @return void
     * @covers ::getRelationships()
     * @covers ::listAssociations()
     */
    public function testGetRelationships()
    {
        $expected = [
            'users' => [
                'links' => [
                    'related' => '/roles/1/users',
                    'self' => '/roles/1/relationships/users',
                ],
            ],
        ];

        $role = $this->Roles->get(1)->jsonApiSerialize();

        $relationships = $role['relationships'];

        static::assertSame($expected, $relationships);
    }

    /**
     * Test getter for relationships.
     *
     * @return void
     * @covers ::getRelationships()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsHidden()
    {
        $role = $this->Roles->newEntity([]);
        $role->setHidden(['users' => true], true);
        $role = $role->jsonApiSerialize();

        $relationships = array_keys(Hash::get($role, 'relationships', []));

        static::assertSame([], $relationships);
    }

    /**
     * Test getter for relationships with included resources.
     *
     * @return void
     * @covers ::getRelationships()
     * @covers ::getIncluded()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsIncluded()
    {
        $expected = [
            'users' => [
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'users',
                    ],
                ],
                'links' => [
                    'related' => '/roles/1/users',
                    'self' => '/roles/1/relationships/users',
                ],
            ],
        ];

        $role = $this->Roles->get(1, ['contain' => ['Users']])->jsonApiSerialize();

        $relationships = $role['relationships'];
        $included = $role['included'];

        static::assertSame($expected, $relationships);
        static::assertCount(1, $included);
    }

    /**
     * Test getter for relationships with included resources.
     *
     * @return void
     * @covers ::getRelationships()
     * @covers ::getIncluded()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsIncludedSingle()
    {
        $expected = [
            'left_relations' => [
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'relations',
                    ],
                ],
                'links' => [
                    'related' => '/model/object_types/2/left_relations',
                    'self' => '/model/object_types/2/relationships/left_relations',
                ],
            ],
            'right_relations' => [
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'relations',
                    ],
                ],
                'links' => [
                    'related' => '/model/object_types/2/right_relations',
                    'self' => '/model/object_types/2/relationships/right_relations',
                ],
            ],
            'parent' => [
                'data' => [
                    'id' => '1',
                    'type' => 'object_types',
                ],
                'links' => [
                    'related' => '/model/object_types/2/parent',
                    'self' => '/model/object_types/2/relationships/parent',
                ],
            ],
        ];

        $objectType = $this->ObjectTypes->get(2, ['contain' => ['Parent', 'RightRelations', 'LeftRelations']])->jsonApiSerialize();

        $relationships = $objectType['relationships'];
        $included = $objectType['included'];

        static::assertSame($expected, $relationships);
        static::assertCount(3, $included);
    }

    /**
     * Test getter for relationships with included resources.
     *
     * @return void
     * @covers ::getRelationships()
     * @covers ::getIncluded()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsIncludedEmpty()
    {
        // This is needed in order to permanently remove user with id 5
        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->get(5);
        $user->created_by = 1;
        $user->modified_by = 1;
        $user = $usersTable->saveOrFail($user);
        $doc = $this->fetchTable('Objects')->get(3);
        $doc->modified_by = 1;
        $doc = $this->fetchTable('Objects')->saveOrFail($doc);
        $this->fetchTable('Annotations')->deleteAll([]);

        $usersTable->delete($usersTable->get(5));

        $expected = [
            'users' => [
                'data' => [],
                'links' => [
                    'related' => '/roles/2/users',
                    'self' => '/roles/2/relationships/users',
                ],
            ],
        ];

        $role = $this->Roles->get(2, ['contain' => ['Users']])->jsonApiSerialize();

        $relationships = $role['relationships'];

        static::assertSame($expected, $relationships);
        static::assertArrayNotHasKey('included', $role);
    }

    /**
     * Test getter for relationships with included resources.
     *
     * @return void
     * @covers ::getRelationships()
     * @covers ::getIncluded()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsIncludedNotSerializable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Objects must implement "BEdita\Core\Utility\JsonApiSerializable", got "string" instead');
        $role = $this->Roles->get(2);
        $role->users = 'Gustavo';
        $role->jsonApiSerialize();
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     * @covers ::getMeta()
     * @covers ::filterFields()
     */
    public function testGetMeta()
    {
        $expected = [
            'created',
            'modified',
            'priority',
            'unchangeable',
        ];

        $role = $this->Roles->get(1)->jsonApiSerialize();

        $meta = array_keys(Hash::get($role, 'meta', []));
        sort($meta);
        static::assertEquals($expected, $meta, '');
        static::assertEqualsCanonicalizing($expected, $meta, '');
        static::assertEqualsWithDelta($expected, $meta, 0, '');

        // test with `fields`
        $role = $this->Roles->get(1)->jsonApiSerialize(0, ['created', 'modified', 'unchangeable']);

        $meta = array_keys(Hash::get($role, 'meta', []));
        sort($meta);
        static::assertEquals($expected, $meta, '');
        static::assertEqualsCanonicalizing($expected, $meta, '');
        static::assertEqualsWithDelta($expected, $meta, 0, '');
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     * @covers ::getMeta()
     * @covers ::customProps()
     */
    public function testGetMetaNotAccessible()
    {
        $role = $this->Roles->get(1);
        $role->setAccess('*', true);
        $role = $role->jsonApiSerialize();

        $meta = array_keys(Hash::get($role, 'meta', []));

        static::assertSame([], $meta);
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     * @covers ::getMeta()
     * @covers ::customProps()
     */
    public function testGetMetaExtra()
    {
        $expected = [
            'created',
            'extra',
            'modified',
            'priority',
            'unchangeable',
        ];
        $expectedExtra = ['my_computed_field' => pi()];

        $role = $this->Roles->get(1)
            ->set('my_computed_field', pi())
            ->jsonApiSerialize();

        $meta = array_keys(Hash::get($role, 'meta', []));
        $extra = Hash::get($role, 'meta.extra');
        sort($meta);
        static::assertEquals($expected, $meta, '');
        static::assertEqualsCanonicalizing($expected, $meta, '');
        static::assertEqualsWithDelta($expected, $meta, 0, '');
        static::assertSame($expectedExtra, $extra);
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     * @covers ::getMeta()
     * @covers ::joinData()
     */
    public function testGetMetaEmptyJoinData()
    {
        $expected = [
            'blocked',
            'created',
            'created_by',
            'external_auth',
            'last_login',
            'last_login_err',
            'locked',
            'modified',
            'modified_by',
            'num_login_err',
            'published',
            'verified',
            'password_modified',
        ];

        $user = $this->Roles->get(1, ['contain' => ['Users']])
            ->users[0]
            ->jsonApiSerialize();

        $meta = array_keys(Hash::get($user, 'meta', []));

        sort($expected);
        sort($meta);
        static::assertEquals($expected, $meta, '');
        static::assertEqualsCanonicalizing($expected, $meta, '');
        static::assertEqualsWithDelta($expected, $meta, 0, '');
    }

    /**
     * Test `tree` join data.
     *
     * @return void
     * @covers ::joinData()
     */
    public function testTreeJoinData(): void
    {
        $folder = TableRegistry::getTableLocator()->get('Folders')->get(12, ['contain' => ['Children']]);
        $child = $folder->children[0]->jsonApiSerialize();

        $expected = [
            'depth_level' => 2,
            'menu' => true,
            'canonical' => true,
        ];
        static::assertEquals($expected, Hash::get($child, 'meta.relation'));
    }

    /**
     * Test missing join data.
     *
     * @return void
     * @covers ::joinData()
     */
    public function testMissingJoinData(): void
    {
        $role = $this->Roles->get(1)->jsonApiSerialize();
        static::assertEmpty(Hash::get($role, 'meta.relation'));
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     * @covers ::getMeta()
     * @covers ::customProps()
     */
    public function testGetMetaJoinData()
    {
        $expected = [
            'blocked',
            'created',
            'created_by',
            'external_auth',
            'last_login',
            'last_login_err',
            'locked',
            'modified',
            'modified_by',
            'num_login_err',
            'published',
            'relation',
            'verified',
            'password_modified',
        ];
        $expectedRelation = [
            'id',
            'role_id',
            'user_id',
        ];

        $user = $this->Roles->get(1, ['contain' => ['Users']])
            ->users[0];
        $user->_joinData->setHidden([]);
        $user = $user->jsonApiSerialize();

        $meta = array_keys(Hash::get($user, 'meta', []));
        $relation = array_keys(Hash::get($user, 'meta.relation', []));

        sort($expected);
        sort($meta);
        static::assertEquals($expected, $meta, '');
        static::assertEqualsCanonicalizing($expected, $meta, '');
        static::assertEqualsWithDelta($expected, $meta, 0, '');
        static::assertEquals($expectedRelation, $relation, '');
        static::assertEqualsCanonicalizing($expectedRelation, $relation, '');
        static::assertEqualsWithDelta($expectedRelation, $relation, 0, '');
    }

    /**
     * Data provider for `testJsonApiSerialize` test case.
     *
     * @return array
     */
    public function jsonApiSerializeProvider()
    {
        return [
            'full' => [
                [],
                0,
            ],
            'no links' => [
                ['links'],
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS,
            ],
            'slim' => [
                ['attributes', 'meta', 'links', 'relationships', 'included'],
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_ATTRIBUTES | JsonApiSerializable::JSONAPIOPT_EXCLUDE_META | JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS | JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS,
            ],
            'commonFields' => [
                ['meta'],
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_META,
                [
                    '_common' => ['name', 'description'],
                ],
            ],
            'sparseFields' => [
                ['meta'],
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_META,
                [
                    'roles' => ['name', 'description'],
                ],
            ],
        ];
    }

    /**
     * Test JSON API serializer.
     *
     * @param string[] $excludedKeys Keys to be excluded.
     * @param int $options JSON API serializer options.
     * @param array $fields Fields filter data.
     * @return void
     * @covers ::jsonApiSerialize()
     * @covers ::setSelected()
     * @dataProvider jsonApiSerializeProvider()
     */
    public function testJsonApiSerialize($excludedKeys, $options, $fields = null)
    {
        $expected = [
            'id' => '1',
            'type' => 'roles',
            'attributes' => [
                'name' => 'first role',
                'description' => 'this is the very first role',
            ],
            'meta' => [
                'created' => '2016-04-15T09:57:38+00:00',
                'modified' => '2016-04-15T09:57:38+00:00',
                'unchangeable' => true,
                'priority' => 0,
            ],
            'links' => [
                'self' => '/roles/1',
            ],
            'relationships' => [
                'users' => [
                    'links' => [
                        'related' => '/roles/1/users',
                        'self' => '/roles/1/relationships/users',
                    ],
                ],
            ],
        ];
        $expected = array_diff_key($expected, array_flip($excludedKeys));

        $role = $this->Roles->get(1)->jsonApiSerialize($options, $fields);
        $role = json_decode(json_encode($role), true);

        static::assertEquals($expected, $role);
    }

    /**
     * Data provider for testJsonApiSerializeCount()
     *
     * @return array
     */
    public function metaCountProvider(): array
    {
        return [
            'count' => [
                2,
                2,
            ],
            'not_valid' => [
                false,
                'ciao',
            ],
        ];
    }

    /**
     * Test that `count` is present in meta of relationships
     *
     * @return void
     * @covers ::jsonApiSerialize()
     * @covers ::getRelationshipCount()
     * @dataProvider metaCountProvider()
     */
    public function testJsonApiSerializeCount($expected, $count): void
    {
        $role = $this->Roles->get(1);
        $role->set('_countData', ['users' => $count]);
        $role = $role->jsonApiSerialize();

        $result = Hash::get($role, 'relationships.users.meta.count', false);

        static::assertEquals($expected, $result);
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     * @covers ::customProps()
     */
    public function testCustomProps()
    {
        $expected = [
            'another_birthdate',
            'created',
            'created_by',
            'locked',
            'modified',
            'modified_by',
            'published',
        ];

        $profile = $this->fetchTable('Profiles')->get(4);
        $profile = $profile->jsonApiSerialize();

        $meta = array_keys((array)Hash::get($profile, 'meta'));
        sort($expected);
        sort($meta);
        static::assertEquals($expected, $meta);
    }
}
