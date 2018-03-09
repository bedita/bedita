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
     * Folders table
     *
     * @var \BEdita\Core\Model\Table\FoldersTable
     */
    public $Folders;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Folders = TableRegistry::get('Folders');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Folders);

        parent::tearDown();
    }

    /**
     * Test getter for `parent`
     *
     * @return void
     *
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
     *
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
     *
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
     *
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
     * Test for isParentSet()
     *
     * @return void
     *
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
     *
     * @covers ::listAssociations()
     */
    public function testListAssociations()
    {
        $folder = $this->Folders->get(12);
        $folder = $folder->jsonApiSerialize();
        static::assertArrayHasKey('parent', $folder['relationships']);
    }
}
