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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Roles = TableRegistry::get('Roles');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Roles);

        parent::tearDown();
    }

    /**
     * Tet getter for table.
     *
     * @return void
     *
     * @covers ::getTable()
     */
    public function testGetTable()
    {
        $role = $this->Roles->newEntity();
        $table = $role->getTable();

        static::assertInstanceOf(get_class($this->Roles), $table);
    }

    /**
     * Test getter for ID.
     *
     * @return void
     *
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
     *
     * @covers ::getType()
     */
    public function testGetType()
    {
        $role = $this->Roles->newEntity()->jsonApiSerialize();

        $type = $role['type'];

        static::assertSame($this->Roles->getTable(), $type);
    }

    /**
     * Test getter for attributes.
     *
     * @return void
     *
     * @covers ::getAttributes()
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
    }

    /**
     * Test getter for meta.
     *
     * @return void
     *
     * @covers ::getLinks()
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
     *
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
     *
     * @covers ::getRelationships()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsHidden()
    {
        $role = $this->Roles->newEntity();
        $role->setHidden(['users' => true], true);
        $role = $role->jsonApiSerialize();

        $relationships = array_keys(Hash::get($role, 'relationships', []));

        static::assertSame([], $relationships);
    }

    /**
     * Test getter for relationships with included resources.
     *
     * @return void
     *
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
     *
     * @covers ::getRelationships()
     * @covers ::getIncluded()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsIncludedEmpty()
    {
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
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Objects must implement "BEdita\Core\Utility\JsonApiSerializable", got "string" instead
     * @covers ::getRelationships()
     * @covers ::getIncluded()
     * @covers ::listAssociations()
     */
    public function testGetRelationshipsIncludedNotSerializable()
    {
        $role = $this->Roles->get(2);
        $role->users = 'Gustavo';
        $role->jsonApiSerialize();
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     *
     * @covers ::getMeta()
     */
    public function testGetMeta()
    {
        $expected = [
            'created',
            'modified',
            'unchangeable',
        ];

        $role = $this->Roles->get(1)->jsonApiSerialize();

        $meta = array_keys(Hash::get($role, 'meta', []));

        static::assertEquals($expected, $meta, '', 0, 10, true);
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     *
     * @covers ::getMeta()
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
     *
     * @covers ::getMeta()
     */
    public function testGetMetaExtra()
    {
        $expected = [
            'created',
            'modified',
            'unchangeable',
            'extra',
        ];
        $expectedExtra = ['my_computed_field' => pi()];

        $role = $this->Roles->get(1)
            ->set('my_computed_field', pi())
            ->jsonApiSerialize();

        $meta = array_keys(Hash::get($role, 'meta', []));
        $extra = Hash::get($role, 'meta.extra');

        static::assertEquals($expected, $meta, '', 0, 10, true);
        static::assertSame($expectedExtra, $extra);
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     *
     * @covers ::getMeta()
     */
    public function testGetMetaEmptyJoinData()
    {
        $expected = [
            'blocked',
            'created',
            'created_by',
            'last_login',
            'last_login_err',
            'locked',
            'modified',
            'modified_by',
            'num_login_err',
            'published',
        ];

        $user = $this->Roles->get(1, ['contain' => ['Users']])
            ->users[0]
            ->jsonApiSerialize();

        $meta = array_keys(Hash::get($user, 'meta', []));

        static::assertEquals($expected, $meta, '', 0, 10, true);
    }

    /**
     * Test getter for meta fields.
     *
     * @return void
     *
     * @covers ::getMeta()
     */
    public function testGetMetaJoinData()
    {
        $expected = [
            'blocked',
            'created',
            'created_by',
            'last_login',
            'last_login_err',
            'locked',
            'modified',
            'modified_by',
            'num_login_err',
            'published',
            'relation',
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

        static::assertEquals($expected, $meta, '', 0, 10, true);
        static::assertEquals($expectedRelation, $relation, '', 0, 10, true);
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
        ];
    }

    /**
     * Test JSON API serializer.
     *
     * @param string[] $excludedKeys Keys to be excluded.
     * @param int $options JSON API serializer options.
     * @return void
     *
     * @covers ::jsonApiSerialize()
     * @dataProvider jsonApiSerializeProvider()
     */
    public function testJsonApiSerialize($excludedKeys, $options)
    {
        $expected = [
            'id' => '1',
            'type' => 'roles',
            'attributes' => [
                'name' => 'first role',
                'description' => 'this is the very first role'
            ],
            'meta' => [
                'created' => '2016-04-15T09:57:38+00:00',
                'modified' => '2016-04-15T09:57:38+00:00',
                'unchangeable' => true,
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

        $role = $this->Roles->get(1)->jsonApiSerialize($options);
        $role = json_decode(json_encode($role), true);

        static::assertEquals($expected, $role);
    }
}
