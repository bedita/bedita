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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * BEdita\Core\Model\Table\FoldersTable Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\FoldersTable
 */
class FoldersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\FoldersTable
     */
    public $Folders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Folders = TableRegistry::getTableLocator()->get('Folders');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Folders);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     * @coversNothing
     */
    public function testInitialize()
    {
        $this->Folders->initialize([]);
        $this->assertEquals('objects', $this->Folders->getTable());
        $this->assertEquals('id', $this->Folders->getPrimaryKey());
        $this->assertEquals('title', $this->Folders->getDisplayField());

        $this->assertInstanceOf(BelongsToMany::class, $this->Folders->Children);
    }

    /**
     * Data provider for testHasAtMostOneParent()
     *
     * @return array
     */
    public function hasAtMostOneParentProvider()
    {
        return [
            'emptyParents' => [
                true,
                [],
            ],
            'valid' => [
                true,
                [
                    ['id' => 11],
                ],
            ],
            'tooMany' => [
                false,
                [
                    ['id' => 11],
                    ['id' => 13],
                ],
            ],
            'missingId' => [
                false,
                [
                    ['title' => 'pippo'],
                ],
            ],
        ];
    }

    /**
     * Test hasAtMostOneParent method
     *
     * @return void
     *
     * @dataProvider hasAtMostOneParentProvider()
     * @covers ::hasAtMostOneParent()
     */
    public function testHasAtMostOneParent($expected, $parents)
    {
        $entity = $this->Folders->newEntity(['parents' => $parents], [
            'accessibleFields' => ['parents' => true, 'id' => true],
        ]);

        $actual = $this->Folders->hasAtMostOneParent($entity);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testSave` test case
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'parentNotSet' => [
                11,
                [
                    'id' => 12,
                    'title' => 'change title',
                ],
            ],
            'parentNotChanged' => [
                11,
                [
                    'id' => 12,
                    'title' => 'change title',
                    'parents' => [
                        ['id' => 11],
                    ],
                ],
            ],
            'createRootFolder' => [
                null,
                [
                    'title' => 'Folder as root',
                ],
            ],
            'createSubfolder' => [
                11,
                [
                    'title' => 'Subfolder',
                    'parents' => [
                        ['id' => 11],
                    ],
                ],
            ],
            'becomeRoot' => [
                null,
                [
                    'id' => 12,
                    'parents' => [],
                ],
            ],
            'changeParent' => [
                13,
                [
                    'id' => 12,
                    'parents' => [
                        ['id' => 13],
                    ],
                ],
            ],
            'tooManyParents' => [
                false,
                [
                    'id' => 12,
                    'parents' => [
                        ['id' => 13],
                        ['id' => 11],
                    ],
                ],
            ],
            'tooManyParents2' => [
                false,
                [
                    'id' => 12,
                    'parents' => [
                        '_ids' => [13, 11]
                    ],
                ],
            ],
            'ok' => [
                13,
                [
                    'id' => 12,
                    'parents' => [
                        '_ids' => [13],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test save folder
     *
     * @param mixed $expected The expected result
     * @param array $data The data to save
     * @return void
     *
     * @dataProvider saveProvider
     * @covers ::beforeSave()
     * @covers ::afterSave()
     * @covers ::updateChildrenDeletedField()
     */
    public function testSave($expected, $data)
    {
        $trees = TableRegistry::getTableLocator()->get('Trees');
        if (!empty($data['id'])) {
            $node = $trees->find()->where(['object_id' => $data['id']])->first();
            $descendants = $trees->childCount($node);
        }

        $entity = $this->Folders->newEntity($data, [
            'accessibleFields' => ['parents' => true, 'id' => true],
        ]);
        $entity->type = 'folders';

        $entity = $this->Folders->save($entity);
        if ($expected === false) {
            static::assertFalse((bool)$entity);

            return;
        }
        static::assertTrue((bool)$entity);

        $actual = $this->Folders->get($entity->id, ['contain' => 'Parents']);
        static::assertSame($expected, $actual->parent_id);

        if (!empty($data['id'])) {
            $node = $trees->find()->where(['object_id' => $data['id']])->first();
            $actual = $trees->childCount($node);
            static::assertEquals($descendants, $actual);
        }
    }

    /**
     * Test `findRoots()`
     *
     * @covers ::findRoots()
     */
    public function testFindRoots()
    {
        $folders = $this->Folders->find('roots')->toArray();
        static::assertNotEmpty($folders);
        $ids = Hash::extract($folders, '{n}.id');
        static::assertEquals([11, 13], $ids);
    }

    /**
     * Test that trashing a folder (soft delete)
     * its subfolders are soft deleted but other object types are not.
     *
     * Test also that restoring the folder restores subfolders too.
     *
     * @return void
     *
     * @covers ::updateChildrenDeletedField()
     */
    public function testSoftDeleteAndRestore()
    {
        // first move root tree as children of another root
        // to have a more complex tree structure
        $folder = $this->Folders->get(11);
        $folder->parent_id = 13;
        $this->Folders->save($folder);

        // get root and trashes it
        $root = $this->Folders->get(13);
        $startDeletedInfo = $this->Folders
            ->find('ancestor', [$root->id])
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'deleted',
            ])
            ->toArray();

        $root->deleted = true;
        $this->Folders->save($root);

        $children = $this->Folders->find('ancestor', [$root->id]);
        foreach ($children as $child) {
            if ($child->type === 'folders') {
                // folders should have deleted field set to true
                static::assertTrue($child->deleted);
            } else {
                // other objects should have deleted field unchanged
                static::assertEquals($startDeletedInfo[$child->id], $child->deleted);
            }
        }

        // restore from trash => deleted false
        $root->deleted = false;
        $this->Folders->save($root);

        $restoredDeletedInfo = $this->Folders
            ->find('ancestor', [$root->id])
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'deleted',
            ])
            ->toArray();

        static::assertSame($startDeletedInfo, $restoredDeletedInfo);
    }

    /**
     * Test that deleting a folder all its subfolders (descendants) are deleted too.
     *
     * @return void
     *
     * @covers ::beforeDelete()
     * @covers ::afterDelete()
     */
    public function testDeleteFolder()
    {
        $parentFolder = $this->Folders->get(12);
        $folderIds = [12];

        // add subfolders
        $subfolder = $this->Folders->newEntity();
        $subfolder->parent = $parentFolder;
        $this->Folders->save($subfolder);
        $folderIds[] = $subfolder->id;

        $anotherSubfolder = $this->Folders->newEntity();
        $anotherSubfolder->parent = $subfolder;
        $this->Folders->save($anotherSubfolder);
        $folderIds[] = $anotherSubfolder->id;

        // get descendants not folders
        $notFoldersIds = $this->Folders
            ->find('ancestor', [$parentFolder->id])
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'id',
            ])
            ->where(function (QueryExpression $exp) {
                return $exp->not(['object_type_id' => $this->Folders->objectType()->id]);
            })
            ->toArray();

        $Trees = TableRegistry::getTableLocator()->get('Trees');
        $Objects = TableRegistry::getTableLocator()->get('Objects');

        // all descendants exist and are on tree
        foreach (array_merge($notFoldersIds, $folderIds) as $id) {
            static::assertTrue($Objects->exists(['id' => $id]));
            static::assertTrue($Trees->exists(['object_id' => $id]));
        }

        $this->Folders->delete($parentFolder);

        // parent folder and subfolders not exit anymore and not are on tree
        foreach ($folderIds as $id) {
            static::assertFalse($Objects->exists(['id' => $id]));
            static::assertFalse($Trees->exists(['object_id' => $id]));
        }

        // other descendants exist yet
        foreach ($notFoldersIds as $id) {
            static::assertTrue($Objects->exists(['id' => $id]));
        }

        $currenTree = $Trees->find()->order(['tree_left' => 'ASC'])->toArray();
        // check that after recover the tree is the same.
        $Trees->recover();
        static::assertEquals($currenTree, $Trees->find()->order(['tree_left' => 'ASC'])->toArray());
    }

    /**
     * Test `isFolderRestorable()` in case of no check on parents.
     *
     * @return void
     *
     * @covers ::isFolderRestorable()
     */
    public function testIsFolderRestorableNoCheckOnParents()
    {
        // new entity
        $folder = $this->Folders->newEntity();
        static::assertTrue($this->Folders->isFolderRestorable($folder));

        // deleted is not dirty
        $folder = $this->Folders->get(11);
        static::assertTrue($this->Folders->isFolderRestorable($folder));

        // deleted is dirty but equal to true
        $folder->deleted = true;
        $folder->setDirty('deleted', true);
        static::assertTrue($this->Folders->isFolderRestorable($folder));
    }

    /**
     * Test that `isFolderRestorable()` is true
     * trying to resume a folder deleted with parent not deleted.
     *
     * @return void
     *
     * @covers ::isFolderRestorable()
     */
    public function testIsFolderRestorableOK()
    {
        $folder = $this->Folders->get(12, ['contain' => ['Parents']]);
        static::assertFalse($folder->parent->deleted);

        $folder->deleted = true;
        $this->Folders->save($folder);
        $folder = $this->Folders->get($folder->id);
        static::assertTrue($folder->deleted);
        $folder->deleted = false;
        static::assertTrue($this->Folders->isFolderRestorable($folder));
    }

    /**
     * Test that `isFolderRestorable()` is false
     * trying to resume a folder deleted with parent deleted.
     *
     * @return void
     *
     * @covers ::isFolderRestorable()
     */
    public function testIsFolderRestorableKO()
    {
        // delete parent (delete also all folder children)
        $parent = $this->Folders->get(11);
        $parent->deleted = true;
        $this->Folders->save($parent);

        $children = $this->Folders
            ->find('ancestor', [11])
            ->where(['object_type_id' => $this->Folders->objectType()->id])
            ->toArray();

        static::assertNotEmpty($children);

        foreach ($children as $child) {
            static::assertTrue($child->deleted);
            $child->deleted = false;
            static::assertFalse($this->Folders->isFolderRestorable($child));
        }
    }

    /**
     * Test that only available children are returned.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testChildrenAvailable(): void
    {
        $folder = $this->Folders->get(11, ['contain' => ['Children']]);
        static::assertNotEmpty($folder->children);

        $firstChild = $folder->children[0];
        $firstChild->status = 'off';
        $this->Folders->Children->saveOrFail($firstChild);

        Configure::write('Status.level', 'off');
        $folder = $this->Folders->get(11, ['contain' => ['Children']]);
        $childrenIds = Hash::extract($folder->children, '{*}.id');
        static::assertContains($firstChild->id, $childrenIds);

        Configure::write('Status.level', 'draft');
        $folder = $this->Folders->get(11, ['contain' => ['Children']]);
        $childrenIds = Hash::extract($folder->children, '{*}.id');
        static::assertNotContains($firstChild->id, $childrenIds);
    }
}
