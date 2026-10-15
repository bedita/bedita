<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\TreeCheckCommand;
use BEdita\Core\Model\Table\CategoriesTable;
use BEdita\Core\Model\Table\TreesTable;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Database\Driver\Mysql;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Command\TreeCheckCommand} Test Case.
 */
#[CoversClass(TreeCheckCommand::class)]
class TreeCheckCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use LocatorAwareTrait;

    /**
     * Trees instance
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    protected TreesTable $Trees;

    /**
     * Categories instance
     *
     * @var \BEdita\Core\Model\Table\CategoriesTable
     */
    protected CategoriesTable $Categories;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->cleanupConsoleTrait();
        $this->Trees = $this->fetchTable('Trees');
        $this->Categories = $this->fetchTable('Categories');
    }

    /**
     * Test execution when tree is already valid.
     *
     * @return void
     */
    public function testExecutionOk(): void
    {
        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitSuccess();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
        $this->assertOutputContains('There are no tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('There are no tree nodes that reference a different root than the root of the parent node');
    }

    /**
     * Test execution when there are folders that are not in the tree.
     *
     * @return void
     */
    public function testExecutionFolderNotInTree(): void
    {
        $this->Trees->deleteOrFail(
            $this->Trees->find()
                ->where(['object_id' => 12])
                ->firstOrFail(),
            ['checkRules' => false, '_primary' => false],
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('Found 1 folders not in tree!');
        $this->assertOutputContains('folder <info>sub-folder</info> (#<info>12</info>) is not in the tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
        $this->assertOutputContains('There are no tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('There are no tree nodes that reference a different root than the root of the parent node');
    }

    /**
     * Test execution when there are ubiquitous folders.
     *
     * @return void
     */
    public function testExecutionUbiquitousFolder(): void
    {
        $this->Trees->saveOrFail(
            $this->Trees->newEntity([
                'object_id' => 12,
                'parent_id' => null,
                'slug' => 'foo-bar',
            ]),
            ['checkRules' => false],
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('Found 1 ubiquitous folders!');
        $this->assertOutputContains('folder <info>sub-folder</info> (#<info>12</info>) is ubiquitous');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
        $this->assertOutputContains('There are no tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('There are no tree nodes that reference a different root than the root of the parent node');
    }

    /**
     * Test execution when there are other objects that are roots.
     *
     * @return void
     */
    public function testExecutionOtherObjectInRoot(): void
    {
        $this->Trees->saveOrFail(
            $this->Trees->newEntity([
                'object_id' => 2,
                'parent_id' => null,
                'slug' => 'foo-bar',
            ]),
            ['checkRules' => false],
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('Found 1 other objects in root!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) is a root');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
        $this->assertOutputContains('There are no tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('There are no tree nodes that reference a different root than the root of the parent node');
    }

    /**
     * Test execution when there are other objects that have children.
     *
     * @return void
     */
    public function testExecutionOtherObjectWithChildren(): void
    {
        $this->Trees->saveOrFail(
            $this->Trees->newEntity([
                'object_id' => 4,
                'parent_id' => 2,
                'slug' => 'foo-bar',
            ]),
            ['checkRules' => false],
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('Found 1 other objects with children!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) has children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
        $this->assertOutputContains('There are no tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('There are no tree nodes that reference a different root than the root of the parent node');
    }

    /**
     * Test execution when there are other objects that have children.
     *
     * @return void
     */
    public function testExecutionObjectTwiceInFolder(): void
    {
        // Worse-than-worst case scenario: drop unique index to simulate a case where the constraint was violated somehow.
        if (!($this->Trees->getConnection()->getDriver() instanceof Mysql)) {
            static::markTestSkipped('This test requires dropping a constraint and happens only on MySQL');
        }

        $this->Trees->getConnection()->execute(
            sprintf('DROP INDEX %s ON %s', 'trees_objectparent_uq', $this->Trees->getTable()),
        );
        $this->Trees->saveOrFail(
            $this->Trees->newEntity([
                'object_id' => 2,
                'parent_id' => 11,
                'slug' => 'foo-bar',
            ]),
            ['checkRules' => false],
        );

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('Found 1 objects that are present multiple times within same parent!');
        $this->assertOutputContains('document <info>title-one</info> (#<info>2</info>) is positioned multiple times within the same parent');
        $this->assertOutputContains('There are no tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('There are no tree nodes that reference a different root than the root of the parent node');
    }

    /**
     * Test execution when there are rows that reference a different `parent_id` than the parent node's `object_id`.
     *
     * @return void
     */
    public function testExecutionInconsistentParentId(): void
    {
        $this->Trees->updateAll(['parent_id' => 11], ['parent_id' => 12]);

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
        $this->assertOutputContains('Found 1 tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('profile <info>gustavo-supporto</info> (#<info>4</info>) references a different parent_id than the object_id in the parent node');
        $this->assertOutputContains('There are no tree nodes that reference a different root than the root of the parent node');
    }

    /**
     * Test execution when there are rows that reference a different `root_id` than the parent node's `root_id`.
     *
     * @return void
     */
    public function testExecutionInconsistentRootId(): void
    {
        $this->Trees->updateAll(['root_id' => 13], ['parent_id' => 12]);

        $this->exec(sprintf('%s --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('There are no folders not in tree');
        $this->assertOutputContains('There are no ubiquitous folders');
        $this->assertOutputContains('There are no other objects in root.');
        $this->assertOutputContains('There are no other objects with children');
        $this->assertOutputContains('There are no objects that are present multiple times within same parent');
        $this->assertOutputContains('There are no tree nodes that reference a different parent than the object of the parent node');
        $this->assertOutputContains('Found 1 tree nodes that reference a different root than the root of the parent node');
        $this->assertOutputContains('profile <info>gustavo-supporto</info> (#<info>4</info>) references a different root_id than the one in the parent node');
    }

    /**
     * Test execution when categories tree is already valid.
     *
     * @return void
     */
    public function testExecutionCategoriesOk(): void
    {
        $this->exec(sprintf('%s --categories --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitSuccess();
        $this->assertOutputContains('Categories tree integrity check passed');
    }

    /**
     * Test execution when categories tree NSM is corrupt.
     *
     * @return void
     */
    public function testExecutionCategoriesCorrupt(): void
    {
        $this->Categories->updateAll(['tree_right' => 10], ['parent_id' => 2]);
        $this->exec(sprintf('%s --categories --verbose', TreeCheckCommand::defaultName()));

        $this->assertExitError();
        $this->assertOutputContains('Categories tree is corrupt!');
        $this->assertOutputContains('Found record where parent.tree_right - 1 != MAX(children.tree_right)');
    }
}
