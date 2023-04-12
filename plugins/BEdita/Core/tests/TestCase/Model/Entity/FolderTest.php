<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Folder} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Folder
 */
class FolderTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.ObjectPermissions',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * Folders table
     *
     * @var \BEdita\Core\Model\Table\FoldersTable
     */
    public $Folders;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Folders = TableRegistry::getTableLocator()->get('Folders');

        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Folders);

        parent::tearDown();
    }

    /**
     * Test getter for `parent`
     *
     * @return void
     * @covers ::_getParent()
     */
    public function testGetParent()
    {
        $folder = new Folder();
        static::assertNull($folder->parent);

        $parent = $this->Folders->get(13);
        $folder->parents = [$parent];
        static::assertEquals($parent, $folder->parent);
    }

    /**
     * Test setter for `parent`
     *
     * @return void
     * @covers ::_setParent()
     */
    public function testSetParent()
    {
        $folder = new Folder();
        $folder->parent = null;
        static::assertEquals([], $folder->parents);

        $parent = $this->Folders->get(13);
        $folder->parent = $parent;
        static::assertEquals([$parent], $folder->parents);
    }

    /**
     * Test getter for `parent_id`
     *
     * @return void
     * @covers ::_getParentId()
     */
    public function testGetParentId()
    {
        $folder = new Folder();
        static::assertNull($folder->parent_id);

        $folder->parents = [$this->Folders->get(13)];
        static::assertEquals(13, $folder->parent_id);
    }

    /**
     * Test setter for `parent_id`
     *
     * @return void
     * @covers ::_setParentId()
     */
    public function testSetParentId()
    {
        $folder = new Folder([], ['source' => 'Folders']);
        $folder->parent_id = 13;
        $parent = $this->Folders->get(13);
        static::assertEquals($parent, $folder->parent);
        static::assertEquals([$parent], $folder->parents);

        $folder->parent_id = null;
        static::assertEquals(null, $folder->parent);
        static::assertEquals([], $folder->parents);
    }

    /**
     * Test getter for `parent_uname`
     *
     * @return void
     * @covers ::_getParentUname()
     */
    public function testGetParentUname()
    {
        $folder = new Folder();
        static::assertNull($folder->parent_uname);

        $folder->parents = [$this->Folders->get(13)];
        static::assertEquals('another-root-folder', $folder->parent_uname);
    }

    /**
     * Test setter for `parent_uname`
     *
     * @return void
     * @covers ::_setParentUname()
     */
    public function testSetParentUname()
    {
        $folder = new Folder([], ['source' => 'Folders']);
        $folder->parent_uname = 'another-root-folder';
        $parent = $this->Folders->get(13);
        static::assertEquals($parent, $folder->parent);
        static::assertEquals([$parent], $folder->parents);
        static::assertEquals('another-root-folder', $folder->get('parent_uname'));

        $folder->parent_uname = null;
        static::assertEquals(null, $folder->parent);
        static::assertEquals([], $folder->parents);
        static::assertEquals(null, $folder->get('parent_uname'));
    }

    /**
     * Test for isParentSet()
     *
     * @return void
     * @covers ::isParentSet()
     */
    public function testIsParentSet()
    {
        $folder = new Folder();
        static::assertFalse($folder->isParentSet());

        $folder->parent = null;
        static::assertTrue($folder->isParentSet());
    }

    /**
     * Test the presence of `parent` association
     *
     * @return void
     * @covers ::listAssociations()
     */
    public function testListAssociations()
    {
        $folder = $this->Folders->get(12);
        $folder = $folder->jsonApiSerialize();
        static::assertArrayHasKey('parent', $folder['relationships']);
    }

    /**
     * Data provider for `testGetPath()`
     *
     * @return array
     */
    public function getPathProvider()
    {
        return [
            'root' => [
                '/11',
                11,
            ],
            'subfolder' => [
                '/11/12',
                12,
            ],
        ];
    }

    /**
     * Test getter for `path`
     *
     * @param string $expected The expected path
     * @param int $id The folder id
     * @return void
     * @dataProvider getPathProvider
     * @covers ::_getPath()
     */
    public function testGetPath($expected, $id)
    {
        $folder = $this->Folders->get($id);
        static::assertEquals($expected, $folder->path);
    }

    /**
     * Test that `path` virtual property is null if folder id is empty.
     *
     * @return void
     * @covers ::_getPath()
     */
    public function testGetPathNull()
    {
        $folder = $this->Folders->newEntity([]);
        static::assertNull($folder->path);
    }

    /**
     * Test getter for `path` throws RuntimeException if folder is orphan.
     *
     * @return void
     * @covers ::_getPath()
     */
    public function testGetPathOrphanFolder()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Folder "12" is not on the tree.');
        TableRegistry::getTableLocator()->get('Trees')->deleteAll(['object_id' => 12]);
        TableRegistry::getTableLocator()->get('Trees')->recover();

        $this->Folders->get(12)->get('path');
    }

    /**
     * Test empty perms.
     *
     * @return void
     * @covers ::_getPerms()
     * @covers ::getInheritedRolesPermissions()
     */
    public function testGetPermsEmpty(): void
    {
        $folder = $this->Folders->get(11);
        static::assertNull($folder->get('perms'));

        $ot = $this->Folders->ObjectTypes->get('folders');
        $ot->associations = ['Permissions'];
        $this->Folders->ObjectTypes->saveOrFail($ot);

        $folder = $this->Folders->get(11);
        static::assertEquals([], $folder->get('perms'));
    }

    /**
     * Test get inherited permissions.
     *
     * @return void
     * @covers ::_getPerms()
     * @covers ::getInheritedRolesPermissions()
     */
    public function testGetPermsInherited(): void
    {
        $ot = $this->Folders->ObjectTypes->get('folders');
        $ot->associations = ['Permissions'];
        $this->Folders->ObjectTypes->saveOrFail($ot);

        $entities = $this->Folders->Permissions->newEntities(
            [
                [
                    'object_id' => 11,
                    'role_id' => 1,
                    'created_by' => 1,
                ],
                [
                    'object_id' => 11,
                    'role_id' => 2,
                    'created_by' => 1,
                ],
            ],
            [
                'accessibleFields' => ['created_by' => true],
            ]
        );

        $this->Folders->Permissions->saveManyOrFail($entities);

        $perms = $this->Folders->get(12)->get('perms');
        static::assertIsArray($perms);
        static::assertArrayHasKey('roles', $perms);
        static::assertArrayHasKey('inherited', $perms);

        $expected = [
            'roles' => ['first role', 'second role'],
            'inherited' => true,
        ];
        sort($perms['roles']);
        static::assertEquals($expected, $perms);
    }
}
