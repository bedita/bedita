<?php
declare(strict_types=1);

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
use BEdita\Core\Model\Table\FoldersTable;
use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * {@see \BEdita\Core\Model\Entity\Folder} Test Case
 */
#[CoversClass(Folder::class)]
class FolderTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.ObjectPermissions',
    ];

    /**
     * Folders table
     *
     * @var \BEdita\Core\Model\Table\FoldersTable
     */
    public FoldersTable $Folders;

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

        $folder->parent_id = '13';
        static::assertEquals($parent, $folder->parent);
        static::assertEquals(13, $folder->parent_id);
    }

    /**
     * Test getter for `parent_uname`
     *
     * @return void
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
    public static function getPathProvider(): array
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
     */
    #[DataProvider('getPathProvider')]
    public function testGetPath($expected, $id)
    {
        $folder = $this->Folders->get($id);
        static::assertEquals($expected, $folder->path);
    }

    /**
     * Test that `path` virtual property is null if folder id is empty.
     *
     * @return void
     */
    public function testGetPathNull()
    {
        $folder = $this->Folders->newEmptyEntity();
        static::assertNull($folder->path);
    }

    /**
     * Test getter for `path` throws RuntimeException if folder is orphan.
     *
     * @return void
     */
    public function testGetPathOrphanFolder()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Folder "12" is not on the tree.');
        TableRegistry::getTableLocator()->get('Trees')->deleteAll(['object_id' => 12]);

        $this->Folders->get(12)->get('path');
    }

    /**
     * Data provider for `testGetSlugPath()`
     *
     * @return array
     */
    public static function getSlugPathProvider()
    {
        return [
            'root' => [
                [[
                    'id' => 11,
                    'menu' => false,
                    'params' => null,
                    'slug' => 'root-folder-11',
                ]],
                11,
            ],
            'subfolder' => [
                [
                    [
                        'id' => 11,
                        'menu' => false,
                        'params' => null,
                        'slug' => 'root-folder-11',
                    ],
                    [
                        'id' => 12,
                        'menu' => true,
                        'params' => null,
                        'slug' => 'sub-folder-12',
                    ],
                ],
                12,
            ],
            'object in subfolder' => [
                [
                    [
                        'id' => 11,
                        'menu' => false,
                        'params' => null,
                        'slug' => 'root-folder-11',
                    ],
                    [
                        'id' => 12,
                        'menu' => true,
                        'params' => null,
                        'slug' => 'sub-folder-12',
                    ],
                    [
                        'id' => 4,
                        'menu' => true,
                        'params' => null,
                        'slug' => 'gustavo-supporto-profile-4',
                    ],
                ],
                4,
            ],
        ];
    }

    /**
     * Test getter for `slug_path`
     *
     * @param array $expected The expected slug path parts
     * @param int $id The folder id
     * @return void
     */
    #[DataProvider('getSlugPathProvider')]
    public function testGetSlugPath($expected, $id)
    {
        $folder = $this->Folders->get($id);
        static::assertEquals($expected, $folder->slug_path);
    }

    /**
     * Test that `slug_path` virtual property is null if folder id is empty.
     *
     * @return void
     */
    public function testGetSlugPathNull()
    {
        $folder = $this->Folders->newEntity([]);
        static::assertNull($folder->slug_path);
    }

    /**
     * Test getter for `path` throws RuntimeException if folder is orphan.
     *
     * @return void
     */
    public function testGetSlugPathOrphanFolder()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Folder "12" is not on the tree.');
        TableRegistry::getTableLocator()->get('Trees')->deleteAll(['object_id' => 12]);

        $this->Folders->get(12)->get('slug_path');
    }

    /**
     * Test empty perms.
     *
     * @return void
     */
    public function testGetPermsEmpty(): void
    {
        $folder = $this->Folders->get(11);
        static::assertNull($folder->get('perms'));

        $ot = $this->Folders->ObjectTypes->get('folders');
        $ot->addAssoc('Permissions');
        $this->Folders->ObjectTypes->saveOrFail($ot);

        $folder = $this->Folders->get(11);
        static::assertEquals([], $folder->get('perms'));
    }

    /**
     * Test get inherited permissions.
     *
     * @return void
     */
    public function testGetPermsInherited(): void
    {
        LoggedUser::setUserAdmin();
        $ot = $this->Folders->ObjectTypes->get('folders');
        $ot->addAssoc('Permissions');
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
            ],
        );

        $this->Folders->Permissions->saveManyOrFail($entities);
        LoggedUser::resetUser();

        $perms = $this->Folders->get(12)->get('perms');
        static::assertIsArray($perms);
        static::assertArrayHasKey('roles', $perms);
        static::assertArrayHasKey('inherited', $perms);

        $expected = [
            'roles' => ['first role', 'second role'],
            'inherited' => true,
            'descendant_perms_granted' => false,
        ];
        sort($perms['roles']);
        static::assertEquals($expected, $perms);
    }

    /**
     * Provider for `testDescendantHavePermissions`.
     *
     * @return array[]
     */
    public static function descendantHavePermissionsProvider(): array
    {
        return [
            'guest user' => [
                // user
                null,
                // permission entities
                [
                    [
                        'object_id' => 11,
                        'role_id' => 1,
                        'created_by' => 1,
                    ],
                ],
                // folder to get
                11,
                // expected
                [
                    'roles' => ['first role'],
                    'inherited' => false,
                    'descendant_perms_granted' => false,
                ],
            ],
            'user without permissions for descendant folder' => [
                [
                    'id' => 5,
                    'roles' => [
                        ['id' => 2],
                    ],
                ],
                [
                    [
                        'object_id' => 11,
                        'role_id' => 1,
                        'created_by' => 1,
                    ],
                ],
                11,
                [
                    'roles' => ['first role'],
                    'inherited' => false,
                    'descendant_perms_granted' => false,
                ],
            ],
            'user with permissions for descendant folder' => [
                [
                    'id' => 5,
                    'roles' => [
                        ['id' => 2],
                    ],
                ],
                [
                    [
                        'object_id' => 11,
                        'role_id' => 1,
                        'created_by' => 1,
                    ],
                    [
                        'object_id' => 12,
                        'role_id' => 2,
                        'created_by' => 1,
                    ],
                ],
                11,
                [
                    'roles' => ['first role'],
                    'inherited' => false,
                    'descendant_perms_granted' => true,
                ],
            ],
            'user is admin' => [
                [
                    'id' => 5,
                    'roles' => [
                        ['id' => 1],
                    ],
                ],
                [
                    [
                        'object_id' => 11,
                        'role_id' => 1,
                        'created_by' => 1,
                    ],
                    [
                        'object_id' => 12,
                        'role_id' => 2,
                        'created_by' => 1,
                    ],
                ],
                11,
                [
                    'roles' => ['first role'],
                    'inherited' => false,
                    'descendant_perms_granted' => true,
                ],
            ],
        ];
    }

    /**
     * Test descendant have permissions.
     *
     * @return void
     */
    #[DataProvider('descendantHavePermissionsProvider')]
    public function testDescendantHavePermissions($user, $entities, $folderId, $expected): void
    {
        $ot = $this->Folders->ObjectTypes->get('folders');
        $ot->addAssoc('Permissions');
        $this->Folders->ObjectTypes->saveOrFail($ot);

        $entities = $this->Folders->Permissions->newEntities($entities, ['accessibleFields' => ['created_by' => true]]);
        $this->Folders->Permissions->saveManyOrFail($entities);

        if ($user !== null) {
            LoggedUser::setUser($user);
        }

        $perms = $this->Folders->get($folderId)->get('perms');

        static::assertIsArray($perms);
        static::assertNotEmpty($perms);
        static::assertArrayHasKey('descendant_perms_granted', $perms);
        static::assertEquals($expected, $perms);
    }
}
