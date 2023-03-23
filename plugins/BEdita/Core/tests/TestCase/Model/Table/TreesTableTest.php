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

use BEdita\Core\Exception\LockedResourceException;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\TreesTable Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\TreesTable
 */
class TreesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    public $Trees;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Trees = TableRegistry::getTableLocator()->get('Trees');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Trees);

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
        $this->Trees->initialize([]);

        static::assertInstanceOf(BelongsTo::class, $this->Trees->Objects);
        static::assertInstanceOf(BelongsTo::class, $this->Trees->ParentObjects);
        static::assertInstanceOf(BelongsTo::class, $this->Trees->RootObjects);
        static::assertInstanceOf(BelongsTo::class, $this->Trees->ParentNode);
        static::assertInstanceOf(HasMany::class, $this->Trees->ChildNodes);
        static::assertInstanceOf(TreeBehavior::class, $this->Trees->behaviors()->get('Tree'));
    }

    /**
     * Data provider for `testIsParentValid()`
     *
     * @return array
     */
    public function isParentValidProvider()
    {
        return [
            'null, no object ID' => [
                false,
                null,
            ],
            'null, folder' => [
                true,
                null,
                12,
            ],
            'null, not a folder' => [
                false,
                null,
                4,
            ],
            'folder' => [
                true,
                12,
            ],
            'not a folder' => [
                false,
                4,
            ],
        ];
    }

    /**
     * Test for `isParentValid()`
     *
     * @param bool $expected The expected result
     * @param int|null $parentId The parent id
     * @param int|null $objectId The object id
     * @return void
     * @dataProvider isParentValidProvider
     * @covers ::isParentValid()
     * @covers ::isFolder()
     */
    public function testIsParentValid($expected, $parentId, $objectId = null)
    {
        $entity = $this->Trees->newEntity([]);
        if ($objectId !== null) {
            $entity->object_id = $objectId;
        }
        $entity->parent_id = $parentId;
        static::assertEquals($expected, $this->Trees->isParentValid($entity));
    }

    /**
     * Data provider for `testIsPositionUnique()`
     *
     * @return array
     */
    public function isPositionUniqueProvider()
    {
        return [
            'folder, not unique' => [
                false,
                12,
                null,
            ],
            'folder, unique' => [
                true,
                13,
                null,
            ],
            'not a folder, appears twice inside parent' => [
                false,
                4,
                12,
            ],
            'not a folder, appears once inside parent' => [
                true,
                4,
                11,
            ],
        ];
    }

    /**
     * Test for `isFolderPositionUnique()`
     *
     * @param bool $expected Expected result.
     * @param int|null $objectId Object ID.
     * @param int|null $parentId Parent ID.
     * @return void
     * @dataProvider isPositionUniqueProvider
     * @covers ::isPositionUnique()
     * @covers ::isFolder()
     */
    public function testIsPositionUnique($expected, $objectId, $parentId)
    {
        $this->Trees->deleteAll(['object_id' => 13]);
        $this->Trees->recover();

        $entity = $this->Trees->newEntity([]);
        $entity->object_id = $objectId;
        $entity->parent_id = $parentId;
        static::assertEquals($expected, $this->Trees->isPositionUnique($entity));
    }

    /**
     * Data provider for `testChangeRoot()`
     *
     * @return array
     */
    public function changeRootProvider()
    {
        return [
            'becomeRoot' => [
                12,
                null,
            ],
            'changeRoot' => [
                13,
                13,
            ],
        ];
    }

    /**
     * Test that moving a node under another `root_id`
     * all children will be migrated to the same `root_id`
     *
     * @param int $rootExpected Expected root ID.
     * @param int|null $parentId Parent ID.
     * @return void
     * @dataProvider changeRootProvider
     * @covers ::afterSave()
     */
    public function testChangeRoot($rootExpected, $parentId)
    {
        $node = $this->Trees->get(2);
        static::assertEquals(11, $node->root_id);
        $children = $this->Trees->find('children', ['for' => 2])->all()->toList();

        $node->parent_id = $parentId;
        static::assertTrue((bool)$this->Trees->save($node));

        $node = $this->Trees->get(2);
        $actualChildren = $this->Trees->find('children', ['for' => 2])->all()->toList();

        static::assertEquals($rootExpected, $node->root_id);
        static::assertCount(count($children), $actualChildren);
        foreach ($actualChildren as $child) {
            static::assertEquals($rootExpected, $child->root_id);
        }
    }

    /**
     * Test `afterSave` on new item
     *
     * @return void
     * @covers ::afterSave()
     */
    public function testAfterSaveNew()
    {
        $entity = $this->Trees->newEntity(
            [
                'object_id' => 2,
                'parent_id' => 12,
                'root_id' => 11,
                'parent_node_id' => 2,
            ]
        );
        static::assertTrue((bool)$this->Trees->save($entity));
    }

    /**
     * Test that moving a parent as child fails.
     *
     * @return void
     * @coversNothing
     */
    public function testMoveParentAsChild()
    {
        $this->expectException(\RuntimeException::class);
        // create new Folder
        LoggedUser::setUserAdmin();
        $Folders = TableRegistry::getTableLocator()->get('Folders');
        $entity = $Folders->newEntity(['title' => 'subsub folder']);
        $entity->type = 'folders';
        $entity->parent = $Folders->get(12);

        $Folders->save($entity);

        $parentNode = $this->Trees
            ->find()
            ->where(['object_id' => $entity->parent->id])
            ->first();

        $parentNode->set('parent_id', $entity->id);

        $this->Trees->save($parentNode);
    }

    /**
     * Data provider for `testDeleteOrphaned` test case.
     *
     * @return array
     */
    public function deleteOrphanedProvider()
    {
        return [
            'not a folder' => [
                true,
                2,
            ],
            'not primary' => [
                true,
                12,
                false,
            ],
            'primary' => [
                new LockedResourceException('This operation would leave an orphaned folder'),
                12,
                true,
            ],
        ];
    }

    /**
     * Test that no folder is ever left out of the tree.
     *
     * @param bool|\Exception $expected Expected result.
     * @param int $objectId Object ID.
     * @param bool $primary Is this a "primary" delete operation?
     * @return void
     * @dataProvider deleteOrphanedProvider()
     * @covers ::beforeDelete()
     * @covers ::isFolder()
     */
    public function testDeleteOrphaned($expected, $objectId, $primary = true)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $node = $this->Trees->find()
            ->where(['object_id' => $objectId])
            ->firstOrFail();

        $result = (bool)$this->Trees->delete($node, ['_primary' => $primary]);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testSetPosition` test case.
     *
     * @return array
     */
    public function setPositionProvider()
    {
        return [
            'first' => [
                1,
                2,
                'first',
            ],
            'last' => [
                2,
                11,
                'last',
            ],
            'invalid' => [
                new BadRequestException('Invalid position'),
                11,
                'gustavo',
            ],
        ];
    }

    /**
     * Test that a children's position is updated.
     *
     * @param int|\Exception $expected Expected final position.
     * @param int $objectId Object ID.
     * @param int|string $position Position.
     * @return void
     * @dataProvider setPositionProvider()
     * @covers ::afterSave()
     */
    public function testSetPosition($expected, $objectId, $position)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $node = $this->Trees->find()
            ->where(['object_id' => $objectId])
            ->firstOrFail();

        $node->set('position', $position);
        $this->Trees->save($node);

        $currentPosition = $this->Trees->getCurrentPosition($node);

        static::assertSame($expected, $currentPosition);
    }

    /**
     * Test set canonical `true`
     *
     * @covers ::afterSave()
     */
    public function testSetCanonical()
    {
        $entity = $this->Trees->newEntity(
            [
                'object_id' => 2,
                'parent_id' => 12,
                'root_id' => 11,
                'parent_node_id' => 2,
            ]
        );
        $entity = $this->Trees->saveOrFail($entity);
        static::assertTrue((bool)$entity);

        $entity = $this->Trees->get($entity->get('id'));
        static::assertFalse($entity->get('canonical'));

        // get other record for the same object
        $other = $this->Trees->get(3);
        static::assertTrue($other->get('canonical'));

        // change canonical in subfolder
        $entity->set('canonical', true);
        $entity = $this->Trees->save($entity);
        static::assertTrue($entity->get('canonical'));
        // other record must have canonical false now
        $other = $this->Trees->get(3);
        static::assertFalse($other->get('canonical'));
    }

    /**
     * Data provider for `testFindPathNodes` test case.
     *
     * @return array
     */
    public function findPathNodesProvider()
    {
        return [
            'first' => [
                [11, 12, 4],
                [4],
            ],
            'invalid' => [
                new RecordNotFoundException('Record not found in table "trees"'),
                [3],
            ],
            'bad' => [
                new BadRequestException('Missing required parameter "object id"'),
                [],
            ],
        ];
    }

    /**
     * Test `findPathNodes` method.
     *
     * @param array|\Exception $expected Expected array path or exception.
     * @param array $options Finder options.
     * @return void
     * @dataProvider findPathNodesProvider()
     * @covers ::findPathNodes()
     * @return void
     */
    public function testFindPathNodes($expected, array $options): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $path = $this->Trees->find('pathNodes', $options)
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'object_id',
            ])
            ->toArray();

        static::assertSame($expected, array_values($path));
    }
}
