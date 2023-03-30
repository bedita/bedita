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

use BEdita\Core\Model\Entity\Tree;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Tree} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Tree
 */
class TreeTest extends TestCase
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
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Trees',
    ];

    /**
     * Trees table
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    public $Trees;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Trees = TableRegistry::getTableLocator()->get('Trees');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Trees);

        parent::tearDown();
    }

    /**
     * Test setter for `parent_id`
     *
     * @return void
     * @covers ::_setParentId()
     */
    public function testSetParentId()
    {
        $tree = new Tree(['object_id' => 12], ['source' => 'Trees']);
        $tree->parent_id = null;
        static::assertEquals($tree->object_id, $tree->root_id);
        static::assertNull($tree->parent_node_id);

        $parentNode = $this->Trees->get(5);
        $tree->parent_id = $parentNode->object_id;
        static::assertEquals($parentNode->root_id, $tree->root_id);
        static::assertEquals($parentNode->id, $tree->parent_node_id);

        $tree = $this->Trees->get(2);
        $tree->parent_id = 11;
        static::assertEquals($tree->root_id, $tree->parent_id);
    }

    /**
     * Test setter for `parent_object`
     *
     * @return void
     * @covers ::_setParentObject()
     */
    public function testSetParentObject()
    {
        $tree = new Tree([], ['source' => 'Trees']);
        $tree->parent_object = null;
        static::assertNull($tree->parent_id);

        $parentFolder = TableRegistry::getTableLocator()->get('Folders')->get(13);
        $tree->parent_object = $parentFolder;
        static::assertEquals(13, $tree->parent_id);
    }
}
