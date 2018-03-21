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
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.object_relations',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.trees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Trees = TableRegistry::get('Trees');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
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

        $this->assertInstanceOf(BelongsTo::class, $this->Trees->Objects);
        $this->assertInstanceOf(BelongsTo::class, $this->Trees->ParentObjects);
        $this->assertInstanceOf(BelongsTo::class, $this->Trees->RootObjects);
        $this->assertInstanceOf(BelongsTo::class, $this->Trees->ParentNode);
        $this->assertInstanceOf(HasMany::class, $this->Trees->ChildNodes);
        $this->assertInstanceOf(TreeBehavior::class, $this->Trees->behaviors()->get('Tree'));
    }

    /**
     * Data provider for `testIsParentValid()`
     *
     * @return array
     */
    public function isParentValidProvider()
    {
        return [
            'nullWithoutObjectId' => [
                false,
                null,
            ],
            'nullAndFolder' => [
                true,
                null,
                12,
            ],
            'nullNotFolder' => [
                false,
                null,
                4,
            ],
            'folder' => [
                true,
                12,
            ],
            'notAFolder' => [
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
     *
     * @dataProvider isParentValidProvider
     * @covers ::isParentValid()
     */
    public function testIsParentValid($expected, $parentId, $objectId = null)
    {
        $entity = $this->Trees->newEntity();
        if ($objectId !== null) {
            $entity->object_id = $objectId;
        }
        $entity->parent_id = $parentId;
        static::assertEquals($expected, $this->Trees->isParentValid($entity));
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
     * @return void
     *
     * @dataProvider changeRootProvider
     * @covers ::afterSave()
     */
    public function testChangeRoot($rootExpected, $parentId)
    {
        $node = $this->Trees->get(2);
        static::assertEquals(11, $node->root_id);
        $children = $this->Trees->find('children', ['for' => 2])->toList();

        $node->parent_id = $parentId;
        static::assertTrue((bool)$this->Trees->save($node));

        $node = $this->Trees->get(2);
        $actualChildren = $this->Trees->find('children', ['for' => 2])->toList();

        static::assertEquals($rootExpected, $node->root_id);
        static::assertCount(count($children), $actualChildren);
        foreach ($actualChildren as $child) {
            static::assertEquals($rootExpected, $child->root_id);
        }
    }

    /**
     * Test that moving a parent as child fails.
     *
     * @return void
     * @coversNothing
     * @expectedException \RuntimeException
     */
    public function testMoveParentAsChild()
    {
        // create new Folder
        LoggedUser::setUser(['id' => 1]);
        $Folders = TableRegistry::get('Folders');
        $entity = $Folders->newEntity(['title' => 'subsub folder']);
        $entity->type = 'folders';
        $entity->parent = $Folders->get(12);

        $Folders->save($entity);

        $parentNode = $this->Trees
            ->find()
            ->where(['object_id' => $entity->parent->id])
            ->first();

        $parentNode->parent_id = $entity->id;

        $this->Trees->save($parentNode);
    }
}
